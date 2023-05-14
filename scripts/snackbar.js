function showSnackbar(message) {
    const snackbar = document.getElementById('snackbar');

    snackbar.textContent = message || "Error: Something went wrong!"
    snackbar.classList.add('show');
    setTimeout(() => {
        snackbar.classList.remove('show');
    }, 4000); // Snackbar will be hidden after 3 seconds
}
