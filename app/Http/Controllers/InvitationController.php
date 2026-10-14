<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvitationController extends Controller
{
    /**
     * GET /invitation?invitation_id=1
     * Mengambil daftar RSVP berdasarkan ID event.
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'invitation_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $entries = Invitation::query()
            ->where('invitation_id', $request->integer('invitation_id'))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $entries,
        ]);
    }

    /**
     * POST /invitation
     * Menyimpan RSVP baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'invitation_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'delegation' => ['nullable', 'string', 'max:255'],
            'attendance' => [
                'required',
                'in:opening_ceremony,closing_ceremony,both_ceremony,undecided',
            ],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $entry = Invitation::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'RSVP berhasil disimpan.',
            'data' => $entry,
        ], 201);
    }
}
