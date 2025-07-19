<?php
require 'auth.php';
require_login();
require 'db.php';

$user_id = $_SESSION['user_id'];

// Handle add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $date = $_POST['date'] ?: null;
    $from = $_POST['from_time'] ?: null;
    $to = $_POST['to_time'] ?: null;
    $reminder = $_POST['reminder_time'] ?: null;
    $category = $_POST['category'] ?: null;
    $priority = $_POST['priority'] ?: 'Medium';
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $target = 'uploads/' . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $target);
        $attachment = $target;
    }
    if ($title) {
        $stmt = $pdo->prepare('INSERT INTO tasks (user_id, title, description, date, from_time, to_time, reminder_time, attachment, category, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $title, $desc, $date, $from, $to, $reminder, $attachment, $category, $priority]);
    }
    header('Location: tasks.php');
    exit;
}

// Handle complete
if (isset($_GET['complete'])) {
    $stmt = $pdo->prepare('UPDATE tasks SET completed = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([$_GET['complete'], $user_id]);
    header('Location: tasks.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$_GET['delete'], $user_id]);
    header('Location: tasks.php');
    exit;
}

// Fetch tasks
$stmt = $pdo->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY completed, date, from_time');
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tasks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="container text-center">
    <h2>My Tasks</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <form method="post" enctype="multipart/form-data" class="row g-3 mb-4 mx-auto" style="max-width:700px;">
        <div class="col-md-6 mb-3"><input type="text" name="title" class="form-control" placeholder="Title" required style="max-width:400px;margin:auto;"></div>
        <div class="col-md-6 mb-3"><input type="text" name="description" class="form-control" placeholder="Description" style="max-width:400px;margin:auto;"></div>
        <div class="col-md-4 mb-3"><input type="date" name="date" class="form-control" style="max-width:400px;margin:auto;"></div>
        <div class="col-md-4 mb-3"><input type="time" name="from_time" class="form-control" style="max-width:400px;margin:auto;"></div>
        <div class="col-md-4 mb-3"><input type="time" name="to_time" class="form-control" style="max-width:400px;margin:auto;"></div>
        <div class="col-md-6 mb-3"><input type="datetime-local" name="reminder_time" class="form-control" placeholder="Reminder" style="max-width:400px;margin:auto;"></div>
        <div class="col-md-3 mb-3">
          <select name="category" class="form-control" style="max-width:400px;margin:auto;">
            <option value="">Category</option>
            <option value="Homework">Homework</option>
            <option value="Exam">Exam</option>
            <option value="Personal">Personal</option>
          </select>
        </div>
        <div class="col-md-3 mb-3">
          <select name="priority" class="form-control" style="max-width:400px;margin:auto;">
            <option value="Low">Low</option>
            <option value="Medium" selected>Medium</option>
            <option value="High">High</option>
          </select>
        </div>
        <div class="col-md-6 mb-3"><input type="file" name="attachment" class="form-control" style="max-width:400px;margin:auto;"></div>
        <div class="col-md-12 mt-2 mb-3"><button type="submit" name="add" class="btn btn-primary w-100">Add Task</button></div>
    </form>
    <table class="table table-bordered bg-white mb-4">
        <thead>
            <tr>
                <th>Title</th><th>Description</th><th>Date</th><th>From</th><th>To</th><th>Reminder</th><th>Category</th><th>Priority</th><th>Attachment</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['description']) ?></td>
                <td><?= htmlspecialchars($task['date']) ?></td>
                <td><?= htmlspecialchars($task['from_time']) ?></td>
                <td><?= htmlspecialchars($task['to_time']) ?></td>
                <td><?= htmlspecialchars($task['reminder_time']) ?></td>
                <td><?= htmlspecialchars($task['category']) ?></td>
                <td><?= htmlspecialchars($task['priority']) ?></td>
                <td><?php if ($task['attachment']) { echo '<a href="' . htmlspecialchars($task['attachment']) . '" target="_blank">Download</a>'; } ?></td>
                <td><?= $task['completed'] ? '<span class="badge bg-success">Done</span>' : '<span class="badge bg-warning text-dark">Pending</span>' ?></td>
                <td>
                    <?php if (!$task['completed']): ?>
                        <a href="?complete=<?= $task['id'] ?>" class="btn btn-sm btn-success">Complete</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $task['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
  </div>
</div>
</body>
</html> 