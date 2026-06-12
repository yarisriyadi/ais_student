<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("location:login.php?pesan=belum_login");
    exit;
}

$user_id = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_nama'])) {
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    
    $update_stmt = $conn->prepare("UPDATE users SET nama_lengkap = ? WHERE id = ?");
    $update_stmt->bind_param("si", $nama_baru, $user_id);
    
    if ($update_stmt->execute()) {
        header("location:profil.php?status=sukses");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $u = $result->fetch_assoc();
    $stmt->close();
}

$display_nama = $u['nama_lengkap'] ?? 'Nama Tidak Ditemukan';
$display_user = $u['username'] ?? $_SESSION['username'];
$display_email = $u['email'] ?? '-';

// --- LOGIKA SAPAAN & IKON WAKTU ---
date_default_timezone_set('Asia/Jakarta');
$jam = date('H');
if ($jam >= 5 && $jam < 11) {
    $sapaan = "Selamat Pagi";
    $ikon_waktu = "fa-sun-rise";
    $warna_ikon = "#ffbd33";
} elseif ($jam >= 11 && $jam < 15) {
    $sapaan = "Selamat Siang";
    $ikon_waktu = "fa-sun";
    $warna_ikon = "#ff9100";
} elseif ($jam >= 15 && $jam < 18) {
    $sapaan = "Selamat Sore";
    $ikon_waktu = "fa-cloud-sun";
    $warna_ikon = "#f39c12";
} else {
    $sapaan = "Selamat Malam";
    $ikon_waktu = "fa-moon";
    $warna_ikon = "#f1c40f";
}

$words = explode(" ", $display_nama);
$initials = "";
foreach ($words as $w) {
    $initials .= mb_substr($w, 0, 1);
}
$initials = strtoupper($initials);
?>
<!DOCTYPE html>
<html lang="id">
<head>
        <link rel="apple-touch-icon" href="bahan_gambar/ais_icon3.png">

    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - AIS Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            document.documentElement.setAttribute('data-theme', 'dark');
        })();
    </script>

    <link rel="stylesheet" href="style_theme.css">

    <style>
        :root[data-theme="dark"] {
            --bg-body: #0b0f1a;
            --bg-card: #161e2d;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #2d3748;
            --primary: #4cc9f0;
            --success: #059669;
            --shadow: rgba(0, 0, 0, 0.3);
        }

        * { box-sizing: border-box; transition: all 0.3s ease; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            margin: 0; 
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; padding: 20px;
        }

        .container { width: 100%; max-width: 450px; }

        .profile-card {
            background: var(--bg-card);
            border-radius: 32px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px var(--shadow);
            position: relative;
        }

        .weather-info {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            background: rgba(255,255,255,0.03);
            padding: 10px;
            border-radius: 20px;
        }

        .greeting-text {
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #temp-display {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-muted);
        }

        .profile-img-container { width: 120px; height: 120px; margin: 0 auto 20px; }
        .profile-img {
            width: 100%; height: 100%;
            border-radius: 50%;
            border: 4px solid var(--primary);
            padding: 5px;
            background: var(--bg-card);
        }

        .name-wrapper {
            display: flex; align-items: center; justify-content: center;
            gap: 10px; margin-bottom: 5px; flex-wrap: wrap;
        }

        .display-name { 
            margin: 0; font-size: 1.6rem; 
            word-break: break-word; /* Nama panjang tetap center */
        }

        .btn-edit-name {
            background: none; border: none; color: var(--text-muted);
            cursor: pointer; font-size: 1.1rem; padding: 5px;
        }

        .badge {
            background: rgba(76, 201, 240, 0.1); color: var(--primary);
            padding: 6px 16px; border-radius: 12px; font-size: 11px;
            font-weight: 700; margin: 10px 0 30px; display: inline-block;
        }

        .info-group { text-align: left; margin-bottom: 30px; }
        .info-item {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding: 15px 0; border-bottom: 1px solid var(--border);
            gap: 20px;
        }
        .info-label { color: var(--text-muted); font-size: 13px; flex-shrink: 0; }
        .info-value { 
            font-weight: 700; font-size: 14px; text-align: right;
            word-break: break-all; /* Email panjang tetap rapi */
            max-width: 250px;
        }

        .btn-back {
            display: flex; align-items: center; justify-content: center;
            background: var(--primary); color: #000; text-decoration: none;
            padding: 16px; border-radius: 20px; font-weight: 800; gap: 10px;
            box-shadow: 0 10px 20px rgba(76, 201, 240, 0.2);
        }
    </style>
</head>
<body>

<form id="formUpdateNama" method="POST" style="display:none;">
    <input type="hidden" name="update_nama" value="1">
    <input type="hidden" name="nama_lengkap" id="inputNamaBaru">
</form>

<div class="container">
    <div class="profile-card">
        <div class="weather-info">
            <i class="fa-solid <?php echo $ikon_waktu; ?>" style="color: <?php echo $warna_ikon; ?>; font-size: 20px;"></i>
            <span class="greeting-text"><?php echo $sapaan; ?></span>
            <span id="temp-display"><i class="fa-solid fa-spinner fa-spin"></i></span>
        </div>

        <div class="profile-img-container">
            <!-- URL API Inisial Nama Lengkap -->
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($initials); ?>&size=200&background=4361ee&color=fff&bold=true&length=1&display=<?php echo $initials; ?>" class="profile-img" alt="Avatar">
             
        </div>
        
        <div class="name-wrapper">
            <h2 class="display-name"><?php echo htmlspecialchars($display_nama); ?></h2>
            <button class="btn-edit-name" onclick="editNama('<?php echo htmlspecialchars($display_nama); ?>')" title="Ubah Nama">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>

        <div class="badge">Mahasiswa Aktif</div>

        <div class="info-group">
            <div class="info-item">
                <span class="info-label">Username</span>
                <span class="info-value">@<?php echo htmlspecialchars($display_user); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Email Aktif</span>
                <span class="info-value"><?php echo htmlspecialchars($display_email); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">ID Pengguna</span>
                <span class="info-value">#<?php echo sprintf("%04d", $user_id); ?></span>
            </div>
            <div class="info-item" style="border-bottom: none;">
                <span class="info-label">Status Akun</span>
                <span class="info-value" style="color: var(--success);"><i class="fa-solid fa-circle-check"></i> Terverifikasi</span>
            </div>
        </div>

        <a href="index.php" class="btn-back">
            <i class="fa-solid fa-house"></i> KEMBALI KE BERANDA
        </a>
    </div>
</div>

<script>
    async function fetchWeather() {
        const tempElement = document.getElementById('temp-display');
        
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(async (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                
                try {
                    const res = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`);
                    const data = await res.json();
                    const temp = data.current_weather.temperature;
                    const condition = data.current_weather.weathercode;
                    
                    let rainStatus = "";
                    if (condition >= 51 && condition <= 67) rainStatus = " | <i class='fa-solid fa-cloud-rain'></i>";
                    
                    tempElement.innerHTML = `${temp}°C${rainStatus}`;
                } catch (err) {
                    tempElement.innerHTML = "Suhu tidak tersedia";
                }
            }, () => {
                tempElement.innerHTML = "Akses lokasi ditolak";
            });
        } else {
            tempElement.innerHTML = "Geo tidak didukung";
        }
    }

    function editNama(currentName) {
        Swal.fire({
            title: 'Ubah Nama Lengkap',
            input: 'text',
            inputValue: currentName,
            background: '#161e2d', 
            color: '#f1f5f9',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            confirmButtonColor: '#4cc9f0',
            cancelButtonColor: '#ef4444',
            inputValidator: (value) => {
                if (!value) return 'Nama tidak boleh kosong!'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('inputNamaBaru').value = result.value;
                document.getElementById('formUpdateNama').submit();
            }
        });
    }

    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data profil Anda telah diperbarui.',
            timer: 2000,
            showConfirmButton: false,
            background: '#161e2d',
            color: '#f1f5f9'
        });
        window.history.replaceState({}, document.title, "profil.php");
    <?php endif; ?>

    window.addEventListener('DOMContentLoaded', fetchWeather);
</script>

</body>
</html>