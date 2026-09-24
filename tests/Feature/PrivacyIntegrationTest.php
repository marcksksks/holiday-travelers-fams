<?php

namespace Tests\Feature;

use App\Models\PrivacyRequest;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PrivacyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const CURRENT_PASSWORD =
        'CurrentPassword123!';

    public function test_authenticated_user_can_view_privacy_request_workspace(): void
    {
        $user =
            User::factory()
                ->create([
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->get(
                route('settings.index')
            )
            ->assertOk()
            ->assertSee(
                'Privacy & Data Rights',
                false
            )
            ->assertSee(
                'Submit privacy request'
            );
    }

    public function test_privacy_request_requires_current_password(): void
    {
        $user =
            User::factory()
                ->create([
                    'password' => Hash::make(
                        self::CURRENT_PASSWORD
                    ),

                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->from(
                route('settings.index')
            )
            ->post(
                route(
                    'settings.privacy-requests.store'
                ),
                [
                    'type' => PrivacyRequest::TYPE_ERASURE,

                    'privacy_current_password' => 'WrongPassword123!',
                ]
            )
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHasErrors(
                'privacy_current_password'
            );

        $this->assertDatabaseCount(
            'privacy_requests',
            0
        );
    }

    public function test_authenticated_user_can_submit_verified_privacy_request(): void
    {
        $user =
            User::factory()
                ->create([
                    'password' => Hash::make(
                        self::CURRENT_PASSWORD
                    ),

                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->post(
                route(
                    'settings.privacy-requests.store'
                ),
                [
                    'type' => PrivacyRequest::TYPE_ERASURE,

                    'details' => 'Please review data that is no longer necessary.',

                    'privacy_current_password' => self::CURRENT_PASSWORD,
                ]
            )
            ->assertRedirect(
                route('settings.index')
            )
            ->assertSessionHas(
                'status'
            );

        $privacyRequest =
            PrivacyRequest::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->firstOrFail();

        $this->assertSame(
            PrivacyRequest::STATUS_PENDING,
            $privacyRequest->status
        );

        $this->assertNotNull(
            $privacyRequest->identity_verified_at
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'module' => 'privacy',

                'record_id' => (string) $privacyRequest->id,
            ]
        );
    }

    public function test_web_visitor_registration_requires_privacy_acknowledgement(): void
    {
        $receptionist =
            User::factory()
                ->role(
                    User::ROLE_RECEPTIONIST
                )
                ->create([
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($receptionist)
            ->from(
                route('visitors.index')
            )
            ->post(
                route('visitors.store'),
                [
                    'full_name' => 'Privacy Visitor',

                    'visitor_type' => 'guest',

                    'is_walk_in' => true,
                ]
            )
            ->assertRedirect(
                route('visitors.index')
            )
            ->assertSessionHasErrors(
                'privacy_acknowledged'
            );

        $this->assertDatabaseMissing(
            'visitors',
            [
                'full_name' => 'Privacy Visitor',
            ]
        );
    }

    public function test_web_visitor_registration_records_notice_acknowledgement(): void
    {
        $receptionist =
            User::factory()
                ->role(
                    User::ROLE_RECEPTIONIST
                )
                ->create([
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($receptionist)
            ->post(
                route('visitors.store'),
                [
                    'full_name' => 'Acknowledged Visitor',

                    'visitor_type' => 'guest',

                    'is_walk_in' => true,

                    'privacy_acknowledged' => true,
                ]
            )
            ->assertRedirect(
                route('visitors.index')
            );

        $visitor =
            Visitor::query()
                ->where(
                    'full_name',
                    'Acknowledged Visitor'
                )
                ->firstOrFail();

        $this->assertDatabaseHas(
            'privacy_consents',
            [
                'visitor_id' => $visitor->id,

                'recorded_by_user_id' => $receptionist->id,

                'purpose' => 'visitor_management',

                'lawful_basis' => 'legitimate_interests',

                'consent_required' => false,

                'granted' => null,

                'source' => 'visitor_desk',
            ]
        );
    }

    public function test_api_visitor_registration_also_records_privacy_acknowledgement(): void
    {
        $receptionist =
            User::factory()
                ->role(
                    User::ROLE_RECEPTIONIST
                )
                ->create([
                    'force_password_change' => false,
                ]);

        Sanctum::actingAs(
            $receptionist
        );

        $this
            ->postJson(
                '/api/visitors',
                [
                    'full_name' => 'API Privacy Visitor',

                    'visitor_type' => 'guest',

                    'is_walk_in' => true,

                    'privacy_acknowledged' => true,
                ]
            )
            ->assertCreated();

        $visitor =
            Visitor::query()
                ->where(
                    'full_name',
                    'API Privacy Visitor'
                )
                ->firstOrFail();

        $this->assertDatabaseHas(
            'privacy_consents',
            [
                'visitor_id' => $visitor->id,

                'purpose' => 'visitor_management',

                'lawful_basis' => 'legitimate_interests',

                'source' => 'visitor_api',
            ]
        );
    }
}
