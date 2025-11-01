<?php
session_start();
include 'config.php';

echo "<h1>Debug Registrasi</h1>";

// Cek koneksi database
if ($conn) {
    echo "<div class='alert alert-success'>Koneksi database berhasil!</div>";
} else {
    echo "<div class='alert alert-danger'>Koneksi database gagal: " . mysqli_connect_error() . "</div>";
    exit;
}

// Cek struktur tabel users
echo "<h2>Struktur Tabel Users</h2>";
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

// Cek data users
echo "<h2>Data Users</h2>";
 $result = mysqli_query($conn, "SELECT * FROM users");
if ($result) {
    if (mysqli_num_rows($result) > 0) {
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
        echo "<div class='alert alert-warning'>Belum ada data di tabel users</div>";
    }
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

echo "<p>Query: " . $query . "</p>";

if (mysqli_query($conn, $query)) {
    echo "<div class='alert alert-success'>Data berhasil disimpan! ID: " . mysqli_insert_id($conn) . "</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
}

// Cek folder uploads
echo "<h2>Cek Folder Uploads</h2>";
if (file_exists('uploads')) {
    echo "<div class='alert alert-success'>Folder uploads ada</div>";
    if (is_writable('uploads')) {
        echo "<div class='alert alert-success'>Folder uploads bisa ditulis</div>";
    } else {
        echo "<div class='alert alert-warning'>Folder uploads tidak bisa ditulis</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Folder uploads tidak ada</div>";
}

if (file_exists('uploads/ktp')) {
    echo "<div class='alert alert-success'>Folder uploads/ktp ada</div>";
    if (is_writable('uploads/ktp')) {
        echo "<div class='alert alert-success'>Folder uploads/ktp bisa ditulis</div>";
    } else {
        echo "<div class='alert alert-warning'>Folder uploads/ktp tidak bisa ditulis</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Folder uploads/ktp tidak ada</div>";
}

// Cek konfigurasi PHP
echo "<h2>Konfigurasi PHP</h2>";
echo "<table class='table table-bordered'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>file_uploads</td><td>" . ini_get('file_uploads') . "</td></tr>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>" . ini_get('max_execution_time') . "</td></tr>";
echo "</table>";

echo "<div class='mt-3'>";
echo "<a href='register.php' class='btn btn-primary'>Kembali ke Registrasi</a>";
echo " ";
echo "<a href='test_db.php' class='btn btn-secondary'>Test Database</a>";
echo "</div>";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
    </div>
</body>
</html>