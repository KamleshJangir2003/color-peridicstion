<?php

namespace App\Services;

use App\Models\OtpVerification;

class OtpService
{
    public function generate(string $phoneOrEmail, string $type): string
    {
        OtpVerification::where('phone_or_email', $phoneOrEmail)
            ->where('type', $type)
            ->where('is_used', false)
            ->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'phone_or_email' => $phoneOrEmail,
            'otp'            => $otp,
            'type'           => $type,
            'expires_at'     => now()->addMinutes(10),
        ]);

        return $otp;
    }

    public function verify(string $phoneOrEmail, string $otp, string $type): bool
    {
        $record = OtpVerification::where('phone_or_email', $phoneOrEmail)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) return false;

        $record->update(['is_used' => true]);
        return true;
    }
}
