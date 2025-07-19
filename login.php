<?php
require 'db.php';
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === 'teacher') {
            header('Location: teacher_dashboard.php');
        } else {
            header('Location: student_dashboard.php');
        }
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="container text-center">
    <h2>Login</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="post" class="mx-auto" style="max-width:400px;">
        <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required style="max-width:400px;margin:auto;"></div>
        <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required style="max-width:400px;margin:auto;"></div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <a href="signup.php" class="btn btn-link">Create an account</a>
    </form>
  </div>
</div>
</body>
</html> 