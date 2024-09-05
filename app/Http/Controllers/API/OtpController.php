<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tzsk\Otp\Facades\Otp;

class OtpController extends Controller
{
    public function send(User $user)
    {
        $uniqueToken = Str::random(10);

        $otp = Otp::generate($uniqueToken);

        $expirationMinutes = config('otp.expiry');

        $receivers = [$user->email];

        $email = Email::create([
            'subject' => 'One Time Password Verification',
            'from' => auth()->id(),
            'is_read' => false,
            'is_sent' => true,
            'is_important' => false,
            'owner_id' => $user->id,
            'group_id' => Str::random(),
        ]);

        $email->messages()->create([
            'content' => "Your OTP is: {$otp}. This OTP will expire in {$expirationMinutes} min. Do not reply to this email.",
            'sender_id' => auth()->id(),
        ]);

        $receivers = collect($receivers)->map(function ($receiver) {
            $user = User::where('email', $receiver)->firstOrFail();
            return ['receiver_id' => $user->id];
        })->push(['receiver_id' => auth()->id()]);

        $email->receivers()->createMany($receivers);

        return response()->json(['message' => 'OTP sent successfully!', 'unique_token' => $uniqueToken]);
    }

    public function verify(User $user, Request $request)
    {
        $validated = $request->validate([
            'unique_token' => ['required', 'string', 'min:10'],
            'otp' => ['required', 'string', 'max:10'],
        ]);

        $isVerified = Otp::verify($validated['otp'], $validated['unique_token']);

        if ($isVerified) {
            otp()->forget($validated['unique_token']);
            return response()->json(['message' => 'OTP verified successfully!']);
        }

        return response()->json(['message' => 'OTP verification failed!'], 400);
    }
}
