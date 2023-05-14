let openedRoomDropdown = null;
let roomSelected = null;
const roomDropdowns = document.querySelectorAll(".roomDropdown");
const roomTypeDropdown = document.getElementById("roomTypeDropdown");
const roomButton = document.getElementById("roomButton");

roomButton.addEventListener("click", (e) => {
    e.preventDefault();
    if (openedRoomDropdown) {
        hideDropdown(openedRoomDropdown);
        openedRoomDropdown = null;
    }

    openedRoomDropdown = roomTypeDropdown
    showDropdown(roomTypeDropdown);
});

document.querySelectorAll(".dropdown-item").forEach((item, index) => {
    item.addEventListener("click", () => {
        document.getElementById("room-type").value = item.textContent.toLowerCase();
        hideDropdown(roomTypeDropdown);
        showDropdown(roomDropdowns[index]);
        openedRoomDropdown = roomDropdowns[index];

        openedRoomDropdown.querySelectorAll(".dropdown-room-item").forEach((roomItem) => {
            if (!roomItem.hasEventListener) {
                roomItem.addEventListener("click", (e) => {
                    const dataId = openedRoomDropdown.querySelector("ul").querySelector("li").getAttribute('data-id');

                    document.getElementById("room-name").value = e.target.textContent.trim();
                    document.getElementById("room-id").value = dataId;
                    hideDropdown(openedRoomDropdown);

                    if (roomSelected) {
                        roomSelected.classList.remove("selected");
                    }

                    roomSelected = openedRoomDropdown.querySelector("ul").querySelector("li");
                    roomSelected.classList.add("selected");
                });

                // Set the flag to indicate that the event listener is registered
                roomItem.hasEventListener = true;
            }
        });
    });
});

document.addEventListener("click", (event) => {
    const target = event.target;
    const dropdowns = document.querySelectorAll(".dropdown");

    // Check if the clicked element is inside a dropdown
    const isInsideDropdown = Array.from(dropdowns).some((dropdown) => {
        return dropdown.contains(target) || roomButton.contains(target);
    });

    // If the clicked element is not inside a dropdown and a dropdown is open, close all dropdowns
    if (!isInsideDropdown && openedRoomDropdown) {
        hideDropdown(openedRoomDropdown);
        openedRoomDropdown = null;
    }
});

function showDropdown(dropdown) {
    const dropdownList = dropdown.querySelector("ul");
    dropdownList.style.display = "block";
    dropdownList.classList.add("slide-in");
}

function hideDropdown(dropdown) {
    const dropdownList = dropdown.querySelector("ul");
    dropdownList.classList.remove("slide-in");
    setTimeout(() => {
        dropdownList.style.display = "none";
    }, 300);
}