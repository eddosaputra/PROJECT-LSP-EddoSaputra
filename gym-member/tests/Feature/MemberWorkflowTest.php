<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_request_an_active_package_and_see_its_status(): void
    {
        $member = User::factory()->create(['approval_status' => 'approved']);
        $package = $this->createPackage();

        $this->withoutVite();
        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee($package->name)
            ->assertSee('id="profile"', false)
            ->assertSee('id="memberships"', false)
            ->assertSee('id="payment-history"', false)
            ->assertSee('id="announcements"', false);

        $this->post(route('member.registrations.store'), [
            'package_id' => $package->id,
        ])->assertRedirect(route('member.dashboard'));

        $this->assertDatabaseHas('registrations', [
            'user_id' => $member->id,
            'package_id' => $package->id,
            'status' => 'pending',
        ]);
    }

    public function test_member_can_submit_a_payment_proof_and_admin_verification_activates_membership(): void
    {
        Storage::fake('local');
        $member = User::factory()->create(['approval_status' => 'approved']);
        $admin = User::factory()->create(['role' => 'admin']);
        $package = $this->createPackage(['duration_days' => 30, 'price' => 450000]);
        $registration = Registration::create([
            'user_id' => $member->id,
            'package_id' => $package->id,
            'status' => 'approved',
        ]);

        $this->actingAs($member)
            ->post(route('member.payments.store', $registration), [
                'payment_method' => 'bank_transfer',
                'proof_image' => UploadedFile::fake()->create('receipt.jpg', 10, 'image/jpeg'),
            ])
            ->assertRedirect(route('member.dashboard'));

        $payment = Payment::firstOrFail();
        $this->assertSame('pending', $payment->status);
        $this->assertSame('450000.00', $payment->amount);
        Storage::disk('local')->assertExists($payment->proof_image);

        $this->withoutVite();
        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Payment History')
            ->assertSee('Bank Transfer')
            ->assertSee(route('member.dashboard').'#payment-history', false);

        $this->actingAs($admin)
            ->post(route('admin.payments.status', $payment), [
                'status' => 'verified',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'verified',
        ]);
        $registration->refresh();
        $this->assertSame('active', $registration->status);
        $this->assertSame(today()->addDays(30)->toDateString(), $registration->end_date->toDateString());
    }

    public function test_member_can_retry_payment_after_rejection(): void
    {
        Storage::fake('local');
        $member = User::factory()->create(['approval_status' => 'approved']);
        $admin = User::factory()->create(['role' => 'admin']);
        $package = $this->createPackage();
        $registration = Registration::create([
            'user_id' => $member->id,
            'package_id' => $package->id,
            'status' => 'approved',
        ]);
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'amount' => $package->price,
            'payment_method' => 'cash',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.status', $payment), [
                'status' => 'rejected',
                'notes' => 'Proof is unclear.',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($member)
            ->post(route('member.payments.store', $registration), [
                'payment_method' => 'e_wallet',
                'proof_image' => UploadedFile::fake()->create('receipt.jpg', 10, 'image/jpeg'),
            ])
            ->assertRedirect(route('member.dashboard'));

        $this->assertDatabaseCount('payments', 2);
    }

    public function test_member_sees_only_published_announcements(): void
    {
        $member = User::factory()->create(['approval_status' => 'approved']);
        $admin = User::factory()->create(['role' => 'admin']);
        Announcement::create([
            'user_id' => $admin->id,
            'title' => 'Published update',
            'content' => 'The gym opens early.',
            'is_published' => true,
        ]);
        Announcement::create([
            'user_id' => $admin->id,
            'title' => 'Draft update',
            'content' => 'Not visible to members.',
            'is_published' => false,
        ]);

        $this->withoutVite();
        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Published update')
            ->assertDontSee('Draft update');
    }

    public function test_admin_can_approve_or_reject_membership_requests(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create();
        $package = $this->createPackage();
        $approvedRequest = Registration::create([
            'user_id' => $member->id,
            'package_id' => $package->id,
            'status' => 'pending',
        ]);
        $rejectedRequest = Registration::create([
            'user_id' => $member->id,
            'package_id' => $package->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.registrations.status', $approvedRequest), ['status' => 'approved'])
            ->assertRedirect(route('admin.dashboard'));
        $this->post(route('admin.registrations.status', $rejectedRequest), ['status' => 'rejected'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('registrations', [
            'id' => $approvedRequest->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $rejectedRequest->id,
            'status' => 'rejected',
        ]);
    }

    public function test_admin_can_manage_packages_and_announcements(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->post(route('admin.packages.store'), [
            'name' => 'Monthly',
            'price' => 300000,
            'duration_days' => 30,
            'description' => 'Monthly membership',
            'is_active' => 1,
        ])->assertRedirect(route('admin.packages.index'));

        $package = Package::firstOrFail();
        $this->put(route('admin.packages.update', $package), [
            'name' => 'Monthly Plus',
            'price' => 350000,
            'duration_days' => 30,
            'description' => 'Updated plan',
            'is_active' => 1,
        ])->assertRedirect(route('admin.packages.index'));
        $this->patch(route('admin.packages.status', $package))
            ->assertRedirect(route('admin.packages.index'));
        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => 'Monthly Plus',
            'is_active' => false,
        ]);

        $this->post(route('admin.announcements.store'), [
            'title' => 'Holiday hours',
            'content' => 'We close early on Friday.',
            'is_published' => 1,
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::firstOrFail();
        $this->put(route('admin.announcements.update', $announcement), [
            'title' => 'Updated holiday hours',
            'content' => 'We close early on Saturday.',
            'is_published' => 1,
        ])->assertRedirect(route('admin.announcements.index'));
        $this->delete(route('admin.announcements.destroy', $announcement))
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_homepage_lists_active_packages_from_admin_package_management(): void
    {
        $member = User::factory()->create();
        $package = $this->createPackage(['name' => 'Homepage plan']);

        $this->withoutVite();
        $this->actingAs($member)
            ->get(route('home'))
            ->assertOk()
            ->assertSee($package->name)
            ->assertSee(route('member.dashboard').'#memberships', false);
    }

    public function test_non_admin_cannot_review_payments_or_manage_announcements(): void
    {
        $member = User::factory()->create(['approval_status' => 'approved']);
        $package = $this->createPackage();
        $registration = Registration::create([
            'user_id' => $member->id,
            'package_id' => $package->id,
            'status' => 'approved',
        ]);
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        $this->actingAs($member)
            ->post(route('admin.payments.status', $payment), ['status' => 'verified'])
            ->assertForbidden();
        $this->post(route('admin.registrations.status', $registration), ['status' => 'approved'])
            ->assertForbidden();
        $this->post(route('admin.announcements.store'), [
            'title' => 'Unauthorized',
            'content' => 'Should not be saved.',
        ])->assertForbidden();
    }

    private function createPackage(array $attributes = []): Package
    {
        return Package::create(array_merge([
            'name' => 'Standard',
            'price' => 300000,
            'duration_days' => 30,
            'description' => 'Standard membership',
            'is_active' => true,
        ], $attributes));
    }
}
