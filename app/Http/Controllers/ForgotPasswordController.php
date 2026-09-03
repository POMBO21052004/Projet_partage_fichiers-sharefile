<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::updateOrCreate(
            ['email' => $request->email],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        Mail::to($request->email)->send(new OtpMail($code));

        session(['reset_email' => $request->email]);

        return redirect()->route('password.verify');
    }

    public function showVerifyForm()
    {
        if (!session('reset_email')) return redirect()->route('password.request');
        return view('auth.verify-password', ['email' => session('reset_email')]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $otp = Otp::where('email', session('reset_email'))
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        session(['otp_verified' => true]);

        return redirect()->route('password.reset');
    }

    public function showResetForm()
    {
        if (!session('otp_verified')) return redirect()->route('password.request');
        return view('auth.reset-password');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', session('reset_email'))->first();
        $user->password = Hash::make($request->password);
        $user->is_verified = true; // Auto verify on reset
        $user->save();

        // Cleanup
        Otp::where('email', session('reset_email'))->delete();
        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé.');
    }
}
