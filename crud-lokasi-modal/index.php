<?php include "koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Lokasi Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">📍 Data Lokasi Pendakian</h2>
        <div class="text-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah
                Lokasi</button>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama Lokasi</th>
                    <th>Provinsi</th>
                    <th>Koordinat</th>
                    <th width="150px">Aksi</th>
                </tr>
            </thead>
            <tbody id="dataLokasi"></tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <form id="formTambah" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Lokasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nama_lokasi" class="form-control mb-2" placeholder="Nama Lokasi" required>
                    <input type="text" name="provinsi" class="form-control mb-2" placeholder="Provinsi" required>
                    <input type="text" name="koordinat" class="form-control mb-2" placeholder="Koordinat" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <form id="formEdit" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Lokasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_lokasi">
                    <input type="text" name="nama_lokasi" class="form-control mb-2" placeholder="Nama Lokasi" required>
                    <input type="text" name="provinsi" class="form-control mb-2" placeholder="Provinsi" required>
                    <input type="text" name="koordinat" class="form-control mb-2" placeholder="Koordinat" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            tampilData();

            // TAMPIL DATA
            function tampilData() {
                $.get("aksi.php", { aksi: "tampil" }, function (data) {
                    $("#dataLokasi").html(data);
                });
            }

            // TAMBAH DATA
            $("#formTambah").on("submit", function (e) {
                e.preventDefault();
                $.post("aksi.php?aksi=tambah", $(this).serialize(), function (res) {
                    $("#modalTambah").modal("hide");
                    $("#formTambah")[0].reset();
                    tampilData();
                });
            });

            // HAPUS DATA
            $(document).on("click", ".btnHapus", function () {
                let id = $(this).data("id");
                if (confirm("Yakin ingin menghapus data ini?")) {
                    $.post("aksi.php?aksi=hapus", { id: id }, function () {
                        tampilData();
                    });
                }
            });

            // AMBIL DATA EDIT
            $(document).on("click", ".btnEdit", function () {
                let id = $(this).data("id");
                $.getJSON("aksi.php", { aksi: "ambil", id: id }, function (data) {
                    $("#formEdit [name='id_lokasi']").val(data.id_lokasi);
                    $("#formEdit [name='nama_lokasi']").val(data.nama_lokasi);
                    $("#formEdit [name='provinsi']").val(data.provinsi);
                    $("#formEdit [name='koordinat']").val(data.koordinat);
                    $("#modalEdit").modal("show");
                });
            });

            // UPDATE DATA
            $("#formEdit").on("submit", function (e) {
                e.preventDefault();
                $.post("aksi.php?aksi=edit", $(this).serialize(), function () {
                    $("#modalEdit").modal("hide");
                    tampilData();
                });
            });
        });
    </script>
</body>

</html>