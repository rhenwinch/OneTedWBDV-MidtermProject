<?php
interface Enum {
    /**
     * Get the enum-like value from a string representation.
     *
     * @param string $value The value to convert.
     * @return string|null The enum constant, or null if not found.
     */
    public static function fromString(string $value): ?string;
}

?>