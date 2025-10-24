<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\SmsService;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function showForgotForm()
    {
        return view('layouts.auth.forgot_phone');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return back()->with('error', 'Phone number not found.');
        }

        $response = $this->smsService->sendOtp($request->phone);

        if (!$response || $response['status'] !== 'success') {
            return back()->with('error', 'Failed to send OTP. Please try again.');
        }

        $otp = $response['data']['otp_code'] ?? null;

        if (!$otp) {
            return back()->with('error', 'Failed to retrieve OTP code from IPROG.');
        }

        DB::table('password_resets')->updateOrInsert(
            ['phone' => $request->phone],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
                'created_at' => Carbon::now()
            ]
        );

        session(['reset_phone' => $request->phone]);

        return redirect()->route('verify_otp')->with('success', 'OTP sent successfully!');
    }

    public function showVerifyForm()
    {
        return view('layouts.auth.verify_otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|array',
        ]);

        $enteredOtp = implode('', $request->otp);
        $phone = session('reset_phone');

        $otpData = DB::table('password_resets')->where('phone', $phone)->first();

        if (!$otpData || $otpData->otp != $enteredOtp || Carbon::now()->greaterThan($otpData->expires_at)) {
            return back()->with('error', 'Invalid or expired OTP.');
        }

        session(['otp_verified' => true]);
        return redirect()->route('reset_password');
    }

    public function showResetPassword()
    {
        return view('layouts.auth.reset_password');
    }

    public function resetPassword(Request $request)
    {
        if (!session('otp_verified')) {
            return redirect()->route('forgot_phone')->with('error', 'Unauthorized Access');
        }

        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $phone = session('reset_phone');
        $user = User::where('phone', $phone)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget(['reset_phone', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password reset successful!');
    }
}
