<?php

namespace App\Controllers;

use App\Models\Student;
use App\Models\Inscription;
use App\Validators\Validator;
use Illuminate\Database\Capsule\Manager as DB;

class EnrollController extends Controller
{
    /**
     * Public endpoint: creates a student and an inscription atomically.
     * Called from the public Apply page — no authentication required.
     */
    public static function store(array $body): void
    {
        $studentData    = $body['student']     ?? [];
        $inscriptionData = $body['inscription'] ?? [];

        // Validate student fields
        $studentErrors = Validator::validate($studentData, [
            'name'                   => 'required|string',
            'email'                  => 'nullable|email',
            'phone'                  => 'nullable|string',
            'gender'                 => 'nullable|in:male,female,other',
            'age'                    => 'nullable|integer',
            'birth_date'             => 'nullable|date',
            'nationality'            => 'nullable|string',
            'province'               => 'nullable|string',
            'commune'                => 'nullable|string',
            'address'                => 'nullable|string',
            'vulnerability_category' => 'nullable|string',
            'education_level'        => 'nullable|string',
            'interest'               => 'nullable|string',
        ]);

        // Validate inscription fields
        $inscriptionErrors = Validator::validate($inscriptionData, [
            'campaign_id'         => 'required|exists:campaigns,id',
            'volet_id'            => 'nullable|exists:volets,id',
            'activity_id'         => 'nullable|exists:activities,id',
            'motivation'          => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'preferred_schedule'  => 'nullable|string',
            'preferred_center'    => 'nullable|string',
        ]);

        $errors = array_merge(
            array_map(fn($e) => array_map(fn($m) => 'student.' . $m, $e), $studentErrors),
            $inscriptionErrors
        );

        if (!empty($studentErrors) || !empty($inscriptionErrors)) {
            apiResponse(false, 'Validation failed', ['student' => $studentErrors, 'inscription' => $inscriptionErrors], 422);
        }

        $result = DB::transaction(function () use ($studentData, $inscriptionData) {
            $birthDate = $studentData['birth_date'] ?? null;
            $calculatedAge = isset($studentData['age']) ? (int) $studentData['age'] : null;

            if (!empty($birthDate)) {
                try {
                    $dob = new \DateTime($birthDate);
                    $now = new \DateTime();
                    $calculatedAge = $now->diff($dob)->y;
                } catch (\Throwable $e) {
                    throw new \Exception('Invalid birth date format. Expected YYYY-MM-DD.');
                }
            }

            $student = Student::create([
                'name'                   => trim($studentData['name']),
                'email'                  => isset($studentData['email']) && $studentData['email']
                    ? strtolower(trim($studentData['email']))
                    : null,
                'phone'                  => $studentData['phone'] ?? null,
                'gender'                 => $studentData['gender'] ?? null,
                'age'                    => $calculatedAge,
                'birth_date'             => $birthDate,
                'nationality'            => $studentData['nationality'] ?? null,
                'province'               => $studentData['province'] ?? null,
                'commune'                => $studentData['commune'] ?? null,
                'address'                => $studentData['address'] ?? null,
                'vulnerability_category' => $studentData['vulnerability_category'] ?? null,
                'education_level'        => $studentData['education_level'] ?? null,
                'interest'               => $studentData['interest'] ?? null,
            ]);

            $refNum = 'INS-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $inscription = Inscription::create([
                'campaign_id'         => (int) $inscriptionData['campaign_id'],
                'student_id'          => $student->id,
                'volet_id'            => isset($inscriptionData['volet_id']) ? (int) $inscriptionData['volet_id'] : null,
                'activity_id'         => isset($inscriptionData['activity_id']) ? (int) $inscriptionData['activity_id'] : null,
                'reference_number'    => $refNum,
                'status'              => 'pending',
                'motivation'          => $inscriptionData['motivation'] ?? null,
                'previous_experience' => $inscriptionData['previous_experience'] ?? null,
                'preferred_schedule'  => $inscriptionData['preferred_schedule'] ?? null,
                'preferred_center'    => $inscriptionData['preferred_center'] ?? null,
                'notes'               => 'Submitted via online portal',
            ]);

            return ['student' => $student, 'inscription' => $inscription];
        });

        apiResponse(true, 'Enrollment submitted successfully', $result, 201);
    }
}
