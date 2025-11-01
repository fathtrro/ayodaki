<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

 $user_id = $_SESSION['user_id'];

// Verifikasi user ada di database
 $user_check = mysqli_query($conn, "SELECT * FROM users WHERE id_user = $user_id");
if (mysqli_num_rows($user_check) == 0) {
    // Jika user tidak ditemukan, redirect ke login
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Proses pendaftaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['daftar'])) {
    $id_gunung = $_POST['id_gunung'];
    $tanggal_pendakian = $_POST['tanggal_pendakian'];
    $tanggal_selesai = date('Y-m-d', strtotime($tanggal_pendakian . ' + ' . $_POST['lama_pendakian'] . ' days'));
    $jumlah_orang = $_POST['jumlah_orang'];
    $no_hp = $_POST['no_hp'];
    
    // Validasi input
    if (empty($id_gunung) || empty($tanggal_pendakian) || empty($jumlah_orang) || empty($no_hp)) {
        $error = "Semua field harus diisi!";
    } else {
        // Cek apakah gunung ada di database
        $gunung_check = mysqli_query($conn, "SELECT * FROM gunung WHERE id_gunung = $id_gunung");
        if (mysqli_num_rows($gunung_check) == 0) {
            $error = "Gunung tidak ditemukan!";
        } else {
            // Simpan data ke tabel registrasi
            $query = "INSERT INTO registrasi (id_user, id_gunung, tanggal_pendakian, tanggal_selesai, no_hp) 
                      VALUES ($user_id, $id_gunung, '$tanggal_pendakian', '$tanggal_selesai', '$no_hp')";
            
            if (mysqli_query($conn, $query)) {
                $id_registrasi = mysqli_insert_id($conn);
                $_SESSION['id_registrasi'] = $id_registrasi;
                $_SESSION['jumlah_orang'] = $jumlah_orang;
                header("Location: form_pendaki.php");
                exit;
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Ambil data gunung
 $query = "SELECT g.*, l.nama_lokasi 
          FROM gunung g 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
          ORDER BY g.nama_gunung";
 $result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pendakian - Portal Booking Pendakian</title>
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
        .card-img-top {
            height: 150px;
            object-fit: cover;
        }
        .gunung-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .gunung-card:hover {
            transform: translateY(-5px);
        }
        .gunung-card.selected {
            border: 2px solid #198754;
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
                            <a class="nav-link active" href="pendaftaran.php">
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
                    <h1 class="h2">Pendaftaran Pendakian</h1>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Pilih Gunung</h6>
                    </div>
                    <div class="card-body">
                        <form method="post" id="formPendaftaran">
                            <div class="row mb-4">
                                <?php while ($gunung = mysqli_fetch_assoc($result)): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card gunung-card h-100" onclick="selectGunung(<?php echo $gunung['id_gunung']; ?>)">
                                            <?php if (!empty($gunung['gambar']) && file_exists($gunung['gambar'])): ?>
                                                <img src="<?php echo $gunung['gambar']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($gunung['nama_gunung']); ?>">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/400x200?text=Tidak+Ada+Gambar" class="card-img-top" alt="Tidak ada gambar">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($gunung['nama_gunung']); ?></h5>
                                                <p class="card-text">
                                                    <strong>Lokasi:</strong> <?php echo htmlspecialchars($gunung['nama_lokasi']); ?><br>
                                                    <strong>Ketinggian:</strong> <?php echo htmlspecialchars($gunung['ketinggian']); ?> mdpl<br>
                                                    <strong>Tingkat Kesulitan:</strong> <?php echo htmlspecialchars($gunung['tingkat_kesulitan']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pendakian" class="form-label">Tanggal Pendakian</label>
                                    <input type="date" class="form-control" id="tanggal_pendakian" name="tanggal_pendakian" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="lama_pendakian" class="form-label">Lama Pendakian (hari)</label>
                                    <select class="form-select" id="lama_pendakian" name="lama_pendakian" required>
                                        <option value="1">1 hari</option>
                                        <option value="2">2 hari</option>
                                        <option value="3">3 hari</option>
                                        <option value="4">4 hari</option>
                                        <option value="5">5 hari</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_orang" class="form-label">Jumlah Orang</label>
                                    <input type="number" class="form-control" id="jumlah_orang" name="jumlah_orang" min="1" max="10" value="1" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo $_SESSION['no_hp']; ?>" required>
                                </div>
                            </div>

                            <input type="hidden" id="id_gunung" name="id_gunung" required>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="daftar" class="btn btn-success">Lanjut ke Input Data Pendaki</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectGunung(id) {
            // Remove selected class from all cards
            document.querySelectorAll('.gunung-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Set the hidden input value
            document.getElementById('id_gunung').value = id;
        }
        
        // Set minimum date to today
        document.getElementById('tanggal_pendakian').min = new Date().toISOString().split('T')[0];
        
        // Form validation
        document.getElementById('formPendaftaran').addEventListener('submit', function(event) {
            const idGunung = document.getElementById('id_gunung').value;
            if (!idGunung) {
                alert('Silahkan pilih gunung terlebih dahulu!');
                event.preventDefault();
            }
        });
    </script>
</body>
</html>