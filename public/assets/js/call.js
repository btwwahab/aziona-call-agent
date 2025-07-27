document.addEventListener('DOMContentLoaded', function () {
    // Fetch dashboard data
    function loadDashboard() {
        fetch('/dashboard/data')
            .then(res => res.json())
            .then(data => {
                document.getElementById('totalCalls').textContent = data.totalCalls;
                document.getElementById('uptime').textContent = data.uptime;
                document.getElementById('avgDuration').textContent = data.avgDuration;
                document.getElementById('successRate').textContent = data.successRate;
                // Call logs
                let callLogsTbody = document.querySelector('#callLogsTable tbody');
                callLogsTbody.innerHTML = '';
                data.calls.forEach(call => {
                    // Set badge color according to status
                    let badgeClass = 'badge-warning';
                    switch (call.status) {
                        case 'completed':
                            badgeClass = 'badge-success';
                            break;
                        case 'failed':
                        case 'dropped':
                            badgeClass = 'badge-error';
                            break;
                        case 'initiated':
                        case 'in_progress':
                        case 'scheduled':
                        default:
                            badgeClass = 'badge-warning';
                    }
                    let createdAtUTC = new Date(call.created_at).toLocaleString('en-US', { timeZone: 'UTC' });
                    callLogsTbody.innerHTML += `<tr>
                    <td>${createdAtUTC} UTC</td>
                    <td>${call.duration ? Math.floor(call.duration / 60) + 'm ' + (call.duration % 60) + 's' : '-'}</td>
                    <td><span class="badge-custom ${badgeClass}">${call.status.charAt(0).toUpperCase() + call.status.slice(1)}</span></td>
                    <td>${call.transcript ? call.transcript.substring(0, 40) + '...' : ''}</td>
                </tr>`;
                });
                // Scheduled Calls table
                let scheduledTbody = document.querySelector('#scheduledCallsTable tbody');
                if (scheduledTbody) {
                    scheduledTbody.innerHTML = '';
                    data.scheduled.forEach(sch => {
                        let scheduledForUTC = new Date(sch.scheduled_for).toLocaleString('en-US', { timeZone: 'UTC' });
                        scheduledTbody.innerHTML += `<tr>
                            <td>${scheduledForUTC} UTC</td>
                            <td>${sch.phone_number}</td>
                            <td><span class="badge-custom badge-${sch.status === 'completed' ? 'success' : (sch.status === 'pending' ? 'info' : 'warning')}">${sch.status.charAt(0).toUpperCase() + sch.status.slice(1)}</span></td>
                            <td>${sch.note ? sch.note : ''}</td>
                        </tr>`;
                    });
                }
                // Appointments table (fetch from API)
                let appointmentsTbody = document.querySelector('#appointmentsTable tbody');
                if (appointmentsTbody) {
                    appointmentsTbody.innerHTML = '';
                    fetch('/api/appointments')
                        .then(res => res.json())
                        .then(appointments => {
                            appointments.forEach(app => {
                                let scheduledForUTC = new Date(app.scheduled_for).toLocaleString('en-US', { timeZone: 'UTC' });
                                appointmentsTbody.innerHTML += `<tr>
                                    <td>${scheduledForUTC} UTC</td>
                                    <td>${app.person_name}</td>
                                    <td>${app.phone}</td>
                                    <td>${app.email ? app.email : '-'}</td>
                                    <td>${app.note ? app.note : ''}</td>
                                    <td><span class="badge-custom badge-${app.status === 'completed' ? 'success' : (app.status === 'pending' ? 'info' : 'warning')}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</span></td>
                                </tr>`;
                            });
                        });
                }
            });
    }
    loadDashboard();

    // Place call
    document.getElementById('startCallBtn').addEventListener('click', function (e) {
        e.preventDefault();
        let phone = document.getElementById('phoneInput').value;
        // No time to convert for single call, just send phone number
        fetch('/call', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ phone_number: phone })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('phoneInput').value = '';
                    loadDashboard();
                }
            });
    });

    // Schedule call
    document.getElementById('scheduleForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let phone = document.getElementById('schedulePhone').value;
        let email = document.getElementById('scheduleEmail').value;
        let date = document.getElementById('scheduleDate').value;
        let time = document.getElementById('scheduleTime').value;
        let note = document.getElementById('scheduleNote').value;
        // Convert to UTC before sending
        let localDateTime = new Date(date + 'T' + time);
        let scheduled_for_utc = localDateTime.toISOString().replace('T', ' ').substring(0, 19); // 'YYYY-MM-DD HH:MM:SS'
        fetch('/schedule', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ phone_number: phone, email: email, scheduled_for: scheduled_for_utc, note })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('scheduleForm').reset();
                    loadDashboard();
                }
            });
    });
});
