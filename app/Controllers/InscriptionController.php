<?php

namespace App\Controllers;

use App\Models\Inscription;
use App\Validators\Validator;
use Illuminate\Database\Capsule\Manager as DB;

class InscriptionController extends Controller
{
    public static function index(): void
    {
        $query = Inscription::with(['campaign', 'student']);
        self::applySearchAndSort($query, ['status'], ['created_at']);
        self::paginate($query, 'Inscriptions retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'campaign_id' => 'required|exists:campaigns,id',
            'student_id' => 'required|exists:students,id',
            'status' => 'nullable|in:pending,approved,rejected,cancelled',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (Inscription::where('campaign_id', $body['campaign_id'])->where('student_id', $body['student_id'])->exists()) {
            apiResponse(false, 'The student is already registered for this campaign.', null, 409);
        }

        $inscription = DB::transaction(function () use ($body) {
            return Inscription::create([
                'campaign_id' => $body['campaign_id'],
                'student_id' => $body['student_id'],
                'status' => $body['status'] ?? 'pending',
            ]);
        });

        apiResponse(true, 'Inscription created successfully', $inscription, 201);
    }

    public static function show(int $id): void
    {
        $inscription = Inscription::with(['campaign', 'student'])->find($id);
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
            'campaign_id' => 'nullable|exists:campaigns,id',
            'student_id' => 'nullable|exists:students,id',
            'status' => 'nullable|in:pending,approved,rejected,cancelled',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (isset($body['campaign_id']) || isset($body['student_id'])) {
            $campaignId = $body['campaign_id'] ?? $inscription->campaign_id;
            $studentId = $body['student_id'] ?? $inscription->student_id;
            $exists = Inscription::where('campaign_id', $campaignId)
                ->where('student_id', $studentId)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                apiResponse(false, 'The student is already registered for this campaign.', null, 409);
            }
        }

        foreach (['campaign_id', 'student_id', 'status'] as $field) {
            if (isset($body[$field])) {
                $inscription->{$field} = $body[$field];
            }
        }
        $inscription->save();

        apiResponse(true, 'Inscription updated successfully', $inscription);
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
