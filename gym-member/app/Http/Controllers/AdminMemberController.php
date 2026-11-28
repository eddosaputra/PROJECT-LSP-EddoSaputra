<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminMemberController extends Controller
{
    public function updateApproval(Request $request, User $user)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        abort_unless($user->role === 'member', 404);

        $validated = $request->validate([
            'approval_status' => ['required', 'in:approved,rejected'],
        ]);

        if ($user->approval_status !== 'pending') {
            return back()->withErrors([
                'approval_status' => 'Permintaan ini sudah diproses sebelumnya.',
            ]);
        }

        $user->update([
            'approval_status' => $validated['approval_status'],
        ]);

        $message = $validated['approval_status'] === 'approved'
            ? 'Akun member berhasil disetujui.'
            : 'Pendaftaran member berhasil ditolak.';

        return redirect()->route('admin.dashboard')->with('success', $message);
    }
}
