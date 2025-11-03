<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user' || !isset($_SESSION['id_registrasi'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

$id_registrasi = $_SESSION['id_registrasi'];
$user_id = $_SESSION['user_id'];

// Ambil data registrasi
$query = "SELECT r.*, g.nama_gunung, g.ketinggian, l.nama_lokasi, l.provinsi 
          FROM registrasi r 
          JOIN gunung g ON r.id_gunung = g.id_gunung 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
          WHERE r.id_registrasi = $id_registrasi AND r.id_user = $user_id";
$result = mysqli_query($conn, $query);
$registrasi = mysqli_fetch_assoc($result);

if (!$registrasi) {
    header("Location: index.php");
    exit;
}

// Ambil data pendaki
$pendaki_result = mysqli_query($conn, "SELECT * FROM pendaki WHERE id_registrasi = $id_registrasi");
$jumlah_pendaki = mysqli_num_rows($pendaki_result);

// Hitung total pembayaran (bisa disesuaikan dengan harga per gunung)
$harga_per_orang = 50000;
$total_pembayaran = $harga_per_orang * $jumlah_pendaki;

// Cek apakah sudah ada pembayaran
$cek_pembayaran = mysqli_query($conn, "SELECT * FROM pembayaran WHERE id_registrasi = $id_registrasi");
$sudah_bayar = mysqli_num_rows($cek_pembayaran) > 0;

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bayar']) && !$sudah_bayar) {
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Buat folder uploads jika belum ada
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    if (!file_exists('uploads/bukti_pembayaran')) {
        mkdir('uploads/bukti_pembayaran', 0777, true);
    }

    // Upload bukti pembayaran
    $target_dir = "uploads/bukti_pembayaran/";
    $file_extension = pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION);
    $new_filename = "bukti_" . $id_registrasi . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    $uploadOk = 1;

    // Cek apakah file adalah gambar
    if (isset($_FILES["bukti_pembayaran"]["tmp_name"]) && !empty($_FILES["bukti_pembayaran"]["tmp_name"])) {
        $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File bukan gambar.";
            $uploadOk = 0;
        }

        // Cek ukuran file (max 2MB)
        if ($_FILES["bukti_pembayaran"]["size"] > 2000000) {
            $error = "Ukuran file terlalu besar. Maksimal 2MB.";
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
                $insert_pembayaran = "INSERT INTO pembayaran (id_registrasi, jumlah_bayar, metode_pembayaran, bukti_pembayaran) 
                          VALUES ($id_registrasi, $total_pembayaran, '$metode_pembayaran', '$target_file')";

                if (mysqli_query($conn, $insert_pembayaran)) {
                    $success = true;
                    $sudah_bayar = true;
                } else {
                    $error = "Gagal menyimpan data pembayaran: " . mysqli_error($conn);
                }
            } else {
                $error = "Maaf, terjadi kesalahan saat mengupload file.";
            }
        }
    } else {
        $error = "Silahkan upload bukti pembayaran.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - MountHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: var(--dark);
        }

       
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 3rem 0;
            margin-bottom: 3rem;
            color: white;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--light-gray);
            color: var(--gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .step.completed .step-number {
            background: var(--primary);
            color: white;
        }

        .step.active .step-number {
            background: var(--primary);
            color: white;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        }

        .step-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray);
        }

        .step.completed .step-label,
        .step.active .step-label {
            color: var(--primary);
        }

        .step-connector {
            width: 80px;
            height: 2px;
            background: var(--light-gray);
        }

        .step.completed+.step-connector {
            background: var(--primary);
        }

        .form-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary);
        }

        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-label {
            width: 200px;
            font-weight: 600;
            color: var(--gray);
        }

        .info-value {
            flex: 1;
            color: var(--dark);
        }

        .payment-summary {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
        }

        .bank-info {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-top: 1rem;
        }

        .bank-info h5 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .bank-account {
            background: var(--light-gray);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
            margin-top: 1rem;
            border: 2px solid #e2e8f0;
        }

        .btn-modern {
            padding: 0.875rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary-modern {
            background: var(--light-gray);
            color: var(--gray);
        }

        .success-card {
            text-align: center;
            padding: 3rem;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 3rem;
        }

        .alert-modern {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        footer {
            background: var(--dark);
            color: white;
            padding: 2rem 0;
            margin-top: 5rem;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.75rem;
            }

            .progress-steps {
                flex-direction: column;
            }

            .step-connector {
                width: 2px;
                height: 40px;
            }
        }

        .btn-outline-modern {
    display: inline-block;
    padding: 10px 18px;
    border: 2px solid var(--primary);
    background: transparent;
    color: var(--primary);
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s ease;
    text-decoration: none;
}

.btn-outline-modern:hover {
    background: var(--primary);
    color: #fff;
}

    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">
                <i class="fas fa-credit-card"></i> Pembayaran
            </h1>
            <p class="page-subtitle">Selesaikan pembayaran untuk menyelesaikan pendaftaran</p>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="step completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-label">Data Pendakian</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-label">Data Pendaki</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step active">
                        <div class="step-number">3</div>
                        <div class="step-label">Pembayaran</div>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert-modern alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert-modern alert-success">
                        <i class="fas fa-check-circle"></i>
                        Pembayaran berhasil dikirim! Menunggu verifikasi admin.
                    </div>
                <?php endif; ?>

                <?php if (!$sudah_bayar): ?>
                    <!-- Detail Pembayaran -->
                    <div class="form-card">
                        <h2 class="section-title">
                            <i class="fas fa-file-invoice"></i>
                            Detail Pendaftaran
                        </h2>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3" style="color: var(--primary);">Informasi Pendakian</h5>
                                <div class="info-row">
                                    <div class="info-label">Gunung</div>
                                    <div class="info-value">: <?php echo htmlspecialchars($registrasi['nama_gunung']); ?>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Lokasi</div>
                                    <div class="info-value">:
                                        <?php echo htmlspecialchars($registrasi['nama_lokasi'] . ", " . $registrasi['provinsi']); ?>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tanggal Pendakian</div>
                                    <div class="info-value">:
                                        <?php echo date('d F Y', strtotime($registrasi['tanggal_pendakian'])); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tanggal Selesai</div>
                                    <div class="info-value">:
                                        <?php echo date('d F Y', strtotime($registrasi['tanggal_selesai'])); ?></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3" style="color: var(--primary);">Data Pendaki</h5>
                                <?php
                                mysqli_data_seek($pendaki_result, 0);
                                $no = 1;
                                while ($pendaki = mysqli_fetch_assoc($pendaki_result)):
                                    ?>
                                    <div class="info-row">
                                        <div class="info-label">Pendaki <?php echo $no++; ?></div>
                                        <div class="info-value">: <?php echo htmlspecialchars($pendaki['nama']); ?></div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <div class="payment-summary">
                            <h4 style="color: var(--primary-dark); font-weight: 700; margin-bottom: 1.5rem;">
                                <i class="fas fa-calculator"></i> Rincian Pembayaran
                            </h4>
                            <div class="info-row" style="border-bottom-color: #10b981;">
                                <div class="info-label" style="color: var(--dark);">Jumlah Pendaki</div>
                                <div class="info-value">: <?php echo $jumlah_pendaki; ?> orang</div>
                            </div>
                            <div class="info-row" style="border-bottom-color: #10b981;">
                                <div class="info-label" style="color: var(--dark);">Harga per Orang</div>
                                <div class="info-value">: Rp <?php echo number_format($harga_per_orang, 0, ',', '.'); ?>
                                </div>
                            </div>
                            <div class="info-row" style="border-bottom: 2px solid #10b981; padding-top: 1rem;">
                                <div class="info-label" style="color: var(--dark); font-size: 1.1rem;"><strong>Total
                                        Pembayaran</strong></div>
                                <div class="info-value" style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                                    : Rp <?php echo number_format($total_pembayaran, 0, ',', '.'); ?></div>
                            </div>

                            <div class="bank-info">
                                <h5><i class="fas fa-university"></i> Transfer ke Rekening</h5>
                                <div class="bank-account">
                                    <strong style="color: var(--primary);">Bank BCA</strong><br>
                                    <strong style="font-size: 1.25rem;">1234567890</strong><br>
                                    <span>a/n <strong>MountHub Indonesia</strong></span>
                                </div>
                                <div class="bank-account">
                                    <strong style="color: var(--primary);">Bank Mandiri</strong><br>
                                    <strong style="font-size: 1.25rem;">9876543210</strong><br>
                                    <span>a/n <strong>MountHub Indonesia</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Upload Bukti -->
                    <div class="form-card">
                        <h2 class="section-title">
                            <i class="fas fa-upload"></i>
                            Upload Bukti Pembayaran
                        </h2>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Metode Pembayaran *</label>
                                    <select class="form-select" name="metode_pembayaran" required>
                                        <option value="">Pilih Metode</option>
                                        <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                        <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                                        <option value="E-Wallet">E-Wallet (OVO/GoPay/DANA)</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Bukti Pembayaran *</label>
                                    <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*"
                                        required onchange="previewImage(event)">
                                    <small class="text-muted">Format: JPG, JPEG, PNG (Max 2MB)</small>
                                </div>

                                <div class="col-12 text-center">
                                    <img id="preview" class="preview-image" style="display: none;">
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-between mt-4">
                                <a href="form_pendaki.php" class="btn-modern btn-secondary-modern">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                                <button type="submit" name="bayar" class="btn-modern btn-primary-modern">
                                    <i class="fas fa-paper-plane"></i>
                                    Kirim Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Success Card -->
<div class="form-card success-card">
    <div class="success-icon">
        <i class="fas fa-check"></i>
    </div>
    <h3 style="color: var(--primary); font-weight: 700; margin-bottom: 1rem;">
        Pembayaran Berhasil Dikirim!
    </h3>
    <p class="text-muted mb-4">
        Terima kasih telah melakukan pembayaran. Pembayaran Anda sedang dalam proses verifikasi oleh
        admin.<br>
        Anda akan menerima notifikasi setelah pembayaran diverifikasi.
    </p>

    <a href="user/dashboard.php" class="btn-modern btn-primary-modern mb-2">
        <i class="fas fa-home"></i>
        Kembali ke Dashboard
    </a>

    <a href="user/riwayat.php" class="btn-modern btn-outline-modern">
        <i class="fas fa-receipt"></i>
        Cek Status Pembayaran
    </a>
</div>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-2"><strong>MountHub</strong> - Platform Booking Pendakian Terpercaya</p>
            <p>&copy; <?php echo date('Y'); ?> All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = "block";
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>