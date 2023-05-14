// Setup dialog event listeners
const dialogContainer = document.getElementById('dialog-container');
const dismissDialogBtn = document.getElementById('dismiss-dialog');
const dialogMessage = document.getElementById('dialog-message');

// Close dialog on dismiss button click
dismissDialogBtn.addEventListener('click', () => {
    dialogContainer.style.visibility = 'hidden';
    dialogContainer.style.opacity = 0;
    dialogMessage.innerHTML = "";
    
    // Remove the 'error' parameter from the URL
    history.pushState({}, document.title, window.location.pathname);
});

// Close dialog on outside box click
dialogContainer.addEventListener('click', (event) => {
    if (event.target === dialogContainer) {
        dialogContainer.style.visibility = 'hidden';
        dialogContainer.style.opacity = 0;
        dialogMessage.innerHTML = "";
        
        // Remove the 'error' parameter from the URL
        history.pushState({}, document.title, window.location.pathname);
    }
});

// Wait for the page to finish loading before checking for error query parameter
window.addEventListener('load', () => {
    // Get the value of the 'error' query parameter from the URL
    const error = new URLSearchParams(window.location.search).get('error');
    
    // If the 'error' parameter exists, show the dialog
    if (error) {
        dialogContainer.style.visibility = 'visible';
        dialogContainer.style.opacity = 1;
    }
});