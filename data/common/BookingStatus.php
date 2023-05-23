<?php
require_once __DIR__ . '/../interfaces/Enum.php';

/**
 * Enum class for booking status.
 */
class BookingStatus implements Enum {
    const CANCELLED = 'Cancelled';
    const CONFIRMED = 'Confirmed';
    const IN_PROGRESS = 'In Progress';
    const COMPLETED = 'Completed';

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