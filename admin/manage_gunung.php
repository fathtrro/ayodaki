<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

// Proses tambah gunung
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_gunung'])) {
    $nama_gunung = $_POST['nama_gunung'];
    $id_lokasi = $_POST['id_lokasi'];
    $ketinggian = $_POST['ketinggian'];
    $tingkat_kesulitan = $_POST['tingkat_kesulitan'];
    $deskripsi = $_POST['deskripsi'];
    $syarat_ketentuan = $_POST['syarat_ketentuan'];
    
    // Buat folder uploads jika belum ada
    if (!file_exists('../uploads')) {
        mkdir('../uploads', 0777, true);
    }
    if (!file_exists('../uploads/gunung')) {
        mkdir('../uploads/gunung', 0777, true);
    }
    
    // Upload gambar
    $target_dir = "../uploads/gunung/";
    $gambar = null;
    
    if (isset($_FILES["gambar"]) && $_FILES["gambar"]["tmp_name"] != "") {
        $file_extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $new_filename = "gunung_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        $uploadOk = 1;
        
        // Cek apakah file adalah gambar
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File bukan gambar.";
            $uploadOk = 0;
        }
        
        // Cek ukuran file
        if ($_FILES["gambar"]["size"] > 500000) {
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
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
            } else {
                $error = "Maaf, terjadi kesalahan saat mengupload file.";
                $uploadOk = 0;
            }
        }
    }
    
    if ($uploadOk == 1 || !isset($_FILES["gambar"]) || $_FILES["gambar"]["tmp_name"] == "") {
        $query = "INSERT INTO gunung (nama_gunung, id_lokasi, ketinggian, tingkat_kesulitan, deskripsi, syarat_ketentuan, gambar) 
                  VALUES ('$nama_gunung', $id_lokasi, $ketinggian, '$tingkat_kesulitan', '$deskripsi', '$syarat_ketentuan', '$gambar')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Gunung berhasil ditambahkan!";
        } else {
            $error = "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}

// Proses edit gunung
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_gunung'])) {
    $id_gunung = $_POST['id_gunung'];
    $nama_gunung = $_POST['nama_gunung'];
    $id_lokasi = $_POST['id_lokasi'];
    $ketinggian = $_POST['ketinggian'];
    $tingkat_kesulitan = $_POST['tingkat_kesulitan'];
    $deskripsi = $_POST['deskripsi'];
    $syarat_ketentuan = $_POST['syarat_ketentuan'];
    
    // Buat folder uploads jika belum ada
    if (!file_exists('../uploads')) {
        mkdir('../uploads', 0777, true);
    }
    if (!file_exists('../uploads/gunung')) {
        mkdir('../uploads/gunung', 0777, true);
    }
    
    // Upload gambar baru jika ada
    $target_dir = "../uploads/gunung/";
    $uploadOk = 1;
    
    if (isset($_FILES["gambar"]) && $_FILES["gambar"]["tmp_name"] != "") {
        $file_extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $new_filename = "gunung_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Cek apakah file adalah gambar
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File bukan gambar.";
            $uploadOk = 0;
        }
        
        // Cek ukuran file
        if ($_FILES["gambar"]["size"] > 500000) {
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
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
                
                // Hapus gambar lama jika ada
                $old_gambar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM gunung WHERE id_gunung = $id_gunung"))['gambar'];
                if ($old_gambar && file_exists($old_gambar)) {
                    unlink($old_gambar);
                }
                
                $query = "UPDATE gunung SET 
                          nama_gunung = '$nama_gunung', 
                          id_lokasi = $id_lokasi, 
                          ketinggian = $ketinggian, 
                          tingkat_kesulitan = '$tingkat_kesulitan', 
                          deskripsi = '$deskripsi', 
                          syarat_ketentuan = '$syarat_ketentuan', 
                          gambar = '$gambar' 
                          WHERE id_gunung = $id_gunung";
            } else {
                $error = "Maaf, terjadi kesalahan saat mengupload file.";
                $uploadOk = 0;
            }
        }
    } else {
        $query = "UPDATE gunung SET 
                  nama_gunung = '$nama_gunung', 
                  id_lokasi = $id_lokasi, 
                  ketinggian = $ketinggian, 
                  tingkat_kesulitan = '$tingkat_kesulitan', 
                  deskripsi = '$deskripsi', 
                  syarat_ketentuan = '$syarat_ketentuan' 
                  WHERE id_gunung = $id_gunung";
    }
    
    if ($uploadOk == 1) {
        if (mysqli_query($conn, $query)) {
            $success = "Gunung berhasil diperbarui!";
        } else {
            $error = "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}

// Proses hapus gunung
if (isset($_GET['hapus']) && $_GET['hapus'] == 'true' && isset($_GET['id'])) {
    $id_gunung = $_GET['id'];
    
    // Hapus gambar jika ada
    $gambar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM gunung WHERE id_gunung = $id_gunung"))['gambar'];
    if ($gambar && file_exists($gambar)) {
        unlink($gambar);
    }
    
    if (mysqli_query($conn, "DELETE FROM gunung WHERE id_gunung = $id_gunung")) {
        $success = "Gunung berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data gunung
 $query = "SELECT g.*, l.nama_lokasi 
          FROM gunung g 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
          ORDER BY g.nama_gunung";
 $result = mysqli_query($conn, $query);

// Ambil data lokasi untuk form
 $lokasi_result = mysqli_query($conn, "SELECT * FROM lokasi ORDER BY nama_lokasi");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Gunung - Portal Booking Pendakian</title>
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">Admin Panel</h4>
                        <p class="text-white-50">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="manage_gunung.php">
                                <i class="bi bi-mountain me-2"></i> Kelola Gunung
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="approve_registrasi.php">
                                <i class="bi bi-check-circle me-2"></i> Approve Registrasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_pendaki.php">
                                <i class="bi bi-people me-2"></i> Kelola Pendaki
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tracking_pendakian.php">
                                <i class="bi bi-geo-alt me-2"></i> Tracking Pendakian
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
                    <h1 class="h2">Kelola Gunung</h1>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahGunungModal">
                        <i class="bi bi-plus-circle"></i> Tambah Gunung
                    </button>
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

                <div class="row">
                    <?php while ($gunung = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
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
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editGunungModal<?php echo $gunung['id_gunung']; ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <a href="manage_gunung.php?hapus=true&id=<?php echo $gunung['id_gunung']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus gunung ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit Gunung -->
                        <div class="modal fade" id="editGunungModal<?php echo $gunung['id_gunung']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Gunung</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_gunung" value="<?php echo $gunung['id_gunung']; ?>">
                                            <input type="hidden" name="edit_gunung" value="true">
                                            
                                            <div class="mb-3">
                                                <label for="nama_gunung<?php echo $gunung['id_gunung']; ?>" class="form-label">Nama Gunung</label>
                                                <input type="text" class="form-control" id="nama_gunung<?php echo $gunung['id_gunung']; ?>" name="nama_gunung" value="<?php echo htmlspecialchars($gunung['nama_gunung']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="id_lokasi<?php echo $gunung['id_gunung']; ?>" class="form-label">Lokasi</label>
                                                <select class="form-select" id="id_lokasi<?php echo $gunung['id_gunung']; ?>" name="id_lokasi" required>
                                                    <?php
                                                    mysqli_data_seek($lokasi_result, 0);
                                                    while ($lokasi = mysqli_fetch_assoc($lokasi_result)) {
                                                        $selected = ($lokasi['id_lokasi'] == $gunung['id_lokasi']) ? 'selected' : '';
                                                        echo '<option value="' . $lokasi['id_lokasi'] . '" ' . $selected . '>' . htmlspecialchars($lokasi['nama_lokasi']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="ketinggian<?php echo $gunung['id_gunung']; ?>" class="form-label">Ketinggian (mdpl)</label>
                                                <input type="number" class="form-control" id="ketinggian<?php echo $gunung['id_gunung']; ?>" name="ketinggian" value="<?php echo htmlspecialchars($gunung['ketinggian']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="tingkat_kesulitan<?php echo $gunung['id_gunung']; ?>" class="form-label">Tingkat Kesulitan</label>
                                                <select class="form-select" id="tingkat_kesulitan<?php echo $gunung['id_gunung']; ?>" name="tingkat_kesulitan" required>
                                                    <option value="Mudah" <?php echo ($gunung['tingkat_kesulitan'] == 'Mudah') ? 'selected' : ''; ?>>Mudah</option>
                                                    <option value="Sedang" <?php echo ($gunung['tingkat_kesulitan'] == 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                                                    <option value="Sulit" <?php echo ($gunung['tingkat_kesulitan'] == 'Sulit') ? 'selected' : ''; ?>>Sulit</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="deskripsi<?php echo $gunung['id_gunung']; ?>" class="form-label">Deskripsi</label>
                                                <textarea class="form-control" id="deskripsi<?php echo $gunung['id_gunung']; ?>" name="deskripsi" rows="3" required><?php echo htmlspecialchars($gunung['deskripsi']); ?></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="syarat_ketentuan<?php echo $gunung['id_gunung']; ?>" class="form-label">Syarat & Ketentuan</label>
                                                <textarea class="form-control" id="syarat_ketentuan<?php echo $gunung['id_gunung']; ?>" name="syarat_ketentuan" rows="3" required><?php echo htmlspecialchars($gunung['syarat_ketentuan']); ?></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="gambar<?php echo $gunung['id_gunung']; ?>" class="form-label">Gambar</label>
                                                <input type="file" class="form-control" id="gambar<?php echo $gunung['id_gunung']; ?>" name="gambar" accept="image/*">
                                                <div class="form-text">Kosongkan jika tidak ingin mengubah gambar</div>
                                                <?php if (!empty($gunung['gambar']) && file_exists($gunung['gambar'])): ?>
                                                    <div class="mt-2">
                                                        <img src="<?php echo $gunung['gambar']; ?>" alt="Gambar saat ini" class="img-thumbnail" style="max-height: 100px;">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Tambah Gunung -->
    <div class="modal fade" id="tambahGunungModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Gunung Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="tambah_gunung" value="true">
                        
                        <div class="mb-3">
                            <label for="nama_gunung" class="form-label">Nama Gunung</label>
                            <input type="text" class="form-control" id="nama_gunung" name="nama_gunung" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="id_lokasi" class="form-label">Lokasi</label>
                            <select class="form-select" id="id_lokasi" name="id_lokasi" required>
                                <?php
                                mysqli_data_seek($lokasi_result, 0);
                                while ($lokasi = mysqli_fetch_assoc($lokasi_result)) {
                                    echo '<option value="' . $lokasi['id_lokasi'] . '">' . htmlspecialchars($lokasi['nama_lokasi']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ketinggian" class="form-label">Ketinggian (mdpl)</label>
                            <input type="number" class="form-control" id="ketinggian" name="ketinggian" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tingkat_kesulitan" class="form-label">Tingkat Kesulitan</label>
                            <select class="form-select" id="tingkat_kesulitan" name="tingkat_kesulitan" required>
                                <option value="Mudah">Mudah</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Sulit">Sulit</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="syarat_ketentuan" class="form-label">Syarat & Ketentuan</label>
                            <textarea class="form-control" id="syarat_ketentuan" name="syarat_ketentuan" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tambah Gunung</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>