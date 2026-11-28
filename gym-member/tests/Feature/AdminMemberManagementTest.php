<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_and_edit_member_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create([
            'name' => 'Original Member',
            'phone' => '08111111111',
        ]);

        $this->withoutVite();
        $this->actingAs($admin)
            ->get(route('admin.members.index'))
            ->assertOk()
            ->assertSee($member->email)
            ->assertSee('Edit');

        $this->get(route('admin.members.edit', $member))
            ->assertOk()
            ->assertSee('Original Member');

        $this->put(route('admin.members.update', $member), [
            'name' => 'Updated Member',
            'email' => 'updated-member@example.com',
            'phone' => '08222222222',
        ])->assertRedirect(route('admin.members.index'));

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Updated Member',
            'email' => 'updated-member@example.com',
            'phone' => '08222222222',
            'role' => 'member',
        ]);
    }

    public function test_admin_cannot_edit_or_delete_an_admin_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.members.edit', $otherAdmin))
            ->assertNotFound();

        $this->delete(route('admin.members.destroy', $otherAdmin))
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $otherAdmin->id,
            'role' => 'admin',
        ]);
    }

    public function test_non_admin_cannot_manage_member_accounts(): void
    {
        $member = User::factory()->create();
        $anotherMember = User::factory()->create();

        $this->actingAs($member)
            ->get(route('admin.members.index'))
            ->assertForbidden();

        $this->put(route('admin.members.update', $anotherMember), [
            'name' => 'Unauthorized Change',
            'email' => 'unauthorized@example.com',
        ])->assertForbidden();

        $this->delete(route('admin.members.destroy', $anotherMember))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $anotherMember->id,
            'name' => $anotherMember->name,
        ]);
    }

    public function test_deleting_a_member_removes_their_membership_records_and_uploaded_files(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create([
            'profile_photo_path' => 'profile-photos/member.jpg',
        ]);
        $package = Package::create([
            'name' => 'Monthly',
            'price' => 100,
            'duration_days' => 30,
            'is_active' => true,
        ]);
        $registration = Registration::create([
            'user_id' => $member->id,
            'package_id' => $package->id,
            'status' => 'pending',
        ]);
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'amount' => 100,
            'payment_method' => 'bank_transfer',
            'proof_image' => 'payment-proofs/member.jpg',
            'status' => 'pending',
        ]);

        Storage::disk('local')->put($payment->proof_image, 'payment proof');
        Storage::disk('public')->put($member->profile_photo_path, 'profile photo');

        $this->actingAs($admin)
            ->delete(route('admin.members.destroy', $member))
            ->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $member->id]);
        $this->assertDatabaseMissing('registrations', ['id' => $registration->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
        Storage::disk('local')->assertMissing($payment->proof_image);
        Storage::disk('public')->assertMissing($member->profile_photo_path);
    }
}
