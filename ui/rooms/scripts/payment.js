document.addEventListener('DOMContentLoaded', () => {
    // Get elements of CC inputs
    const expMonthInput = document.getElementById('exp-month');
    const expYearInput = document.getElementById('exp-year');
    const ccInput = document.getElementById('cc');
    const cvvInput = document.getElementById('cvv');
    
    // Get elements of EWallet inputs
    const ewalletNumbers = document.querySelectorAll('.ewallet-number');
    
    // Get elements of Paypal inputs
    const paypalEmail = document.getElementById('paypal-email');

    // Attach input event listeners to the input elements
    expMonthInput.addEventListener('input', () => {
        validateExpDate(expMonthInput, expYearInput);
    });

    expYearInput.addEventListener('input', () => {
        validateExpDate(expMonthInput, expYearInput);
    });

    cvvInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid =
            validateCVV(e.target.value)
            || isEmpty(e.target.value);

        if (isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    ccInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid =
            validateCCNumber(e.target.value)
            || isEmpty(e.target.value);

        if (isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    paypalEmail.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validateEmail(e.target.value) || isEmpty(e.target.value);
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    
    ewalletNumbers.forEach(item => {
        item.addEventListener('input', (e) => {
            const parentContainer = e.target.parentElement;
            const isValid = validatePhilippinePhoneNumber(e.target.value)
            || isEmpty(e.target.value);
            
            if(isValid) {
                parentContainer.classList.remove('error-container');
            } else {
                parentContainer.classList.add('error-container');
            }
        });
    });

    const paymentOptions = document.getElementById('payment-method');
    paymentOptions.value = "";

    paymentOptions.addEventListener('change', (e) => {
        handlePaymentMethod(e.target.value);
    })
})

function handlePaymentMethod(paymentMethod) {
    const paymentFields = document.getElementsByClassName("payment-field");
    const submitContainer = document.getElementById("submit-container");

    submitContainer.classList.add('hidden');
    for (let i = 0; i < paymentFields.length; i++) {
        paymentFields[i].classList.add("hidden");
    }

    switch (paymentMethod) {
        case 'gcash':
            document.getElementById('gcash-field').classList.remove('hidden');
            submitContainer.classList.remove('hidden');
            break;
        case 'paymaya':
            document.getElementById('paymaya-field').classList.remove('hidden');
            submitContainer.classList.remove('hidden');
            break;
        case 'paypal':
            document.getElementById('paypal-field').classList.remove('hidden');
            submitContainer.classList.remove('hidden');
            break;
        case 'card':
            document.getElementById('card-field').classList.remove('hidden');
            submitContainer.classList.remove('hidden');
            break;
        case 'wire':
            document.getElementById('wire-field').classList.remove('hidden');
            submitContainer.classList.remove('hidden');
            break;
    }

}

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

function validateEmail(email) {
    // regular expression pattern for email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhilippinePhoneNumber(phoneNumber) {
    const regex = /^(09|639|\+639)\d{9}$/;
    return regex.test(phoneNumber);
}

function isEmpty(str) {
    return str.trim().length === 0;
}
  