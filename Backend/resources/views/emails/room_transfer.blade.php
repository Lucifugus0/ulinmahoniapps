<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Room Transfer Notification</title>
</head>

<body>
    <h2>Hello {{ $transferDetails['guest_name'] }},</h2>

    <p>Your room has been transferred as per the following details:</p>

    <ul>
        <li><strong>Booking ID:</strong> {{ $transferDetails['order_id'] }}</li>
        <li><strong>From Room:</strong> {{ $transferDetails['previous_room'] }}</li>
        <li><strong>To Room:</strong> {{ $transferDetails['new_room'] }}</li>
        <li><strong>Reason:</strong> {{ $transferDetails['reason'] }}</li>
        <li><strong>Transfer Date:</strong> {{ $transferDetails['transfer_date'] }}</li>
    </ul>

    <p>If you have any questions, please contact our front desk.</p>

    <p>Best regards,<br>{{ config('app.name') }}</p>

    <p>
        <img src="{{ asset('images/apple-touch-icon.png') }}" alt="Company Logo" width="100">
    </p>
</body>

</html>
