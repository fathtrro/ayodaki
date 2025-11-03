# Portal Booking Pendakian - AyoDaki

## Overview Project

**AyoDaki** adalah platform booking pendakian gunung berbasis web yang dirancang untuk memudahkan pendaki dalam melakukan pendaftaran dan pembayaran secara online. Platform ini hadir sebagai solusi digitalisasi sistem pendaftaran pendakian yang sebelumnya masih dilakukan secara manual.

### Background
Proses pendaftaran pendakian gunung yang konvensional seringkali memakan waktu dan kurang efisien, baik bagi pendaki maupun pengelola. Pendaki harus datang langsung ke lokasi untuk mendaftar, sedangkan pengelola kesulitan dalam mengelola data dan verifikasi pembayaran.

### Tujuan Project
- Memudahkan pendaki dalam melakukan registrasi dan pembayaran pendakian secara online
- Menyediakan sistem manajemen data pendaki yang terintegrasi untuk admin
- Meningkatkan efisiensi proses verifikasi pembayaran dan penerbitan SIMAKSI
- Memberikan pengalaman booking yang modern dan user-friendly

---

## Tech Stack & App Features

### Tech Stack
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5.3.3
- **Backend**: PHP (Native)
- **Database**: MySQL
- **Icons**: Font Awesome 6.4.0
- **Fonts**: Google Fonts (Inter)

### App Features

#### 🎯 User Features
- **Registrasi & Login**: Sistem autentikasi dengan role-based access (User/Admin)
- **Katalog Gunung**: Pencarian dan filter gunung berdasarkan tingkat kesulitan, nama, dan ketinggian
- **Detail Gunung**: Informasi lengkap gunung termasuk lokasi, ketinggian, deskripsi, dan syarat pendakian
- **Form Pendaftaran Multi-Step**: 
  - Step 1: Input jadwal pendakian dan jumlah pendaki
  - Step 2: Input data lengkap setiap pendaki
  - Step 3: Pembayaran dan upload bukti transfer
- **Riwayat Pendakian**: Cek status pembayaran dan download SIMAKSI
- **Profile Management**: Kelola data profil pengguna

#### 👨‍💼 Admin Features
- **Dashboard**: Overview statistik pendakian, pembayaran, dan pendaki
- **Manajemen Gunung**: CRUD data gunung dan lokasi
- **Verifikasi Pembayaran**: Approve/reject pembayaran dengan preview bukti transfer
- **Manajemen Pendaki**: Lihat dan kelola data pendaki terdaftar
- **Laporan**: Export data pendakian dan pembayaran

#### ✨ Additional Features
- **Responsive Design**: Tampilan optimal di desktop, tablet, dan mobile
- **Modern UI/UX**: Interface clean dengan gradient dan smooth animations
- **Image Upload**: Upload foto KTP dan bukti pembayaran
- **Real-time Search**: Filter dan sorting data secara real-time
- **Pagination**: Navigasi data yang efisien
- **Alert System**: Notifikasi sukses/error yang informatif

---

## How to Use

### Prerequisites
- XAMPP/WAMP/LAMP (PHP 7.4+ & MySQL)
- Web Browser (Chrome, Firefox, Safari, Edge)
- Text Editor (VS Code, Sublime, etc.)

### Installation Steps

1. **Clone atau Download Project**
   ```bash
   git clone [repository-url]
   ```
   Atau download ZIP dan extract ke folder htdocs (XAMPP) atau www (WAMP)

2. **Setup Database**
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Buat database baru dengan nama `db_pendakian`
   - Uncomment bagian code di `config.php` untuk membuat tabel otomatis
   - Jalankan file `config.php` sekali untuk inisialisasi database

3. **Konfigurasi Folder Upload**
   - Jalankan `create_folder.php` untuk membuat folder upload otomatis
   - Atau buat folder secara manual:
     ```
     /uploads
       /ktp
       /gunung
       /bukti_pembayaran
     ```

4. **Akses Aplikasi**
   - Buka browser dan akses: `http://localhost/ayodaki`
   - Login sebagai admin:
     - Username: `admin`
     - Password: `admin123`

5. **Testing User Flow**
   - Registrasi akun baru melalui halaman register
   - Login dengan akun user
   - Pilih gunung dan lakukan pendaftaran
   - Upload bukti pembayaran
   - Login sebagai admin untuk verifikasi pembayaran
   - Kembali ke akun user untuk download SIMAKSI

### Default Admin Account
```
Username: admin
Password: admin123
Email: admin@pendakian.com
```

---

## Member List

### Development Team

| Nama | Role | Responsibilities |
|------|------|------------------|
| **[Nama Anggota 1]** | Project Manager | Koordinasi tim, manajemen project timeline, dokumentasi |
| **[Nama Anggota 2]** | Full Stack Developer | Develop fitur user (registrasi, booking, pembayaran) |
| **[Nama Anggota 3]** | Full Stack Developer | Develop fitur admin (dashboard, verifikasi, manajemen) |
| **[Nama Anggota 4]** | UI/UX Designer | Design interface, create mockup, implement styling |
| **[Nama Anggota 5]** | Database Engineer | Database design, optimization, data management |
| **[Nama Anggota 6]** | QA Tester | Testing, bug fixing, user acceptance testing |

---

## Project Structure

```
ayodaki/
├── admin/                  # Admin panel files
│   ├── dashboard.php
│   ├── kelola_gunung.php
│   ├── verifikasi_pembayaran.php
│   └── ...
├── user/                   # User dashboard files
│   ├── dashboard.php
│   ├── riwayat.php
│   └── ...
├── uploads/               # Upload directory
│   ├── ktp/
│   ├── gunung/
│   └── bukti_pembayaran/
├── config.php            # Database configuration
├── index.php             # Homepage
├── login.php             # Login page
├── register.php          # Registration page
├── detail_gunung.php     # Mountain detail page
├── form_pendaftaran.php  # Booking form step 1
├── form_pendaki.php      # Booking form step 2
├── pembayaran.php        # Payment page
└── README.md             # This file
```

---

## Database Schema

### Tables Overview
- **users**: Menyimpan data user (admin & pendaki)
- **lokasi**: Data lokasi Taman Nasional
- **gunung**: Data gunung yang tersedia
- **registrasi**: Data pendaftaran pendakian
- **pendaki**: Detail data pendaki per registrasi
- **pembayaran**: Data transaksi pembayaran
- **konfirmasi_kedatangan**: Verifikasi kedatangan pendaki
- **denda**: Data denda (jika ada)

---

## Features Roadmap

### ✅ Implemented
- [x] User authentication & authorization
- [x] Mountain catalog with search & filter
- [x] Multi-step booking process
- [x] Payment verification system
- [x] Admin dashboard & management
- [x] Responsive design

### 🚀 Future Enhancements
- [ ] Email notification system
- [ ] WhatsApp integration for notifications
- [ ] Online payment gateway (Midtrans/Xendit)
- [ ] Weather forecast integration
- [ ] Hiking trail map visualization
- [ ] Rating & review system
- [ ] Mobile application (Android/iOS)
- [ ] Export data to Excel/PDF

---

## Security Notes

### For Development
- Default admin credentials provided for testing
- Database credentials stored in plain text in `config.php`
- File upload validation implemented

### For Production
- ⚠️ **IMPORTANT**: Change default admin password immediately
- Use environment variables for sensitive data
- Implement HTTPS/SSL certificate
- Add rate limiting for login attempts
- Sanitize all user inputs
- Implement CSRF protection
- Regular security audits
- Database backup automation

---

## Troubleshooting

### Common Issues

**Problem**: Cannot upload images
- **Solution**: Check folder permissions (chmod 755 or 777 for uploads folder)

**Problem**: Database connection error
- **Solution**: Verify MySQL service is running and credentials in config.php are correct

**Problem**: Blank page after login
- **Solution**: Check PHP error logs, ensure session is started

**Problem**: Images not displaying
- **Solution**: Check file path and ensure files exist in uploads folder

---

## Contributing

Kontribusi sangat terbuka! Jika ingin berkontribusi:

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## License

Project ini dibuat untuk keperluan edukasi dan pembelajaran.

---

## Contact & Support

Jika mengalami kendala dalam instalasi atau penggunaan, silakan:
- Buat issue di repository project
- Hubungi tim development
- Email: support@ayodaki.com

---

## Acknowledgments

- Bootstrap team untuk framework CSS
- Font Awesome untuk icon library
- Google Fonts untuk typography
- Seluruh tim yang telah berkontribusi dalam project ini

---

**© 2024 AyoDaki - Platform Booking Pendakian Terpercaya**

*"Mendaki Gunung, Meraih Impian"*