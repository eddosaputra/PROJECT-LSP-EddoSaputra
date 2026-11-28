<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_member_registration_waits_for_admin_approval(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'New Member',
            'phone' => '08123456789',
            'email' => 'new-member@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas(
            'success',
            'Registration submitted successfully. Please wait for an administrator to approve your account.'
        );
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'new-member@example.com',
            'role' => 'member',
            'approval_status' => 'pending',
        ]);
    }

    public function test_pending_and_rejected_members_cannot_log_in(): void
    {
        $pending = User::factory()->create([
            'approval_status' => 'pending',
        ]);
        $rejected = User::factory()->create([
            'approval_status' => 'rejected',
        ]);

        foreach ([
            [$pending, 'awaiting administrator approval'],
            [$rejected, 'rejected by an administrator'],
        ] as [$user, $message]) {
            $response = $this->post(route('login.process'), [
                'email' => $user->email,
                'password' => 'password',
            ]);

            $response->assertRedirect(route('login'));
            $response->assertSessionHasErrors('email');
            $response->assertSessionHas('errors', function ($errors) use ($message) {
                return str_contains($errors->first('email'), $message);
            });
            $this->assertGuest();
        }
    }

    public function test_admin_can_approve_or_reject_pending_members(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $approvedMember = User::factory()->create(['approval_status' => 'pending']);
        $rejectedMember = User::factory()->create(['approval_status' => 'pending']);

        $this->withoutVite();
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Applications Awaiting Review')
            ->assertSee($approvedMember->email);

        $this->actingAs($admin)
            ->post(route('admin.members.approval', $approvedMember), [
                'approval_status' => 'approved',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $approvedMember->id,
            'approval_status' => 'approved',
        ]);

        $this->post(route('admin.members.approval', $rejectedMember), [
            'approval_status' => 'rejected',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $rejectedMember->id,
            'approval_status' => 'rejected',
        ]);
    }

    public function test_non_admin_cannot_decide_member_approval(): void
    {
        $member = User::factory()->create();
        $pendingMember = User::factory()->create(['approval_status' => 'pending']);

        $this->actingAs($member)
            ->post(route('admin.members.approval', $pendingMember), [
                'approval_status' => 'approved',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $pendingMember->id,
            'approval_status' => 'pending',
        ]);
    }

    public function test_approved_members_can_log_in(): void
    {
        $member = User::factory()->create(['approval_status' => 'approved']);

        $this->post(route('login.process'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticatedAs($member);
    }
}
