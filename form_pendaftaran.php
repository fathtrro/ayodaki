<?php
session_start();
include 'config.php';
$id_gunung = $_GET['id_gunung'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['id_gunung'] = $id_gunung;
    $_SESSION['tanggal_pendakian'] = $_POST['tanggal_pendakian'];
    $_SESSION['jumlah_orang'] = $_POST['jumlah_orang'];
    $_SESSION['no_hp'] = $_POST['no_hp'];
    
    header("Location: form_pendaftaran_detail.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Portal Booking Pendakian</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">Form Pendaftaran Pendakian</h4>
                </div>
                <div class="card-body">
                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="tanggal_pendakian" class="form-label">Tanggal Pendakian</label>
                            <input type="date" class="form-control" id="tanggal_pendakian" name="tanggal_pendakian" required>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah_orang" class="form-label">Jumlah Orang</label>
                            <input type="number" class="form-control" id="jumlah_orang" placeholder="Max 10 orang" name="jumlah_orang" min="1" max="10" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP (Kontak Utama)</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="0812xxxxxxx" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Lanjut</button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="index.php" class="text-decoration-none text-success">&larr; Kembali ke Daftar Gunung</a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center text-muted py-3 mt-5 border-top">
    <small>&copy; <?php echo date('Y'); ?> Portal Booking Pendakian</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validasi form Bootstrap
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

</body>
</html>
