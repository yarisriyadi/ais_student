<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("location:login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:index.php");
    exit;
}

$id_mk = mysqli_real_escape_string($conn, $_GET['id']);
$query_mk = mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id_mk = '$id_mk'");
$data_mk = mysqli_fetch_assoc($query_mk);

if (!$data_mk) {
    header("location:index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Ujian - <?php echo $data_mk['nama_mk']; ?></title>
    
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
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            overflow-x: hidden;
        }

        .guide-container { 
            background: var(--container-bg); 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px; 
            border-radius: 24px; 
            box-shadow: var(--card-shadow); 
            max-width: 600px; 
            width: 90%; 
            border: 1px solid var(--border-color);
            position: relative;
            z-index: 1;
            margin: 40px 0;
        }

        h2 { 
            color: #3498db; 
            margin-top: 0; 
            font-size: 28px;
            font-weight: 800; 
            letter-spacing: -0.5px;
            text-transform: uppercase;
            text-align: center;
        }
        
        .info-box { 
            background: rgba(52, 152, 219, 0.1); 
            padding: 20px; 
            border-radius: 16px; 
            margin-bottom: 25px; 
            display: flex; 
            justify-content: space-around;
            gap: 15px; 
            font-size: 14px; 
            font-weight: 700;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .rules-list { 
            padding-left: 20px; 
            line-height: 1.8; 
            font-size: 15px; 
            color: var(--text-muted); 
        }

        .rules-list li { margin-bottom: 12px; }

        .actions { 
            margin-top: 35px; 
            display: flex; 
            gap: 15px; 
        }

        .btn-ready { 
            flex: 2; 
            background-image: linear-gradient(90deg, #3498db, #2ecc71, #3498db);
            background-size: 200% 100%;
            color: white; 
            text-align: center; 
            padding: 16px; 
            border-radius: 12px; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 16px;
            text-transform: uppercase;
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .btn-ready:hover { 
            transform: translateY(-3px); 
            animation: auroraMove 2s linear infinite;
            box-shadow: 0 12px 25px rgba(52, 152, 219, 0.4); 
            color: white;
        }

        @keyframes auroraMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        .btn-cancel { 
            flex: 1; 
            background: var(--input-bg); 
            color: var(--text-color); 
            text-align: center; 
            padding: 16px; 
            border-radius: 12px; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 16px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel:hover { 
            background: #e74c3c;
            color: white;
            border-color: #e74c3c;
            transform: translateY(-3px);
        }

        .theme-switcher {
            position: fixed; bottom: 25px; left: 25px; z-index: 1000;
        }
        
        .theme-btn {
            background: var(--container-bg);
            border: 1px solid var(--border-color);
            width: 48px; height: 48px;
            border-radius: 12px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: var(--card-shadow);
            color: var(--text-color);
        }

        .theme-btn:hover { 
            transform: translateY(-3px); 
            border-color: #3498db; 
            background-image: linear-gradient(90deg, #4361ee, #4cc9f0, #4361ee);
            background-size: 200% 100%;
            animation: auroraMove 2s linear infinite;
            color: white;
        }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: var(--body-bg); }
        ::-webkit-scrollbar-thumb { background: #555; border-radius: 10px; }

        @media (max-width: 600px) {
            .guide-container { padding: 25px; margin: 20px; }
            .info-box { flex-direction: column; gap: 10px; text-align: center; }
            .actions { flex-direction: column-reverse; }
            .btn-ready, .btn-cancel { width: 100%; flex: none; }
            .theme-switcher { bottom: 20px; left: 20px; }
        }
    </style>
</head>
<body>

<div class="guide-container">
    <h2>Konfirmasi Ujian</h2>
    <p style="color: var(--text-muted); text-align: center; margin-bottom: 5px;">Anda akan mengikuti ujian untuk mata kuliah:</p>
    <h3 style="margin-bottom: 25px; color: var(--text-color); text-align: center; font-weight: 700;"><?php echo htmlspecialchars($data_mk['nama_mk']); ?></h3>
    
    <div class="info-box">
        <span><i class="fa-solid fa-clock" style="color:#3498db;"></i> Durasi: <?php echo $data_mk['durasi']; ?> Menit</span>
        <span><i class="fa-solid fa-circle-exclamation" style="color:#e74c3c;"></i> Sekali Percobaan</span>
    </div>

    <div style="border-top: 1px solid var(--border-color); padding-top: 20px;">
        <h4 style="margin-bottom: 15px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-shield-halved" style="color: #3498db;"></i> Peraturan & Ketentuan:
        </h4>
        <ul class="rules-list">
            <li>Dilarang membuka tab lain atau aplikasi lain selama ujian berlangsung.</li>
            <li>Waktu akan terus berjalan meskipun halaman dimuat ulang (refresh).</li>
            <li>Pastikan koneksi internet Anda stabil sebelum menekan tombol mulai.</li>
            <li>Ujian akan otomatis terkirim jika waktu pengerjaan habis.</li>
        </ul>
    </div>

    <div class="actions">
        <a href="index.php" class="btn-cancel">BATAL</a>
        <a href="ujian.php?id=<?php echo $id_mk; ?>" class="btn-ready">MULAI UJIAN SEKARANG</a>
    </div>
</div>

<script>
    function syncThemeIcon() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const sunIcon = document.getElementById('theme-icon-sun');
        const moonIcon = document.getElementById('theme-icon-moon');
        
        if (!sunIcon || !moonIcon) return;

        if (currentTheme === 'dark') {
            sunIcon.style.setProperty('display', 'none', 'important');
            moonIcon.style.setProperty('display', 'inline-block', 'important');
        } else {
            sunIcon.style.setProperty('display', 'inline-block', 'important');
            moonIcon.style.setProperty('display', 'none', 'important');
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        syncThemeIcon();
    });
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'data-theme') {
                syncThemeIcon();
            }
        });
    });

    observer.observe(document.documentElement, { attributes: true });
    const originalToggleTheme = window.toggleTheme;
    window.toggleTheme = function() {
        if (typeof originalToggleTheme === 'function') {
            originalToggleTheme();
            setTimeout(syncThemeIcon, 50); 
        }
    };
</script>
</body>
</html>