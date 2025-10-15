<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>CRUD Gunung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-4">
        <h2 class="mb-4 text-center">Data Gunung</h2>

        <!-- Tombol Tambah -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">+ Tambah
            Gunung</button>

        <!-- Tabel Data Gunung -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Gunung</th>
                    <th>Lokasi</th>
                    <th>Ketinggian (mdpl)</th>
                    <th>Tingkat Kesulitan</th>
                    <th>Deskripsi</th>
                    <th>Syarat Ketentuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($conn, "SELECT g.*, l.nama_lokasi FROM gunung g 
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
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $d['id_gunung'] ?>">Edit</button>
                            <a href="aksi.php?hapus=<?= $d['id_gunung'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin hapus data ini?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $d['id_gunung'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="aksi.php" method="POST">
                                    <input type="hidden" name="id_gunung" value="<?= $d['id_gunung'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Data Gunung</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                        <button type="submit" name="edit" class="btn btn-success">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="aksi.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Gunung</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Gunung</label>
                            <input type="text" name="nama_gunung" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Lokasi</label>
                            <select name="id_lokasi" class="form-select" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php
                                $lokasi = mysqli_query($conn, "SELECT * FROM lokasi");
                                while ($l = mysqli_fetch_assoc($lokasi)) {
                                    echo "<option value='{$l['id_lokasi']}'>{$l['nama_lokasi']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Ketinggian (mdpl)</label>
                            <input type="number" name="ketinggian" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tingkat Kesulitan</label>
                            <input type="text" name="tingkat_kesulitan" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Syarat & Ketentuan</label>
                            <textarea name="syarat_ketentuan" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>