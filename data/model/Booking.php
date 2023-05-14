<?php
require_once __DIR__ . '/../common/BookingStatus.php';
require_once __DIR__ . '/../service/Sanitizer.php';
require_once __DIR__ . '/../interfaces/JsonSerializerInterface.php';
require_once 'Room.php';

class Booking implements JsonSerializerInterface {
    /** @var Room The name of the room */
    private $roomDetails;

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
     * @param Room $roomDetails
     * @param DateTime $bookingDate
     * @param DateTime $arrivalDate
     * @param DateTime $departureDate
     * @param string $bookingId
     * @param BookingStatus $bookingStatus
     * @param float $bookingPrice
     */
    public function __construct(Room $roomDetails, DateTime $bookingDate, DateTime $arrivalDate, DateTime $departureDate, string $bookingId, ?string $bookingStatus, float $bookingPrice) {
        $this->roomDetails = $roomDetails;
        $this->bookingDate = $bookingDate;
        $this->arrivalDate = $arrivalDate;
        $this->departureDate = $departureDate;
        $this->bookingId = $bookingId;
        $this->bookingStatus = $bookingStatus;
        $this->bookingPrice = $bookingPrice;

        if ($bookingPrice <= 0) {
            $this->bookingPrice = $this->calculateBookingPrice();
        } else {
            $this->bookingPrice = $bookingPrice;
        }
    }
    

    /**
     * Get the room name.
     *
     * @return Room The room name.
     */
    public function getRoom(): Room {
        return $this->roomDetails;
    }

    /**
     * Get the arrival date.
     *
     * @return string The arrival date on string.
     */
    public function getArrivalDate(): string {
        return $this->arrivalDate->format('F j, Y');
    }

    /**
     * Get the departure date.
     *
     * @return string The departure date on string.
     */
    public function getDepartureDate(): string {
        return $this->departureDate->format('F j, Y');
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
     * Get the receipt ID.
     *
     * @return string The receipt ID.
     */
    public function getBookingId(): string {
        return $this->bookingId;
    }

    /**
     * Get the booking status.
     *
     * @return string The booking status.
     */
    public function getBookingStatus(): string {
        return (string) $this->bookingStatus;
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
    public function calculateBookingPrice(): float {
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
        return new Booking(
            Room::fromJson($json->roomDetails),
            new DateTime($json->arrivalDate),
            new DateTime($json->departureDate),
            new DateTime($json->bookingDate),
            $json->bookingId,
            BookingStatus::fromString($json->bookingStatus),
            $json->bookingPrice
        );
    }
}


?>