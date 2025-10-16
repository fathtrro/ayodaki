<?php include 'config.php'; ?>
<?php include '../layouts/header.php'; ?>
<?php include '../layouts/sidebar.php'; ?>
<?php include '../layouts/top-bar.php'; ?>

<div class="main-content p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-success"><i class="fa-solid fa-mountain me-2"></i>Data Gunung</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="fa-solid fa-plus"></i> Tambah Gunung
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>Nama Gunung</th>
                            <th>Lokasi</th>
                            <th>Ketinggian (mdpl)</th>
                            <th>Tingkat Kesulitan</th>
                            <th>Deskripsi</th>
                            <th>Syarat Ketentuan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($conn, "SELECT g.*, l.nama_lokasi 
                                                     FROM gunung g 
                                                     LEFT JOIN lokasi l ON g.id_lokasi = l.id_lokasi");
                        while ($d = mysqli_fetch_assoc($query)):
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($d['nama_gunung']) ?></td>
                                <td><?= htmlspecialchars($d['nama_lokasi']) ?></td>
                                <td><?= $d['ketinggian'] ?></td>
                                <td><?= htmlspecialchars($d['tingkat_kesulitan']) ?></td>
                                <td><?= htmlspecialchars($d['deskripsi']) ?></td>
                                <td><?= htmlspecialchars($d['syarat_ketentuan']) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $d['id_gunung'] ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="aksi.php?hapus=<?= $d['id_gunung'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin hapus data ini?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal<?= $d['id_gunung'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="aksi.php" method="POST">
                                            <input type="hidden" name="id_gunung" value="<?= $d['id_gunung'] ?>">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Edit Data Gunung</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Nama Gunung</label>
                                                    <input type="text" name="nama_gunung" class="form-control"
                                                        value="<?= htmlspecialchars($d['nama_gunung']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Lokasi</label>
                                                    <select name="id_lokasi" class="form-select" required>
                                                        <option value="">-- Pilih Lokasi --</option>
                                                        <?php
                                                        $lokasi = mysqli_query($conn, "SELECT * FROM lokasi");
                                                        while ($l = mysqli_fetch_assoc($lokasi)) {
                                                            $selected = ($l['id_lokasi'] == $d['id_lokasi']) ? 'selected' : '';
                                                            echo "<option value='{$l['id_lokasi']}' $selected>{$l['nama_lokasi']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Ketinggian (mdpl)</label>
                                                    <input type="number" name="ketinggian" class="form-control"
                                                        value="<?= $d['ketinggian'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Tingkat Kesulitan</label>
                                                    <input type="text" name="tingkat_kesulitan" class="form-control"
                                                        value="<?= htmlspecialchars($d['tingkat_kesulitan']) ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Deskripsi</label>
                                                    <textarea name="deskripsi"
                                                        class="form-control"><?= htmlspecialchars($d['deskripsi']) ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Syarat & Ketentuan</label>
                                                    <textarea name="syarat_ketentuan"
                                                        class="form-control"><?= htmlspecialchars($d['syarat_ketentuan']) ?></textarea>
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
