<?php
session_start();

$pesan_url = isset($_GET['pesan']) ? $_GET['pesan'] : '';

if ($pesan_url == 'update_berhasil') {
    unset($_SESSION['attempt']);   
    unset($_SESSION['error_msg']);  
}

include 'config_maintenance.php';

$is_locked = (isset($_SESSION['attempt']) && $_SESSION['attempt'] >= 5);

if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    if ($_SESSION['role'] == 'admin') {
        header("location:proses_admin.php");
    } else {
        if ($maintenance_mode) {
            header("location:maintenance.php");
        } else {
            header("location:index.php");
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark"> 

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN - Ais Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_theme.css">
    
    <style>
        * {
            transition: background 0.4s ease, color 0.4s ease, border-color 0.4s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #0f172a; 
            background: var(--body-bg);
            overflow: hidden;
        }

        .login-container {
            background: var(--container-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px; 
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            width: 90%; 
            max-width: 400px;
            box-sizing: border-box; 
            position: relative; 
            z-index: 1;
        }

        .login-container h2 {
            text-align: center;
            margin: 0 0 30px 0;
            font-size: 28px;
            font-weight: 800;
            color: var(--text-color);
            letter-spacing: -0.5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 15px;
            background: var(--input-bg);
            color: var(--text-color);
            outline: none;
        }

        .form-group input:focus {
            border-color: #4361ee;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border: none;
            color: white;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            margin-top: 15px;
            position: relative;
            overflow: hidden;
            background-image: linear-gradient(90deg, #4361ee, #4cc9f0, #4361ee);
            background-size: 200% 100%;
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            text-transform: uppercase;
        }

        .btn-login:hover:not(:disabled) {
            transform: translateY(-3px);
            animation: auroraMove 2s linear infinite;
            box-shadow: 0 12px 25px rgba(67, 97, 238, 0.4);
        }

        @keyframes auroraMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        .btn-login:disabled {
            background: #555 !important;
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Alert Styles */
        .alert {
            padding: 14px;
            border-radius: 12px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .warning-session { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }

        .footer-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .reg-link {
    color: #4ade80 !important; /* Warna Hijau Mint Cerah */
    text-decoration: none;
    font-weight: 700;
}

        .copyright {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
        }

        .password-container { position: relative; width: 100%; }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
            font-size: 18px;
            z-index: 10;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>LOGIN</h2>

        <?php
        if ($is_locked) {
            echo "<div class='alert error'>
                    <strong>Sistem Terkunci!</strong><br>
                    Batas percobaan login tercapai.<br>
                    <a href='lupa_password.php' style='color:#991b1b; font-weight:bold; text-decoration:underline;'>Reset Password</a>
                  </div>";
        } else {
            if (isset($_SESSION['error_msg'])) {
                echo "<div class='alert error'>" . $_SESSION['error_msg'] . "</div>";
                unset($_SESSION['error_msg']);
            }

            if (isset($_GET['pesan'])) {
                $pesan = htmlspecialchars($_GET['pesan']);
                if ($pesan == "sesi_habis") echo "<div class='alert warning-session'>Sesi berakhir. Silakan login kembali.</div>";
                else if ($pesan == "logout") echo "<div class='alert success'>Berhasil logout.</div>";
                else if ($pesan == "belum_login") echo "<div class='alert error'>Silakan login terlebih dahulu.</div>";
                else if ($pesan == "berhasil_regis") echo "<div class='alert success'>Registrasi Berhasil! Silakan Login.</div>";
                else if ($pesan == "update_berhasil") echo "<div class='alert success'>Password berhasil diperbarui! Silakan login.</div>";
            }
        }
        ?>

        <form action="proses_login.php" method="POST">
            <div class="form-group">
                <label>Username / Email</label>
                <input type="text" name="username" required placeholder="Username atau Email"
                autocomplete="off" <?php echo $is_locked ? 'disabled' : ''; ?>>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" required placeholder="Password" <?php echo $is_locked ? 'disabled' : ''; ?>>
                    <i class="fa-solid fa-eye-slash toggle-password" id="togglePassword"></i>
                </div>
            </div>
            <button type="submit" class="btn-login" <?php echo $is_locked ? 'disabled' : ''; ?>>LOGIN</button>
        </form>

        <div class="footer-links">
            Belum punya akun? <a href="register.php" class="reg-link">Registrasi</a>
        </div>
        <div class="copyright">&copy; 2026 Ais Student.</div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            if (!passwordField.disabled) {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        });

        if (window.history.replaceState) {
            const url = new URL(window.location);
            if (url.searchParams.has('pesan')) {
                url.searchParams.delete('pesan');
                window.history.replaceState({}, document.title, url.pathname);
            }
        }
    </script>
</body>
</html>