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
     * @param string $email The user's email address
     * @param string $password The user's password
     * @param int|null $userId The unique user ID (optional)
     * @param string|null $name The user's name (optional)
     * @param string|null $profilePicture The user's profile picture link (optional)
     * @param string|null $phoneNumber The user's phone number (optional)
     * @param bool|null $isMember Whether the user is a member or not (optional)
     * @param Booking[]|null $bookingHistory The user's booking history (optional)
     */
    public function __construct(
        string $email,
        string $password,
        ?int $userId = null,
        string $name = "",
        string $profilePicture = "../../res/images/person.png",
        string $phoneNumber = "",
        bool $isMember = false,
        array $bookingHistory = array()
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->userId = $userId;
        $this->name = $name;
        $this->profilePicture = $profilePicture;
        $this->phoneNumber = $phoneNumber;
        $this->isMember = $isMember;
        $this->bookingHistory = $bookingHistory;
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
     * Get the user's email address.
     *
     * @return string The user's email address
     */
    public function getEmail() {
        return $this->email;
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
     * Get the user's booking history.
     *
     * @return Booking[] The user's booking history
     */
    public function getBookingHistory() {
        return $this->bookingHistory;
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
     * Get the user's profile picture link.
     *
     * @return string The user's name
     */
    public function getProfilePicture() {
        return $this->profilePicture;
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
     * Check if the user is a member.
     *
     * @return bool Whether the user is a member or not
     */
    public function isMember() {
        return $this->isMember;
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
        return new User(
            $json->email,
            $json->password,
            $json->userId,
            $json->name,
            $json->profilePicture,
            $json->phoneNumber,
            $json->isMember,
            $bookingHistory
        );
    }
}
    

?>