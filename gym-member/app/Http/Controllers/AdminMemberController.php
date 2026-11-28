<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminMemberController extends Controller
{
    public function index()
    {
        $this->ensureAdmin();

        return view('admin.members.index', [
            'members' => User::where('role', 'member')->latest()->paginate(15),
        ]);
    }

    public function edit(User $user)
    {
        $this->ensureMember($user);

        return view('admin.members.edit', [
            'member' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->ensureMember($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Member account updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->ensureMember($user);

        $paymentProofs = Payment::whereHas('registration', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereNotNull('proof_image')->pluck('proof_image');
        $profilePhoto = $user->profile_photo_path;

        DB::transaction(function () use ($user) {
            $user->delete();
        });

        Storage::disk('local')->delete($paymentProofs->all());

        if ($profilePhoto) {
            Storage::disk('public')->delete($profilePhoto);
        }

        return redirect()->route('admin.members.index')
            ->with('success', 'Member account and associated membership records deleted successfully.');
    }

    public function updateApproval(Request $request, User $user)
    {
        $this->ensureMember($user);

        $validated = $request->validate([
            'approval_status' => ['required', 'in:approved,rejected'],
        ]);

        if ($user->approval_status !== 'pending') {
            return back()->withErrors([
                'approval_status' => 'This request has already been processed.',
            ]);
        }

        $user->update([
            'approval_status' => $validated['approval_status'],
        ]);

        $message = $validated['approval_status'] === 'approved'
            ? 'Member account approved successfully.'
            : 'Member registration rejected successfully.';

        return redirect()->route('admin.dashboard')->with('success', $message);
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }

    private function ensureMember(User $user): void
    {
        $this->ensureAdmin();
        abort_unless($user->role === 'member', 404);
    }
}
