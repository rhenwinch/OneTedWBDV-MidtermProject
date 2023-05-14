<?php
require_once 'RoomRepositoryJsonDataProvider.php';

class RoomRepository {
    /** @var array The array containing the rooms */
    private $rooms;

    /** @var RoomRepositoryJsonDataProvider The data provider for the rooms */
    private $dataProvider;

    /**
     * RoomRepository constructor.
     *
     * @param string $jsonFilePath The file path to the JSON data
     */
    public function __construct($jsonFilePath) {
        $this->dataProvider = new RoomRepositoryJsonDataProvider($jsonFilePath);
        $this->loadRooms();
    }

    /**
     * Loads the rooms from the data provider.
     */
    private function loadRooms() {
        $roomTypes = $this->dataProvider->loadRooms();
        $this->rooms = [];

        foreach ($roomTypes as $roomType => $rooms) {
            foreach ($rooms as $room) {
                $this->rooms[$roomType][$room['id']] = $room;
            }
        }
    }

    /**
     * Saves the modified rooms using the data provider.
     */
    private function saveRooms() {
        $rooms = [];

        foreach ($this->rooms as $roomType => $roomsById) {
            $rooms[$roomType] = array_values($roomsById);
        }

        $this->dataProvider->saveRooms($rooms);
    }

    /**
     * Retrieves all rooms from the repository.
     *
     * @return array Array of rooms
     */
    public function getAllRooms() {
        return $this->rooms;
    }

    /**
     * Retrieves all rooms from the repository by their room type.
     *
     * @param string $selectedRoomFilter The room type filter 
     * @return array Array of rooms
     */
    public function getAllRoomsByType($selectedRoomFilter) {
        return $this->rooms[$selectedRoomFilter];
    }

    /**
     * Retrieves all deluxe rooms from the repository.
     *
     * @return array Array of deluxe rooms
     */
    public function getDeluxeRooms() {
        return $this->rooms["deluxe"];
    }

    /**
     * Retrieves a room by its ID.
     *
     * @param int $id The ID of the room
     * @return array|null The room with the specified ID, or null if not found
     */
    public function getRoomById($id) {
        foreach ($this->rooms as $roomType => $rooms) {
            if (isset($rooms[$id])) {
              return $rooms[$id];
            }
        }
        return null;
    }

    /**
     * Books a room by its ID and updates the availability.
     *
     * @param int $id The ID of the room
     * @return bool True if the booking was successful, false otherwise
     */
    public function bookRoomById($id) {
        foreach ($this->rooms as $roomType => &$rooms) {
            if (isset($rooms[$id]) && $rooms[$id]['availability'] > 0) {
              $rooms[$id]['availability']--;
              $this->saveRooms();
              return true;
            }
        }
        return false;
    }
}
