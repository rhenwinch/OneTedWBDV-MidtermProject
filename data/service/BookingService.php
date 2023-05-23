<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';

/**
 * The UserService class handles the business logic for user-related functionality.
 */
class BookingService {
    /**
     * The UserRepository instance.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Create a new UserService instance.
     *
     * @param UserRepository|null $userRepository The UserRepository instance.
     */
    public function __construct(?UserRepository $userRepository = null) {
        if ($userRepository == null) {
            $jsonFilePath = __DIR__ . '/../users.json'; // path to the JSON file containing user data
            $this->userRepository = new UserRepository($jsonFilePath);
            return;
        }

        $this->userRepository = $userRepository;
    }

    /**
     * Appends a booking item on the user's booking history
     *
     * @param User $user The user to update.
     * @param Booking $bookingItem The user's booking item to be appended on their history
     * @return User The updated user object.
     */
    public function addBooking(
        User $user,
        Booking $bookingItem
    ): User {
        $bookings = $user->getBookingHistory();
        $bookings[] = $bookingItem;

        $updatedUser = new User(
            $user->getEmail(),
            $user->getPassword(),
            $user->getUserId(),
            $user->getName(),
            $user->getProfilePicture(),
            $user->getPhoneNumber(),
            $user->isMember(),
            $bookings,
        );

        $this->userRepository->updateUser($updatedUser);
        return $updatedUser;
    }

    /**
     * Verifies the status of a booking based on the arrival and departure dates.
     *
     * @param User $user The user to verify.
     *
     * @return User The updated user object.
     */
    public function updateUserBookings(User $user): User {
        $updatedUser = $user;
        $updatedBookings = $updatedUser->getBookingHistory();

        foreach ($updatedBookings as $index => $booking) {
            $now = strtotime('today');  // Get the current date

            $arrivalDate = strtotime($booking->getArrivalDate());
            $departureDate = strtotime($booking->getDepartureDate());
            
            $updatedBookingStatus = null;
            if ($now < $arrivalDate && $booking->getBookingStatus() != BookingStatus::CONFIRMED) {
                // Booking is confirmed
                $updatedBookingStatus = BookingStatus::CONFIRMED;
            } else if ($now >= $arrivalDate && $now <= $departureDate && $booking->getBookingStatus() != BookingStatus::IN_PROGRESS) {
                // Booking is ongoing
                $updatedBookingStatus = BookingStatus::IN_PROGRESS;
            } else if ($now > $departureDate && $booking->getBookingStatus() != BookingStatus::COMPLETED) {
                // Booking has ended
                $updatedBookingStatus = BookingStatus::COMPLETED;
            }
            
            

            if($updatedBookingStatus !== null) {
                $updatedBookings[$index] = new Booking(
                    Room::fromJson($booking->getRoom()),
                    $booking->getBookingDate(),
                    new DateTime($booking->getArrivalDate()),
                    new DateTime($booking->getDepartureDate()),
                    $booking->getBookingId(),
                    $updatedBookingStatus,
                    $booking->getBookingPrice()
                );
            }
        }


        $updatedUser = new User(
            $user->getEmail(),
            $user->getPassword(),
            $user->getUserId(),
            $user->getName(),
            $user->getProfilePicture(),
            $user->getPhoneNumber(),
            $user->isMember(),
            $updatedBookings,
        );
        $this->userRepository->updateUser($updatedUser);

        return $updatedUser;
    }

    /**
     * Generate a random booking ID.
     *
     * @param int $length The length of the booking ID (default: 8)
     * @param bool $ignoreUniqueness Flag to ignore uniqueness check (default: false)
     * @return string The generated booking ID
     */
    public function generateRandomBookingId($length = 8, $ignoreUniqueness = false) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $id = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($characters) - 1);
            $id .= $characters[$randomIndex];
        }

        if ($ignoreUniqueness) {
            return $id;
        }

        while (!$this->isBookingIdUnique($id)) {
            $id = $this->generateRandomBookingId(8, true);
        }

        return $id;
    }

    /**
     * Check if a booking ID is unique among all users' booking history.
     *
     * @param string $bookingId The booking ID to check
     * @return bool Whether the booking ID is unique or not
     */
    public function isBookingIdUnique($bookingId) {
        $users = $this->userRepository->getAllUsers();

        foreach ($users as $user) {
            foreach ($user->getBookingHistory() as $booking) {
                if ($booking->getBookingId() === $bookingId) {
                    return false;
                }
            }
        }

        return true;
    }
}
