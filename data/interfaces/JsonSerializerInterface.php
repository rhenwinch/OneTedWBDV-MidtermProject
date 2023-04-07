<?php

/**
 * Interface JsonSerializerInterface
 * Represents an object that can be serialized to JSON and deserialized from JSON.
 */
interface JsonSerializerInterface {
    /**
     * Convert the object to an associative array for serialization.
     *
     * @return array The associative array representation of the object.
     */
    public function toArray(): array;

    /**
     * Deserialize an object from JSON.
     *
     * @param stdClass $json The JSON object to deserialize.
     * @return mixed|null The deserialized object.
     */
    public static function fromJson(stdClass $json);
}


?>