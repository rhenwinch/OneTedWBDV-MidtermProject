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

// Initialize calendar
calendar.forEach((item, i) => {
    item.innerHTML = '';
    item.appendChild(generateCalendar(currentYear, currentMonth, i));

    // Populate the select options for months and years
    populateMonth(currentYear, i);
    populateYear(i);
})


// Function to generate the calendar HTML
function generateCalendar(year, month, index) {
    const currentDate = new Date();
    const currentDay = currentDate.getDate();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

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
            } else if(currentDay > day && currentMonth === month && currentYear === year) {
                td.textContent = day;
                td.classList.add('disabled');
            } else {
                td.textContent = day;
                td.addEventListener('click', () => {
                    if(selectedDay[index] !== null)
                        selectedDay[index].classList.remove('selected');

                    calendarInput[index].value = `${year}-${month + 1}-${day.toString().padStart(2, '0')}`;
                    console.log(calendarInput[index].value);
                    td.classList.add('selected');
                    selectedDay[index] = td;
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
        const month = (new Date()).getMonth();
        const year = (new Date()).getFullYear();

        const currentMonthIndex = parseInt(selectMonth[i].value, 10);
        const currentYear = parseInt(selectYear[i].value, 10);
        let newMonthIndex = currentMonthIndex - 1;
        let newYear = currentYear;

        if(newMonthIndex < month && newYear === year)
            return;

        if (newMonthIndex < 0) {
            newMonthIndex = 11;
            newYear = currentYear - 1;
    
            if(newYear < year)
                return;

            populateMonth(newYear, i);
        }
    
    
        selectMonth[i].value = newMonthIndex.toString().padStart(2, '0');
        selectYear[i].value = newYear.toString();
        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(newYear, newMonthIndex, i));
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
            if(newYear > nextYear)
                return;

            populateMonth(newYear, i);
        }
        
        selectMonth[i].value = newMonthIndex.toString().padStart(2, '0');
        selectYear[i].value = newYear.toString();
        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(newYear, newMonthIndex, i));
    });
});

// Event listener to switch to a specific month and year
selectMonth.forEach((item, i) => {
    item.addEventListener('change', () => {
        const year = parseInt(selectYear[i].value, 10);
        const month = parseInt(selectMonth[i].value, 10);
        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(year, month, i));
    });
})

selectYear.forEach((item, i) => {
    item.addEventListener('change', () => {
        const year = parseInt(selectYear[i].value, 10);
        const month = parseInt(selectMonth[i].value, 10);
        
        populateMonth(year, i);
        calendar[i].innerHTML = '';
        calendar[i].appendChild(generateCalendar(year, month, i));
    });
})

function populateMonth(year, index) {
    selectMonth[index].innerHTML = '';

    let currentMonth = (new Date()).getMonth();

    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();

    if(year > currentYear)
        currentMonth = 0;

    for (let i = currentMonth; i < 12; i++) {
        const option = document.createElement('option');
        option.value = i.toString().padStart(2, '0');
        option.textContent = months[i];
        if (i === currentMonth) {
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
        if (i === currentYear) {
            option.selected = true;
        }
        selectYear[index].appendChild(option);
    }
}