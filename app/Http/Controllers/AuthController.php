<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is banned
            if ($user->is_banned) {
                Auth::logout();
                return back()->with(['error' => 'Akun Anda telah dibekukan. Hubungi administrator.']);
            }

            // DEV BYPASS: Skip 2FA for Admin roles
            if (in_array($user->role, ['global_admin', 'univ_admin'])) {
                $request->session()->regenerate();
                return redirect()->intended('/');
            }

            // Manual users must verify email before 2FA
            if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
                try {
                    $user->sendEmailVerificationNotification();
                } catch (\Exception $e) {
                    Log::warning('Email verification notification failed: ' . $e->getMessage());
                }

                Auth::logout();
                return redirect()->route('verification.notice')->with('error', 'Email belum diverifikasi. Silakan cek inbox.');
            }

            // Generate 2FA Code
            $code = rand(100000, 999999);
            $user->two_factor_secret = bcrypt($code);
            $user->save();

            // Send Email
            try {
                Mail::to($user->email)->send(new \App\Mail\TwoFactorCode($user, $code));
            } catch (\Exception $e) {
                Log::error('Mail fail: ' . $e->getMessage());
            }

            // Store user ID in session for verify step and logout temporarily
            $request->session()->put('auth.2fa.id', $user->id);
            Auth::logout();

            return redirect()->route('2fa.verify');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function redirectToGoogle()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret') || !config('services.google.redirect')) {
            return redirect()->route('login')->with('error', 'Login dengan Google belum dikonfigurasi.');
        }

        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::warning('Google OAuth callback failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect()->route('login')->with('error', 'Google tidak memberikan email.');
        }

        // Enforce Raharja email domain
        if (!preg_match('/@(raharja\.info|raharja\.ac\.id)$/', $email)) {
            return redirect()->route('login')->with('error', 'Hanya email @raharja.info atau @raharja.ac.id yang diperbolehkan.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $name = $googleUser->getName() ?: Str::before($email, '@');
            $baseUsername = Str::slug(Str::before($email, '@'), '_');
            $username = $this->generateUniqueUsername($baseUsername);

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
            ]);
        }

        // Update Google fields (if columns exist)
        if (Schema::hasColumn('users', 'google_id')) {
            $user->google_id = $googleUser->getId();
        }

        if (Schema::hasColumn('users', 'avatar') && $googleUser->getAvatar()) {
            // Store avatar (prefer keeping existing if already set)
            if (!$user->avatar) {
                $user->avatar = $googleUser->getAvatar();
            }
        }

        // SSO users are treated as verified by default
        if (Schema::hasColumn('users', 'email_verified_at')) {
            $user->email_verified_at = $user->email_verified_at ?: now();
        }

        $user->save();

        // Check if user is banned
        if ($user->is_banned) {
            return redirect()->route('login')->with('error', 'Akun Anda telah dibekukan. Hubungi administrator.');
        }

        // DEV BYPASS: Skip 2FA for Admin roles (same behavior as password login)
        if (in_array($user->role, ['global_admin', 'univ_admin'])) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // SSO users do not require 2FA
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    private function generateUniqueUsername(string $baseUsername): string
    {
        $baseUsername = preg_replace('/[^a-zA-Z0-9_\-]/', '', $baseUsername) ?: 'user';

        $candidate = $baseUsername;
        $counter = 1;

        while (User::where('username', $candidate)->exists()) {
            $counter++;
            $candidate = $baseUsername . '_' . $counter;
        }

        return $candidate;
    }

    public function verify2faView()
    {
        if (!session()->has('auth.2fa.id')) {
            return redirect()->route('login');
        }
        return view('auth.verify-2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        if (!session()->has('auth.2fa.id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('auth.2fa.id'));

        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        if (Hash::check($request->code, $user->two_factor_secret)) {
            $request->session()->forget('auth.2fa.id');
            $user->two_factor_secret = null; // Clear code after use
            $user->save();

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->with('error', 'Kode verifikasi salah.');
    }

    public function registerView()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/@(raharja\.info|raharja\.ac\.id)$/', $value)) {
                        $fail('Hanya email @raharja.info atau @raharja.ac.id yang diperbolehkan.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
