<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Visitor;
use App\Services\VisitorForecastDemoDataService;
use App\Services\VisitorTrafficForecastService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisitorForecastDemoDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_synthetic_dataset_makes_local_model_ready(): void
    {
        Facility::create([
            'name' => 'Synthetic Test Meeting Room',

            'description' => 'Forecast test facility',

            'location' => 'Test Floor',

            'capacity' => 30,

            'facility_type' => 'meeting_room',

            'status' => 'available',

            'equipment' => [],
        ]);

        $reference =
            CarbonImmutable::parse(
                '2026-09-26'
            );

        $result =
            app(
                VisitorForecastDemoDataService::class
            )->seed(
                $reference
            );

        $this->assertTrue(
            $result['active']
        );

        $this->assertGreaterThanOrEqual(
            500,
            $result['visitors']
        );

        $this->assertGreaterThan(
            100,
            $result['appointments']
        );

        $this->assertGreaterThan(
            0,
            $result['reservations']
        );

        $distinctDays =
            DB::table('visitors')
                ->where(
                    'notes',
                    'like',
                    VisitorForecastDemoDataService::MARKER
                    .'%'
                )
                ->whereNotNull(
                    'check_in_at'
                )
                ->selectRaw(
                    'COUNT(DISTINCT DATE(check_in_at)) AS total'
                )
                ->value(
                    'total'
                );

        $this->assertGreaterThanOrEqual(
            60,
            (int) $distinctDays
        );

        $forecast =
            app(
                VisitorTrafficForecastService::class
            )->forecastDay(
                $reference->addDay()
            );

        $this->assertSame(
            'ready',
            $forecast['status']
        );

        $this->assertSame(
            'ml_walk_ins_plus_scheduled_demand',
            $forecast['forecast_source']
        );

        $this->assertGreaterThan(
            0,
            $forecast['predicted_walk_ins']
        );

        $this->assertGreaterThan(
            0,
            $forecast['scheduled_visitors']
        );

        $this->assertGreaterThan(
            0,
            $forecast['expected_visitors']
        );
    }

    public function test_purge_removes_only_marked_synthetic_records(): void
    {
        Facility::create([
            'name' => 'Synthetic Test Facility',

            'description' => 'Forecast test facility',

            'location' => 'Test Floor',

            'capacity' => 20,

            'facility_type' => 'meeting_room',

            'status' => 'available',

            'equipment' => [],
        ]);

        $reference =
            CarbonImmutable::parse(
                '2026-09-26'
            );

        app(
            VisitorForecastDemoDataService::class
        )->seed(
            $reference
        );

        $realVisitor =
            Visitor::create([
                'full_name' => 'Non Synthetic Visitor',

                'visitor_type' => 'guest',

                'purpose' => 'Must survive demo purge',

                'is_walk_in' => true,

                'status' => 'completed',

                'check_in_at' => $reference
                    ->subDay()
                    ->setTime(
                        16,
                        0
                    ),

                'check_out_at' => $reference
                    ->subDay()
                    ->setTime(
                        16,
                        30
                    ),

                'duration_minutes' => 30,

                'notes' => 'Normal operational record',
            ]);

        $result =
            app(
                VisitorForecastDemoDataService::class
            )->purge();

        $this->assertFalse(
            $result['active']
        );

        $this->assertSame(
            0,
            $result['total']
        );

        $this->assertDatabaseHas(
            'visitors',
            [
                'id' => $realVisitor->id,

                'full_name' => 'Non Synthetic Visitor',
            ]
        );

        $this->assertDatabaseCount(
            'visitors',
            1
        );
    }

    public function test_reseeding_does_not_duplicate_synthetic_dataset(): void
    {
        Facility::create([
            'name' => 'Synthetic Test Facility',

            'description' => 'Forecast test facility',

            'location' => 'Test Floor',

            'capacity' => 20,

            'facility_type' => 'meeting_room',

            'status' => 'available',

            'equipment' => [],
        ]);

        $service =
            app(
                VisitorForecastDemoDataService::class
            );

        $reference =
            CarbonImmutable::parse(
                '2026-09-26'
            );

        $first =
            $service->seed(
                $reference
            );

        $second =
            $service->seed(
                $reference
            );

        $this->assertSame(
            $first['visitors'],
            $second['visitors']
        );

        $this->assertSame(
            $first['appointments'],
            $second['appointments']
        );

        $this->assertSame(
            $first['reservations'],
            $second['reservations']
        );

        $this->assertSame(
            $first['total'],
            $second['total']
        );
    }
}
