document.addEventListener('DOMContentLoaded', () => {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    emailInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validateEmail(e.target.value) || isEmpty(e.target.value);
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    passwordInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validatePassword(e.target.value) || isEmpty(e.target.value);
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });
});

function isEmpty(str) {
    return str.trim().length === 0;
}

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
