<?php
session_start();
include 'config.php';

// Cek apakah user sudah login dan rolenya user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$id_gunung = $_GET['id_gunung'];
$id_user = $_SESSION['user_id'];

// Ambil data gunung
$result = mysqli_query($conn, "SELECT g.*, l.nama_lokasi, l.provinsi 
                              FROM gunung g 
                              JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
                              WHERE g.id_gunung = $id_gunung");
$gunung = mysqli_fetch_assoc($result);

if (!$gunung) {
    header("Location: index.php");
    exit();
}

// Ambil data user
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id_user");
$user = mysqli_fetch_assoc($user_result);

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['daftar'])) {
    $tanggal_pendakian = $_POST['tanggal_pendakian'];
    $tanggal_turun = $_POST['tanggal_turun'];
    $jumlah_pendaki = $_POST['jumlah_pendaki'];
    $no_telp = $_POST['no_telp'];

    // Validasi input
    if (empty($tanggal_pendakian) || empty($tanggal_turun) || empty($jumlah_pendaki) || empty($no_telp)) {
        $error = "Semua field harus diisi!";
    } else {
        // Insert ke tabel registrasi
        $insert_registrasi = "INSERT INTO registrasi (id_user, id_gunung, tanggal_pendakian, tanggal_selesai, no_hp) 
                             VALUES ($id_user, $id_gunung, '$tanggal_pendakian', '$tanggal_turun', '$no_telp')";

        if (mysqli_query($conn, $insert_registrasi)) {
            $id_registrasi = mysqli_insert_id($conn);
            $_SESSION['id_registrasi'] = $id_registrasi;
            $_SESSION['jumlah_pendaki'] = $jumlah_pendaki;
            header("Location: form_pendaki.php");
            exit();
        } else {
            $error = "Pendaftaran gagal. Silahkan coba lagi. Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pendakian <?php echo $gunung['nama_gunung']; ?> - MountHub</title>
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
            --secondary: #6366f1;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fafafa;
            color: var(--dark);
            line-height: 1.6;
        }


        /* Page Header */
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

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Progress Steps */
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
            position: relative;
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
            transition: all 0.3s ease;
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

        .step.active .step-label {
            color: var(--primary);
        }

        .step-connector {
            width: 80px;
            height: 2px;
            background: var(--light-gray);
            margin: 0 -1rem;
        }

        /* Form Container */
        .form-container {
            padding: 0 0 3rem;
        }

        .form-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
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

        /* Form Elements */
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
            margin-top: 0.25rem;
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-box-title {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .info-box li {
            color: var(--dark);
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        /* Mountain Info Card */
        .mountain-info {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .mountain-info-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .mountain-icon {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
        }

        .mountain-details h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .mountain-location {
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mountain-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 0.75rem;
            background: white;
            border-radius: 8px;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Buttons */
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

        /* Alert */
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

        /* Footer */
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

            .mountain-stats {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 1.5rem;
            }

            .progress-steps {
                flex-direction: column;
                gap: 0.5rem;
            }

            .step-connector {
                width: 2px;
                height: 40px;
                margin: -0.5rem 0;
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
                <i class="fas fa-clipboard-list"></i> Form Pendaftaran Pendakian
            </h1>
            <p class="page-subtitle">Lengkapi data di bawah ini untuk mendaftar pendakian</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <!-- Progress Steps -->
                    <div class="progress-steps">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <div class="step-label">Data Pendakian</div>
                        </div>
                        <div class="step-connector"></div>
                        <div class="step">
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

                    <!-- Mountain Info -->
                    <div class="mountain-info">
                        <div class="mountain-info-header">
                            <div class="mountain-icon">
                                <i class="fas fa-mountain"></i>
                            </div>
                            <div class="mountain-details">
                                <h3><?php echo htmlspecialchars($gunung['nama_gunung']); ?></h3>
                                <div class="mountain-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($gunung['nama_lokasi'] . ", " . $gunung['provinsi']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="mountain-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo number_format($gunung['ketinggian']); ?></div>
                                <div class="stat-label">Mdpl</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo htmlspecialchars($gunung['tingkat_kesulitan']); ?>
                                </div>
                                <div class="stat-label">Kesulitan</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">
                                    <i class="fas fa-star" style="color: #fbbf24;"></i> 4.5
                                </div>
                                <div class="stat-label">Rating</div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-info-circle"></i>
                            Informasi Penting
                        </div>
                        <ul>
                            <li>Pastikan semua data yang Anda masukkan sudah benar dan sesuai</li>
                            <li>Simpan bukti pendaftaran untuk proses verifikasi</li>
                            <li>Lakukan pembayaran sesuai dengan instruksi yang diberikan</li>
                            <li>Hubungi admin jika ada kendala dalam proses pendaftaran</li>
                        </ul>
                    </div>

                    <!-- Form Card -->
                    <form method="POST" action="">
                        <div class="form-card">
                            <h2 class="section-title">
                                <i class="fas fa-calendar-alt"></i>
                                Jadwal Pendakian
                            </h2>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Pendakian *</label>
                                    <input type="date" class="form-control" name="tanggal_pendakian"
                                        min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                    <div class="form-text">Minimal H+1 dari sekarang</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Turun *</label>
                                    <input type="date" class="form-control" name="tanggal_turun"
                                        min="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" required>
                                    <div class="form-text">Estimasi tanggal turun dari gunung</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Jumlah Pendaki *</label>
                                    <input type="number" class="form-control" name="jumlah_pendaki" min="1" max="20"
                                        value="1" required>
                                    <div class="form-text">Maksimal 20 pendaki per kelompok</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telepon *</label>
                                    <input type="tel" class="form-control" name="no_telp" placeholder="08xxxxxxxxxx"
                                        required>
                                    <div class="form-text">Nomor yang dapat dihubungi</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-between">
                            <a href="detail_gunung.php?id=<?php echo $id_gunung; ?>"
                                class="btn-modern btn-secondary-modern">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>
                            <button type="submit" name="daftar" class="btn-modern btn-primary-modern">
                                Lanjut ke Input Data Pendaki
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
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
           // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });

            // Prevent dropdown from closing when clicking inside
            profileDropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // Validasi tanggal
        document.querySelector('input[name="tanggal_pendakian"]').addEventListener('change', function () {
            const tanggalPendakian = new Date(this.value);
            const inputTurun = document.querySelector('input[name="tanggal_turun"]');

            // Set minimum tanggal turun = tanggal pendakian + 1 hari
            const minTurun = new Date(tanggalPendakian);
            minTurun.setDate(minTurun.getDate() + 1);

            const minTurunStr = minTurun.toISOString().split('T')[0];
            inputTurun.setAttribute('min', minTurunStr);

            // Reset value jika tanggal turun lebih kecil
            if (inputTurun.value && new Date(inputTurun.value) <= tanggalPendakian) {
                inputTurun.value = '';
            }
        });
    </script>
</body>

</html>