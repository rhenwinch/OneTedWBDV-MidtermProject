const datesButton = document.getElementById('datesButton');
const dropdown = document.querySelector(".calendar-dropdown");
const selectMonthElement = document.querySelector('.select-month');
const selectYearElement = document.querySelector('.select-year');
const prevMonth = document.querySelector('.prev-month');
const nextMonth = document.querySelector('.next-month');
const date = document.getElementById("date");
let dates = document.querySelectorAll("td");
let selectedDateElement = null;

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

selectMonthElement.addEventListener('change', resetDates);
selectYearElement.addEventListener('change', resetDates);
prevMonth.addEventListener('click', resetDates);
nextMonth.addEventListener('click', resetDates);

function resetDates() {
    dates = document.querySelectorAll("td");
    selectedDateElement = null;
    initializeDates();
}

function initializeDates() {
    dates.forEach(td => {
        td.addEventListener('click', (e) => {
            if(!e.target.classList.contains("disabled")) {
                if(selectedDateElement)
                selectedDateElement.classList.remove('selected');

                const day = e.target.textContent;
                const month = months.indexOf(selectMonthElement.options[selectMonthElement.selectedIndex].text);
                const year = selectYearElement.options[selectYearElement.selectedIndex].text;


                date.value = `${year}-${month + 1}-${day.toString().padStart(2, '0')}`;
                e.target.classList.add('selected');
                selectedDateElement = e.target;
            }
        });
    });
}

initializeDates();