const form = document.getElementById('schedule-form');
const adminLoadForm = document.getElementById('admin-load-form');
const tableBody = document.querySelector('#schedule-table tbody');
const adminLoadTableBody = document.querySelector('#admin-load-table tbody');
const filterButton = document.getElementById('filter-button');
const schedulePagination = document.getElementById('schedule-pagination');
const adminLoadPagination = document.getElementById('admin-load-pagination');

let schedules = [];
let adminLoads = [];
let scheduleCurrentPage = 1;
let adminLoadCurrentPage = 1;
const rowsPerPage = 10;

const displaySchedules = (page) => {
    scheduleCurrentPage = page;
    const startIndex = (page - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const paginatedSchedules = schedules.slice(startIndex, endIndex);

    tableBody.innerHTML = '';
    paginatedSchedules.forEach(schedule => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${schedule.teacher}</td>
            <td>${schedule.room}</td>
            <td>${schedule.day}</td>
            <td>${formatTime(schedule.time_start)}</td>
            <td>${formatTime(schedule.time_end)}</td>
            <td>${schedule.year}</td>
            <td>${schedule.block}</td>
            <td>${schedule.subject}</td>
            <td>${schedule.course}</td>
            <td>${schedule.lec || ''}</td>
            <td>${schedule.lab || ''}</td>
            <td>
                <button onclick="editSchedule(${schedule.id})">Edit</button>
                <button onclick="deleteSchedule(${schedule.id})">Delete</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
    setupSchedulePagination();
};

const setupSchedulePagination = () => {
    const pageCount = Math.ceil(schedules.length / rowsPerPage);
    schedulePagination.innerHTML = '';

    for (let i = 1; i <= pageCount; i++) {
        const btn = document.createElement('a');
        btn.href = '#';
        btn.innerText = i;
        if (i === scheduleCurrentPage) {
            btn.classList.add('active');
        }
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            displaySchedules(i);
        });
        schedulePagination.appendChild(btn);
    }
};

const displayAdminLoads = (page) => {
    adminLoadCurrentPage = page;
    const startIndex = (page - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const paginatedAdminLoads = adminLoads.slice(startIndex, endIndex);

    adminLoadTableBody.innerHTML = '';
    paginatedAdminLoads.forEach(load => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${load.teacher}</td>
            <td>${load.office}</td>
            <td>${load.load}</td>
            <td>${load.day}</td>
            <td>${load.time}</td>
            <td>${load.hours}</td>
            <td>
                <button onclick="editAdminLoad(${load.id})">Edit</button>
                <button onclick="deleteAdminLoad(${load.id})">Delete</button>
            </td>
        `;
        adminLoadTableBody.appendChild(row);
    });
    setupAdminLoadPagination();
};

const setupAdminLoadPagination = () => {
    const pageCount = Math.ceil(adminLoads.length / rowsPerPage);
    adminLoadPagination.innerHTML = '';

    for (let i = 1; i <= pageCount; i++) {
        const btn = document.createElement('a');
        btn.href = '#';
        btn.innerText = i;
        if (i === adminLoadCurrentPage) {
            btn.classList.add('active');
        }
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            displayAdminLoads(i);
        });
        adminLoadPagination.appendChild(btn);
    }
};

const fetchSchedules = async (params = {}) => {
    const query = new URLSearchParams(params).toString();
    const response = await fetch(`api.php?${query}`);
    schedules = await response.json();
    displaySchedules(1);
};

const fetchAdminLoad = async () => {
    const response = await fetch('api.php?action=get_admin_load');
    adminLoads = await response.json();
    displayAdminLoads(1);
};

const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = hours % 12 || 12;
    return `${formattedHours}:${minutes} ${ampm}`;
};

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const id = formData.get('id');
    const action = id ? 'update_schedule' : 'add_schedule';
    formData.append('action', action);

    const response = await fetch('api.php', {
        method: 'POST',
        body: formData,
    });

    const result = await response.json();

    if (result.status === 'error') {
        alert(result.message);
    } else {
        fetchSchedules();
        form.reset();
    }
});

adminLoadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(adminLoadForm);
    const id = formData.get('id');
    const action = id ? 'update_admin_load' : 'add_admin_load';
    formData.append('action', action);

    const response = await fetch('api.php', {
        method: 'POST',
        body: formData,
    });

    const result = await response.json();

    if (result.status === 'error') {
        alert(result.message);
    } else {
        fetchAdminLoad();
        adminLoadForm.reset();
    }
});

filterButton.addEventListener('click', () => {
    const teacher = document.getElementById('filter-teacher').value;
    const course = document.getElementById('filter-course').value;
    const year = document.getElementById('filter-year').value;
    const block = document.getElementById('filter-block').value;

    const params = {};
    if (teacher) params.teacher = teacher;
    if (course) params.course = course;
    if (year) params.year = year;
    if (block) params.block = block;

    fetchSchedules(params);
});

document.getElementById('print-button').addEventListener('click', () => {
    const teacher = document.getElementById('filter-teacher').value;
    if (!teacher) {
        alert('Please filter by teacher to print a schedule.');
        return;
    }

    const printWindow = window.open('print.html', '_blank');
    printWindow.onload = async () => {
        const scheduleResponse = await fetch(`api.php?teacher=${teacher}`);
        const schedules = await scheduleResponse.json();

        const adminLoadResponse = await fetch(`api.php?action=get_admin_load&teacher=${teacher}`);
        const adminLoads = await adminLoadResponse.json();

        const printDoc = printWindow.document;
        printDoc.getElementById('teacher-name').textContent = teacher;

        const adminLoadTbody = printDoc.getElementById('admin-load-tbody');
        adminLoadTbody.innerHTML = '';
        let totalAdminHours = 0;
        adminLoads.forEach(load => {
            const row = printDoc.createElement('tr');
            row.innerHTML = `
                <td>${load.office}</td>
                <td>${load.load}</td>
                <td>${load.day}</td>
                <td>${load.time}</td>
                <td>${load.hours}</td>
            `;
            adminLoadTbody.appendChild(row);
            totalAdminHours += parseInt(load.hours, 10);
        });

        const totalAdminRow = printDoc.createElement('tr');
        totalAdminRow.innerHTML = `
            <td colspan="4"><strong>TOTAL</strong></td>
            <td><strong>${totalAdminHours}</strong></td>
        `;
        adminLoadTbody.appendChild(totalAdminRow);

        const teachingLoadTbody = printDoc.getElementById('teaching-load-tbody');
        teachingLoadTbody.innerHTML = '';
        let totalLec = 0;
        let totalLab = 0;
        let totalUnits = 0;

        schedules.forEach(schedule => {
            const row = printDoc.createElement('tr');
            const time = `${formatTime(schedule.time_start)} - ${formatTime(schedule.time_end)}`;
            const lec = schedule.lec || 0;
            const lab = schedule.lab || 0;
            const units = parseInt(lec, 10) + parseInt(lab, 10);
            
            totalLec += parseInt(lec, 10);
            totalLab += parseInt(lab, 10);
            totalUnits += units;

            row.innerHTML = `
                <td>College</td>
                <td>${schedule.subject}</td>
                <td>${schedule.block}</td>
                <td>${schedule.day}</td>
                <td>${time}</td>
                <td>${schedule.room}</td>
                <td></td>
                <td>${units}</td>
                <td>${lec}</td>
                <td>${lab}</td>
            `;
            teachingLoadTbody.appendChild(row);
        });

        const totalRow = printDoc.createElement('tr');
        totalRow.innerHTML = `
            <td colspan="7"><strong>TOTAL</strong></td>
            <td><strong>${totalUnits}</strong></td>
            <td><strong>${totalLec}</strong></td>
            <td><strong>${totalLab}</strong></td>
        `;
        teachingLoadTbody.appendChild(totalRow);
        
        printDoc.getElementById('summary-college-load').textContent = totalLec;
        printDoc.getElementById('summary-lab-load').textContent = totalLab;
        printDoc.getElementById('summary-total-units').textContent = totalUnits;


        printWindow.print();
    };
});

const editSchedule = async (id) => {
    const response = await fetch(`api.php?action=get_schedule&id=${id}`);
    const schedule = await response.json();

    document.getElementById('edit-schedule-id').value = schedule.id;
    document.getElementById('teacher').value = schedule.teacher;
    document.getElementById('room').value = schedule.room;
    document.getElementById('day').value = schedule.day;
    document.getElementById('time_start').value = schedule.time_start;
    document.getElementById('time_end').value = schedule.time_end;
    document.getElementById('year').value = schedule.year;
    document.getElementById('block').value = schedule.block;
    document.getElementById('subject').value = schedule.subject;
    document.getElementById('course').value = schedule.course;
    document.getElementById('lec').value = schedule.lec;
    document.getElementById('lab').value = schedule.lab;
};

const deleteSchedule = async (id) => {
    if (confirm('Are you sure you want to delete this record?')) {
        const response = await fetch('api.php', {
            method: 'POST',
            body: new URLSearchParams({
                action: 'delete_schedule',
                id: id,
            }),
        });

        const result = await response.json();

        if (result.status === 'error') {
            alert(result.message);
        } else {
            fetchSchedules();
        }
    }
};

const editAdminLoad = async (id) => {
    const response = await fetch(`api.php?action=get_admin_load&id=${id}`);
    const load = await response.json();

    document.getElementById('edit-admin-load-id').value = load.id;
    document.getElementById('admin-teacher').value = load.teacher;
    document.getElementById('office').value = load.office;
    document.getElementById('load').value = load.load;
    document.getElementById('admin-day').value = load.day;
    document.getElementById('admin-time').value = load.time;
    document.getElementById('hours').value = load.hours;
};

const deleteAdminLoad = async (id) => {
    if (confirm('Are you sure you want to delete this record?')) {
        const response = await fetch('api.php', {
            method: 'POST',
            body: new URLSearchParams({
                action: 'delete_admin_load',
                id: id,
            }),
        });

        const result = await response.json();

        if (result.status === 'error') {
            alert(result.message);
        } else {
            fetchAdminLoad();
        }
    }
};

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

document.getElementsByClassName("tablinks")[0].click();

fetchSchedules();
fetchAdminLoad();
