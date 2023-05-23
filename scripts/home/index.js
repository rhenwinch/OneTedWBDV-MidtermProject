// Go to top script
window.addEventListener('scroll', function () {
    var goToTopBtn = document.getElementById('goToTopBtn');
    if (window.pageYOffset > 0) {
        goToTopBtn.classList.add('show');
    } else {
        goToTopBtn.classList.remove('show');
    }
});

document.getElementById('goToTopBtn').addEventListener('click', function () {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

const clickableItems = document.querySelectorAll('.room-item.clickable');
clickableItems.forEach(item => {
    item.addEventListener('click', () => {
        // TODO: Implement navigator to the room details page
    });
});

// Room filter selector
const buttons = document.querySelectorAll('.row-container .room-category-filter'); // Get all the buttons within the row container

// Attach click event listener to each button
const roomLists = document.querySelectorAll(".list");
let previousSelectedFilter = 0;
buttons.forEach((button, index) => {
    button.addEventListener('click', () => {
        // Remove the "active" class from the current active button
        const currentActiveButton = document.querySelector('.row-container .button.active');
        currentActiveButton.classList.remove('active');


        // Add the "active" class to the clicked button
        button.classList.add('active');
        roomLists[previousSelectedFilter].classList.toggle('hidden');
        roomLists[index].classList.toggle('hidden');
        previousSelectedFilter = index;
    });
});

const downButton = document.getElementById('down-arrow');
const navbar = document.getElementById('navbar');

downButton.addEventListener('click', () => {
    // Smooth scroll to the target section/div
    document.getElementById('body-content').scrollIntoView({
        behavior: 'smooth'
    });
});

// Script for the list scrolling
const leftArrows = document.querySelectorAll('.left-arrow');
const rightArrows = document.querySelectorAll('.right-arrow');
const listContainers = document.querySelectorAll('.list-container');

listContainers.forEach((listContainer, i) => {
    let startX = 0;
    let scrollLeft = 0;
    let isDragging = false;

    listContainer.addEventListener('mouseenter', () => {
        listContainer.addEventListener('wheel', (e) => {
            scrollWithMouse(e, listContainer)
        });
    });

    listContainer.addEventListener('mouseleave', () => {
        listContainer.removeEventListener('wheel', (e) => {
            scrollWithMouse(e, listContainer)
        });
    });

    listContainer.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.pageX - listContainer.offsetLeft;
        scrollLeft = listContainer.scrollLeft;
    });

    listContainer.addEventListener('mouseup', () => {
        isDragging = false;
    });

    listContainer.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - listContainer.offsetLeft;
        const walk = x - startX;
        listContainer.scrollLeft = scrollLeft - walk;
    });

    listContainer.addEventListener('touchstart', (e) => {
        isDragging = true;
        startX = e.touches[0].pageX - listContainer.offsetLeft;
        scrollLeft = listContainer.scrollLeft;
    });

    listContainer.addEventListener('touchend', () => {
        isDragging = false;
    });

    listContainer.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.touches[0].pageX - listContainer.offsetLeft;
        const walk = x - startX;
        listContainer.scrollLeft = scrollLeft - walk;
    });

    leftArrows[i].addEventListener('click', () => {
        listContainer.scrollBy({
            left: -listContainer.offsetWidth,
            behavior: 'smooth'
        });
    });

    rightArrows[i].addEventListener('click', () => {
        listContainer.scrollBy({
            left: listContainer.offsetWidth,
            behavior: 'smooth'
        });
    });


})

window.addEventListener('scroll', () => {
    // Check if the user has scrolled out of the first section/div
    const firstSection = document.getElementById('resting-navbar');
    const scrollPosition = window.scrollY || window.pageYOffset;
    const firstSectionBottom = firstSection.offsetTop + firstSection.offsetHeight;

    if (scrollPosition >= firstSectionBottom) {
        // Add the 'show-navbar' class to make the navbar visible
        navbar.classList.add('show-navbar');
    } else {
        // Remove the 'show-navbar' class to hide the navbar
        navbar.classList.remove('show-navbar');
    }
});


function scrollWithMouse(e, listContainer) {
    e.preventDefault();
    listContainer.scrollLeft -= e.deltaY;
}


