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
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background 0.4s ease, color 0.4s ease, border-color 0.4s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        body {
            background: #0f172a; 
            background: var(--body-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            padding: 20px;
        }

        .auth-container {
            position: relative;
            width: 1000px;
            max-width: 100%;
            height: 600px;
            background: #1e293b;
            background: var(--container-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            display: flex;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            padding: 40px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; 
            z-index: 1;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .login-side {
            left: 0;
            opacity: 1;
            transform: translateX(0);
        }

        .register-side {
            left: 0;
            opacity: 0;
            transform: translateX(-50px);
            pointer-events: none;
            z-index: 0;
        }

        .auth-container.active .login-side {
            opacity: 0;
            transform: translateX(50px);
            pointer-events: none;
            z-index: 0;
        }

        .auth-container.active .register-side {
            opacity: 1;
            transform: translateX(100%);
            pointer-events: all;
            z-index: 2;
        }

        .form-wrapper {
            width: 100%;
            max-width: 360px;
            display: flex;
            flex-direction: column;
            text-align: center; 
        }

        .form-wrapper h2 {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            color: var(--text-color);
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .form-wrapper .subtitle {
            font-size: 14px;
            color: #94a3b8;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left; 
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            color: var(--text-color);
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box i.field-icon {
            position: absolute;
            left: 15px;
            color: #64748b;
            color: var(--text-muted);
            font-size: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 15px;
            background: #0f172a;
            background: var(--input-bg);
            color: #ffffff;
            color: var(--text-color);
            outline: none;
        }

        .form-group input:focus {
            border-color: #4361ee;
        }

        .password-container { position: relative; width: 100%; }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
            color: var(--text-muted);
            font-size: 16px;
            z-index: 10;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border: none;
            color: white;
            border-radius: 12px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            margin-top: 10px;
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

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 10;
        }

        .auth-container.active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay-bg {
            background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), 
                              url('https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1000&auto=format&fit=crop');
            
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            
            color: #ffffff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .auth-container.active .overlay-bg {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .auth-container.active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .auth-container.active .overlay-right {
            transform: translateX(20%);
        }

        .overlay-panel h1 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .overlay-panel p {
            font-size: 14px;
            font-weight: 400;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 300px;
            opacity: 0.9;
        }

        .btn-ghost {
            background: transparent;
            border: 2px solid #ffffff;
            color: #ffffff;
            padding: 12px 35px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.03);
        }

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

        .copyright {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            color: var(--text-muted);
            margin-top: 30px;
            width: 100%;
        }

        .mobile-footer {
            display: none;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .mobile-footer a {
            color: #4361ee;
            text-decoration: none;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .auth-container {
                height: auto;
                min-height: 530px;
                flex-direction: column;
            }
            .form-container {
                position: relative;
                width: 100%;
                height: auto;
                padding: 40px 24px;
                opacity: 1 !important;
                transform: none !important;
            }
            .register-side {
                display: none; 
            }
            .auth-container.active .login-side {
                display: none;
            }
            .auth-container.active .register-side {
                display: flex;
                transform: none !important;
            }
            .overlay-container {
                display: none; 
            }
            .mobile-footer {
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container" id="authContainer">
        
        <div class="form-container login-side">
            <div class="form-wrapper">
                <h2>Selamat Datang</h2>
                <p class="subtitle">Silakan masuk ke akun anda</p>

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
                        <div class="input-box">
                            <i class="fa-solid fa-user field-icon"></i>
                            <input type="text" name="username" required placeholder="Username atau Email"
                            autocomplete="off" <?php echo $is_locked ? 'disabled' : ''; ?>>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-box password-container">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" name="password" id="password" required placeholder="Password" <?php echo $is_locked ? 'disabled' : ''; ?>>
                            <i class="fa-solid fa-eye-slash toggle-password" id="togglePassword"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn-login" <?php echo $is_locked ? 'disabled' : ''; ?>>LOGIN</button>
                </form>

                <div class="mobile-footer">
                    Belum punya akun? <a href="#" id="toRegisterMobile">Registrasi</a>
                </div>

                <div class="copyright">&copy; 2026 Ais Student.</div>
            </div>
        </div>

        <div class="form-container register-side">
            <div class="form-wrapper">
                <h2>Buat Akun</h2>
                <p class="subtitle">Silakan daftarkan identitas diri Anda</p>
                
                <iframe src="register.php" style="width:100%; height:340px; border:none; background:transparent;"></iframe>
                
                <div class="mobile-footer">
                    Sudah punya akun? <a href="#" id="toLoginMobile">LOGIN</a>
                </div>
                
                <div class="copyright">&copy; 2026 Ais Student.</div>
            </div>
        </div>

        <div class="overlay-container">
            <div class="overlay-bg">
                <div class="overlay-panel overlay-left">
                    <h1>Sudah Punya Akun?</h1>
                    <p>Silakan masuk kembali menggunakan akun Anda yang sudah terdaftar.</p>
                    <button class="btn-ghost" id="toLogin">LOGIN</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Halo, Teman!</h1>
                    <p>Isi data diri Anda dan mulailah perjalanan akademik bersama kami.</p>
                    <button class="btn-ghost" id="toRegister">REGISTRASI</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        const authContainer = document.getElementById('authContainer');
        const toRegisterBtn = document.getElementById('toRegister');
        const toLoginBtn = document.getElementById('toLogin');
        
        const toRegisterMobile = document.getElementById('toRegisterMobile');
        const toLoginMobile = document.getElementById('toLoginMobile');

        toRegisterBtn.addEventListener('click', () => {
            authContainer.classList.add('active');
        });

        toLoginBtn.addEventListener('click', () => {
            authContainer.classList.remove('active');
        });

        toRegisterMobile.addEventListener('click', (e) => {
            e.preventDefault();
            authContainer.classList.add('active');
        });

        toLoginMobile.addEventListener('click', (e) => {
            e.preventDefault();
            authContainer.classList.remove('active');
        });

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