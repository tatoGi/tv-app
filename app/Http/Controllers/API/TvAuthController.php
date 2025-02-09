<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TvAuth\{RequestCodeRequest, AuthenticateCodeRequest, PollTokenRequest};
use App\Models\DeviceCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class TvAuthController extends Controller
{
    private const MAX_ATTEMPTS = 3;
    private const BLOCK_DURATION = 300; // 5 minutes

    public function generateTvCode(Request $request)
    {
        try {
            // Check if IP is blocked
            $ip = $request->ip();
            if ($this->isIPBlocked($ip)) {
                return response()->json([
                    'message' => 'Too many attempts. Please try again later.'
                ], 429);
            }

            $code = strtoupper(Str::random(6));

            $deviceCode = DeviceCode::create([
                'code' => $code,
                'expires_at' => now()->addMinutes(5),
                'is_used' => false
            ]);

            return response()->json([
                'code' => $code,
                'expires_in' => 300
            ]);
        } catch (\Exception $e) {
            Log::error('TV Code Generation Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error generating code'], 500);
        }
    }

    public function activateTvCode(Request $request)
    {
        try {
            $request->validate([
                'code' => ['required', 'string', 'size:6', 'regex:/^[A-Z0-9]+$/']
            ]);

            $deviceCode = DeviceCode::where('code', $request->code)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->first();

            if (!$deviceCode) {
                $this->incrementFailedAttempts($request->ip());
                return response()->json([
                    'message' => 'Invalid or expired code'
                ], 400);
            }

            $deviceCode->update([
                'user_id' => $request->user()->id,
                'is_used' => true
            ]);

            // Clear failed attempts after successful authentication
            Cache::forget("tv_auth_attempts:{$request->ip()}");

            return response()->json([
                'message' => 'Code activated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('TV Code Activation Error: ' . $e->getMessage());
            return response()->json(['message' => 'Activation error'], 500);
        }
    }

    public function pollTvCode(Request $request)
    {

        try {
            $request->validate([
                'code' => ['required', 'string', 'size:6', 'regex:/^[A-Z0-9]+$/']
            ]);

            $deviceCode = DeviceCode::with('user')
                ->where('code', $request->code)
                ->where('expires_at', '>', now())
                ->first();

            if (!$deviceCode) {
                return response()->json([
                    'message' => 'Code expired or invalid'
                ], 400);
            }

            if (!$deviceCode->is_used) {
                return response()->json([
                    'status' => 'pending'
                ]);
            }

            if (!$deviceCode->user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $token = $deviceCode->user->createToken('TV-Device', ['tv-access']);

            // Invalidate the code after successful token generation
            $deviceCode->delete();

            return response()->json([
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'scope' => 'tv-access'
            ]);
        } catch (\Exception $e) {
            Log::error('TV Code Poll Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing request'], 500);
        }
    }

    private function isIPBlocked(string $ip): bool
    {
        $attempts = Cache::get("tv_auth_attempts:$ip", 0);
        return $attempts >= self::MAX_ATTEMPTS;
    }

    private function incrementFailedAttempts(string $ip): void
    {
        $attempts = Cache::get("tv_auth_attempts:$ip", 0) + 1;
        Cache::put("tv_auth_attempts:$ip", $attempts, now()->addSeconds(self::BLOCK_DURATION));

        if ($attempts >= self::MAX_ATTEMPTS) {
            Log::warning('IP blocked due to multiple failed attempts', ['ip' => $ip]);
        }
    }
}