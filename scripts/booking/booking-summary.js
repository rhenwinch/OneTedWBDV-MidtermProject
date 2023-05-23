function handleSubmit(e) {
    e.preventDefault(); // Prevent form submission

    const form = document.getElementById('form');
    const paymentFields = document.querySelectorAll('.payment-field');

    for (let i = 0; i < paymentFields.length; i++) {
        const paymentField = paymentFields[i];
        const isHidden = paymentField.classList.contains('hidden');
        const hasErrorContainer = paymentField.classList.contains('error-container');

        if(isHidden)
            continue;

        if (!hasErrorContainer) {
            const inputs = paymentField.querySelectorAll('input[required]');

            let isEmpty = false;
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i].hasAttribute('required') && inputs[i].value === '') {
                    isEmpty = true;
                    break;
                }
            }

            if (isEmpty) {
                showSnackbar("Error: Fill out all required fields!");
                break;
            } else {
                // Form validation passed, continue with submission or other actions
                form.submit();
            }
        } else {
            showSnackbar("Error: Fix all errors!");
        }
    }
}

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

        if (isValid) {
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

            if (isValid) {
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

document.getElementById('confirm').addEventListener('click', handleSubmit);