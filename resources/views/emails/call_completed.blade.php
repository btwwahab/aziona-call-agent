<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Call Completed</title>
</head>
<body>
    <h2>Your Appointment Call is Complete</h2>
    <p>Dear user,</p>
    <p>Your scheduled call has been completed.</p>
    <ul>
        <li><strong>Phone Number:</strong> {{ $scheduledCall->phone_number }}</li>
        <li><strong>Scheduled For (UTC):</strong> {{ $scheduledCall->scheduled_for }}</li>
        <li><strong>Note:</strong> {{ $scheduledCall->note }}</li>
    </ul>
    <p>Thank you for using our service!</p>
</body>
</html>
