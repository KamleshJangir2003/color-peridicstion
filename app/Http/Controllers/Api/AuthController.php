<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\UserLogin;
use App\Services\OtpService;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        private OtpService $otpService,
        private ReferralService $referralService
    ) {}

    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => 'required|unique:users,phone',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6|confirmed',
            'referral_code'=> 'required|exists:users,referral_code',
            'otp'          => 'required|string|size:6',
        ]);

        if (!$this->otpService->verify($request->email, $request->otp, 'register')) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        $user = User::create([
            'name'          => $request->name,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'referral_code' => strtoupper(Str::random(8)),
            'referred_by'   => $request->referral_code,
            'device_id'     => $request->header('X-Device-ID'),
        ]);

        Wallet::create(['user_id' => $user->id]);

        if ($request->referral_code) {
            $this->referralService->registerReferral($user, $request->referral_code);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->is_blocked) {
            return response()->json(['message' => 'Account blocked'], 403);
        }

        $user->update(['last_login_at' => now(), 'device_id' => $request->header('X-Device-ID')]);

        \App\Models\UserLogin::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'device_id'  => $request->header('X-Device-ID'),
            'user_agent' => $request->userAgent(),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type'  => 'required|in:register,login,forgot_password,withdrawal',
        ]);

        $otp = $this->otpService->generate($request->email, $request->type);

        Mail::raw("Your ColorWin OTP is: {$otp}\n\nValid for 10 minutes. Do not share with anyone.", function ($msg) use ($request, $otp) {
            $msg->to($request->email)
                ->subject('ColorWin OTP Verification');
        });

        return response()->json(['message' => 'OTP sent to your email']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone'    => 'required|exists:users,phone',
            'otp'      => 'required|size:6',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!$this->otpService->verify($request->phone, $request->otp, 'forgot_password')) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        User::where('phone', $request->phone)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password reset successful']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->where('is_admin', true)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid admin credentials'], 401);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('admin-api', ['admin'])->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }
}
