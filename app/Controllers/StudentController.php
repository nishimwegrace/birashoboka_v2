<?php

namespace App\Controllers;

use App\Models\Student;
use App\Validators\Validator;

class StudentController extends Controller
{
    public static function index(): void
    {
        $query = Student::query();
        self::applySearchAndSort($query, ['name', 'email', 'phone', 'interest', 'address'], ['name', 'created_at']);
        self::paginate($query, 'Students retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer',
            'address' => 'nullable|string',
            'interest' => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $student = Student::create([
            'name' => trim($body['name']),
            'email' => $body['email'] ? strtolower(trim($body['email'])) : null,
            'phone' => $body['phone'] ?? null,
            'gender' => $body['gender'] ?? null,
            'age' => isset($body['age']) ? (int) $body['age'] : null,
            'address' => $body['address'] ?? null,
            'interest' => $body['interest'] ?? null,
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
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer',
            'address' => 'nullable|string',
            'interest' => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        foreach (['name', 'email', 'phone', 'gender', 'age', 'address', 'interest'] as $field) {
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
