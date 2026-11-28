<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_update_name_phone_and_profile_photo(): void
    {
        Storage::fake('public');
        $member = User::factory()->create(['approval_status' => 'approved']);
        $oldPhotoPath = UploadedFile::fake()->create('old.jpg', 10, 'image/jpeg')
            ->store('profile-photos', 'public');
        $member->update(['profile_photo_path' => $oldPhotoPath]);

        $this->withoutVite();
        $this->actingAs($member)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Full name')
            ->assertSee('Phone number')
            ->assertSee('topbar-avatar-image');

        $this->put(route('profile.update'), [
            'name' => 'Updated Member',
            'phone' => '08123456789',
            'profile_photo' => UploadedFile::fake()->create('member.jpg', 10, 'image/jpeg'),
        ])->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success', 'Profile updated successfully.');

        $member->refresh();
        $this->assertSame('Updated Member', $member->name);
        $this->assertSame('08123456789', $member->phone);
        $this->assertNotSame($oldPhotoPath, $member->profile_photo_path);
        Storage::disk('public')->assertExists($member->profile_photo_path);
        Storage::disk('public')->assertMissing($oldPhotoPath);
    }

    public function test_admin_can_update_name_phone_and_profile_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('profile.update'), [
                'name' => 'Updated Admin',
                'phone' => '08987654321',
                'profile_photo' => UploadedFile::fake()->create('admin.png', 10, 'image/png'),
            ])
            ->assertRedirect(route('profile.edit'));

        $admin->refresh();
        $this->assertSame('Updated Admin', $admin->name);
        $this->assertSame('08987654321', $admin->phone);
        Storage::disk('public')->assertExists($admin->profile_photo_path);
    }

    public function test_saving_profile_without_a_new_photo_keeps_the_existing_photo(): void
    {
        Storage::fake('public');
        $member = User::factory()->create(['approval_status' => 'approved']);
        $photoPath = UploadedFile::fake()->create('current.jpg', 10, 'image/jpeg')
            ->store('profile-photos', 'public');
        $member->update(['profile_photo_path' => $photoPath]);

        $this->actingAs($member)
            ->put(route('profile.update'), [
                'name' => 'Updated Without Photo',
                'phone' => '08111111111',
            ])
            ->assertRedirect(route('profile.edit'));

        $member->refresh();
        $this->assertSame($photoPath, $member->profile_photo_path);
        Storage::disk('public')->assertExists($photoPath);
    }
}
