<?php
require 'auth.php';
require_login();
require 'db.php';
$user_id = $_SESSION['user_id'];

// If this is an AJAX request for events, output JSON
if (isset($_GET['events'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare('SELECT id, title, date, from_time, to_time, completed FROM tasks WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $events = [];
    foreach ($stmt->fetchAll() as $task) {
        if ($task['date']) {
            $start = $task['date'];
            if ($task['from_time']) $start .= 'T' . $task['from_time'];
            $end = $task['date'];
            if ($task['to_time']) $end .= 'T' . $task['to_time'];
            $events[] = [
                'id' => $task['id'],
                'title' => $task['title'] . ($task['completed'] ? ' (Done)' : ''),
                'start' => $start,
                'end' => $end !== $start ? $end : null,
                'color' => $task['completed'] ? '#28a745' : '#007bff',
            ];
        }
    }
    echo json_encode($events);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Calendar View</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="assets/darkmode.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: 'calendar.php?events=1',
            eventClick: function(info) {
                alert('Task: ' + info.event.title + '\nStart: ' + info.event.start.toLocaleString());
            }
        });
        calendar.render();
    });
    </script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="container text-center">
    <h2>Calendar View</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <div id='calendar'></div>
  </div>
</div>
</body>
</html> 