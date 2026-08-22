<?php

namespace App\Controllers;

use App\Models\Inscription;
use App\Validators\Validator;
use Illuminate\Database\Capsule\Manager as DB;

class InscriptionController extends Controller
{
    public static function index(): void
    {
        $query = Inscription::with(['campaign', 'student', 'volet', 'activity']);
        self::applySearchAndSort($query, ['status', 'reference_number'], ['created_at']);
        self::paginate($query, 'Inscriptions retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'campaign_id'         => 'required|exists:campaigns,id',
            'student_id'          => 'required|exists:students,id',
            'volet_id'            => 'nullable|exists:volets,id',
            'activity_id'         => 'nullable|exists:activities,id',
            'status'              => 'nullable|in:pending,approved,rejected,cancelled',
            'motivation'          => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'preferred_schedule'  => 'nullable|string',
            'preferred_center'    => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (Inscription::where('campaign_id', $body['campaign_id'])->where('student_id', $body['student_id'])->exists()) {
            apiResponse(false, 'The student is already registered for this campaign.', null, 409);
        }

        $inscription = DB::transaction(function () use ($body) {
            $refNum = 'INS-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            return Inscription::create([
                'campaign_id'         => $body['campaign_id'],
                'student_id'          => $body['student_id'],
                'volet_id'            => $body['volet_id'] ?? null,
                'activity_id'         => $body['activity_id'] ?? null,
                'reference_number'    => $refNum,
                'status'              => $body['status'] ?? 'pending',
                'motivation'          => $body['motivation'] ?? null,
                'previous_experience' => $body['previous_experience'] ?? null,
                'preferred_schedule'  => $body['preferred_schedule'] ?? null,
                'preferred_center'    => $body['preferred_center'] ?? null,
                'notes'               => $body['notes'] ?? null,
            ]);
        });

        apiResponse(true, 'Inscription created successfully', $inscription->load(['campaign', 'student', 'volet', 'activity']), 201);
    }

    public static function show(int $id): void
    {
        $inscription = Inscription::with(['campaign', 'student', 'volet', 'activity'])->find($id);
        if (!$inscription) {
            apiResponse(false, 'Inscription not found', null, 404);
        }

        apiResponse(true, 'Inscription retrieved successfully', $inscription);
    }

    public static function update(int $id, array $body): void
    {
        $inscription = Inscription::find($id);
        if (!$inscription) {
            apiResponse(false, 'Inscription not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'campaign_id'         => 'nullable|exists:campaigns,id',
            'student_id'          => 'nullable|exists:students,id',
            'volet_id'            => 'nullable|exists:volets,id',
            'activity_id'         => 'nullable|exists:activities,id',
            'status'              => 'nullable|in:pending,approved,rejected,cancelled',
            'motivation'          => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'preferred_schedule'  => 'nullable|string',
            'preferred_center'    => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (isset($body['campaign_id']) || isset($body['student_id'])) {
            $campaignId = $body['campaign_id'] ?? $inscription->campaign_id;
            $studentId  = $body['student_id']  ?? $inscription->student_id;
            $exists = Inscription::where('campaign_id', $campaignId)
                ->where('student_id', $studentId)
                ->where('id', '!=', $id)
                ->exists();
            if ($exists) {
                apiResponse(false, 'The student is already registered for this campaign.', null, 409);
            }
        }

        $fields = ['campaign_id', 'student_id', 'volet_id', 'activity_id', 'status',
                   'motivation', 'previous_experience', 'preferred_schedule', 'preferred_center', 'notes'];
        foreach ($fields as $field) {
            if (isset($body[$field])) {
                $inscription->{$field} = $body[$field];
            }
        }
        $inscription->save();

        apiResponse(true, 'Inscription updated successfully', $inscription->load(['campaign', 'student', 'volet', 'activity']));
    }

    public static function destroy(int $id): void
    {
        $inscription = Inscription::find($id);
        if (!$inscription) {
            apiResponse(false, 'Inscription not found', null, 404);
        }

        $inscription->delete();
        apiResponse(true, 'Inscription deleted successfully', null, 200);
    }
}
