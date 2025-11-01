<?php
session_start();
include 'config.php';

// Jika sudah login, redirect ke halaman sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit;
}

// Fungsi untuk membuat folder jika belum ada
function createFolder($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        return true;
    }
    return false;
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $no_hp = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    
    // Validasi input
    $errors = [];
    
    // Validasi username
    if (empty($username)) {
        $errors[] = "Username harus diisi.";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username minimal 4 karakter.";
    } else {
        $check_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_username) > 0) {
            $errors[] = "Username sudah digunakan. Silahkan pilih username lain.";
        }
    }
    
    // Validasi password
    if (empty($password)) {
        $errors[] = "Password harus diisi.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter.";
    } elseif (!preg_match("/^(?=.*[a-zA-Z])(?=.*[0-9])/", $password)) {
        $errors[] = "Password harus mengandung kombinasi huruf dan angka.";
    }
    
    // Validasi nama lengkap
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap harus diisi.";
    }
    
    // Validasi email
    if (empty($email)) {
        $errors[] = "Email harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    } else {
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $errors[] = "Email sudah terdaftar. Silahkan gunakan email lain.";
        }
    }
    
    // Validasi no HP
    if (empty($no_hp)) {
        $errors[] = "No. HP harus diisi.";
    } elseif (!preg_match("/^[0-9]{10,13}$/", $no_hp)) {
        $errors[] = "Format no. HP tidak valid. Gunakan 10-13 digit angka.";
    }
    
    // Validasi file upload
    if (empty($_FILES["foto_ktp"]["name"])) {
        $errors[] = "Foto KTP harus diupload.";
    } else {
        // Buat folder uploads jika belum ada
        createFolder('uploads');
        createFolder('uploads/ktp');
        
        // Upload foto KTP
        $target_dir = "uploads/ktp/";
        $file_extension = pathinfo($_FILES["foto_ktp"]["name"], PATHINFO_EXTENSION);
        $new_filename = "ktp_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        $uploadOk = 1;
        
        // Cek apakah file adalah gambar
        $check = getimagesize($_FILES["foto_ktp"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $errors[] = "File bukan gambar.";
            $uploadOk = 0;
        }
        
        // Cek ukuran file
        if ($_FILES["foto_ktp"]["size"] > 500000) {
            $errors[] = "Ukuran file terlalu besar. Maksimal 500KB.";
            $uploadOk = 0;
        }
        
        // Cek format file
        $allowed_types = array('jpg', 'jpeg', 'png');
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            $errors[] = "Hanya file JPG, JPEG, PNG yang diperbolehkan.";
            $uploadOk = 0;
        }
    }
    
    // Jika tidak ada error, lanjutkan proses
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["foto_ktp"]["tmp_name"], $target_file)) {
                // Simpan data ke database
                $query = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, foto_ktp) 
                          VALUES ('$username', '$hashed_password', '$nama_lengkap', '$email', '$no_hp', '$target_file')";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['register_success'] = true;
                    header("Location: login.php");
                    exit;
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            } else {
                $error = "Maaf, terjadi kesalahan saat mengupload file.";
            }
        }
    } else {
        // Tampilkan semua error
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .register-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #198754;
            color: white;
            text-align: center;
            border-radius: 10px 10px 0 0 !important;
        }
        .preview-container {
            margin-top: 10px;
            text-align: center;
        }
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }
        .form-text {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="card">
                <div class="card-header py-3">
                    <h4 class="mb-0">Registrasi Akun</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data" id="registerForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="form-text">Username minimal 4 karakter dan harus unik.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Minimal 8 karakter dengan kombinasi huruf dan angka.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="form-text">Email harus valid dan belum terdaftar sebelumnya.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                            <div class="form-text">Contoh: 081234567890 (10-13 digit angka)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto_ktp" class="form-label">Foto KTP</label>
                            <input type="file" class="form-control" id="foto_ktp" name="foto_ktp" accept="image/*" required onchange="previewImage(event)">
                            <div class="form-text">Format: JPG, JPEG, PNG. Maksimal: 500KB</div>
                            <div class="preview-container">
                                <img id="preview" class="preview-image" style="display: none;">
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Daftar</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Sudah punya akun? <a href="login.php">Login</a></p>
                    </div>
                </div>
            </div>
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
        
        // Validasi form di sisi klien
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var email = document.getElementById('email').value;
            var no_hp = document.getElementById('no_hp').value;
            var foto_ktp = document.getElementById('foto_ktp').value;
            
            // Validasi username
            if (username.length < 4) {
                alert('Username minimal 4 karakter.');
                event.preventDefault();
                return;
            }
            
            // Validasi password
            if (password.length < 8 || !/(?=.*[a-zA-Z])(?=.*[0-9])/.test(password)) {
                alert('Password minimal 8 karakter dengan kombinasi huruf dan angka.');
                event.preventDefault();
                return;
            }
            
            // Validasi email
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Format email tidak valid.');
                event.preventDefault();
                return;
            }
            
            // Validasi no HP
            var phoneRegex = /^[0-9]{10,13}$/;
            if (!phoneRegex.test(no_hp)) {
                alert('Format no. HP tidak valid. Gunakan 10-13 digit angka.');
                event.preventDefault();
                return;
            }
            
            // Validasi file KTP
            if (foto_ktp === '') {
                alert('Foto KTP harus diupload.');
                event.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>