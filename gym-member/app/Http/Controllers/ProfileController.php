<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $oldPhotoPath = $user->profile_photo_path;
        $profilePhoto = $validated['profile_photo'] ?? null;
        unset($validated['profile_photo']);

        if ($profilePhoto) {
            $validated['profile_photo_path'] = $profilePhoto->store('profile-photos', 'public');
        }

        $user->update($validated);

        if ($oldPhotoPath && isset($validated['profile_photo_path'])) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
