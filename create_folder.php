<?php
// Fungsi untuk membuat folder jika belum ada
function createFolder($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        echo "Folder $path berhasil dibuat.<br>";
    } else {
        echo "Folder $path sudah ada.<br>";
    }
}

// Buat folder yang diperlukan
createFolder('uploads');
createFolder('uploads/ktp');
createFolder('uploads/gunung');
createFolder('uploads/bukti_pembayaran');

echo "<p>Semua folder yang diperlukan telah dibuat.</p>";
echo "<p><a href='index.php'>Kembali ke halaman utama</a></p>";
?>