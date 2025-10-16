<?php include 'layouts/header.php'; ?>
<?php include 'layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-success mb-4">Selamat Datang,
            <?php echo htmlspecialchars($_SESSION['admin_username']); ?> 👋</h3>
        <p class="text-muted mb-4">Berikut adalah ringkasan data sistem pendakian Anda.</p>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-stats text-center">
                    <div class="card-body">
                        <i class="fa-solid fa-mountain mb-2"></i>
                        <h5 class="fw-semibold">12 Gunung</h5>
                        <p class="text-muted small">Total gunung terdaftar</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stats text-center">
                    <div class="card-body">
                        <i class="fa-solid fa-person-hiking mb-2"></i>
                        <h5 class="fw-semibold">87 Pendaki</h5>
                        <p class="text-muted small">Total pendaki aktif</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stats text-center">
                    <div class="card-body">
                        <i class="fa-solid fa-clipboard-list mb-2"></i>
                        <h5 class="fw-semibold">34 Pendaftaran</h5>
                        <p class="text-muted small">Menunggu konfirmasi</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-5">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-chart-line"></i> Aktivitas Terbaru</h5>
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pendaki</th>
                            <th>Gunung</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Budi Santoso</td>
                            <td>Gunung Merapi</td>
                            <td><span class="badge bg-success">Diterima</span></td>
                            <td>15 Okt 2025</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Siti Nurhaliza</td>
                            <td>Gunung Rinjani</td>
                            <td><span class="badge bg-warning text-dark">Menunggu</span></td>
                            <td>14 Okt 2025</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Agus Pratama</td>
                            <td>Gunung Gede</td>
                            <td><span class="badge bg-danger">Ditolak</span></td>
                            <td>13 Okt 2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
