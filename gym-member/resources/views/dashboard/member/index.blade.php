@extends('layouts.dashboard')

@section('title', 'Member Dashboard')

@section('page-heading', 'Member Dashboard')

@section('dashboard-content')

<div class="dashboard-section-heading" id="profile">
    <h3>Welcome, {{ auth()->user()->name }}</h3>
    <span>Membership, payments, and gym updates in one place</span>
</div>

<div class="dashboard-panel p-4 mb-4">
    <div class="row g-3">
        <div class="col-md-4"><strong>Name:</strong> {{ auth()->user()->name }}</div>
        <div class="col-md-4"><strong>Email:</strong> {{ auth()->user()->email }}</div>
        <div class="col-md-4"><strong>Phone:</strong> {{ auth()->user()->phone ?: '-' }}</div>
    </div>
</div>

<div class="admin-stat-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Membership requests</span>
            <i class="bi bi-card-checklist"></i>
        </div>
        <h2>{{ $registrations->count() }}</h2>
        <small>Your membership order history</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Active membership</span>
            <i class="bi bi-person-check"></i>
        </div>
        <h2>{{ $registrations->where('status', 'active')->count() }}</h2>
        <small>Verified and active memberships</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Announcements</span>
            <i class="bi bi-megaphone"></i>
        </div>
        <h2>{{ $announcements->count() }}</h2>
        <small>Latest updates from the gym</small>
    </div>
</div>

<div class="dashboard-section-heading mt-4" id="memberships">
    <h3>Membership Packages</h3>
    <span>Choose a plan to send a membership request</span>
</div>

<div class="dashboard-panel admin-table-panel p-4">
    @if($canBook && $packages->isNotEmpty())
        <form action="{{ route('member.registrations.store') }}" method="POST" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-9">
                <label for="package_id" class="form-label">Available package</label>
                <select id="package_id" name="package_id" class="form-select" required>
                    <option value="">Select a membership package</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" @selected(old('package_id') == $package->id)>
                            {{ $package->name }} — Rp{{ number_format((float) $package->price, 0, ',', '.') }}
                            / {{ $package->duration_days }} days
                        </option>
                    @endforeach
                </select>
                @error('package_id')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-danger w-100">Request membership</button>
            </div>
        </form>
    @elseif(! $canBook)
        <p class="mb-0">You already have a pending request, an approved order awaiting payment, or an active membership.</p>
    @else
        <p class="mb-0">Belum ada paket membership aktif. Hubungi admin agar menambahkan paket sebelum Anda memesan.</p>
    @endif
</div>

<div class="dashboard-section-heading mt-4" id="membership-orders">
    <h3>My Membership Orders</h3>
    <span>Track approval and payment status</span>
</div>

<div class="dashboard-panel admin-table-panel">
    <div class="table-responsive">
        <table class="table admin-member-table align-middle">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Price</th>
                    <th>Order status</th>
                    <th>Payment status</th>
                    <th>Membership period</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $registration)
                    @php($latestPayment = $registration->payments->first())
                    <tr>
                        <td>{{ $registration->package->name }}</td>
                        <td>Rp{{ number_format((float) $registration->package->price, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($registration->status) }}</td>
                        <td>{{ $latestPayment ? ucfirst($latestPayment->status) : 'Not submitted' }}</td>
                        <td>
                            @if($registration->start_date && $registration->end_date)
                                {{ $registration->start_date->format('d M Y') }} – {{ $registration->end_date->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @if($latestPayment && $latestPayment->status === 'rejected' && $latestPayment->notes)
                        <tr>
                            <td colspan="5" class="text-warning">Payment note: {{ $latestPayment->notes }}</td>
                        </tr>
                    @endif
                    @if($registration->status === 'approved' && (! $latestPayment || $latestPayment->status === 'rejected'))
                        <tr>
                            <td colspan="5">
                                <form action="{{ route('member.payments.store', $registration) }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label">Payment method</label>
                                        <select name="payment_method" class="form-select" required>
                                            <option value="">Select method</option>
                                            <option value="bank_transfer">Bank transfer</option>
                                            <option value="e_wallet">E-wallet</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment proof (JPG, PNG, WEBP; max 5 MB)</label>
                                        <input type="file" name="proof_image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-danger w-100">Submit payment confirmation</button>
                                    </div>
                                    @error('payment')
                                        <div class="col-12 text-danger">{{ $message }}</div>
                                    @enderror
                                    @error('proof_image')
                                        <div class="col-12 text-danger">{{ $message }}</div>
                                    @enderror
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="empty-table">You have not requested a membership package yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="dashboard-section-heading mt-4" id="payment-history">
    <h3>Payment History</h3>
    <span>Review submitted payment confirmations and their status</span>
</div>

<div class="dashboard-panel admin-table-panel">
    <div class="table-responsive">
        <table class="table admin-member-table align-middle">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Admin note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->registration->package->name }}</td>
                        <td>Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                        <td>{{ $payment->notes ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">No payment confirmations have been submitted.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="dashboard-section-heading mt-4" id="announcements">
    <h3>Gym Announcements</h3>
    <span>Updates from FITMEMBER</span>
</div>

<div class="row g-3">
    @forelse($announcements as $announcement)
        <div class="col-12">
            <article class="dashboard-panel p-4">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <h4>{{ $announcement->title }}</h4>
                    <small>{{ $announcement->created_at->format('d M Y') }}</small>
                </div>
                <p class="mb-0">{!! nl2br(e($announcement->content)) !!}</p>
            </article>
        </div>
    @empty
        <div class="col-12">
            <div class="dashboard-panel p-4">There are no announcements at this time.</div>
        </div>
    @endforelse
</div>

@endsection
