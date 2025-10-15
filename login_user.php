<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard_user.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login User</title>
</head>
<body>
    <h2>Login User</h2>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <?php if (isset($_SESSION['success'])) {
        echo "<p style='color:green'>".$_SESSION['success']."</p>";
        unset($_SESSION['success']);
    } ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Register di sini</a></p>
    <p>Login sebagai <a href="login_admin.php">Admin</a></p>
</body>
</html>