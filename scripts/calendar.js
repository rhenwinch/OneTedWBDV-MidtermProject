const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// Important for avoiding multi selection of dates
let selectedDay = Array(2).fill(null);

const calendarPicker = document.querySelectorAll('.calendar-picker');
const calendarInput = document.querySelectorAll('.calendar-input');
const calendar = document.querySelectorAll('.calendar');
const prevMonthButton = document.querySelectorAll('.prev-month');
const nextMonthButton = document.querySelectorAll('.next-month');
const selectMonth = document.querySelectorAll('.select-month');
const selectYear = document.querySelectorAll('.select-year');
const currentYear = new Date().getFullYear();
const currentMonth = new Date().getMonth();
let departureMinimumDate = new Date();
const postedDates = [null, null]


// Initialize calendar
calendar.forEach((item, i) => {
    item.innerHTML = '';

    postedDates[i] = item.getAttribute('data-selected');
    let selectedDate = postedDates[i];

    if (selectedDate !== "" && typeof selectedDate !== 'undefined' && selectedDate !== null) {
        selectedDate = new Date(selectedDate);
        postedDates[i] = selectedDate;
        if (i == 0) {
            resetMinimumDate(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate());
        }

        item.appendChild(generateCalendar(selectedDate.getFullYear(), selectedDate.getMonth(), i, getMinimumDate(i)));
        populateMonth(departureMinimumDate.getFullYear(), i, getMinimumDate(i));

        const monthAcronym = selectedDate.toLocaleString('default', { month: 'short' });
        for (let i = 0; i < selectMonth[1].options.length; i++) {
            if (selectMonth[1].options[i].text === monthAcronym) {
                selectMonth[1].selectedIndex = i;
                break; // Exit the loop if the string is found
            }
        }

        selectYear[1].selectedIndex = selectedDate.getFullYear() > departureMinimumDate.getFullYear() ? 1 : 0;
    }
    else {
        item.appendChild(generateCalendar(currentYear, currentMonth, i, getMinimumDate(i)));
        populateMonth(currentYear, i, getMinimumDate(i));
    }

    populateYear(i);
});

function isPostedDateAvailable(index) {
    return postedDates[index] !== "" && typeof postedDates[index] !== 'undefined' && postedDates[index] !== null
}

function triggetCalendarInputEvent(index) {
    // Dispatch an event
    const event = new Event('input', { bubbles: true });
    calendarInput[index].dispatchEvent(event);
}

function getMinimumDate(i) {
    return i == 1 ? departureMinimumDate : new Date();
}

function resetMinimumDate(year, month, day) {
    let departureYear = year;
    let departureMonth = month;
    let departureDay = day + 1;

    // Check if the day exceeds the maximum number of days in the month
    const lastDayOfMonth = new Date(departureYear, departureMonth + 1, 0).getDate();
    if (departureDay > lastDayOfMonth) {
        // If the day exceeds the maximum, adjust the month and reset the day
        departureMonth += 1;
        departureDay = 1;

        // Check if the month exceeds 12 (December)
        if (departureMonth > 11) {
            // If the month exceeds 12, adjust the year and reset the month
            departureYear += 1;
            departureMonth = 0;
        }
    }

    departureMinimumDate = new Date(`${departureYear}-${departureMonth + 1}-${departureDay.toString().padStart(2, '0')}`);
}


// Function to generate the calendar HTML
function generateCalendar(
    year,
    month,
    index,
    minimumDate = new Date()
) {
    const currentDay = minimumDate.getDate();
    const currentMonth = minimumDate.getMonth();
    const currentYear = minimumDate.getFullYear();

    const weeks = [];
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const numDays = lastDay.getDate();

    let dayOfWeek = firstDay.getDay();
    let day = 1;

    while (day <= numDays) {
        const week = [];
        for (let i = 0; i < 7; i++) {
            if ((dayOfWeek === i || day > 1) && day <= numDays) {
                week.push(day);
                day++;
            } else {
                week.push(null);
            }
        }
        weeks.push(week);
        dayOfWeek = 0;
    }

    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');

    // Generate the table header
    const headerRow = document.createElement('tr');
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    daysOfWeek.forEach((day) => {
        const th = document.createElement('th');
        th.textContent = day;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    // Generate the table body
    weeks.forEach((week) => {
        const tr = document.createElement('tr');
        week.forEach((day) => {
            const td = document.createElement('td');
            if (day === null) {
                td.classList.add('disabled');
            } else if (currentDay > day && currentMonth === month && currentYear === year) {
                td.textContent = day;
                td.classList.add('disabled');
            } else {
                td.textContent = day;
                if (isPostedDateAvailable(index) && year === postedDates[index].getFullYear() && month === postedDates[index].getMonth() && day === postedDates[index].getDate()) {
                    td.classList.add('selected');
                    selectedDay[index] = td;
                    calendarInput[index].value = `${year}-${month + 1}-${day.toString().padStart(2, '0')}`
                }
                td.addEventListener('click', () => {
                    try {
                        calendarInput[index].value; // Test case if it will throw an error

                        postedDates[index] = null;
                        if (selectedDay[index] !== null)
                            selectedDay[index].classList.remove('selected');

                        calendarInput[index].value = `${year}-${month + 1}-${day.toString().padStart(2, '0')}`;
                        td.classList.add('selected');
                        selectedDay[index] = td;
                        triggetCalendarInputEvent(index);
                    } catch (e) {
                        console.error(e);
                    }

                    if (index == 0) {
                        resetMinimumDate(year, month, day);
                        try {
                            postedDates[1] = null;
                            calendarInput[1].value = "";
                            triggetCalendarInputEvent(1);
                        } catch(e) {}

                        calendar[1].innerHTML = '';
                        calendar[1].appendChild(generateCalendar(departureMinimumDate.getFullYear(), departureMinimumDate.getMonth(), 1, departureMinimumDate));

                        populateMonth(departureMinimumDate.getFullYear(), 1, departureMinimumDate);
                        const monthAcronym = departureMinimumDate.toLocaleString('default', { month: 'short' });
                        for (let i = 0; i < selectMonth[1].options.length; i++) {
                            if (selectMonth[1].options[i].text === monthAcronym) {
                                selectMonth[1].selectedIndex = i;
                                break; // Exit the loop if the string is found
                            }
                        }

                        selectYear[1].selectedIndex = (new Date()).getFullYear() !== departureMinimumDate.getFullYear() ? 1 : 0;
                    }
                });
            }
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
    table.appendChild(tbody);

    return table;
}

// Event listener to switch to the previous month
prevMonthButton.forEach((item, i) => {
    item.addEventListener('click', () => {
        let month = (new Date()).getMonth();
        let year = (new Date()).getFullYear();

        if (i == 1) {
            month = departureMinimumDate.getMonth();
            year = departureMinimumDate.getFullYear();
        }

        const currentMonthIndex = parseInt(selectMonth[i].value, 10);
        const currentYear = parseInt(selectYear[i].value, 10);
        let newMonthIndex = currentMonthIndex - 1;
        let newYear = currentYear;

        if (newMonthIndex < month && newYear === year)
            return;

        if (newMonthIndex < 0) {
            newMonthIndex = 11;
            newYear = currentYear - 1;

            if (newYear < year)
                return;

            populateMonth(newYear, i);
        }


        selectMonth[i].value = newMonthIndex.toString().padStart(2, '0');
        selectYear[i].value = newYear.toString();
        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(newYear, newMonthIndex, i, getMinimumDate(i)));
    });
})

// Event listener to switch to the next month
nextMonthButton.forEach((item, i) => {
    item.addEventListener('click', () => {
        const currentMonthIndex = parseInt(selectMonth[i].value, 10);
        const currentYear = parseInt(selectYear[i].value, 10);
        let newMonthIndex = currentMonthIndex + 1;
        let newYear = currentYear;
        if (newMonthIndex > 11) {
            newMonthIndex = 0;
            newYear = currentYear + 1;

            const nextYear = (new Date()).getFullYear() + 1;
            if (newYear > nextYear)
                return;

            populateMonth(newYear, i);
        }

        selectMonth[i].value = newMonthIndex.toString().padStart(2, '0');
        selectYear[i].value = newYear.toString();
        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(newYear, newMonthIndex, i, getMinimumDate(i)));
    });
});

// Event listener to switch to a specific month and year
selectMonth.forEach((item, i) => {
    item.addEventListener('change', () => {
        const year = parseInt(selectYear[i].value, 10);
        const month = parseInt(selectMonth[i].value, 10);

        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(year, month, i, getMinimumDate(i)));
    });
})

selectYear.forEach((item, i) => {
    item.addEventListener('change', () => {
        const year = parseInt(selectYear[i].value, 10);
        populateMonth(year, i, getMinimumDate(i));

        const month = parseInt(selectMonth[i].value, 10);

        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(year, month, i, getMinimumDate(i)));
    });
})

function populateMonth(year, index, currentDate = new Date()) {
    selectMonth[index].innerHTML = '';

    let currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

    if (year > currentYear)
        currentMonth = 0;

    for (let i = currentMonth; i < 12; i++) {
        const option = document.createElement('option');
        option.value = i.toString().padStart(2, '0');
        option.textContent = months[i];
        if (i === currentMonth || isPostedDateAvailable(index) && postedDates[index] !== null && i === postedDates[index].getMonth() && index === 0) {
            option.selected = true;
        }
        selectMonth[index].appendChild(option);
    }
}

function populateYear(index) {
    for (let i = currentYear; i <= currentYear + 1; i++) {
        const option = document.createElement('option');
        option.value = i.toString();
        option.textContent = i.toString();
        if (i === currentYear || isPostedDateAvailable(index) && postedDates[index] !== null && i === postedDates[index].getFullYear() && index === 0) {
            option.selected = true;
        }
        selectYear[index].appendChild(option);
    }
}