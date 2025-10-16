<div class="sidebar">
    <h4>Admin Panel</h4>

    <a href="/admin/dashboard.php"
        class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

    <a href="/admin/crud-gunung-modal/index.php"
        class="<?php echo strpos($path, 'crud-gunung-modal') !== false ? 'active' : ''; ?>">
        <i class="fa-solid fa-mountain"></i> Data Gunung
    </a>

    <a href="/admin/crud-lokasi-modal/index.php"
        class="<?php echo strpos($path, 'crud-lokasi-modal') !== false ? 'active' : ''; ?>">
        <i class="fa-solid fa-location-dot"></i> Data Lokasi
    </a>

    <a href="/admin/crud-pendaki-modal/index.php"
        class="<?php echo strpos($path, 'crud-pendaki-modal') !== false ? 'active' : ''; ?>">
        <i class="fa-solid fa-person-hiking"></i> Data Pendaki
    </a>

    <a href="/admin/data_registrasi.php"
        class="<?php echo basename($_SERVER['PHP_SELF']) == 'data_registrasi.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-clipboard-list"></i> Pendaftaran
    </a>

    <div class="mt-auto p-3">
        <hr class="border-secondary">
        <p class="small mb-1">👤 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
        <a href="/admin/logout.php" class="btn btn-outline-light btn-sm w-100">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</div>