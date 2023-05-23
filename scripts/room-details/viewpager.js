const buttons = document.querySelectorAll('.row-container .room-category-filter'); // Get all the buttons within the row container
const roomLists = document.querySelectorAll(".viewpager");
const roomInfoContainers = document.querySelectorAll(".room-info-container");
const leftArrow = document.querySelector('.left-arrow');
const rightArrow = document.querySelector('.right-arrow');
let previousSelectedFilter = 0;
let pages = Array.from(roomLists[previousSelectedFilter].querySelectorAll('.page'));
let maxPages = pages.length;
let currentFilterSelected = 0;
let currentPage = 0;

document.addEventListener('DOMContentLoaded', () => {
    // Get the current URL
    const currentUrl = new URL(window.location.href);
  
    // Get the value of the 'roomType' parameter
    const roomType = currentUrl.searchParams.get('roomType');
  
    // Get the value of the 'roomId' parameter
    const page = currentUrl.searchParams.get('page');

    const filters = document.querySelectorAll('.room-category-filter');
    filters.forEach((element, i) => {
        console.log(element.id, roomType, element.id.toLowerCase() === roomType);
        if(element.id === roomType) {
            currentFilterSelected = i;
            previousSelectedFilter = i;
            pages = Array.from(roomLists[previousSelectedFilter].querySelectorAll('.page'));
            maxPages = pages.length;
            currentPage = parseInt(page);
            return;
        }
    });
    
    updateViewpager();
});
  

buttons.forEach((button, index) => {
    button.addEventListener('click', () => {
        currentFilterSelected = index;
        
        // Remove the "active" class from the current active button
        const currentActiveButton = document.querySelector('.row-container .button.active');
        currentActiveButton.classList.remove('active');
        

        // Add the "active" class to the necessary view
        button.classList.add('active');
        roomLists[previousSelectedFilter].classList.toggle('hidden');
        roomLists[index].classList.toggle('hidden');
        previousSelectedFilter = index;

        // Reset viewpager
        roomLists[index].style.transform = `translateX(0px)`;
        currentPage = 0;
        pages = Array.from(roomLists[index].querySelectorAll('.page'));
        maxPages = pages.length;

        // Remove the active class from the current container info
        const activeInfoContainer = document.querySelector(".room-info-container.active");
        activeInfoContainer.classList.remove('active');
        activeInfoContainer.classList.add('hidden');
        
        roomInfoContainers[currentFilterSelected * maxPages + currentPage].classList.add('active');
        roomInfoContainers[currentFilterSelected * maxPages + currentPage].classList.remove('hidden');
    });
});

leftArrow.addEventListener('click', () => {
    if (currentPage > 0) {
        currentPage--;
        updateViewpager();
    }
});

rightArrow.addEventListener('click', () => {
    if (currentPage < pages.length - 1) {
        currentPage++;
        updateViewpager();
    }
});

function updateViewpager() {
    const translateX = -currentPage * roomLists[previousSelectedFilter].clientWidth;
    roomLists[previousSelectedFilter].style.transform = `translateX(${translateX}px)`;
    
    // Change the container info
    const activeInfoContainer = document.querySelector(".room-info-container.active");
    activeInfoContainer.classList.remove('active');
    activeInfoContainer.classList.add('hidden');

    roomInfoContainers[currentFilterSelected * maxPages + currentPage].classList.add('active');
    roomInfoContainers[currentFilterSelected * maxPages + currentPage].classList.remove('hidden');
}
