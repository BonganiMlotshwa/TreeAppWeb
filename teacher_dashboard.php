<?php
require 'auth.php';
require_login();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: student_dashboard.php');
    exit;
}
require 'db.php';

// Handle task creation
$task_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $attachment = null;
    
    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        $allowed_types = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed_types)) {
            $attachment = uniqid('task_' . $_SESSION['user_id'] . '_') . '.' . $ext;
            $upload_path = 'uploads/' . $attachment;
            
            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                $task_error = 'Failed to upload file. Please try again.';
            }
        } else {
            $task_error = 'Invalid file type. Allowed: ' . implode(', ', $allowed_types);
        }
    }
    
    if ($title && $due_date && !$task_error) {
        $stmt = $pdo->prepare('INSERT INTO tasks (teacher_id, title, description, due_date, attachment) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $title, $description, $due_date, $attachment]);
    } elseif (!$task_error) {
        $task_error = 'Title and due date are required.';
    }
}
// Fetch tasks created by this teacher
$stmt = $pdo->prepare('SELECT * FROM tasks WHERE teacher_id = ? ORDER BY due_date DESC');
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="container mt-5">
    <a href="index.php" class="btn btn-outline-primary mb-3">&larr; Back to Home</a>
    <h1>Teacher Dashboard</h1>
    <p>Welcome, teacher!</p>
    <a href="view_reports.php" class="btn btn-warning mb-3">View Student Reports</a>
    <hr>
    <h3>Create New Task</h3>
    <?php if ($task_error): ?><div class="alert alert-danger"><?= htmlspecialchars($task_error) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="mb-4" style="max-width:500px;">
        <input type="hidden" name="create_task" value="1">
        <div class="mb-3">
            <label for="title" class="form-label">Task Title *</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Enter task title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Task Description</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter detailed task description"></textarea>
        </div>
        <div class="mb-3">
            <label for="due_date" class="form-label">Due Date *</label>
            <input type="date" name="due_date" id="due_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="attachment" class="form-label">Task File (Optional)</label>
            <input type="file" name="attachment" id="attachment" class="form-control">
            <div class="form-text">Upload supporting files: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, ZIP, RAR</div>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-plus"></i> Create Task
        </button>
    </form>
    <h3>Your Tasks</h3>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Title</th><th>Description</th><th>Due Date</th><th>Attachment</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['description']) ?></td>
                <td><?= htmlspecialchars($task['due_date']) ?></td>
                <td>
                    <?php if ($task['attachment']): ?>
                        <a href="uploads/<?= htmlspecialchars($task['attachment']) ?>" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-download"></i> Download
                        </a>
                    <?php else: ?>
                        <span class="text-muted">No file</span>
                    <?php endif; ?>
                </td>
                <td><a href="view_submissions.php?task_id=<?= $task['id'] ?>" class="btn btn-primary btn-sm">View Submissions</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</body>
</html> 