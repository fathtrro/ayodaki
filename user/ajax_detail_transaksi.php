<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    echo '<div class="alert alert-danger">Unauthorized</div>';
    exit;
}

include '../config.php';

$id_registrasi = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil detail registrasi
$query = "SELECT r.*, g.nama_gunung, g.ketinggian, l.nama_lokasi, l.provinsi,
          p.jumlah_bayar, p.metode_pembayaran, p.bukti_pembayaran, p.status_pembayaran
          FROM registrasi r 
          JOIN gunung g ON r.id_gunung = g.id_gunung 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi
          LEFT JOIN pembayaran p ON r.id_registrasi = p.id_registrasi
          WHERE r.id_registrasi = $id_registrasi AND r.id_user = $user_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo '<div class="alert alert-danger">Data tidak ditemukan</div>';
    exit;
}

// Ambil data pendaki
$pendaki_query = "SELECT * FROM pendaki WHERE id_registrasi = $id_registrasi";
$pendaki_result = mysqli_query($conn, $pendaki_query);
?>

<style>
    .detail-section {
        margin-bottom: 1.5rem;
    }

    .detail-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #10b981;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .info-table {
        width: 100%;
    }

    .info-table tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .info-table td {
        padding: 0.75rem 0;
    }

    .info-table td:first-child {
        font-weight: 600;
        color: #64748b;
        width: 180px;
    }

    .bukti-image {
        max-width: 100%;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        margin-top: 1rem;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    .pendaki-item {
        background: #f1f5f9;
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
</style>

<div class="detail-section">
    <div class="detail-section-title">
        <i class="fas fa-mountain"></i> Informasi Pendakian
    </div>
    <table class="info-table">
        <tr>
            <td>ID Registrasi</td>
            <td>: <strong>#<?php echo str_pad($data['id_registrasi'], 6, '0', STR_PAD_LEFT); ?></strong></td>
        </tr>
        <tr>
            <td>Gunung</td>
            <td>: <?php echo htmlspecialchars($data['nama_gunung']); ?></td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td>: <?php echo htmlspecialchars($data['nama_lokasi'] . ', ' . $data['provinsi']); ?></td>
        </tr>
        <tr>
            <td>Ketinggian</td>
            <td>: <?php echo number_format($data['ketinggian']); ?> mdpl</td>
        </tr>
        <tr>
            <td>Tanggal Pendakian</td>
            <td>: <?php echo date('d F Y', strtotime($data['tanggal_pendakian'])); ?></td>
        </tr>
        <tr>
            <td>Tanggal Selesai</td>
            <td>: <?php echo date('d F Y', strtotime($data['tanggal_selesai'])); ?></td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>: <?php echo htmlspecialchars($data['no_hp']); ?></td>
        </tr>
    </table>
</div>

<div class="detail-section">
    <div class="detail-section-title">
        <i class="fas fa-users"></i> Data Pendaki
    </div>
    <?php
    $no = 1;
    while ($pendaki = mysqli_fetch_assoc($pendaki_result)):
        ?>
        <div class="pendaki-item">
            <strong>Pendaki <?php echo $no++; ?>:</strong> <?php echo htmlspecialchars($pendaki['nama']); ?><br>
            <small class="text-muted">
                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($pendaki['email']); ?>
            </small>
        </div>
    <?php endwhile; ?>
</div>

<div class="detail-section">
    <div class="detail-section-title">
        <i class="fas fa-credit-card"></i> Informasi Pembayaran
    </div>
    <?php if (!empty($data['bukti_pembayaran'])): ?>
        <table class="info-table">
            <tr>
                <td>Total Pembayaran</td>
                <td>: <strong style="color: #10b981; font-size: 1.2rem;">Rp
                        <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></strong></td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>: <?php echo htmlspecialchars($data['metode_pembayaran']); ?></td>
            </tr>
            <tr>
                <td>Status Pembayaran</td>
                <td>:
                    <?php
                    $status = $data['status_pembayaran'] ?? 'pending';
                    if ($status == 'approved') {
                        echo '<span class="status-badge" style="background: #d1fae5; color: #065f46;">✓ Disetujui</span>';
                    } elseif ($status == 'rejected') {
                        echo '<span class="status-badge" style="background: #fee2e2; color: #991b1b;">✗ Ditolak</span>';
                    } else {
                        echo '<span class="status-badge" style="background: #fef3c7; color: #92400e;">⏳ Menunggu Verifikasi</span>';
                    }
                    ?>
                </td>
            </tr>
        </table>

        <div class="mt-3">
            <strong>Bukti Pembayaran:</strong>
            <img src="../<?php echo htmlspecialchars($data['bukti_pembayaran']); ?>" class="bukti-image"
                alt="Bukti Pembayaran">
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Belum melakukan pembayaran
        </div>
    <?php endif; ?>
</div>