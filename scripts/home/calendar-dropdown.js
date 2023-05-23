const datesButton = document.getElementById('datesButton');
const dropdown = document.querySelector(".calendar-dropdown");
const selectMonthElements = document.querySelectorAll('.select-month');
const selectYearElements = document.querySelectorAll('.select-year');
const prevMonths = document.querySelectorAll('.prev-month');
const nextMonths = document.querySelectorAll('.next-month');
const durationElement = document.getElementById("duration");
const dateInputs = document.querySelectorAll(".date-input");
const departureInput = document.getElementById("duration");
let dates = [document.querySelectorAll(".arrival td"), document.querySelectorAll(".departure td")];
let selectedDateElements = [null, null];
let selectedDates = [null, null];

datesButton.addEventListener('click', (e) => {
    e.preventDefault();
    dropdown.classList.remove('show');
    dropdown.classList.add('show');
});

document.addEventListener("click", (event) => {
    const target = event.target;

    // Check if the clicked element is inside a dropdown
    const isInsideDropdown = dropdown.contains(target) || datesButton.contains(target);

    // If the clicked element is not inside a dropdown and a dropdown is open, close all dropdowns
    if (!isInsideDropdown && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
    }
});

selectMonthElements.forEach((item, index) => {
    item.addEventListener('change', () => {
        resetDates(index)
    });
});
selectYearElements.forEach((item, index) => {
    item.addEventListener('change', () => {
        resetDates(index)
    });
});
prevMonths.forEach((item, index) => {
    item.addEventListener('click', () => {
        resetDates(index)
    });
});
nextMonths.forEach((item, index) => {
    item.addEventListener('click', () => {
        resetDates(index)
    });
});

function resetDates(index) {
    let datesXpath = ".arrival td";
    if (index == 1) {
        datesXpath = ".departure td";
    }
    
    dates[index] = document.querySelectorAll(datesXpath);
    selectedDateElements[index] = null;
    initializeDates(index);
}

function initializeDates(index) {
    dates[index].forEach(td => {
        td.addEventListener('click', (e) => {
            if (!e.target.classList.contains("disabled")) {
                if (selectedDateElements[index])
                    selectedDateElements[index].classList.remove('selected');

                const day = e.target.textContent;
                const month = months.indexOf(selectMonthElements[index].options[selectMonthElements[index].selectedIndex].text);
                const year = selectYearElements[index].options[selectYearElements[index].selectedIndex].text;

                e.target.classList.add('selected');
                selectedDateElements[index] = e.target;
                selectedDates[index] = `${year}-${month + 1}-${day.toString().padStart(2, '0')}`;

                if(index == 0) {
                    selectedDates[1] = null;
                    dateInputs[1].value = "";
                    durationElement.textContent = "Dates";
                }

                if (selectedDates[0] && index == 1) {
                    const timeDiff = (new Date(selectedDates[index])).getTime() - (new Date(selectedDates[0])).getTime();
                    const durationDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    const durationMonths = Math.floor(durationDays / 30);
                    const durationYears = Math.floor(durationMonths / 12);

                    if (isNaN(timeDiff)) {
                        alert('Invalid date!');
                    } else {
                        let duration;

                        if (durationYears > 0) {
                            duration = `${durationYears} year(s)`;
                        } else if (durationMonths > 0) {
                            duration = `${durationMonths} month(s)`;
                        } else {
                            duration = `${durationDays} day(s)`;
                        }

                        durationElement.textContent = duration;
                        dateInputs[0].value = selectedDates[0];
                        dateInputs[1].value = selectedDates[1];
                    }


                } else {
                    resetDates(1);
                }
            }
        });
    });
}

initializeDates(0);
initializeDates(1);