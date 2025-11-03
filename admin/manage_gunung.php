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
    <title>Kelola Gunung - MountHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #22c55e;
            --info: #3b82f6;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: var(--dark);
            font-size: 14px;
        }

        .content {
            padding: 1.5rem;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: 0 3px 5px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        /* Alert */
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.85rem;
            padding: 1rem 1.25rem;
        }

        /* Card Gunung */
        .mountain-card {
            background: white;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .mountain-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .mountain-card-img {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }

        .mountain-card-body {
            padding: 1.25rem;
        }

        .mountain-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .mountain-info {
            font-size: 0.8rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .mountain-info i {
            width: 18px;
            color: var(--primary);
        }

        .difficulty-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .difficulty-mudah {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #059669;
        }

        .difficulty-sedang {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #d97706;
        }

        .difficulty-sulit {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
        }

        .mountain-card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        /* Button Modern */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, var(--primary-dark) 100%);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(34, 197, 94, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #4f46e5 100%);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.75rem;
        }

        /* Modal */
        .modal-content {
            border-radius: 14px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 14px 14px 0 0;
            padding: 1.25rem 1.5rem;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
            padding: 0.6rem 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        @media (max-width: 768px) {
            .content {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .mountain-card-img {
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'navbar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1><i class="fas fa-mountain"></i> Kelola Gunung</h1>
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#tambahGunungModal">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Gunung
                    </button>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php while ($gunung = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="mountain-card">
                                <?php if (!empty($gunung['gambar']) && file_exists($gunung['gambar'])): ?>
                                    <img src="<?php echo $gunung['gambar']; ?>" class="mountain-card-img" alt="<?php echo htmlspecialchars($gunung['nama_gunung']); ?>">
                                <?php else: ?>
                                    <img src="https://www.alshameltechno.com/wp-content/themes/alshameltechno/images/sample.webp" class="mountain-card-img" alt="Tidak ada gambar">
                                <?php endif; ?>
                                
                                <div class="mountain-card-body">
                                    <h5 class="mountain-card-title"><?php echo htmlspecialchars($gunung['nama_gunung']); ?></h5>
                                    
                                    <div class="mountain-info">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($gunung['nama_lokasi']); ?>
                                    </div>
                                    
                                    <div class="mountain-info">
                                        <i class="fas fa-mountain"></i> <?php echo number_format($gunung['ketinggian']); ?> mdpl
                                    </div>
                                    
                                    <span class="difficulty-badge difficulty-<?php echo strtolower($gunung['tingkat_kesulitan']); ?>">
                                        <?php echo htmlspecialchars($gunung['tingkat_kesulitan']); ?>
                                    </span>
                                    
                                    <div class="mountain-card-actions">
                                        <button type="button" class="btn btn-sm btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#editGunungModal<?php echo $gunung['id_gunung']; ?>">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </button>
                                        <a href="manage_gunung.php?hapus=true&id=<?php echo $gunung['id_gunung']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus gunung ini?')">
                                            <i class="fas fa-trash"></i>
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
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
    <script>
        // Animate cards on load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.mountain-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html