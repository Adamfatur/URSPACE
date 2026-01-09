<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

            // Generate 2FA Code
            $code = rand(100000, 999999);
            $user->two_factor_secret = bcrypt($code);
            $user->save();

            // Send Email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorCode($user, $code));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail fail: ' . $e->getMessage());
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

    public function bypassLogin(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        $request->validate([
            'role' => ['required', 'string', 'in:global_admin,univ_admin,dept_admin,moderator,user'],
        ]);

        $user = User::where('role', $request->role)->first();

        if (!$user) {
            return back()->with('error', 'User dengan role tersebut tidak ditemukan.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'Berhasil masuk sebagai ' . $user->name);
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
