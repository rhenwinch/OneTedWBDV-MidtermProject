<?php

class Enum {
    /**
     * Get the enum-like value from a string representation.
     *
     * @param string $value The value to convert.
     * @return string|null The enum constant, or null if not found.
     */
    public static function fromString(string $value): ?string {
        $value = strtolower($value); // Convert input to lowercase for case-insensitive comparison
        $reflection = new ReflectionClass(__CLASS__);
        $constants = $reflection->getConstants();

        // Loop through constants and find a match
        foreach ($constants as $constantValue) {
            if (strtolower($constantValue) === $value) {
                return $constantValue;
            }
        }

        return null;
    }
}

?>