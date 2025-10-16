<?php include 'koneksi.php'; ?>
<?php include '../layouts/header.php'; ?>
<?php include '../layouts/sidebar.php'; ?>
<?php include '../layouts/top-bar.php'; ?>

<div class="main-content p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-success"><i class="fa-solid fa-map-location-dot me-2"></i>Data Lokasi</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="fa-solid fa-plus"></i> Tambah Lokasi
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>Nama Lokasi</th>
                            <th>Provinsi</th>
                            <th>Koordinat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($conn, "SELECT * FROM lokasi");
                        while ($d = mysqli_fetch_assoc($query)):
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($d['nama_lokasi']) ?></td>
                                    <td><?= htmlspecialchars($d['provinsi']) ?></td>
                                    <td><?= htmlspecialchars($d['koordinat']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $d['id_lokasi'] ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="aksi.php?hapus=<?= $d['id_lokasi'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin hapus data ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal<?= $d['id_lokasi'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="aksi.php" method="POST">
                                                <input type="hidden" name="id_lokasi" value="<?= $d['id_lokasi'] ?>">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Edit Data Lokasi</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Nama Lokasi</label>
                                                        <input type="text" name="nama_lokasi" class="form-control"
                                                            value="<?= htmlspecialchars($d['nama_lokasi']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Provinsi</label>
                                                        <input type="text" name="provinsi" class="form-control"
                                                            value="<?= htmlspecialchars($d['provinsi']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Koordinat</label>
                                                        <input type="text" name="koordinat" class="form-control"
                                                            value="<?= htmlspecialchars($d['koordinat']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="edit" class="btn btn-success">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="aksi.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah Data Lokasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Koordinat</label>
                        <input type="text" name="koordinat" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>