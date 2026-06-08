<?php

namespace App\Shared\Validation;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $fieldRules) {

            $value = trim($data[$field] ?? '');

            // REQUIRED
            if (($fieldRules['required'] ?? false) && $value === '') {
                $this->errors[$field] =
                    ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                continue;
            }

            // STOP if already error exists
            if (isset($this->errors[$field])) {
                continue;
            }

            // INTEGER
            if (
                ($fieldRules['integer'] ?? false)
                && $value !== ''
                && filter_var($value, FILTER_VALIDATE_INT) === false
            ) {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be an integer.';
                continue;
            }

            if (
                isset($fieldRules['min_value'])
                && $value !== ''
                && filter_var($value, FILTER_VALIDATE_INT) !== false
                && (int) $value < $fieldRules['min_value']
            ) {
                $this->errors[$field] =
                    ucfirst(str_replace('_', ' ', $field))
                    . ' must be at least '
                    . $fieldRules['min_value']
                    . '.';
                continue;
            }

            // EMAIL
            if (
                ($fieldRules['email'] ?? false)
                && $value !== ''
                && !filter_var($value, FILTER_VALIDATE_EMAIL)
            ) {
                $this->errors[$field] = 'Invalid email format.';
                continue;
            }

            // MIN LENGTH
            if (
                isset($fieldRules['min'])
                && strlen($value) < $fieldRules['min']
            ) {
                $this->errors[$field] =
                    ucfirst(str_replace('_', ' ', $field))
                    . ' must be at least '
                    . $fieldRules['min']
                    . ' characters.';
                continue;
            }

            // MAX LENGTH
            if (
                isset($fieldRules['max'])
                && strlen($value) > $fieldRules['max']
            ) {
                $this->errors[$field] =
                    ucfirst(str_replace('_', ' ', $field))
                    . ' must not exceed '
                    . $fieldRules['max']
                    . ' characters.';
                continue;
            }

            // MATCH FIELD
            if (isset($fieldRules['match'])) {

                $matchField = $fieldRules['match'];
                $matchValue = $data[$matchField] ?? '';

                if ($value !== $matchValue) {
                    $this->errors[$field] =
                        ucfirst(str_replace('_', ' ', $field))
                        . ' does not match '
                        . str_replace('_', ' ', $matchField)
                        . '.';
                    continue;
                }
            }

            // STRONG PASSWORD
            if (($fieldRules['strong'] ?? false) && $value !== '') {

                $hasUppercase = preg_match('/[A-Z]/', $value);
                $hasLowercase = preg_match('/[a-z]/', $value);
                $hasNumber = preg_match('/[0-9]/', $value);
                $hasSpecial = preg_match('/[\W]/', $value);

                if (!$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial) {
                    $this->errors[$field] =
                        ucfirst(str_replace('_', ' ', $field))
                        . ' must contain uppercase, lowercase, number and special character.';
                    continue;
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
