<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user' || !isset($_SESSION['id_registrasi'])) {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

 $id_registrasi = $_SESSION['id_registrasi'];

// Ambil data registrasi
 $query = "SELECT r.*, g.nama_gunung, l.nama_lokasi 
          FROM registrasi r 
          JOIN gunung g ON r.id_gunung = g.id_gunung 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
          WHERE r.id_registrasi = $id_registrasi";
 $result = mysqli_query($conn, $query);
 $registrasi = mysqli_fetch_assoc($result);

// Ambil data pendaki
 $pendaki_result = mysqli_query($conn, "SELECT * FROM pendaki WHERE id_registrasi = $id_registrasi");
 $jumlah_pendaki = mysqli_num_rows($pendaki_result);

// Hitung total pembayaran
 $harga_per_orang = 50000;
 $total_pembayaran = $harga_per_orang * $jumlah_pendaki;

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bayar'])) {
    $metode_pembayaran = $_POST['metode_pembayaran'];
    
    // Buat folder uploads jika belum ada
    if (!file_exists('../uploads')) {
        mkdir('../uploads', 0777, true);
    }
    if (!file_exists('../uploads/bukti_pembayaran')) {
        mkdir('../uploads/bukti_pembayaran', 0777, true);
    }
    
    // Upload bukti pembayaran
    $target_dir = "../uploads/bukti_pembayaran/";
    $file_extension = pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION);
    $new_filename = "bukti_" . $id_registrasi . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    $uploadOk = 1;
    
    // Cek apakah file adalah gambar
    $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $error = "File bukan gambar.";
        $uploadOk = 0;
    }
    
    // Cek ukuran file
    if ($_FILES["bukti_pembayaran"]["size"] > 500000) {
        $error = "Ukuran file terlalu besar.";
        $uploadOk = 0;
    }
    
    // Cek format file
    $allowed_types = array('jpg', 'jpeg', 'png');
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        $error = "Hanya file JPG, JPEG, PNG yang diperbolehkan.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
            // Simpan data pembayaran
            $query = "INSERT INTO pembayaran (id_registrasi, jumlah_bayar, metode_pembayaran, bukti_pembayaran) 
                      VALUES ($id_registrasi, $total_pembayaran, '$metode_pembayaran', '$target_file')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Pembayaran berhasil dikirim! Menunggu konfirmasi admin.";
                $payment_sent = true;
            } else {
                $error = "Error: " . $query . "<br>" . mysqli_error($conn);
            }
        } else {
            $error = "Maaf, terjadi kesalahan saat mengupload file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: #198754;
        }
        .content {
            padding: 20px;
        }
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">User Panel</h4>
                        <p class="text-white-50">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pendaftaran.php">
                                <i class="bi bi-clipboard-plus me-2"></i> Pendaftaran Pendakian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="riwayat.php">
                                <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="konfirmasi.php">
                                <i class="bi bi-check-circle me-2"></i> Konfirmasi Kedatangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="bi bi-person-circle me-2"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item mt-auto">
                            <a class="nav-link" href="../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Pembayaran</h1>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!isset($payment_sent)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">Detail Pembayaran</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Informasi Pendakian</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150">Gunung</td>
                                            <td>: <?php echo htmlspecialchars($registrasi['nama_gunung']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Lokasi</td>
                                            <td>: <?php echo htmlspecialchars($registrasi['nama_lokasi']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Pendakian</td>
                                            <td>: <?php echo date('d/m/Y', strtotime($registrasi['tanggal_pendakian'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Selesai</td>
                                            <td>: <?php echo date('d/m/Y', strtotime($registrasi['tanggal_selesai'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td>No. HP</td>
                                            <td>: <?php echo htmlspecialchars($registrasi['no_hp']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5>Data Pendaki</h5>
                                    <table class="table table-borderless">
                                        <?php while ($pendaki = mysqli_fetch_assoc($pendaki_result)): ?>
                                            <tr>
                                                <td width="150">Nama</td>
                                                <td>: <?php echo htmlspecialchars($pendaki['nama']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>: <?php echo htmlspecialchars($pendaki['email']); ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><hr></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5>Rincian Pembayaran</h5>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="200">Jumlah Pendaki</td>
                                                    <td>: <?php echo $jumlah_pendaki; ?> orang</td>
                                                </tr>
                                                <tr>
                                                    <td>Harga per Orang</td>
                                                    <td>: Rp <?php echo number_format($harga_per_orang, 0, ',', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Pembayaran</strong></td>
                                                    <td>: <strong>Rp <?php echo number_format($total_pembayaran, 0, ',', '.'); ?></strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <h5>Transfer ke</h5>
                                            <p class="mb-1"><strong>Bank BCA</strong></p>
                                            <p class="mb-1"><strong>No. Rekening: 1234567890</strong></p>
                                            <p class="mb-0"><strong>a/n Portal Pendakian</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                            <option value="Transfer Bank">Transfer Bank</option>
                                            <option value="E-Wallet">E-Wallet</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="bukti_pembayaran" class="form-label">Upload Bukti Pembayaran</label>
                                        <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required onchange="previewImage(event)">
                                        <div class="mt-2">
                                            <img id="preview" class="preview-image" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="form_pendaki.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" name="bayar" class="btn btn-success">
                                        <i class="bi bi-credit-card"></i> Bayar Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card shadow mb-4">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Pembayaran Berhasil Dikirim</h4>
                            <p class="text-muted">Pembayaran Anda sedang dalam proses verifikasi oleh admin. Silahkan cek status pembayaran di halaman riwayat transaksi.</p>
                            <div class="mt-4">
                                <a href="riwayat.php" class="btn btn-success">
                                    <i class="bi bi-clock-history"></i> Lihat Riwayat Transaksi
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = "block";
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>