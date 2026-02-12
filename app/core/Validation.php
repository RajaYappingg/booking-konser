<?php

declare(strict_types=1);

class Validation
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        $clean = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            if (is_string($value)) {
                $value = trim($value);
            }

            $clean[$field] = $value;
            $ruleList = explode('|', $ruleSet);

            foreach ($ruleList as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

                if ($name === 'required' && ($value === null || $value === '')) {
                    $errors[$field][] = 'This field is required.';
                }

                if ($value === null || $value === '') {
                    continue;
                }

                if ($name === 'email' && !filter_var((string)$value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Enter a valid email address.';
                }

                if ($name === 'min' && is_numeric($param) && strlen((string)$value) < (int)$param) {
                    $errors[$field][] = 'Must be at least ' . (int)$param . ' characters.';
                }

                if ($name === 'max' && is_numeric($param) && strlen((string)$value) > (int)$param) {
                    $errors[$field][] = 'Must be at most ' . (int)$param . ' characters.';
                }

                if ($name === 'int' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field][] = 'Must be a valid number.';
                }
            }
        }

        return [empty($errors), $errors, $clean];
    }
}
