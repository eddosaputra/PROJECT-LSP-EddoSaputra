<?php

use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\AdminWorkflowController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', function () {
    return view('home', [
        'packages' => Package::where('is_active', true)->orderBy('price')->get(),
    ]);
})->name('home');

// Authentication - Guest
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.process');

});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Dashboard
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/member/dashboard', [MemberPortalController::class, 'index'])
        ->name('member.dashboard');
    Route::post('/member/registrations', [MemberPortalController::class, 'storeRegistration'])
        ->name('member.registrations.store');
    Route::post('/member/registrations/{registration}/payments', [MemberPortalController::class, 'storePayment'])
        ->name('member.payments.store');

    Route::get('/admin/dashboard', function () {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.index', [
            'pendingMembers' => User::where('role', 'member')
                ->where('approval_status', 'pending')
                ->latest()
                ->get(),
            'pendingRegistrations' => Registration::with(['user', 'package'])
                ->where('status', 'pending')
                ->latest()
                ->get(),
            'pendingPayments' => Payment::with(['registration.user', 'registration.package'])
                ->where('status', 'pending')
                ->latest()
                ->get(),
            'activeMemberships' => Registration::where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', today());
                })
                ->count(),
            'expiredMemberships' => Registration::where('status', 'expired')
                ->orWhere(function ($query) {
                    $query->where('status', 'active')
                        ->whereDate('end_date', '<', today());
                })
                ->count(),
        ]);
    })->name('admin.dashboard');

    Route::get('/admin/members', [AdminMemberController::class, 'index'])
        ->name('admin.members.index');
    Route::get('/admin/members/{user}/edit', [AdminMemberController::class, 'edit'])
        ->name('admin.members.edit');
    Route::put('/admin/members/{user}', [AdminMemberController::class, 'update'])
        ->name('admin.members.update');
    Route::delete('/admin/members/{user}', [AdminMemberController::class, 'destroy'])
        ->name('admin.members.destroy');
    Route::post('/admin/members/{user}/approval', [AdminMemberController::class, 'updateApproval'])
        ->name('admin.members.approval');

    Route::post('/admin/registrations/{registration}/status', [AdminWorkflowController::class, 'updateRegistrationStatus'])
        ->name('admin.registrations.status');
    Route::post('/admin/payments/{payment}/status', [AdminWorkflowController::class, 'updatePaymentStatus'])
        ->name('admin.payments.status');
    Route::get('/admin/payments/{payment}/proof', [AdminWorkflowController::class, 'downloadPaymentProof'])
        ->name('admin.payments.proof');

    Route::get('/admin/packages', [PackageController::class, 'index'])
        ->name('admin.packages.index');
    Route::post('/admin/packages', [PackageController::class, 'store'])
        ->name('admin.packages.store');
    Route::get('/admin/packages/{package}/edit', [PackageController::class, 'edit'])
        ->name('admin.packages.edit');
    Route::put('/admin/packages/{package}', [PackageController::class, 'update'])
        ->name('admin.packages.update');
    Route::patch('/admin/packages/{package}/status', [PackageController::class, 'toggleStatus'])
        ->name('admin.packages.status');

    Route::get('/admin/announcements', [AnnouncementController::class, 'index'])
        ->name('admin.announcements.index');
    Route::post('/admin/announcements', [AnnouncementController::class, 'store'])
        ->name('admin.announcements.store');
    Route::get('/admin/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])
        ->name('admin.announcements.edit');
    Route::put('/admin/announcements/{announcement}', [AnnouncementController::class, 'update'])
        ->name('admin.announcements.update');
    Route::delete('/admin/announcements/{announcement}', [AnnouncementController::class, 'destroy'])
        ->name('admin.announcements.destroy');

});
