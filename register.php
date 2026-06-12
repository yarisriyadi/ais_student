<!DOCTYPE html>
<html lang="id" data-theme="dark"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRASI - UJIAN ONLINE</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style_theme.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        body { 
            background: transparent; 
            color: #ffffff;
            overflow-x: hidden;
            padding: 2px 5px;
        }

        .reg-container { 
            width: 100%;
            background: transparent;
            box-shadow: none;
            border: none;
            padding: 0;
            margin: 0;
        }

        .reg-container h2, 
        .reg-container .login-link, 
        .reg-container .copyright { 
            display: none; 
        }

        .form-group {
            margin-bottom: 18px; 
            text-align: left;
            position: relative;
        }

        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-size: 13px; 
            font-weight: 600; 
            color: var(--text-color, #ffffff);
            letter-spacing: 0.3px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-box i.field-icon {
            position: absolute;
            left: 16px;
            color: #64748b;
            font-size: 16px;
            z-index: 5;
            pointer-events: none; 
        }

        .form-group input { 
            width: 100%; 
            padding: 13px 16px 13px 46px; 
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1)); 
            border-radius: 12px; 
            box-sizing: border-box; 
            font-size: 14px; 
            background: var(--input-bg, #1e293b); 
            color: #ffffff; 
            outline: none;
        }

        .form-group input:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }

        .form-group input:focus + i.field-icon {
            color: #4361ee;
        }

        .password-container { 
            position: relative; 
            display: flex;
            align-items: center;
            width: 100%; 
        }

        .toggle-password { 
            position: absolute; 
            right: 16px; 
            cursor: pointer; 
            color: #64748b; 
            font-size: 16px; 
            z-index: 10; 
            padding: 4px;
        }

        .toggle-password:hover {
            color: #ffffff;
        }

        .msg-error { 
            font-size: 11.5px; 
            margin-top: 6px; 
            font-weight: 600; 
            color: #ff4d4d; 
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .msg-success { 
            font-size: 11.5px; 
            margin-top: 6px; 
            font-weight: 600; 
            color: #2ecc71; 
            display: flex;
            align-items: center;
            gap: 4px;
        }

.btn-reg { 
    width: 100%; 
    padding: 14px; 
    background-image: linear-gradient(90deg, #4cc9f0, #4361ee, #4cc9f0);
    background-size: 200% 100%;
    border: none; 
    color: white; 
    border-radius: 12px; 
    cursor: pointer; 
    font-size: 14px; 
    font-weight: 700; 
    margin-top: 12px; 
    margin-bottom: 5px;
    text-transform: uppercase; 
    box-shadow: none; /* DIUBAH KESINI */
    position: relative;
    z-index: 1;
    overflow: hidden;
    letter-spacing: 0.5px;
}

.btn-reg:hover { 
    transform: translateY(-2px);
    animation: auroraMove 2s linear infinite;
    box-shadow: none; 
}

        .btn-reg:active {
            transform: translateY(0);
        }

        @keyframes auroraMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        /* Kustomisasi scrollbar internal iframe biar rapi */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
        
        .swal2-popup {
            background: #1e293b !important;
            color: #ffffff !important;
            border-radius: 20px !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
    </style>
</head>
<body>

    <div class="reg-container">
        <h2>REGISTRASI</h2>
        <form action="proses_register.php" method="POST" id="regForm" onsubmit="return handleRegistration(event)">
            <input type="hidden" name="device_id" id="device_id">
            
            <div class="form-group">
                <label>Username</label>
                <div class="input-box">
                    <i class="fa-solid fa-at field-icon"></i>
                    <input type="text" name="username" id="username" required autocomplete="off" placeholder="Username"
                    oninvalid="this.setCustomValidity('Tidak boleh kosong!')"
                    oninput="this.setCustomValidity('')">
                </div>
                <div id="user-message"></div>
            </div>
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <div class="input-box">
                    <i class="fa-solid fa-id-card field-icon"></i>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" required placeholder="Nama Lengkap"
                    oninvalid="this.setCustomValidity('Tidak boleh kosong!')"
                    oninput="this.setCustomValidity('')">
                </div>
                <div id="name-message"></div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-box">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <input type="email" name="email" id="email" required placeholder="Masukan Email Aktif" autocomplete="off"
                    oninvalid="this.setCustomValidity('Tidak boleh kosong!')"
                    oninput="this.setCustomValidity('')">
                </div>
                <div id="email-message"></div> 
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <div class="input-box password-container">
                    <i class="fa-solid fa-lock field-icon"></i>
                    <input type="password" name="password" id="password" required placeholder="Masukan Password"
                    oninvalid="this.setCustomValidity('Tidak boleh kosong!')"
                    oninput="this.setCustomValidity('')">
                    <i class="fa-solid fa-eye-slash toggle-password"></i>
                </div>
                <div id="pass-message"></div>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <div class="input-box password-container">
                    <i class="fa-solid fa-shield-halved field-icon"></i>
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Ulangi password"
                    oninvalid="this.setCustomValidity('Tidak boleh kosong!')"
                    oninput="this.setCustomValidity('')">
                    <i class="fa-solid fa-eye-slash toggle-password"></i>
                </div>
                <div id="confirm-message"></div>
            </div>

            <button type="submit" class="btn-reg">REGISTRASI</button>
        </form>
        
        <div class="login-link">
            Sudah punya Akun? <a href="login.php">Login</a>
        </div>
        <div class="copyright">&copy; 2026 Ujian Online.</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://openfpcdn.io/fingerprintjs/v4/i.js"></script>

    <script>
    const fpPromise = import('https://openfpcdn.io/fingerprintjs/v4').then(FingerprintJS => FingerprintJS.load())
    fpPromise.then(fp => fp.get()).then(result => {
        document.getElementById('device_id').value = result.visitorId;
    })

    const usernameInput = document.getElementById('username');
    const nameInput = document.getElementById('nama_lengkap');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    
    const userMsg = document.getElementById('user-message');
    const nameMsg = document.getElementById('name-message');
    const emailMsg = document.getElementById('email-message');
    const passMsg = document.getElementById('pass-message');
    const confirmMsg = document.getElementById('confirm-message');

    let isUserValid = false, isNameValid = false, isEmailValid = false, isPassValid = false, isConfirmValid = false;

    function validateForm() {
        const user = usernameInput.value;
        const name = nameInput.value;
        emailInput.value = emailInput.value.toLowerCase(); 
        const email = emailInput.value;

        if (user === "") { userMsg.innerHTML = ""; isUserValid = false; }
        else if (!/^[A-Z]/.test(user)) {
            userMsg.className = "msg-error";
            userMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Diawali Huruf Besar!';
            isUserValid = false;
        } else if (user.length < 3) {
            userMsg.className = "msg-error";
            userMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Minimal 3 karakter!';
            isUserValid = false;
        } else {
            userMsg.className = "msg-success";
            userMsg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Username Oke';
            isUserValid = true;
        }

        if (name === "") { nameMsg.innerHTML = ""; isNameValid = false; }
        else if (!/^[A-Z]/.test(name)) {
            nameMsg.className = "msg-error";
            nameMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Diawali Huruf Besar!';
            isNameValid = false;
        } else {
            nameMsg.className = "msg-success";
            nameMsg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Nama Oke';
            isNameValid = true;
        }

        if (email === "") { emailMsg.innerHTML = ""; isEmailValid = false; }
        else if (!email.endsWith("@gmail.com")) {
            emailMsg.className = "msg-error";
            emailMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Wajib gunakan @gmail.com!';
            isEmailValid = false;
        } else {
            emailMsg.className = "msg-success";
            emailMsg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Email Oke';
            isEmailValid = true;
        }

        const hasSymbol = /[!@#$%^&*(),.?":{}|<>_]/.test(passwordInput.value);
        const hasNumber = /[0-9]/.test(passwordInput.value);
        const pass = passwordInput.value;

        if (pass === "") { passMsg.innerHTML = ""; isPassValid = false; }
        else if (!/^[A-Z]/.test(pass)) {
            passMsg.className = "msg-error";
            passMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Diawali Huruf Besar!';
            isPassValid = false;
        } else if (pass.length < 6) {
            passMsg.className = "msg-error";
            passMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Minimal 6 karakter!';
            isPassValid = false;
        } else if (!hasNumber || !hasSymbol) {
            passMsg.className = "msg-error";
            passMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Wajib ada Angka & Simbol!';
            isPassValid = false;
        } else {
            passMsg.className = "msg-success";
            passMsg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Password Kuat';
            isPassValid = true;
        }

        const confirm = confirmInput.value;
        if (confirm === "") { confirmMsg.innerHTML = ""; isConfirmValid = false; }
        else if (confirm !== pass) {
            confirmMsg.className = "msg-error";
            confirmMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Tidak cocok!';
            isConfirmValid = false;
        } else {
            confirmMsg.className = "msg-success";
            confirmMsg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Password Cocok';
            isConfirmValid = true;
        }
    }

    function handleRegistration(e) {
        e.preventDefault(); 
        validateForm(); 

        if (!isUserValid || !isNameValid || !isEmailValid || !isPassValid || !isConfirmValid) {
            Swal.fire({
                title: 'DATA BELUM VALID',
                text: 'Harap periksa kembali semua kolom inputan Anda.',
                icon: 'error',
                confirmButtonColor: '#4361ee'
            });
            return false;
        }

        Swal.fire({
            title: 'KONFIRMASI',
            text: "Apakah data yang Anda masukkan sudah benar?",
            icon: 'question',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#4361ee',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'YA, DAFTAR',
            cancelButtonText: 'BATAL'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'PROSES...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                
                const formData = new FormData(document.getElementById('regForm'));
                fetch('proses_register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    Swal.close();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;
                    const scripts = tempDiv.getElementsByTagName('script');
                    for (let n = 0; n < scripts.length; n++) {
                        eval(scripts[n].innerHTML);
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                });
            }
        });
    }

    [usernameInput, nameInput, emailInput, passwordInput, confirmInput].forEach(el => {
        el.addEventListener('keyup', validateForm);
        el.addEventListener('blur', validateForm);
    });

    document.querySelectorAll('.toggle-password').forEach(item => {
        item.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    });
</script>
</body>
</html>