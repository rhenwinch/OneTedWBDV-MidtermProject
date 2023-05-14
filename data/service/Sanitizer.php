<?php

class Sanitizer {
    /**
     * Sanitize a string value.
     *
     * @param string $value The value to be sanitized
     * @return string The sanitized value
     */
    public static function sanitizeString($value) {
        return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    /**
     * Sanitize an email address.
     *
     * @param string $email The email address to be sanitized
     * @return string The sanitized email address
     */
    public static function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize a boolean value.
     *
     * @param bool $value The boolean value to be sanitized
     * @return bool The sanitized boolean value
     */
    public static function sanitizeBool($value) {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Sanitize a float value.
     *
     * @param float $value The float value to be sanitized
     * @param int $decimalPlaces The number of decimal places to round to
     * @return float The sanitized float value
     */
    public static function sanitizeFloat($value, $decimalPlaces = 2) {
        $sanitizedValue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return round(floatval($sanitizedValue), $decimalPlaces);
    }
}


?>