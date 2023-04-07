<?php
require_once __DIR__ . '/../interfaces/Enum.php';

/**
 * Enum class for room types.
 */
class RoomType extends Enum {
    const STANDARD = "Standard";
    const DELUXE = "Deluxe";
    const SUITE = "Suite";

    private static $roomData = [
        self::STANDARD => [
            "displayName" => "Standard",
            "roomPrice" => 100.00
        ],
        self::DELUXE => [
            "displayName" => "Deluxe",
            "roomPrice" => 150.00
        ],
        self::SUITE => [
            "displayName" => "Suite",
            "roomPrice" => 200.00
        ]
    ];

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


    /**
     * Get the room price of a room type.
     *
     * @param string $roomType The room type constant value.
     * @return float The room price of the room type.
     */
    public static function getRoomPrice($roomType) {
        // Sanitize the input parameter to ensure it matches a valid room type constant
        $sanitizedRoomType = filter_var($roomType, FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Check if the sanitized room type exists in the room data array
        return isset(self::$roomData[$sanitizedRoomType]['roomPrice']) ? self::$roomData[$sanitizedRoomType]['roomPrice'] : 0.00;
    }
}



?>