function handleSubmit(e) {
    e.preventDefault(); // Prevent form submission

    const form = document.getElementById('form');
    const inputs = form.getElementsByTagName('input');

    let isEmpty = false;

    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].hasAttribute('required') && inputs[i].value === '') {
            isEmpty = true;
            break;
        }
    }

    if (isEmpty) {
        showSnackbar("Error: Fill out all required fields!");
    } else {
        // Form validation passed, continue with submission or other actions
        form.submit();
    }
}

document.getElementById("book").addEventListener('click', handleSubmit)