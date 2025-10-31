<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserBiometric;
use Carbon\Carbon;
use App\Traits\ApiResponseTrait;

class BiometricAuthController extends Controller
{
    use ApiResponseTrait;
    public function challenge(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $biometric = UserBiometric::where('user_id', $request->user_id)->first();
        if (!$biometric) {
            return response()->json(['error' => 'No public key found'], 404);
        }

        $challenge = Str::random(32);
        Cache::put("biometric_challenge:{$request->user_id}", [
            'challenge' => $challenge,
            'user_id' => $request->user_id
        ], now()->addMinutes(2));

        return $this->success([
            'challenge' => $challenge,
        ], 'Biometric challenge generated successfully');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'challenge' => 'required|string',
            'signature' => 'required|string'
        ]);

        $cached = Cache::get("biometric_challenge:{$request->user_id}");
        if (!$cached || $cached['challenge'] !== $request->challenge) {
            return response()->json(['error' => 'Invalid or expired challenge'], 400);
        }

        $biometric = UserBiometric::where('user_id', $request->user_id)->first();
        if (!$biometric) {
            return response()->json(['error' => 'Public key not found'], 404);
        }

        $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split($biometric->public_key, 64, "\n") .
            "-----END PUBLIC KEY-----";

        $verified = openssl_verify(
            $request->challenge,
            base64_decode($request->signature),
            $publicKey,
            OPENSSL_ALGO_SHA256
        );

        if ($verified !== 1) {
            return response()->json(['error' => 'Signature verification failed'], 401);
        }

        $user = User::find($request->user_id);
        $token = $user->createToken('Personal Access Token');
        $accessToken = $token->accessToken;
        $expiresAt = Carbon::parse($token->token->expires_at)->toDateTimeString();

        $tokenResult = [
            'role' => $user->roles[0]->name ?? 'admin',
            'access_token' => $accessToken,
            'expires_at' => $expiresAt,
            'user_id' => $user->id,
        ];

        return $this->success($tokenResult, 'Login successful');
    }
}
