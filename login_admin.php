<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard_admin.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 900px;
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
        }

        .login-form {
            flex: 1;
            background-color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .signup-info {
            flex: 1;
            background-color: #00b894;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 40px;
        }

        .form-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .form-title h2 {
            font-size: 42px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .form-input {
            position: relative;
            margin-bottom: 25px;
        }

        .form-input input {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input input:focus {
            border-color: #00b894;
            box-shadow: 0 0 0 3px rgba(0, 184, 148, 0.1);
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 14px;
        }

        .forgot-password a {
            color: #00b894;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #00a085;
        }

        .sign-in-btn {
            background-color: #00b894;
            border: none;
            color: white;
            padding: 14px 0;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .sign-in-btn:hover {
            background-color: #00a085;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 184, 148, 0.3);
        }

        .signup-text h2 {
            font-size: 36px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .signup-text p {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .signup-btn {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .signup-btn:hover {
            background-color: white;
            color: #00b894;
        }

        .user-login-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }

        .user-login-link a {
            color: #00b894;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .user-login-link a:hover {
            color: #00a085;
        }

        @media (max-width: 767px) {
            .login-card {
                flex-direction: column;
                height: auto;
            }
            
            .signup-info {
                padding: 30px 40px;
            }
            
            .login-form {
                padding: 30px 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Kolom Login -->
            <div class="login-form">
                <div class="form-title">
                    <h2>Login</h2>
                </div>
                
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger py-2 text-center mb-4"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="form-input">
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                    </div>
                    
                    <div class="form-input">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    </div>
                    
                    <div class="forgot-password">
                        Lupa kata sandi anda?
                        <a href="#">Reset Password</a>
                    </div>
                    
                    <button type="submit" class="sign-in-btn w-100">LOGIN</button>
                </form>
                
                <div class="user-login-link">
                    Login sebagai <a href="login_user.php">User</a>
                </div>
            </div>
            
            <!-- Kolom Informasi Pendaftaran -->
            <div class="signup-info">
                <div class="signup-text">
                    <h2>Halo, Admin!</h2>
                    <p>Login sebagai admin untuk mengelola data dan informasi gunung dan pendaki.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi form Bootstrap
        (() => {
          'use strict'
          const forms = document.querySelectorAll('.needs-validation')
          Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
              if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
              }
              form.classList.add('was-validated')
            }, false)
          })
        })()
    </script>
</body>
</html>