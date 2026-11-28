<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminWorkflowController extends Controller
{
    public function updateRegistrationStatus(Request $request, Registration $registration)
    {
        $this->admin();

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        if ($registration->status !== 'pending') {
            return back()->withErrors([
                'registration' => 'This membership request has already been processed.',
            ]);
        }

        $registration->update(['status' => $validated['status']]);

        $message = $validated['status'] === 'approved'
            ? 'Membership request approved successfully.'
            : 'Membership request rejected successfully.';

        return redirect()->route('admin.dashboard')->with('success', $message);
    }

    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $this->admin();

        $validated = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $processed = DB::transaction(function () use ($payment, $validated) {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->status !== 'pending') {
                return false;
            }

            $registration = $payment->registration()->with('package')->lockForUpdate()->firstOrFail();

            if ($validated['status'] === 'verified' && $registration->status !== 'approved') {
                return false;
            }

            $payment->status = $validated['status'];
            $payment->notes = $validated['notes'] ?? null;
            $payment->save();

            if ($validated['status'] === 'verified') {
                $startDate = today();
                $registration->status = 'active';
                $registration->start_date = $startDate;
                $registration->end_date = $startDate->copy()
                    ->addDays($registration->package->duration_days);
                $registration->save();
            }

            return true;
        });

        if (! $processed) {
            return back()->withErrors([
                'payment' => 'This payment cannot be processed because its status changed or the membership request has not been approved.',
            ]);
        }

        $message = $validated['status'] === 'verified'
            ? 'Payment verified and membership activated successfully.'
            : 'Payment rejected successfully.';

        return redirect()->route('admin.dashboard')->with('success', $message);
    }

    public function downloadPaymentProof(Payment $payment)
    {
        $this->admin();
        abort_unless($payment->proof_image && Storage::disk('local')->exists($payment->proof_image), 404);

        return Storage::disk('local')->download($payment->proof_image);
    }

    private function admin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }
}
