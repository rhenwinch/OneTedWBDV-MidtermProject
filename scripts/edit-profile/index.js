 // Profile picture previewer
 document.getElementById("profile-picture").addEventListener('change', (event) => {
    previewImage(event);
});

// Function for previewing the image
function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "#";
    }
}

// Validate fields
document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('name');
    const contactNumberInput = document.getElementById('contact-number');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    emailInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validateEmail(e.target.value) || isEmpty(e.target.value);

        if (isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    nameInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = isEmpty(e.target.value);

        if (isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    contactNumberInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validatePhilippinePhoneNumber(e.target.value) || isEmpty(e.target.value);

        if (isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    passwordInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validatePassword(e.target.value) || isEmpty(e.target.value);

        if (isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });
});