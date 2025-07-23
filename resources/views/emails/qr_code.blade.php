<!DOCTYPE html>
<html>
<head>
    <title>Your Event QR Code</title>
</head>
<body>
    <h1>Hello, This is OrgBit!</h1>
    <p>Here is your QR code for the event. Please present this QR code at the event entrance.</p>
    <p><strong>Event Name:</strong> {{ $eventName }}</p>
    <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($eventStartDate)->format('F j, Y g:i A') }} - {{ \Carbon\Carbon::parse($eventEndDate)->format('F j, Y g:i A') }}</p>
    <p><strong>Event Location:</strong> {{ $eventLocation }}</p>
    <p>Let the Officers of the Organization scan this attached QR code to be checked in at the event.</p>
</body>
</html>