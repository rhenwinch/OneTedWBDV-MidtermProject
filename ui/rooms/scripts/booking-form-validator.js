const MINIMUM_GUEST_ALLOWED = 1;
const MAXIMUM_GUEST_ALLOWED = 19;

document.addEventListener('DOMContentLoaded', () => {
    const contactInput = document.getElementById('contact');
    const emailInput = document.getElementById('email');
    const guestNumberInput = document.getElementById('guest-number');
    const increaseGuestButton = document.getElementById('increase-guest');
    const decreaseGuestButton = document.getElementById('decrease-guest');

    guestNumberInput.addEventListener('change', (e) => {
        e.target.value = validateGuestNumber(e.target.value);
    })

    emailInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validateEmail(e.target.value) || e.target.value === "";
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    })

    contactInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validatePhilippinePhoneNumber(e.target.value) || e.target.value === "";
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    })

    increaseGuestButton.addEventListener('click', () => {
        guestNumberInput.value = increaseGuest(guestNumberInput.value);
    })

    decreaseGuestButton.addEventListener('click', () => {
        guestNumberInput.value = decreaseGuest(guestNumberInput.value);
    })
})

function validateGuestNumber(inputValue) {
    let guestNumber = parseInt(inputValue);

    const isValueNotIntegerOrLessThanMinimum =
        isNaN(guestNumber) || guestNumber < MINIMUM_GUEST_ALLOWED;
    if (isValueNotIntegerOrLessThanMinimum) {
        guestNumber = MINIMUM_GUEST_ALLOWED;
    }

    const isValueGreaterThanMaximum = guestNumber > MAXIMUM_GUEST_ALLOWED;
    if (isValueGreaterThanMaximum) {
        guestNumber = MAXIMUM_GUEST_ALLOWED;
    }

    return guestNumber;
}

function increaseGuest(inputValue) {
    let increasedGuestNumber = parseInt(inputValue) + 1;

    if(increasedGuestNumber >= MAXIMUM_GUEST_ALLOWED) {
        increasedGuestNumber = MAXIMUM_GUEST_ALLOWED;
    }

    return increasedGuestNumber;
}

function decreaseGuest(inputValue) {
    let decreasedGuestNumber = parseInt(inputValue) - 1;

    if(decreasedGuestNumber <= 0) {
        decreasedGuestNumber = MINIMUM_GUEST_ALLOWED;
    }

    return decreasedGuestNumber;
}

function validateEmail(email) {
    // regular expression pattern for email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhilippinePhoneNumber(phoneNumber) {
    const regex = /^(09|639|\+639)\d{9}$/;
    return regex.test(phoneNumber);
}  
  