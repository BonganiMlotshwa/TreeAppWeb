<?php
require 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if ($username && $email && $_POST['password'] && $role) {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
        try {
            $stmt->execute([$username, $email, $password, $role]);
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $error = "Username or email already exists.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="container text-center">
    <h2>Sign Up</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="post" class="mx-auto" style="max-width:400px;">
        <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required style="max-width:400px;margin:auto;"></div>
        <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required style="max-width:400px;margin:auto;"></div>
        <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required style="max-width:400px;margin:auto;"></div>
        <div class="mb-3">
            <label for="role" class="form-label">Register as:</label>
            <select name="role" id="role" class="form-control" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        <a href="login.php" class="btn btn-link">Already have an account?</a>
    </form>
  </div>
</div>
</body>
</html> 