function calculatePrice(basePrice, guestCount, stayDuration) {
    const pricePerGuest = 1000;
    const weekendSurcharge = 2000;
    const discountThreshold = 7;
    const discountPercentage = 0.1;

    let totalPrice = basePrice;

    // Apply additional charges for guests
    totalPrice += guestCount * pricePerGuest;

    // Apply weekend surcharge
    const startDate = new Date();
    const endDate = new Date();
    endDate.setDate(startDate.getDate() + stayDuration);
    let currentDate = new Date(startDate);
    let weekendCount = 0;

    while (currentDate <= endDate) {
        if (currentDate.getDay() === 0 || currentDate.getDay() === 6) {
            weekendCount++;
        }
        currentDate.setDate(currentDate.getDate() + 1);
    }

    const weekendSurchargeTotal = weekendCount * weekendSurcharge;
    totalPrice += weekendSurchargeTotal;

    // Apply discount if stay duration qualifies
    if (stayDuration >= discountThreshold) {
        const discountAmount = totalPrice * discountPercentage;
        totalPrice -= discountAmount;
    }

    return totalPrice;
}

function calculateDateDuration(arrivalDate, departureDate) {
    const timeDiff = (new Date(departureDate)).getTime() - (new Date(arrivalDate)).getTime();
    const durationDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

    if (isNaN(timeDiff)) {
        alert('Invalid date!');
    } else {
        return durationDays
    }

}

let basePrice;
const calendarInputs = document.querySelectorAll('.calendar-input');
calendarInputs.forEach(item => {
    item.addEventListener('input', updatePrice);
});

window.addEventListener('load', () => {
    basePrice = parseInt(document.getElementById('price').textContent.replace('₱', ''));
    updatePrice()
});

function updatePrice() {
    const guests = parseInt(document.getElementById('guest-number').value);
    const arrivalDate = calendarInputs[0].value;
    const departureDate = calendarInputs[1].value;
    
    let price;
    if(departureDate !== "" && arrivalDate !== "") {
        const stayDuration = calculateDateDuration(arrivalDate, departureDate);
        const bookingPrice = calculatePrice(basePrice, guests, stayDuration);
        price = '₱' + bookingPrice;
        document.getElementById('price-input').value = bookingPrice;
    } else {
        price = 'Select valid dates!'
    }

    document.getElementById('price').textContent = price;
}
