<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Visitor;
use App\Services\VisitorTrafficForecastService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorTrafficForecastServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'visitor_forecast.demo_mode' => false,
        ]);
    }

    public function test_cold_start_does_not_invent_ml_prediction(): void
    {
        $target = CarbonImmutable::parse(
            '2026-10-05'
        );

        Appointment::create([
            'visitor_name' => 'Scheduled Visitor One',

            'visitor_type' => 'guest',

            'date' => $target->toDateString(),

            'start_time' => '10:00',

            'end_time' => '10:30',

            'purpose' => 'Synthetic test appointment',

            'status' => 'scheduled',
        ]);

        Appointment::create([
            'visitor_name' => 'Scheduled Visitor Two',

            'visitor_type' => 'guest',

            'date' => $target->toDateString(),

            'start_time' => '10:00',

            'end_time' => '10:30',

            'purpose' => 'Synthetic test appointment',

            'status' => 'scheduled',
        ]);

        $forecast = app(
            VisitorTrafficForecastService::class
        )->forecastDay(
            $target
        );

        $this->assertSame(
            'cold_start',
            $forecast['status']
        );

        $this->assertSame(
            'scheduled_demand_only',
            $forecast['forecast_source']
        );

        $this->assertSame(
            'unavailable',
            $forecast['confidence']
        );

        $this->assertSame(
            2,
            $forecast['scheduled_visitors']
        );

        $this->assertSame(
            0.0,
            $forecast['predicted_walk_ins']
        );

        $this->assertSame(
            2,
            $forecast['expected_visitors']
        );

        $tenAm = collect(
            $forecast['hourly']
        )->firstWhere(
            'hour',
            '10:00'
        );

        $this->assertNotNull(
            $tenAm
        );

        $this->assertSame(
            2,
            $tenAm['scheduled_appointments']
        );

        $this->assertSame(
            0.0,
            $tenAm['predicted_walk_ins']
        );

        $this->assertSame(
            2,
            $tenAm['expected_visitors']
        );
    }

    public function test_model_learns_historical_walk_in_pattern_locally(): void
    {
        $target = CarbonImmutable::parse(
            '2026-07-13'
        );

        /*
         * 21 historical dates × 5 walk-ins = 105 visits.
         * This crosses the MVP model-readiness thresholds.
         */
        for ($day = 1; $day <= 21; $day++) {
            $visitDate = $target
                ->subDays($day)
                ->setTime(
                    10,
                    0
                );

            for ($visitor = 1; $visitor <= 5; $visitor++) {
                Visitor::create([
                    'full_name' => "Synthetic Visitor {$day}-{$visitor}",

                    'visitor_type' => 'guest',

                    'purpose' => 'Synthetic forecasting test',

                    'is_walk_in' => true,

                    'status' => 'completed',

                    'check_in_at' => $visitDate->addMinutes(
                        $visitor
                    ),

                    'check_out_at' => $visitDate->addMinutes(
                        30
                        +
                        $visitor
                    ),

                    'duration_minutes' => 30,
                ]);
            }
        }

        Appointment::create([
            'visitor_name' => 'Synthetic Scheduled One',

            'visitor_type' => 'guest',

            'date' => $target->toDateString(),

            'start_time' => '10:00',

            'end_time' => '10:30',

            'purpose' => 'Synthetic forecasting test',

            'status' => 'scheduled',
        ]);

        Appointment::create([
            'visitor_name' => 'Synthetic Scheduled Two',

            'visitor_type' => 'guest',

            'date' => $target->toDateString(),

            'start_time' => '10:00',

            'end_time' => '10:30',

            'purpose' => 'Synthetic forecasting test',

            'status' => 'scheduled',
        ]);

        $forecast = app(
            VisitorTrafficForecastService::class
        )->forecastDay(
            $target
        );

        $this->assertSame(
            'ready',
            $forecast['status']
        );

        $this->assertSame(
            'local_knn_regression_v1',
            $forecast['model']
        );

        $this->assertSame(
            'ml_walk_ins_plus_scheduled_demand',
            $forecast['forecast_source']
        );

        $this->assertSame(
            105,
            $forecast['history']['timestamped_visits']
        );

        $this->assertSame(
            21,
            $forecast['history']['distinct_visit_days']
        );

        $tenAm = collect(
            $forecast['hourly']
        )->firstWhere(
            'hour',
            '10:00'
        );

        $this->assertNotNull(
            $tenAm
        );

        $this->assertSame(
            2,
            $tenAm['scheduled_appointments']
        );

        $this->assertGreaterThan(
            0,
            $tenAm['predicted_walk_ins']
        );

        $this->assertGreaterThan(
            2,
            $tenAm['expected_visitors']
        );

        $this->assertContains(
            $forecast['confidence'],
            [
                'low',
                'medium',
                'high',
            ]
        );
    }

    public function test_forecast_output_contains_no_visitor_pii(): void
    {
        $target = CarbonImmutable::parse(
            '2026-10-05'
        );

        Visitor::create([
            'full_name' => 'Sensitive Example Person',

            'contact_number' => '09123456789',

            'email' => 'sensitive@example.test',

            'organization' => 'Private Example Organization',

            'visitor_type' => 'guest',

            'purpose' => 'Sensitive purpose should not appear in forecast',

            'is_walk_in' => true,

            'status' => 'completed',

            'check_in_at' => $target
                ->subDay()
                ->setTime(
                    10,
                    0
                ),

            'check_out_at' => $target
                ->subDay()
                ->setTime(
                    10,
                    30
                ),

            'duration_minutes' => 30,
        ]);

        $forecast = app(
            VisitorTrafficForecastService::class
        )->forecastDay(
            $target
        );

        $encoded = json_encode(
            $forecast,
            JSON_THROW_ON_ERROR
        );

        $this->assertStringNotContainsString(
            'Sensitive Example Person',
            $encoded
        );

        $this->assertStringNotContainsString(
            '09123456789',
            $encoded
        );

        $this->assertStringNotContainsString(
            'sensitive@example.test',
            $encoded
        );

        $this->assertStringNotContainsString(
            'Private Example Organization',
            $encoded
        );

        $this->assertStringNotContainsString(
            'Sensitive purpose should not appear in forecast',
            $encoded
        );
    }

    public function test_demo_mode_uses_in_memory_training_without_persisting_history(): void
    {
        config([
            'visitor_forecast.demo_mode' => true,
        ]);

        $forecast =
            app(
                VisitorTrafficForecastService::class
            )->forecastForDisplayDay(
                '2026-10-05'
            );

        $this->assertTrue(
            $forecast['demo_mode']
        );

        $this->assertSame(
            'ready',
            $forecast['status']
        );

        $this->assertSame(
            'synthetic_demo_model_plus_scheduled_demand',
            $forecast['forecast_source']
        );

        $this->assertSame(
            'demonstration',
            $forecast['confidence']
        );

        $this->assertSame(
            0,
            $forecast['history']['real_timestamped_visits']
        );

        $this->assertSame(
            0,
            $forecast['history']['real_distinct_visit_days']
        );

        $this->assertGreaterThanOrEqual(
            500,
            $forecast['history']['timestamped_visits']
        );

        $this->assertSame(
            70,
            $forecast['history']['distinct_visit_days']
        );

        $this->assertGreaterThan(
            0,
            $forecast['predicted_walk_ins']
        );

        $this->assertDatabaseCount(
            'visitors',
            0
        );

        $this->assertDatabaseCount(
            'appointments',
            0
        );

        $this->assertDatabaseCount(
            'reservations',
            0
        );
    }

    public function test_synthetic_demo_model_uses_chronological_holdout_backtest(): void
    {
        config([
            'visitor_forecast.demo_mode' => true,
        ]);

        $validation =
            app(
                VisitorTrafficForecastService::class
            )->validateSyntheticDemoModel(
                '2026-10-05'
            );

        $this->assertTrue(
            $validation['available']
        );

        $this->assertSame(
            'synthetic_academic_demo',
            $validation['dataset']
        );

        $this->assertSame(
            'chronological_holdout',
            $validation['method']
        );

        $this->assertSame(
            56,
            $validation['training_days']
        );

        $this->assertSame(
            14,
            $validation['validation_days']
        );

        $this->assertSame(
            560,
            $validation['training_observations']
        );

        $this->assertSame(
            140,
            $validation['validation_observations']
        );

        $this->assertGreaterThanOrEqual(
            0,
            $validation['mae_visitors_per_hour']
        );

        $this->assertGreaterThanOrEqual(
            0,
            $validation['wape_percent']
        );

        $this->assertStringContainsString(
            'do not establish real-world production accuracy',
            $validation['notice']
        );
    }

    public function test_synthetic_backtest_is_unavailable_when_demo_mode_is_disabled(): void
    {
        config([
            'visitor_forecast.demo_mode' => false,
        ]);

        $validation =
            app(
                VisitorTrafficForecastService::class
            )->validateSyntheticDemoModel(
                '2026-10-05'
            );

        $this->assertFalse(
            $validation['available']
        );

        $this->assertSame(
            'none',
            $validation['dataset']
        );
    }
}
