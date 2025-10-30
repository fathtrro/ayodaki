<?php
session_start();
include 'config.php';

 $id_registrasi = $_SESSION['id_registrasi'];

// Generate SIMAKSI
 $simaksi = "SIM" . rand(10000, 99999);

// Update status pembayaran dan SIMAKSI
mysqli_query($conn, "UPDATE registrasi SET status_pembayaran = 'success', simaksi = '$simaksi' WHERE id_registrasi = $id_registrasi");

 $_SESSION['simaksi'] = $simaksi;
header("Location: cetak_simaksi.php");
?>