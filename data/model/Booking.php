<?php
require_once __DIR__ . '/../common/BookingStatus.php';
require_once __DIR__ . '/../common/Sanitizer.php';
require_once __DIR__ . '/../interfaces/JsonSerializerInterface.php';
require_once 'Room.php';

class Booking implements JsonSerializerInterface {
    /** @var Room The name of the room */
    private  $roomDetails;

    /** @var DateTime The date of arrival */
    private $arrivalDate;

    /** @var DateTime The date of departure */
    private $departureDate;

    /** @var DateTime The date of booking */
    private $bookingDate;

    /** @var string A unique receipt ID */
    private $bookingId;

    /** @var BookingStatus The current status of the booking (Pending, Reserved, Completed) */
    private $bookingStatus;

    /** @var float The total price to pay */
    private $bookingPrice;


    /**
     * Booking constructor.
     *
     * @param Room $room
     */
    public function __construct(Room $room) {
        $this->roomDetails = $room;
        $this->bookingDate = new DateTime();
        $this->arrivalDate = new DateTime();
        $this->departureDate = new DateTime();
        $this->bookingPrice = $this->calculateBookingPrice();
    }
    

    /**
     * Get the room name.
     *
     * @return Room The room name.
     */
    public function getRoomName(): Room {
        return $this->roomDetails;
    }

    /**
     * Set the room name.
     *
     * @param Room $roomName The room name.
     */
    public function setRoom(Room $room): void {
        $this->roomDetails = $room;
    }

    /**
     * Get the arrival date.
     *
     * @return DateTime The arrival date.
     */
    public function getArrivalDate(): DateTime {
        return $this->arrivalDate;
    }

    /**
     * Set the arrival date.
     *
     * @param DateTime $arrivalDate The arrival date.
     */
    public function setArrivalDate(DateTime $arrivalDate): void {
        $this->arrivalDate = $arrivalDate;
    }

    /**
     * Get the departure date.
     *
     * @return DateTime The departure date.
     */
    public function getDepartureDate(): DateTime {
        return $this->departureDate;
    }

    /**
     * Set the departure date.
     *
     * @param DateTime $departureDate The departure date.
     */
    public function setDepartureDate(DateTime $departureDate): void {
        $this->departureDate = $departureDate;
    }

    /**
     * Get the booking date.
     *
     * @return DateTime The booking date.
     */
    public function getBookingDate(): DateTime {
        return $this->bookingDate;
    }

    /**
     * Set the booking date.
     *
     * @param DateTime $bookingDate The booking date.
     */
    public function setBookingDate(DateTime $bookingDate): void {
        $this->bookingDate = $bookingDate;
    }

    /**
     * Get the receipt ID.
     *
     * @return string The receipt ID.
     */
    public function getBookingId(): string {
        return $this->bookingId;
    }

    /**
     * Set the receipt ID.
     *
     * @param string $bookingId The receipt ID.
     */
    public function setBookingId(string $bookingId): void {
        $this->bookingId = Sanitizer::sanitizeString($bookingId);
    }

    /**
     * Get the booking status.
     *
     * @return string The booking status.
     */
    public function getBookingStatus(): BookingStatus {
        return $this->bookingStatus;
    }

    /**
     * Set the booking status.
     *
     * @param BookingStatus $bookingStatus The booking status.
     */
    public function setBookingStatus($bookingStatus): void {
        $this->bookingStatus = $bookingStatus;
    }

    /**
     * Get the total price.
     *
     * @return float The total price.
     */
    public function getBookingPrice(): float {
        return $this->bookingPrice;
    }

    /**
     * Set the total price.
     *
     * @param float $bookingPrice The total price.
     */
    public function setBookingPrice(float $bookingPrice): void {
        $this->bookingPrice = $bookingPrice;
    }

    /**
     * Calculate the duration of the booking in days.
     *
     * @return int The duration of the booking in days.
     */
    private function calculateDuration(): int {
        $interval = $this->arrivalDate->diff($this->departureDate);
        return $interval->days;
    }

    /**
     * Calculate the total price of the booking.
     *
     * @return float The total price of the booking.
     */
    private function calculateBookingPrice(): float {
        $duration = $this->calculateDuration();
        $roomPrice = RoomType::getRoomPrice($this->roomDetails->getRoomType());
        return $duration * $roomPrice;
    }

    /**
     * Confirm the booking.
     */
    public function confirmBooking(): void {
        $this->bookingStatus = BookingStatus::CONFIRMED;
    }

    /**
     * Cancel the booking.
     */
    public function cancelBooking(): void {
        $this->bookingStatus = BookingStatus::CANCELLED;
    }

     /**
     * Convert Booking object to an associative array
     */
    public function toArray(): array {
        return [
            'roomDetails' => $this->roomDetails->toArray(),
            'bookingDate' => $this->bookingDate->format('M d, Y D'),
            'bookingPrice' => $this->bookingPrice,
            'bookingStatus' => $this->bookingStatus,
            'bookingId' => $this->bookingId,
            'departureDate' => $this->departureDate->format('M d, Y D'),
            'arrivalDate' => $this->arrivalDate->format('M d, Y D'),
        ];
    }

     /**
     * Convert JSON to Booking object
     *
     * @return Booking|null
     */
    public static function fromJson($json): ?self {
        if($json == null)
            return null;

        // Create Booking object
        $booking = new Booking(Room::fromJson($json->roomDetails));
        $booking->setArrivalDate(new DateTime($json->arrivalDate));
        $booking->setDepartureDate(new DateTime($json->departureDate));
        $booking->setBookingDate(new DateTime($json->bookingDate));
        $booking->setBookingId($json->bookingId);
        $booking->setBookingStatus(BookingStatus::fromString($json->bookingStatus));
        $booking->setBookingPrice($json->bookingPrice);
        return $booking;
    }
}


?>