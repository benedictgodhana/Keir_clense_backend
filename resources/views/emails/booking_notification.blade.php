<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Notification</title>
</head>
<body>
    <h2>Booking Details</h2>
    <p>Dear {{ $user->name }},</p>
    <p>Thank you for your booking!</p>
    <p><strong>Booking Details:</strong></p>
    <ul>
        <li><strong>Service:</strong> {{ $booking->service->name }}</li>
        <li><strong>Date and Time:</strong> {{ $booking->date_time }}</li>
        <li><strong>Location:</strong> {{ $booking->location }}</li>
        <li><strong>Service Price:</strong> {{ $booking->service->price }}</li>
        <li><strong>Paybill:</strong> 12345</li>
        <li><strong>Employee:</strong> {{ $employeeName }}</li>
    </ul>
    <p>You can view your booking details <a href="http://localhost:5173/my-bookings">here</a>.</p>
</body>
</html>
