<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login_admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
</head>
<body>
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <?php echo $_SESSION['admin_username']; ?>!</p>
    <p>Ini adalah halaman khusus admin.</p>
    <a href="logout.php">Logout</a>
</body>
</html>