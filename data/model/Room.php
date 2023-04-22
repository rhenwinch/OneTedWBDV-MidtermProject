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

    /**
     * Get the name of the room.
     *
     * @return string The name of the room.
     */
    public function getRoomName() {
        return $this->roomName;
    }

    /**
     * Set the name of the room.
     *
     * @param string $roomName The name of the room.
     */
    public function setRoomName($roomName) {
        $this->roomName = $roomName;
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
     * Set the type of the room.
     *
     * @param RoomType $roomType The type of the room.
     */
    public function setRoomType($roomType) {
        $this->roomType = $roomType;
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
     * Set the address of the room.
     *
     * @param string $roomAddress The address of the room.
     */
    public function setRoomAddress($roomAddress) {
        $this->roomAddress = $roomAddress;
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
        $room = new Room();
        $room->setRoomName($json->roomName);
        $room->setRoomType(RoomType::fromString($json->roomType));
        $room->setRoomAddress($json->roomAddress);
        return $room;
    }
}

?>