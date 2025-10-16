<?php
include "koneksi.php";

$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'tampil') {
    $q = mysqli_query($conn, "SELECT * FROM lokasi ORDER BY id_lokasi DESC");
    $no = 1;
    while ($d = mysqli_fetch_assoc($q)) {
        echo "<tr>
                <td>{$d['id_lokasi']}</td>
                <td>{$d['nama_lokasi']}</td>
                <td>{$d['provinsi']}</td>
                <td>{$d['koordinat']}</td>
                <td>
                    <button class='btn btn-sm btn-warning btnEdit' data-id='{$d['id_lokasi']}'>Edit</button>
                    <button class='btn btn-sm btn-danger btnHapus' data-id='{$d['id_lokasi']}'>Hapus</button>
                </td>
              </tr>";
    }
} elseif ($aksi == 'tambah') {
    $nama = $_POST['nama_lokasi'];
    $provinsi = $_POST['provinsi'];
    $koordinat = $_POST['koordinat'];
    mysqli_query($conn, "INSERT INTO lokasi (nama_lokasi, provinsi, koordinat) VALUES ('$nama','$provinsi','$koordinat')");
} elseif ($aksi == 'ambil') {
    $id = $_GET['id'];
    $q = mysqli_query($conn, "SELECT * FROM lokasi WHERE id_lokasi='$id'");
    echo json_encode(mysqli_fetch_assoc($q));
} elseif ($aksi == 'edit') {
    $id = $_POST['id_lokasi'];
    $nama = $_POST['nama_lokasi'];
    $provinsi = $_POST['provinsi'];
    $koordinat = $_POST['koordinat'];
    mysqli_query($conn, "UPDATE lokasi SET nama_lokasi='$nama', provinsi='$provinsi', koordinat='$koordinat' WHERE id_lokasi='$id'");
} elseif ($aksi == 'hapus') {
    $id = $_POST['id'];
    mysqli_query($conn, "DELETE FROM lokasi WHERE id_lokasi='$id'");
}
?>