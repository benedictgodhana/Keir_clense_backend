<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Notification</title>
</head>
<body>
    <h2>New Booking Notification</h2>
    <p>Dear {{ $employeeName }},</p>
    <p>A new booking has been created by {{ $booking->user->name }}.</p>
    <p><strong>Booking Details:</strong></p>
    <ul>
        <li><strong>Service:</strong> {{ $booking->service->name }}</li>
        <li><strong>Date and Time:</strong> {{ $booking->date_time }}</li>
        <li><strong>Location:</strong> {{ $booking->location }}</li>
    </ul>
    <p>You can view the details by clicking the button below:</p>
    <a href="{{ url('/bookings/' . $booking->id) }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none;">View Booking</a>
</body>
</html>
