<?php

namespace App\Controllers;

use App\Models\Student;
use App\Validators\Validator;

class StudentController extends Controller
{
    public static function index(): void
    {
        $query = Student::query();
        self::applySearchAndSort($query, ['name', 'email', 'phone', 'interest', 'address', 'province', 'commune'], ['name', 'created_at']);
        self::paginate($query, 'Students retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
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

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $birthDate = $body['birth_date'] ?? null;
        $calculatedAge = isset($body['age']) ? (int) $body['age'] : null;

        if (!empty($birthDate)) {
            try {
                $dob = new \DateTime($birthDate);
                $now = new \DateTime();
                $calculatedAge = $now->diff($dob)->y;
            } catch (\Throwable $e) {
                // Fallback if date parsing fails
            }
        }

        $student = Student::create([
            'name'                   => trim($body['name']),
            'email'                  => isset($body['email']) && $body['email'] ? strtolower(trim($body['email'])) : null,
            'phone'                  => $body['phone'] ?? null,
            'gender'                 => $body['gender'] ?? null,
            'age'                    => $calculatedAge,
            'birth_date'             => $birthDate,
            'nationality'            => $body['nationality'] ?? null,
            'province'               => $body['province'] ?? null,
            'commune'                => $body['commune'] ?? null,
            'address'                => $body['address'] ?? null,
            'vulnerability_category' => $body['vulnerability_category'] ?? null,
            'education_level'        => $body['education_level'] ?? null,
            'interest'               => $body['interest'] ?? null,
        ]);

        apiResponse(true, 'Student created successfully', $student, 201);
    }

    public static function show(int $id): void
    {
        $student = Student::with('inscriptions')->find($id);
        if (!$student) {
            apiResponse(false, 'Student not found', null, 404);
        }

        apiResponse(true, 'Student retrieved successfully', $student);
    }

    public static function update(int $id, array $body): void
    {
        $student = Student::find($id);
        if (!$student) {
            apiResponse(false, 'Student not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'name'                   => 'nullable|string',
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

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (isset($body['birth_date']) && !empty($body['birth_date'])) {
            try {
                $dob = new \DateTime($body['birth_date']);
                $now = new \DateTime();
                $body['age'] = $now->diff($dob)->y;
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        $fields = ['name', 'email', 'phone', 'gender', 'age', 'birth_date', 'nationality',
                   'province', 'commune', 'address', 'vulnerability_category', 'education_level', 'interest'];
        foreach ($fields as $field) {
            if (isset($body[$field])) {
                $student->{$field} = $field === 'email' && $body[$field] ? strtolower(trim($body[$field])) : $body[$field];
            }
        }
        $student->save();

        apiResponse(true, 'Student updated successfully', $student);
    }

    public static function destroy(int $id): void
    {
        $student = Student::find($id);
        if (!$student) {
            apiResponse(false, 'Student not found', null, 404);
        }

        $student->delete();
        apiResponse(true, 'Student deleted successfully', null, 200);
    }
}
