<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi SIMAKSI</title>
</head>
<body>
    <h1>Verifikasi SIMAKSI di Pos</h1>
    <form method="post">
        <label>Masukkan Nomor SIMAKSI:</label>
        <input type="text" name="simaksi" required>
        <input type="submit" name="submit" value="Verifikasi">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $simaksi = $_POST['simaksi'];
        $sql = "SELECT p.*, g.nama_gunung, l.nama_lokasi 
                FROM pendaki p 
                JOIN gunung g ON p.id_gunung = g.id_gunung 
                JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
                WHERE p.simaksi = '$simaksi'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            echo "<h3>Data Pendaki:</h3>";
            echo "Nama: " . $data['nama'] . "<br>";
            echo "Email: " . $data['email'] . "<br>";
            echo "No. HP: " . $data['no_hp'] . "<br>";
            echo "Lokasi: " . $data['nama_lokasi'] . "<br>";
            echo "Gunung: " . $data['nama_gunung'] . "<br>";
            echo "Tanggal: " . $data['tanggal_pendakian'] . "<br>";
            echo "Status Pembayaran: " . $data['status_pembayaran'] . "<br>";
            echo "<p>SIMAKSI valid!</p>";
        } else {
            echo "<p>SIMAKSI tidak ditemukan!</p>";
        }
    }
    ?>
</body>
</html>