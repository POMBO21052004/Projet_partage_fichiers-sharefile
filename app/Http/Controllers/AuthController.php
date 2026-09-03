<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Otp;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Identifiants invalides.']);
        }

        // Check status
        $accessCheck = $user->canLogin();
        if (!$accessCheck['can_login']) {
            return back()->withErrors(['email' => $accessCheck['message']]);
        }

        // Logic for OTP (Example: Always for first login or every 4 days)
        $latestActivity = $user->updated_at ?? clone $user->created_at; 
        $daysInactive = $latestActivity ? Carbon::now()->diffInDays($latestActivity) : 0;
        $requiresOtp = !$user->is_verified || $daysInactive >= 4;

        if (!$requiresOtp) {
            $user->touch();
            Auth::login($user, $request->has('remember'));
            return $this->redirectBasedOnRole($user);
        }

        // Generate and Send OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::updateOrCreate(
            ['email' => $user->email],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        try {
            Mail::to($user->email)->send(new OtpMail($code));
        } catch (\Exception $e) {
            // Log error or handle
        }

        session(['auth_email' => $user->email, 'auth_remember' => $request->has('remember')]);

        return redirect()->route('otp.verify');
    }

    public function showOtpVerify()
    {
        if (!session('auth_email')) return redirect()->route('login');
        return view('auth.verify-otp', ['email' => session('auth_email')]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $email = session('auth_email');
        $otp = Otp::where('email', $email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        $user = User::where('email', $email)->first();
        Auth::login($user, session('auth_remember'));

        if (!$user->is_verified) {
            $user->is_verified = true;
            $user->save();
        }

        $otp->delete();
        session()->forget(['auth_email', 'auth_remember']);

        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectBasedOnRole($user)
    {
        return $user->role === 'admin' 
            ? redirect()->intended(route('admin.dashboard')) 
            : redirect()->intended(route('user.dashboard'));
    }
}
