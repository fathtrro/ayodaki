
<!DOCTYPE html>
<html>
<head>
    <title>Form Pendaftaran Pendakian</title>
</head>
<body>
    <h1>Form Pendaftaran Pendakian</h1>
    
    <form method="post">
        <label>Tanggal Pendakian:</label>
        <input type="date" name="tanggal_pendakian" required><br><br>
        
        <label>Jumlah Orang:</label>
        <input type="number" name="jumlah_orang" min="1" max="10" required><br><br>
        
        <label>No. HP (Kontak Utama):</label>
        <input type="text" name="no_hp" required><br><br>
        
        <input type="submit" value="Lanjut">
    </form>
</body>
</html>