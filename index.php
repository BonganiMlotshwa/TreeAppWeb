<?php
require 'auth.php';
require_login();
require 'db.php';

$user_role = $_SESSION['role'];
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to TreeApp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
    <style>
        .feature-card { min-height: 180px; }
    </style>
</head>
<body class="bg-light">
<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="container" style="max-width: 800px;">
    <div class="text-center mb-5">
      <h1 class="display-4 fw-bold mb-3">Welcome to TreeApp</h1>
      <p class="lead">A Modern Platform For Teachers And Students To Manage Assignments, Submissions, Feedback, And More.</p>
      <span class="badge bg-primary fs-6">Logged in as: <?= $username ?> (<?= ucfirst($user_role) ?>)</span>
    </div>
    <div class="row g-4 mb-4">
      <?php if ($user_role === 'teacher'): ?>
        <div class="col-md-6">
          <div class="card feature-card shadow-sm">
            <div class="card-body text-center">
              <h5 class="card-title">Teacher Dashboard</h5>
              <p class="card-text">Create tasks, upload files, view student submissions, and provide feedback.</p>
              <a href="teacher_dashboard.php" class="btn btn-success">Go to Teacher Dashboard</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card feature-card shadow-sm">
            <div class="card-body text-center">
              <h5 class="card-title">View Reports</h5>
              <p class="card-text">See and respond to student problem reports for your assignments.</p>
              <a href="view_reports.php" class="btn btn-warning">View Reports</a>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="col-md-6">
          <div class="card feature-card shadow-sm">
            <div class="card-body text-center">
              <h5 class="card-title">Student Dashboard</h5>
              <p class="card-text">View assigned tasks, submit your work, download files, and track feedback.</p>
              <a href="student_dashboard.php" class="btn btn-success">Go to Student Dashboard</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card feature-card shadow-sm">
            <div class="card-body text-center">
              <h5 class="card-title">My Submissions</h5>
              <p class="card-text">Edit or download your submissions, and view teacher feedback.</p>
              <a href="student_dashboard.php" class="btn btn-info">View Submissions</a>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <div class="col-md-6">
        <div class="card feature-card shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Courses</h5>
            <p class="card-text">Browse and manage your courses and related materials.</p>
            <a href="courses.php" class="btn btn-primary">Go to Courses</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card feature-card shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Calendar</h5>
            <p class="card-text">View your schedule and upcoming deadlines in calendar view.</p>
            <a href="calendar.php" class="btn btn-warning">View Calendar</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card feature-card shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Profile</h5>
            <p class="card-text">Update your profile information and settings.</p>
            <a href="profile.php" class="btn btn-secondary">Edit Profile</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card feature-card shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Logout</h5>
            <p class="card-text">Sign out of your TreeApp account securely.</p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
          </div>
        </div>
      </div>
    </div>
    <div class="alert alert-info text-center mt-4">
      <b>Tip:</b> Use the dark mode toggle in the top right for a more comfortable viewing experience!
    </div>
  </div>
</div>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html> 