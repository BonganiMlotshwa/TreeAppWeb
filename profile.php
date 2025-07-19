<?php
require 'auth.php';
require_login();
require 'db.php';
$user_id = $_SESSION['user_id'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    if ($username && $email) {
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
        $stmt->execute([$username, $email, $user_id]);
        $_SESSION['username'] = $username;
        header('Location: profile.php?success=1');
        exit;
    } else {
        $error = 'All fields required.';
    }
}
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head><title>Profile</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/darkmode.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="container text-center">
    <h2>Profile</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Profile updated!</div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="mx-auto mb-4" style="max-width:400px;">
      <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required style="max-width:400px;margin:auto;"></div>
      <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required style="max-width:400px;margin:auto;"></div>
      <div class="mb-3"><input type="file" name="profile_pic" class="form-control" style="max-width:400px;margin:auto;"></div>
      <button type="submit" class="btn btn-primary w-100 mb-3">Update Profile</button>
      <a href="index.php" class="btn btn-secondary w-100">Back</a>
    </form>
  </div>
</div>
</body>
</html> 