<?php

namespace Modules\EOffice\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Letter;

class LetterVerificationController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $letter = Letter::query()->where('verification_token', $token)->first();

        if ($letter === null) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json([
            'valid' => true,
            'letter_number' => $letter->code,
            'name' => $letter->name,
            'status' => $letter->status,
        ]);
    }
}
