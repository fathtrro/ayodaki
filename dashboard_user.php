<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login_user.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard User</title>
</head>
<body>
    <h2>Selamat datang, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Ini adalah halaman dashboard user.</p>
    <a href="logout.php">Logout</a>
</body>
</html>