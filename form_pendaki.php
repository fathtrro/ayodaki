<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user' || !isset($_SESSION['id_registrasi'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$id_registrasi = $_SESSION['id_registrasi'];
$jumlah_pendaki = $_SESSION['jumlah_pendaki'];

// Verifikasi registrasi ada di database dan milik user yang login
$registrasi_check = mysqli_query($conn, "SELECT r.*, g.nama_gunung, l.nama_lokasi, l.provinsi 
                                         FROM registrasi r 
                                         JOIN gunung g ON r.id_gunung = g.id_gunung
                                         JOIN lokasi l ON g.id_lokasi = l.id_lokasi
                                         WHERE r.id_registrasi = $id_registrasi AND r.id_user = $user_id");

if (mysqli_num_rows($registrasi_check) == 0) {
    unset($_SESSION['id_registrasi']);
    unset($_SESSION['jumlah_pendaki']);
    header("Location: index.php");
    exit;
}

$registrasi = mysqli_fetch_assoc($registrasi_check);

// Proses simpan data pendaki
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $success = true;

    for ($i = 0; $i < $jumlah_pendaki; $i++) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama_pendaki'][$i]);
        $email = mysqli_real_escape_string($conn, $_POST['email'][$i]);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat'][$i]);

        $insert = "INSERT INTO pendaki (id_registrasi, nama, email, alamat) 
                  VALUES ($id_registrasi, '$nama', '$email', '$alamat')";

        if (!mysqli_query($conn, $insert)) {
            $success = false;
            $error = "Error: " . mysqli_error($conn);
            break;
        }
    }

    if ($success) {
        header("Location: pembayaran.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaki - MountHub</title>
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
            line-height: 1.6;
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
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
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

        .pendaki-card {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 2px solid #e2e8f0;
        }

        .pendaki-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-text {
            color: var(--gray);
            font-size: 0.85rem;
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

        .btn-secondary-modern:hover {
            background: #e2e8f0;
            color: var(--dark);
        }

        .info-summary {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
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
    </style>
</head>

<body>
<?php include 'navbar.php'; ?>
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">
                <i class="fas fa-users"></i> Data Pendaki
            </h1>
            <p class="page-subtitle">Lengkapi data semua pendaki yang akan mendaki</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="step completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-label">Data Pendakian</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step active">
                        <div class="step-number">2</div>
                        <div class="step-label">Data Pendaki</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step">
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

                <!-- Info Summary -->
                <div class="info-summary">
                    <h5 style="color: var(--primary-dark); font-weight: 700; margin-bottom: 1rem;">
                        <i class="fas fa-mountain"></i> Informasi Pendakian
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Gunung:</strong>
                                <?php echo htmlspecialchars($registrasi['nama_gunung']); ?></p>
                            <p class="mb-2"><strong>Lokasi:</strong>
                                <?php echo htmlspecialchars($registrasi['nama_lokasi'] . ", " . $registrasi['provinsi']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Tanggal Pendakian:</strong>
                                <?php echo date('d F Y', strtotime($registrasi['tanggal_pendakian'])); ?></p>
                            <p class="mb-2"><strong>Jumlah Pendaki:</strong> <?php echo $jumlah_pendaki; ?> orang</p>
                        </div>
                    </div>
                </div>

                <!-- Form Data Pendaki -->
                <form method="POST" action="">
                    <?php for ($i = 0; $i < $jumlah_pendaki; $i++): ?>
                        <div class="pendaki-card">
                            <div class="pendaki-header">
                                <i class="fas fa-user-circle"></i>
                                Pendaki <?php echo $i + 1; ?>
                                <?php if ($i == 0): ?>
                                    <span style="font-size: 0.85rem; color: var(--gray);">(Ketua Kelompok)</span>
                                <?php endif; ?>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" name="nama_pendaki[]" required>
                                    <div class="form-text">Sesuai KTP/identitas</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email[]" required>
                                    <div class="form-text">Email aktif untuk komunikasi</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Alamat Lengkap *</label>
                                    <textarea class="form-control" name="alamat[]" rows="2" required></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>

                    <div class="d-flex gap-3 justify-content-between mt-4">
                        <a href="form_pendaftaran.php?id_gunung=<?php echo $registrasi['id_gunung']; ?>"
                            class="btn-modern btn-secondary-modern">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                        <button type="submit" name="simpan" class="btn-modern btn-primary-modern">
                            Lanjut ke Pembayaran
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
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
</body>

</html>