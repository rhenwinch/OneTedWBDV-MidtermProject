<?php
require_once __DIR__ . '/../interfaces/JsonSerializerInterface.php';

/**
 * Class Room
 * Represents a hotel room with room name, room type, room number, and room address properties.
 */
class Room implements JsonSerializerInterface {
    /** @var string The name of the room. */
    private $roomName;
    /** @var int The id of the room. */
    private $roomId;
    /** @var string The type of the room. */
    private $roomType;
    /** @var string The address of the room. */
    private $roomAddress;

    public function __construct(
        int $roomId, 
        string $roomName, 
        ?string $roomType, 
        string $roomAddress
    ) {
        $this->roomId = $roomId;
        $this->roomName = $roomName;
        $this->roomType = $roomType;
        $this->roomAddress = $roomAddress;
    }

    /**
     * Get the id of the room.
     *
     * @return int The name of the room.
     */
    public function getRoomId() {
        return $this->roomId;
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
     * @return string The type of the room.
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
            'roomId' => $this->roomId,
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
        
        $room = null;
        if(is_object($json)) {
            // Create Room object
            $room = new Room(
                $json->roomId,
                $json->roomName,
                $json->roomType,
                $json->roomAddress
            );
        } else {
            // Create Room object
            $room = new Room(
                $json['id'],
                $json['name'],
                $json['type'],
                $json['address']
            );
        }

        return $room;
    }
}

?>