<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

class TwoFactorController extends Controller
{
    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(Request $request, EnableTwoFactorAuthentication $enable): JsonResponse
    {
        $enable($request->user());

        return response()->json([
            'message' => 'Two-factor authentication enabled successfully.',
            'recovery_codes' => $request->user()->recoveryCodes(),
        ]);
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(Request $request, DisableTwoFactorAuthentication $disable): JsonResponse
    {
        $disable($request->user());

        return response()->json([
            'message' => 'Two-factor authentication disabled successfully.',
        ]);
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(Request $request, GenerateNewRecoveryCodes $generate): JsonResponse
    {
        $generate($request->user());

        return response()->json([
            'message' => 'Recovery codes regenerated successfully.',
            'recovery_codes' => $request->user()->recoveryCodes(),
        ]);
    }

    /**
     * Get the current two-factor authentication status.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'enabled' => $user->hasEnabledTwoFactorAuthentication(),
            'recovery_codes_count' => $user->recoveryCodes()->count(),
            'qr_code_url' => $user->twoFactorQrCodeUrl(),
            'secret' => $user->twoFactorSecret(),
        ]);
    }

    /**
     * Get the QR code URL for setting up 2FA.
     */
    public function qrCode(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json([
                'error' => 'Two-factor authentication not enabled.',
            ], 400);
        }

        return response()->json([
            'qr_code_url' => $user->twoFactorQrCodeUrl(),
            'secret' => $user->twoFactorSecret(),
        ]);
    }

    /**
     * Confirm two-factor authentication setup.
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (!$user->twoFactorSecret()) {
            return response()->json([
                'error' => 'Two-factor authentication not set up.',
            ], 400);
        }

        if (!$user->verifyTwoFactorCode($request->code)) {
            return response()->json([
                'error' => 'Invalid two-factor code.',
            ], 422);
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Two-factor authentication confirmed successfully.',
            'recovery_codes' => $user->recoveryCodes(),
        ]);
    }

    /**
     * Get recovery codes.
     */
    public function recoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasEnabledTwoFactorAuthentication()) {
            return response()->json([
                'error' => 'Two-factor authentication not enabled.',
            ], 400);
        }

        return response()->json([
            'recovery_codes' => $user->recoveryCodes(),
        ]);
    }
}