<?php
require_once __DIR__ . '/../interfaces/JsonSerializerInterface.php';
require_once __DIR__ . '/../common/RoomType.php';

/**
 * Class Room
 * Represents a hotel room with room name, room type, room number, and room address properties.
 */
class Room implements JsonSerializerInterface {
    /** @var string The name of the room. */
    private $roomName;
    /** @var RoomType The type of the room. */
    private $roomType;
    /** @var string The address of the room. */
    private $roomAddress;

    public function __construct(string $roomName, ?string $roomType, string $roomAddress) {
        $this->roomName = $roomName;
        $this->roomType = $roomType;
        $this->roomAddress = $roomAddress;
    }

    /**
     * Get the name of the room.
     *
     * @return string The name of the room.
     */
    public function getRoomName() {
        return $this->roomName;
    }

    /**
     * Get the type of the room.
     *
     * @return RoomType The type of the room.
     */
    public function getRoomType() {
        return $this->roomType;
    }

    /**
     * Get the address of the room.
     *
     * @return string The address of the room.
     */
    public function getRoomAddress() {
        return $this->roomAddress;
    }


    /**
     * Convert Room object to an associative array
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'roomName' => $this->roomName,
            'roomType' => $this->roomType,
            'roomAddress' => $this->roomAddress,
        ];
    }

     /**
     * Convert JSON to Room object
     *
     * @return Room|null
     */
    public static function fromJson($json): ?self {
        if($json == null)
            return null;
        
        // Create Room object
        $room = new Room(
            $json->roomName,
            RoomType::fromString($json->roomType),
            $json->roomAddress
        );

        return $room;
    }
}

?>