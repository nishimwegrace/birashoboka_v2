<?php

namespace App\Validators;

use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;
            $ruleParts = is_array($ruleset) ? $ruleset : explode('|', $ruleset);
            $nullable = in_array('nullable', $ruleParts, true);
            $required = in_array('required', $ruleParts, true);

            if ($required && ($value === null || $value === '')) {
                $errors[$field][] = sprintf('The %s field is required.', $field);
                continue;
            }

            if ($value === null || $value === '') {
                if ($nullable) {
                    continue;
                }
                if (!$required) {
                    continue;
                }
            }

            foreach ($ruleParts as $rule) {
                if ($rule === 'required' || $rule === 'nullable') {
                    continue;
                }

                if ($rule === 'string' && !is_string($value)) {
                    $errors[$field][] = sprintf('The %s must be a string.', $field);
                    continue;
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = sprintf('The %s must be a valid email address.', $field);
                }

                if (str_starts_with($rule, 'min:')) {
                    $limit = (int) explode(':', $rule, 2)[1];
                    if (is_string($value) && mb_strlen($value) < $limit) {
                        $errors[$field][] = sprintf('The %s must be at least %d characters.', $field, $limit);
                    }
                }

                if (str_starts_with($rule, 'integer') && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $errors[$field][] = sprintf('The %s must be an integer.', $field);
                }

                if (str_starts_with($rule, 'date') && !self::isValidDate($value)) {
                    $errors[$field][] = sprintf('The %s must be a valid date.', $field);
                }

                if (str_starts_with($rule, 'in:')) {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowed, true)) {
                        $errors[$field][] = sprintf('The %s must be one of: %s.', $field, implode(', ', $allowed));
                    }
                }

                if (str_starts_with($rule, 'unique:')) {
                    [$table, $column, $ignoreId, $idColumn] = array_pad(explode(',', substr($rule, 7)), 4, null);
                    $idColumn = $idColumn ?: 'id';
                    $query = Capsule::table($table)->where($column, $value);
                    if ($ignoreId) {
                        $query->where($idColumn, '!=', $ignoreId);
                    }
                    if ($query->exists()) {
                        $errors[$field][] = sprintf('The %s has already been taken.', $field);
                    }
                }

                if (str_starts_with($rule, 'exists:')) {
                    [$table, $column] = explode(',', substr($rule, 7));
                    $exists = Capsule::table($table)->where($column, $value)->exists();
                    if (!$exists) {
                        $errors[$field][] = sprintf('The selected %s is invalid.', $field);
                    }
                }
            }
        }

        return $errors;
    }

    private static function isValidDate($value): bool
    {
        if (!$value || !is_string($value)) {
            return false;
        }

        $timestamp = strtotime($value);
        return $timestamp !== false;
    }
}
