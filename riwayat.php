<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("location:login.php?pesan=belum_login");
    exit;
}

$user_id = $_SESSION['id_user'];

$query_riwayat = mysqli_query($conn, "
    SELECT n.*, m.nama_mk 
    FROM nilai_ujian n 
    JOIN mata_kuliah m ON n.id_mk = m.id_mk 
    WHERE n.id_user = '$user_id' 
    ORDER BY n.tanggal_ujian DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Ujian - AIS Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
    (function() {
        document.documentElement.setAttribute('data-theme', 'dark');
    })();
</script>
    
    <link rel="stylesheet" href="style_theme.css">
    
    <style>
        * { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--body-bg); 
            color: var(--text-color); 
            margin: 0; 
            line-height: 1.6;
        }
        
        nav { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 15px 5%; background: var(--container-bg); 
            border-bottom: 1px solid var(--border-color);
            position: sticky; top: 0; z-index: 1000;
            backdrop-filter: blur(12px);
        }

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

        .container { padding: 40px 6%; max-width: 1100px; margin: auto; }
        
        .header-page { 
            margin-bottom: 35px; 
            border-left: 5px solid #3498db;
            padding: 5px 20px;
        }
        
        .header-page h2 { 
            font-size: 28px; 
            font-weight: 800; 
            margin: 0;
            letter-spacing: -0.5px;
        }

        .table-container {
            background: var(--container-bg);
            border-radius: 28px;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }
        
        th { 
            background: rgba(52, 152, 219, 0.08); 
            color: #3498db; 
            text-align: left; 
            padding: 22px; 
            font-size: 12px; 
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        td { 
            padding: 24px 22px; 
            border-bottom: 1px solid var(--border-color); 
            font-size: 15px;
        }

        tr:hover td { background: rgba(52, 152, 219, 0.03); }

        .badge-score {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 50px;
            height: 40px;
            padding: 0 15px; 
            border-radius: 12px; 
            font-weight: 800;
            background: #2ecc71; 
            color: white;
            box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
            font-size: 16px;
        }

        .btn-review {
            text-decoration: none;
            background: #4361ee;
            color: white !important;
            padding: 12px 24px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .theme-switcher { position: fixed; bottom: 30px; left: 30px; z-index: 9999; }
        .theme-btn {
            width: 54px; height: 54px; border-radius: 18px;
            border: 1px solid var(--border-color); background: var(--container-bg);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: var(--text-color);
        }

        .empty-state-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 20px;
            width: 100%;
        }
.badge-grade {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 14px;
    margin-right: 8px;
}

.grade-a { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981; }
.grade-b { background: rgba(52, 152, 219, 0.1); color: #3498db; border: 1px solid #3498db; }
.grade-c { background: rgba(241, 196, 15, 0.1); color: #f1c40f; border: 1px solid #f1c40f; }
.grade-d { background: rgba(230, 126, 34, 0.1); color: #e67e22; border: 1px solid #e67e22; }
.grade-e { background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid #e74c3c; }

/* Responsive adjustment */
@media (max-width: 768px) {
    .badge-grade { width: 28px; height: 28px; font-size: 12px; }
}

        @media (max-width: 768px) {
            thead { display: none; }
            .table-container { background: transparent; border: none; box-shadow: none; }
            tr { 
                display: block; 
                margin-bottom: 20px; 
                padding: 24px; 
                border: 1px solid var(--border-color); 
                border-radius: 24px;
                background: var(--container-bg);
                box-shadow: var(--card-shadow);
            }
            td { 
                display: flex; 
                justify-content: space-between; 
                align-items: flex-start; 
                padding: 12px 0; 
                border: none;
                gap: 15px; 
            }
            td::before {
                content: attr(data-label); 
                font-weight: 700;
                color: var(--text-muted); 
                font-size: 11px; 
                text-transform: uppercase;
                width: 100px; 
                flex-shrink: 0;
                text-align: left;
            }

            td > div, td > span {
                text-align: right;
                width: 100%;
                word-wrap: break-word; 
            }

            tr.no-data-row { padding: 0; }
            tr.no-data-row td { display: block; width: 100%; padding: 0; }
            tr.no-data-row td::before { display: none; }
        }
        hr { border: 0; border-top: 1px solid var(--border-color); margin: 5px 0; }
        
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
            <a href="index.php"><i class="fa-solid fa-house" style="margin-right:12px;"></i> Dashboard</a>
            <a href="profil.php"><i class="fa-solid fa-user-pen" style="margin-right:12px;"></i> Profil Saya</a>
            <a href="riwayat.php"><i class="fa-solid fa-clock-rotate-left" style="margin-right:12px;"></i> Riwayat Ujian</a>
            <hr>
            <a href="logout.php" style="color: #e74c3c; font-weight: 600;"><i class="fa-solid fa-power-off" style="margin-right:12px;"></i> Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="header-page">
        <h2>Riwayat Ujian</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0 0; font-size: 14px;">Daftar hasil evaluasi ujian Anda.</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Mata Kuliah</th>
                    <th>Tanggal</th>
                    <th class="stats-col">Statistik</th>
                    <th style="text-align: center;">Grade</th> 
                    <th style="text-align: center;">Skor</th>
                    <th style="text-align: center;">Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($query_riwayat) > 0): ?>
                    <?php while($r = mysqli_fetch_assoc($query_riwayat)): 
    // Logika Penentuan Grade
    $skor = round($r['skor']);
    if ($skor >= 85) { $grade = 'A'; $g_class = 'grade-a'; }
    elseif ($skor >= 75) { $grade = 'B'; $g_class = 'grade-b'; }
    elseif ($skor >= 60) { $grade = 'C'; $g_class = 'grade-c'; }
    elseif ($skor >= 40) { $grade = 'D'; $g_class = 'grade-d'; }
    else { $grade = 'E'; $g_class = 'grade-e'; }
?>

<tr>
    <td data-label="Mata Kuliah">
        <div style="font-weight: 700; color: var(--text-color); font-size: 16px; line-height: 1.3;">
            <?php echo htmlspecialchars($r['nama_mk']); ?>
        </div>
    </td>
    <td data-label="Tanggal">
        <div>
            <div class="local-date" data-utc="<?php echo $r['tanggal_ujian']; ?>" style="font-weight: 600;">-</div>
            <div class="local-time" data-utc="<?php echo $r['tanggal_ujian']; ?>" style="font-size: 12px; opacity: 0.6;">-</div>
        </div>
    </td>
    <td data-label="Statistik" class="stats-col">
        <div>
            <span style="color: #2ecc71; font-weight: 700;"><i class="fa-solid fa-check-circle"></i> <?php echo $r['jumlah_benar']; ?></span>
            <span style="margin: 0 8px; opacity: 0.2;">|</span>
            <span style="color: #e74c3c; font-weight: 700;"><i class="fa-solid fa-times-circle"></i> <?php echo $r['jumlah_salah']; ?></span>
        </div>
    </td>
    <!-- KOLOM GRADE BARU -->
    <td data-label="Grade" style="text-align: center;">
        <span class="badge-grade <?php echo $g_class; ?>"><?php echo $grade; ?></span>
    </td>
    <td data-label="Skor" style="text-align: center;">
        <span class="badge-score"><?php echo $skor; ?></span>
    </td>
    <td data-label="Opsi" style="text-align: center;">
        <a href="review_ujian.php?id=<?php echo $r['id_mk']; ?>" class="btn-review">
            <i class="fa-solid fa-eye"></i> Review
        </a>
    </td>
</tr>
<?php endwhile; ?>
                <?php else: ?>
                    <tr class="no-data-row">
                        <td colspan="6">
                            <div class="empty-state-wrapper">
                                <i class="fa-solid fa-inbox" style="font-size: 64px; color: #3498db; opacity: 0.2; margin-bottom: 20px;"></i>
                                <h3 style="margin: 0; opacity: 0.6; font-weight: 700; font-size: 22px;">Belum ada data riwayat</h3>
                                <p style="margin: 10px 0 0 0; opacity: 0.4; font-size: 14px; max-width: 280px; line-height: 1.4;">Hasil ujian Anda akan muncul di sini setelah Anda menyelesaikannya.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="theme_script.js"></script>
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
        if (userTrigger && !userTrigger.contains(e.target)) dropdown.classList.remove('show');
    }

    function convertToLocal() {
        const dateElements = document.querySelectorAll('.local-date');
        const timeElements = document.querySelectorAll('.local-time');
        dateElements.forEach((el, index) => {
            const utcString = el.getAttribute('data-utc').replace(/-/g, "/");
            const date = new Date(utcString + " UTC");
            el.innerText = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            timeElements[index].innerText = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + " WIB";
        });
    }
    document.addEventListener('DOMContentLoaded', convertToLocal);

    function syncThemeIcon() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const icon = document.getElementById('theme-icon');
        if (!icon) return;
        icon.className = currentTheme === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
        icon.style.color = currentTheme === 'dark' ? '#f1c40f' : '#f39c12';
    }
    window.addEventListener('load', () => {
        syncThemeIcon();
        const originalToggle = window.toggleTheme;
        if (typeof originalToggle === 'function') {
            window.toggleTheme = function() { originalToggle(); syncThemeIcon(); };
        }
    });
</script>
</body>
</html>