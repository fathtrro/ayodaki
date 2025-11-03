<style>
    /* Navbar Modern */
        .navbar {
            background: var(--white) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1.2rem 0;
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        .nav-link {
            color: var(--gray) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            background: var(--light-gray);
        }

        .nav-link i {
            margin-right: 0.4rem;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--light-gray);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            color: var(--dark);
        }

        .profile-btn:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            min-width: 220px;
            padding: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .dropdown-menu-custom.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header-custom {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
        }

        .dropdown-user-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .dropdown-user-role {
            font-size: 0.8rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .dropdown-item-custom:hover {
            background: var(--light-gray);
            color: var(--primary);
        }

        .dropdown-item-custom i {
            width: 20px;
            text-align: center;
        }

        .dropdown-divider-custom {
            height: 1px;
            background: #e2e8f0;
            margin: 0.5rem 0;
        }

        .dropdown-item-custom.logout {
            color: #ef4444;
        }

        .dropdown-item-custom.logout:hover {
            background: #fee2e2;
            color: #dc2626;
        }
</style>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-mountain"></i>AyoDaki
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/dashboard.php">
                                <i class="fas fa-th-large"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Profile Dropdown for User -->
                        <li class="nav-item profile-dropdown">
                            <button class="profile-btn" id="profileBtn">
                                <div class="profile-avatar">
                                    <?php
                                    // Get first letter of username
                                    $username = $_SESSION['username'] ?? 'U';
                                    echo strtoupper(substr($username, 0, 1));
                                    ?>
                                </div>
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                            </button>
                            <div class="dropdown-menu-custom" id="profileDropdown">
                                <div class="dropdown-header-custom">
                                    <div class="dropdown-user-name"><?php echo htmlspecialchars($_SESSION['username']); ?>
                                    </div>
                                    <div class="dropdown-user-role">User Account</div>
                                </div>

                                <a href="user/riwayat.php" class="dropdown-item-custom">
                                    <i class="fas fa-receipt"></i>
                                    Cek Pembayaran
                                </a>
                                <div class="dropdown-divider-custom"></div>
                                <a href="logout.php" class="dropdown-item-custom logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="fas fa-user-plus"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
       // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });

            // Prevent dropdown from closing when clicking inside
            profileDropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

</script>