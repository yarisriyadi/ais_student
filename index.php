<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("location:login.php?pesan=belum_login");
    exit;
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - AIS Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <script>
    document.documentElement.setAttribute('data-theme', 'dark');
</script>

    <link rel="stylesheet" href="style_theme.css">
    
    <style>
        * {
            transition: background 0.4s ease, color 0.4s ease, border-color 0.4s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--body-bg); 
            color: var(--text-color); 
            margin: 0; 
        }
        
        nav { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 15px 5%; background: var(--container-bg); 
            border-bottom: 1px solid var(--border-color);
            position: sticky; top: 0; z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .container { padding: 40px 5%; max-width: 1200px; margin: auto; }
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 40px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .welcome-section { flex: 1; min-width: 280px; }
        .welcome-section h1 { font-size: 28px; margin-bottom: 10px; font-weight: 800; color: var(--text-color); }
        .welcome-section p { color: var(--text-muted); font-size: 16px; }

        /* Style Bar Pencarian */
        .search-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search-container input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            background: var(--input-bg);
            color: var(--text-color);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            box-sizing: border-box;
        }

        .search-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .search-container input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .grid-mk { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 25px; 
        }

        .card-mk {
            background: var(--container-bg); 
            border: 1px solid var(--border-color);
            padding: 30px; border-radius: 24px; 
            position: relative; overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .card-mk:hover { 
            transform: translateY(-8px); 
            border-color: #3498db; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end; 
    gap: 15px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}
        .icon-box {
            width: 50px; height: 50px; background: rgba(52, 152, 219, 0.15);
            color: #3498db; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 20px;
        }

        .mk-title { font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--text-color); }
        .mk-desc { font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 25px; height: 45px; overflow: hidden; }

        .stats-mk { 
    display: flex; 
    gap: 18px; 
    font-size: 14px; 
    font-weight: 700; 
    color: var(--text-color); 
    padding-top: 15px; 
    border-top: 1px solid var(--border-color);
}
        .stats-mk span {
    display: flex;
    align-items: center;
}

        .btn-start {
            display: inline-block; padding: 12px 25px; 
            background: #3498db; color: white; 
            text-decoration: none; border-radius: 12px;
            font-weight: 700; font-size: 14px;
        }
        .btn-start:hover { background: #2980b9; transform: scale(1.05); }

        .user-menu { position: relative; }
        .user-trigger { 
            cursor: pointer; display: flex; align-items: center; gap: 12px; 
            padding: 6px 16px; border-radius: 50px; 
            background: var(--input-bg); border: 1px solid var(--border-color);
        }
        .user-trigger:hover { border-color: #3498db; }

        .dropdown-content {
            display: none; position: absolute; right: 0; top: 55px;
            background: var(--container-bg); border: 1px solid var(--border-color);
            min-width: 210px; border-radius: 18px; 
            box-shadow: 0 15px 30px rgba(0,0,0,0.2); overflow: hidden;
            z-index: 1100;
            backdrop-filter: blur(10px);
        }
        .dropdown-content a { padding: 14px 20px; text-decoration: none; display: block; color: var(--text-color); font-size: 14px; }
        .dropdown-content a:hover { background: rgba(52, 152, 219, 0.1); color: #3498db; }
        .show { display: block; animation: fadeIn 0.2s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .theme-switcher { position: fixed; bottom: 25px; left: 25px; z-index: 9999; }
        .theme-btn {
            width: 48px; height: 48px; border-radius: 12px; border: 1px solid var(--border-color);
            background: var(--container-bg); color: var(--text-color); cursor: pointer;
            display: flex; align-items: center; justify-content: center; position: relative;
            overflow: hidden; padding: 0; outline: none;
        }

        .theme-btn:hover {
            transform: translateY(-3px);
            background-image: linear-gradient(90deg, #4361ee, #4cc9f0, #4361ee);
            background-size: 200% 100%;
            animation: auroraMove 2s linear infinite;
            border-color: transparent; color: white;
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        @keyframes auroraMove { 0% { background-position: 0% 50%; } 100% { background-position: 200% 50%; } }

        #theme-icon { font-size: 1.2rem; z-index: 2; transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        hr { border: 0; border-top: 1px solid var(--border-color); margin: 5px 0; }
        .btn-start.disabled { background: #94a3b8; cursor: not-allowed; opacity: 0.6; pointer-events: none; box-shadow: none; }
        
        .nav-logo {
    height: 70px; 
    width: auto;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    filter: drop-shadow(0 0 10px rgba(52, 152, 219, 0.4)); 
    cursor: pointer;
}

.nav-logo:hover {
    transform: scale(1.1) rotate(-2deg); 
    filter: drop-shadow(0 0 20px rgba(52, 152, 219, 0.8)); 
}

nav {
    padding: 10px 5% !important; 
}
        
    </style>
</head>
<body>

<nav>
    <a href="index.php" style="text-decoration: none; display: flex; align-items: center;">
        <img src="bahan_gambar/ais_icon3.png" alt="AIS Logo" class="nav-logo">
    </a>
    
    <div class="user-menu">
        <div class="user-trigger" id="userTrigger">

            <span style="font-weight: 700; font-size: 14px; color: var(--text-color);"><?php echo $_SESSION['username']; ?></span>

            <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['username']; ?>&background=3498db&color=fff&bold=true&length=1" style="width:32px; height:32px; border-radius:50%; border: 2px solid #3498db;">

        </div>
        <div id="myDropdown" class="dropdown-content">
            <a href="profil.php"><i class="fa-solid fa-user-pen" style="margin-right:12px;"></i> Profil Saya</a>
            <a href="riwayat.php"><i class="fa-solid fa-clock-rotate-left" style="margin-right:12px;"></i> Riwayat Ujian</a>
            <hr>
            <a href="logout.php" style="color: #e74c3c; font-weight: 600;"><i class="fa-solid fa-power-off" style="margin-right:12px;"></i> Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="header-flex">
        <div class="welcome-section">
            <h1>Halo, <?php echo $_SESSION['username']; ?>! 👋</h1>
            <p>Pilih mata kuliah di bawah ini untuk memulai simulasi ujian.</p>
        </div>

        <!-- Form Pencarian -->
        <form action="" method="GET" class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" placeholder="Cari mata kuliah..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>

    <div class="grid-mk">
    <?php
    $user_id = $_SESSION['id_user']; 
    $sql_mk = "SELECT * FROM mata_kuliah";
    if (!empty($search)) {
        $sql_mk .= " WHERE nama_mk LIKE '%$search%'";
    }
    $sql_mk .= " ORDER BY id_mk DESC";
    
    $query_mk = mysqli_query($conn, $sql_mk);
    
    if (mysqli_num_rows($query_mk) > 0) {
        while ($mk = mysqli_fetch_array($query_mk)) {
            $id_mk = $mk['id_mk'];
            $hitung_soal = mysqli_query($conn, "SELECT id_soal FROM soal WHERE id_mk = '$id_mk'");
            $jumlah_soal = mysqli_num_rows($hitung_soal);
            
            $hitung_peserta = mysqli_query($conn, "SELECT DISTINCT id_user FROM nilai_ujian WHERE id_mk = '$id_mk'");
			$jumlah_peserta = mysqli_num_rows($hitung_peserta);

            $query_cek_nilai = mysqli_query($conn, "SELECT skor FROM nilai_ujian WHERE id_mk = '$id_mk' AND id_user = '$user_id'");
            $data_nilai = mysqli_fetch_assoc($query_cek_nilai);
            $sudah_ujian = mysqli_num_rows($query_cek_nilai) > 0;
    ?>
    <div class="card-mk">
        <div class="icon-box">
            <i class="fa-solid fa-book-bookmark"></i>
        </div>
        <div class="mk-title"><?php echo htmlspecialchars($mk['nama_mk']); ?></div>
        <div class="mk-desc">
            <?php echo !empty($mk['deskripsi']) ? htmlspecialchars($mk['deskripsi']) : "Belum ada deskripsi untuk mata kuliah ini."; ?>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div class="stats-mk">
    <span title="Jumlah Soal">
        <i class="fa-solid fa-file-lines" style="color:#3498db; margin-right: 5px;"></i> 
        <?php echo $jumlah_soal; ?>
    </span>
    <span title="Durasi (Menit)">
        <i class="fa-solid fa-clock" style="color:#3498db; margin-right: 5px;"></i> 
        <?php echo $mk['durasi']; ?>
    </span>
    <span title="Jumlah Audiens">
        <i class="fa-solid fa-users" style="color:#3498db; margin-right: 5px;"></i> 
        <?php echo $jumlah_peserta; ?>
    </span>
</div>

            <?php if ($sudah_ujian): ?>
                <div style="text-align: right;">
                    <span style="display:block; font-size: 11px; color: var(--text-muted); font-weight: 600;">Skor Anda:</span>
                    <strong style="color: #2ecc71; font-size: 1.4rem; font-weight: 800;"><?php echo round($data_nilai['skor'], 1); ?></strong>
                </div>
            <?php else: ?>
                <?php if ($jumlah_soal > 0): ?>
                    <a href="panduan.php?id=<?php echo $id_mk; ?>" class="btn-start">Mulai</a>
                <?php else: ?>
                    <a href="javascript:void(0)" class="btn-start disabled" title="Soal belum tersedia">Mulai</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php 
        } 
    } else {
        echo "<div style='grid-column: 1/-1; text-align: center; padding: 80px 20px; color: var(--text-muted);'>
                <i class='fa-solid fa-folder-open' style='font-size: 56px; margin-bottom: 20px; display: block; opacity: 0.3;'></i>
                <p style='font-size: 18px;'>Mata kuliah tidak ditemukan.</p>
                <a href='index.php' style='color:#3498db; text-decoration:none;'>Kembali ke semua mata kuliah</a>
              </div>";
    }
    ?>
    </div>
</div>
 

<script>
    const userTrigger = document.getElementById('userTrigger');
    const dropdown = document.getElementById('myDropdown');

    if (userTrigger) {
        userTrigger.onclick = (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        }
    }

    window.onclick = (e) => {
        if (userTrigger && !userTrigger.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    }

    // 2. Fungsi Sinkronisasi Ikon
    function syncThemeIcon() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const icon = document.getElementById('theme-icon');
        if (!icon) return;

        if (currentTheme === 'dark') {
            icon.className = 'fa-solid fa-moon';
            icon.style.color = '#f1c40f';
        } else {
            icon.className = 'fa-solid fa-sun';
            icon.style.color = '#f39c12';
        }
    }

    window.addEventListener('load', () => {
        syncThemeIcon();
        const originalToggle = window.toggleTheme;
        if (typeof originalToggle === 'function') {
            window.toggleTheme = function() {
                originalToggle();
                syncThemeIcon();
            };
        }
    });
</script>
</body>
</html>