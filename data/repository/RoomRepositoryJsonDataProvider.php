<?php

class RoomRepositoryJsonDataProvider {
    /** @var string The file path to the JSON data */
    private $jsonFilePath;

    /**
     * RoomRepositoryJsonDataProvider constructor.
     *
     * @param string $jsonFilePath The file path to the JSON data
     */
    public function __construct($jsonFilePath) {
        $this->jsonFilePath = $jsonFilePath;
    }

    /**
     * Loads the rooms from the JSON data file.
     *
     * @return array The loaded rooms
     */
    public function loadRooms() {
        $data = file_get_contents($this->jsonFilePath);
        return json_decode($data, true);
    }

    /**
     * Saves the modified rooms back to the JSON data file.
     *
     * @param array $rooms The rooms to be saved
     */
    public function saveRooms($rooms) {
        $jsonData = json_encode($rooms, JSON_PRETTY_PRINT);
        file_put_contents($this->jsonFilePath, $jsonData);
    }
}
