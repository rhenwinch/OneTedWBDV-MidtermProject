function validateEmail(email) {
    // regular expression pattern for email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    // Check length (at least 8 characters)
    if (password.length < 8) {
        return false;
    }

    // If we made it this far, the password is valid
    return true;
}

function validatePhilippinePhoneNumber(phoneNumber) {
    const regex = /^(09|639|\+639)\d{9}$/;
    return regex.test(phoneNumber);
}
  
function isEmpty(str) {
    return str.trim().length === 0;
}

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


// Payment validators
function validateExpDate(expMonthInput, expYearInput) {
    const MAX_MONTH = 12;
    const MIN_MONTH = 1;
    const MAX_YEAR = 2033;

    // Get the month and year input values
    const expMonth = parseInt(expMonthInput.value);
    const expYear = parseInt(expYearInput.value);

    // Get the current date
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth() + 1;

    // Check if the expiration date is in the past
    if (expYear < currentYear || expYear > MAX_YEAR) {
        expYearInput.parentElement.classList.add('error-container');
        return;
    }
    
    if(expYear === currentYear && expMonth < currentMonth || expMonth < MIN_MONTH || expMonth > MAX_MONTH) {
        expMonthInput.parentElement.classList.add('error-container');
        return;
    }
    
    expMonthInput.parentElement.classList.remove('error-container');
    expYearInput.parentElement.classList.remove('error-container');
}

function validateCVV(cvv) {
    // The CVV must be a 3 digit number
    const regex = /^[0-9]{3}$/;
    return regex.test(cvv);
}

function validateCCNumber(ccNumber) {
    // The card number must be a string of 13-19 digits
    const regex = /^[0-9]{13,19}$/;
    if (!regex.test(ccNumber)) {
        return false;
    }

    // Determine the card type based on the first digit(s)
    let cardType;
    if (/^4/.test(ccNumber)) {
        cardType = 'VISA';
    } else if (/^5[1-5]/.test(ccNumber)) {
        cardType = 'Mastercard';
    } else {
        return false;
    }

    // Validate the card number using the Luhn-algorithm
    // https://stackoverflow.com/a/26384206
    let checksum = 0;
    let isSecondDigit = false;
    for (let i = ccNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(ccNumber.charAt(i));
        if (isSecondDigit) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        checksum += digit;
        isSecondDigit = !isSecondDigit;
    }
    return (checksum % 10) == 0;
}