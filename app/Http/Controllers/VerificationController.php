<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show verification status page
     */
    public function show()
    {
        $user = Auth::user();
        
        return view('auth.verify', compact('user'));
    }

    /**
     * Resend email verification
     */
    public function resendEmail()
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return back()->with('message', 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();
        
        return back()->with('message', 'Verification email sent.');
    }

    /**
     * Send phone verification code
     */
    public function sendPhoneCode()
    {
        $user = Auth::user();
        
        if (!$user->telephone) {
            return back()->withErrors(['phone' => 'No phone number on file.']);
        }

        if ($user->hasVerifiedPhone()) {
            return back()->with('message', 'Phone already verified.');
        }

        $code = $user->generatePhoneVerificationCode();
        
        // In production, send SMS here
        // For testing, show in session message
        if (app()->environment('local', 'testing')) {
            return back()->with('verification_code', "Your verification code is: {$code}");
        }

        // TODO: Implement SMS sending
        // SMS::send($user->telephone, "Your verification code is: {$code}");
        
        return back()->with('message', 'Verification code sent to your phone.');
    }

    /**
     * Verify phone with code
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        
        if ($user->verifyPhone($request->verification_code)) {
            return back()->with('message', 'Phone verified successfully!');
        }

        return back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }

    /**
     * Bypass verification (testing only)
     */
    public function bypassVerification()
    {
        if (!app()->environment('local', 'testing')) {
            abort(404);
        }

        $user = Auth::user();
        $user->update([
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'bypass_verification' => true,
        ]);

        return redirect()->route('dashboard')->with('message', 'Verification bypassed for testing.');
    }
}
