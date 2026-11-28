<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberPortalController extends Controller
{
    public function index()
    {
        $user = $this->member();
        $registrations = $user->registrations()
            ->with(['package', 'payments'])
            ->latest()
            ->get();

        $canBook = ! $this->hasCurrentRegistration($user);

        return view('dashboard.member.index', [
            'registrations' => $registrations,
            'payments' => Payment::whereHas('registration', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('registration.package')->latest()->get(),
            'packages' => Package::where('is_active', true)->orderBy('price')->get(),
            'announcements' => Announcement::where('is_published', true)->latest()->get(),
            'canBook' => $canBook,
        ]);
    }

    public function storeRegistration(Request $request)
    {
        $user = $this->member();

        $validated = $request->validate([
            'package_id' => [
                'required',
                Rule::exists('packages', 'id')->where('is_active', true),
            ],
        ]);

        $created = DB::transaction(function () use ($user, $validated) {
            User::query()->lockForUpdate()->findOrFail($user->id);

            if ($this->hasCurrentRegistration($user)) {
                return false;
            }

            Registration::create([
                'user_id' => $user->id,
                'package_id' => $validated['package_id'],
                'status' => 'pending',
            ]);

            return true;
        });

        if (! $created) {
            return back()->withErrors([
                'package_id' => 'Anda sudah memiliki pemesanan yang belum selesai atau membership aktif.',
            ]);
        }

        return redirect()->route('member.dashboard')
            ->with('success', 'Pemesanan membership berhasil dikirim dan menunggu persetujuan admin.');
    }

    public function storePayment(Request $request, Registration $registration)
    {
        $user = $this->member();
        abort_unless($registration->user_id === $user->id, 404);

        if ($registration->status !== 'approved') {
            return back()->withErrors([
                'payment' => 'Pembayaran hanya dapat dikonfirmasi untuk pemesanan yang sudah disetujui.',
            ]);
        }

        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(['bank_transfer', 'e_wallet', 'cash'])],
            'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $created = DB::transaction(function () use ($registration, $user, $validated) {
            $registration = Registration::query()->lockForUpdate()->findOrFail($registration->id);

            if ($registration->user_id !== $user->id
                || $registration->status !== 'approved'
                || $registration->payments()->whereIn('status', ['pending', 'verified'])->exists()) {
                return false;
            }

            $registration->load('package');
            $proofPath = $validated['proof_image']->store('payment-proofs', 'local');

            Payment::create([
                'registration_id' => $registration->id,
                'amount' => $registration->package->price,
                'payment_method' => $validated['payment_method'],
                'proof_image' => $proofPath,
                'status' => 'pending',
                'paid_at' => now(),
            ]);

            return true;
        });

        if (! $created) {
            return back()->withErrors([
                'payment' => 'Konfirmasi pembayaran tidak dapat dikirim karena pemesanan sudah berubah atau pembayaran telah diproses.',
            ]);
        }

        return redirect()->route('member.dashboard')
            ->with('success', 'Konfirmasi pembayaran berhasil dikirim dan menunggu verifikasi admin.');
    }

    private function member(): User
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->role === 'member' && $user->approval_status === 'approved',
            403
        );

        return $user;
    }

    private function hasCurrentRegistration(User $user): bool
    {
        return $user->registrations()
            ->where(function ($query) {
                $query->whereIn('status', ['pending', 'approved'])
                    ->orWhere(function ($query) {
                        $query->where('status', 'active')
                            ->where(function ($query) {
                                $query->whereNull('end_date')
                                    ->orWhereDate('end_date', '>=', today());
                            });
                    });
            })
            ->exists();
    }
}
