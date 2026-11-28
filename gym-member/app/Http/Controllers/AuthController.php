<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'member' && $user->approval_status !== 'approved') {
            $statusMessage = $user->approval_status === 'rejected'
                ? 'Your member registration was rejected by an administrator.'
                : 'Your account is awaiting administrator approval.';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => $statusMessage]);
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'member') {
            $announcements = Announcement::where('is_published', true)
                ->latest()
                ->take(5)
                ->get(['title', 'content'])
                ->map(fn (Announcement $announcement): array => [
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                ])
                ->all();

            return redirect()->route('member.dashboard')
                ->with('login_announcements', $announcements);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors(['email' => 'This account has an invalid role.']);
    }

    // Menampilkan halaman register
    public function showRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('member.dashboard');
        }

        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'member',
            'approval_status' => 'pending',
        ]);

        return redirect()->route('login')
            ->with('success', 'Registration submitted successfully. Please wait for an administrator to approve your account.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
