// test_db.php
<?php
include 'config.php';

echo "<h1>Test Database Connection</h1>";

// Test koneksi
if ($conn) {
    echo "<div class='alert alert-success'>Koneksi database berhasil!</div>";
} else {
    echo "<div class='alert alert-danger'>Koneksi database gagal: " . mysqli_connect_error() . "</div>";
    exit;
}

// Test tabel users
echo "<h2>Test Tabel Users</h2>";
 $result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    echo "<table class='table table-bordered'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
}

// Test data users
echo "<h2>Data Users</h2>";
 $result = mysqli_query($conn, "SELECT * FROM users");
if ($result) {
    echo "<table class='table table-bordered'>";
    echo "<tr><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Email</th><th>No. HP</th><th>Role</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_user'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['nama_lengkap'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['no_hp'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
}

// Test insert data
echo "<h2>Test Insert Data</h2>";
 $username = "testuser_" . time();
 $password = password_hash("password123", PASSWORD_DEFAULT);
 $nama_lengkap = "Test User";
 $email = "test" . time() . "@example.com";
 $no_hp = "081234567890";

 $query = "INSERT INTO users (username, password, nama_lengkap, email, no_hp) 
          VALUES ('$username', '$password', '$nama_lengkap', '$email', '$no_hp')";

echo "Query: " . $query . "<br>";

if (mysqli_query($conn, $query)) {
    echo "<div class='alert alert-success'>Data berhasil disimpan! ID: " . mysqli_insert_id($conn) . "</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
}

echo "<a href='register.php' class='btn btn-primary'>Kembali ke Registrasi</a>";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
    </div>
</body>
</html>