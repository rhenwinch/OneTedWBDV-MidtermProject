function calculateBookingPrice(basePrice, guests, arrivalDate) {
    const pricePerGuest = 1000;
    const weekendSurcharge = 2000;
    const discountThreshold = 7; // Number of days to qualify for a discount
    const discountPercentage = 0.1; // 10% discount

    let price = basePrice;

    // Apply surcharge for weekends (Saturday or Sunday)
    const dayOfWeek = new Date(arrivalDate).getDay();
    if (dayOfWeek === 6 || dayOfWeek === 0) {
        price += weekendSurcharge;
    }

    // Calculate price based on the number of guests
    price += guests * pricePerGuest;

    // Apply discount if the arrival date is more than discountThreshold days away
    const today = new Date();
    const daysUntilArrival = Math.floor(
        (new Date(arrivalDate) - today) / (1000 * 60 * 60 * 24)
    );
    if (daysUntilArrival > discountThreshold) {
        price -= price * discountPercentage;
    }

    return price;
}

window.addEventListener('load', function() {
    const guests = parseInt(document.getElementById('guest-number').value);
    const arrivalDate = document.querySelectorAll('.calendar-input')[0].value;
    const basePrice = parseInt(document.getElementById('price').textContent.replace('₱', ''));
    const bookingPrice = calculateBookingPrice(basePrice, guests, arrivalDate);
    
    document.getElementById('price').textContent = '₱' + bookingPrice;
});
  