<?php
require_once __DIR__ . '/../service/Sanitizer.php';
require_once 'Booking.php';
require_once __DIR__ . '/../interfaces/JsonSerializerInterface.php';

/**
 * Class User
 * Represents a User with various properties such as userId, email, password, booking history, name, phone number,
 * and membership status.
 */
class User implements JsonSerializerInterface {
    /**
     * @var int The unique user ID
     */
    private $userId;

    /**
     * @var string The user's email address
     */
    private $email;

    /**
     * @var string The user's password
     */
    private $password;

    /**
     * @var Booking[] The user's booking history
     */
    private $bookingHistory;

    /**
     * @var string The user's name
     */
    private $name;

    /**
     * @var string The user's profile picture link
     */
    private $profilePicture;

    /**
     * @var string The user's phone number
     */
    private $phoneNumber;

    /**
     * @var bool Whether the user is a member or not
     */
    private $isMember;

    /**
     * User constructor.
     * 
     */
    public function __construct() {
        $this->profilePicture = "../../images/person.png";
        $this->bookingHistory = array();
    }

    /**
     * Get the user's ID.
     *
     * @return int The user's ID
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Set the user's ID.
     * 
     * @param string $userId The user's id
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * Get the user's email address.
     *
     * @return string The user's email address
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set the user's email address.
     *
     * @param string $email The user's email address
     */
    public function setEmail($email) {
        $this->email = Sanitizer::sanitizeEmail($email);
    }

    /**
     * Get the user's password.
     *
     * @return string The user's password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set the user's password.
     *
     * @param string $password The user's password
     */
    public function setPassword($password) {
        $this->password = Sanitizer::sanitizeString($password);
    }

    /**
     * Get the user's booking history.
     *
     * @return Booking[] The user's booking history
     */
    public function getBookingHistory() {
        return $this->bookingHistory;
    }

    /**
     * Set the user's booking history.
     *
     * @param array The user's booking history
     */
    public function setBookingHistory($bookingHistory) {
        $this->bookingHistory = $bookingHistory;
    }

    /**
     * Add a booking to the user's booking history.
     * @param Booking $booking The booking to be added.
     */
    public function addBooking(Booking $booking) {
        $this->bookingHistory[] = $booking;
    }

    /**
     * Remove a booking from the user's booking history.
     * 
     * @param string $bookingId The booking id of the booking to be removed.
     * @return bool True if the booking was removed successfully, false otherwise.
     */
    public function removeBooking(string $bookingId): bool {
        foreach ($this->bookingHistory as $key => $booking) {
            if ($booking->getBookingId() === $bookingId) {
                // Remove the booking from the array
                unset($this->bookingHistory[$key]);

                // Reindex the array
                $this->bookingHistory = array_values($this->bookingHistory);
                return true;
            }
        }

        return false;
    }

    /**
     * Get the user's name.
     *
     * @return string The user's name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the user's name.
     *
     * @param string $name The user's name
     */
    public function setName($name) {
        $this->name = Sanitizer::sanitizeString($name);
    }

    /**
     * Get the user's profile picture link.
     *
     * @return string The user's name
     */
    public function getProfilePicture() {
        return $this->profilePicture;
    }

    /**
     * Set the user's profile picture link.
     *
     * @param string $pictureLink The user's profile picture link
     */
    public function setProfilePicture($pictureLink) {
        $this->profilePicture = $pictureLink;
    }

    /**
     * Get the user's phone number.
     *
     * @return string The user's phone number
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * Set the user's phone number.
     *
     * @param string $phoneNumber The user's phone number
     */
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = Sanitizer::sanitizeString($phoneNumber);
    }

    /**
     * Check if the user is a member.
     *
     * @return bool Whether the user is a member or not
     */
    public function isMember() {
        return $this->isMember;
    }

    /**
     * Set whether the user is a member or not.
     *
     * @param bool $isMember Whether the user is a member or not
     */
    public function setMembership($isMember) {
        $this->isMember = Sanitizer::sanitizeBool($isMember);
    }


    /**
     * Convert User object to an associative/json array
     *
     */
    public function toArray(): array {
        return [
            'userId' => $this->userId,
            'email' => $this->email,
            'password' => $this->password,
            'bookingHistory' => array_map(function($booking) {
                return $booking->toArray();
            }, $this->bookingHistory),
            'name' => $this->name,
            'profilePicture' => $this->profilePicture,
            'phoneNumber' => $this->phoneNumber,
            'isMember' => $this->isMember
        ];
    }

    
    /**
     * Convert User object from a json string
     *
     * @return User|null
     */
    public static function fromJson($json): ?self {
        if($json == null)
            return null;

        $bookingHistory = array_map(function($booking) {
            return Booking::fromJson($booking);
        }, $json->bookingHistory);

        // Create Room object
        $user = new User();
        $user->setUserId($json->userId);
        $user->setEmail($json->email);
        $user->setPassword($json->password);
        $user->setBookingHistory($bookingHistory);
        $user->setName($json->name);
        $user->setProfilePicture($json->profilePicture);
        $user->setPhoneNumber($json->phoneNumber);
        $user->setMembership($json->isMember);
        return $user;
    }
}
    

?>