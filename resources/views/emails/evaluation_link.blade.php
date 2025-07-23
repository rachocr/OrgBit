<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Evaluation Link</title>
</head>
<body>
    <h1>Event Evaluation</h1>
    <p>Dear Attendee,</p>
    <p>Thank you for attending the event <strong>{{ $eventName }}</strong>. Please take a moment to provide your feedback by clicking the link below:</p>
    <p>
        <a href="{{ $evaluationLink }}" target="_blank">Evaluate Event</a>
    </p>
    <p>Thank you for your participation!</p>
</body>
</html>