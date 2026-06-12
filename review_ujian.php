<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status'])) { header("location:login.php"); exit; }
if (!isset($_GET['id'])) { header("location:index.php"); exit; }

$id_mk = mysqli_real_escape_string($conn, $_GET['id']);
$id_user = $_SESSION['id_user'];
$query_nilai = mysqli_query($conn, "SELECT * FROM nilai_ujian WHERE id_user = '$id_user' AND id_mk = '$id_mk' ORDER BY id_nilai DESC LIMIT 1");
$data_nilai = mysqli_fetch_assoc($query_nilai);

if (!$data_nilai) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Review Not Found</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <style>
            :root { --bg: #0f172a; --card: #1e293b; --text: #f8fafc; --primary: #3b82f6; }
            body { 
                margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: var(--bg); color: var(--text);
                display: flex; align-items: center; justify-content: center; height: 100vh;
            }
            .error-container { text-align: center; padding: 40px; }
            .error-code { 
                font-size: 120px; font-weight: 800; margin: 0; 
                background: linear-gradient(to right, #3b82f6, #2dd4bf);
                -webkit-background-clip: text; -webkit-text-fill-color: transparent;
                line-height: 1;
            }
            .error-msg { font-size: 24px; font-weight: 700; margin: 20px 0 10px; }
            .error-desc { color: #94a3b8; margin-bottom: 30px; max-width: 400px; }
            .btn-back {
                text-decoration: none; background: var(--primary); color: white;
                padding: 12px 25px; border-radius: 12px; font-weight: 600;
                display: inline-flex; align-items: center; gap: 10px;
                transition: all 0.3s ease;
            }
            .btn-back:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 class="error-code">404</h1>
            <div class="error-msg">Exam Review Not Found</div>
            <p class="error-desc">We're sorry, the exam data you are looking for has been removed by the administrator or is no longer available.</p>
            <a href="index.php" class="btn-back">Back to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$query_mk = mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id_mk = '$id_mk'");
$data_mk = mysqli_fetch_assoc($query_mk);

$hitung_benar = $data_nilai['jumlah_benar']; 
$hitung_salah = $data_nilai['jumlah_salah']; 
$skor_akhir   = round($data_nilai['skor']);
$jawaban_user = [];
$query_jwb = mysqli_query($conn, "SELECT id_soal, jawaban FROM jawaban_mhs WHERE id_user = '$id_user' AND id_mk = '$id_mk'");
while ($row_jwb = mysqli_fetch_assoc($query_jwb)) {
    $jawaban_user[$row_jwb['id_soal']] = $row_jwb['jawaban'];
}

$soal_query = mysqli_query($conn, "SELECT * FROM soal WHERE id_mk = '$id_mk'");
$list_soal = []; 
while ($row = mysqli_fetch_assoc($soal_query)) {
    $list_soal[] = $row;
}

// Logika Grade tetap sama
if ($skor_akhir >= 85) { $grade = 'A'; $g_color = '#2ecc71'; }
elseif ($skor_akhir >= 75) { $grade = 'B'; $g_color = '#3498db'; }
elseif ($skor_akhir >= 60) { $grade = 'C'; $g_color = '#f1c40f'; }
elseif ($skor_akhir >= 40) { $grade = 'D'; $g_color = '#e67e22'; }
else { $grade = 'E'; $g_color = '#ef4444'; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Review Hasil - <?php echo htmlspecialchars($data_mk['nama_mk']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
    (function() {
        document.documentElement.setAttribute('data-theme', 'dark');
    })();
    </script>
    <link rel="stylesheet" href="style_theme.css">

    <style>
    :root {
        --primary: #4361ee;
        --success: #10b981;
        --danger: #ef4444;
    }

    * { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

    body { 
        font-family: 'Plus Jakarta Sans', sans-serif; 
        background-color: var(--body-bg); 
        color: var(--text-color);
        margin: 0; 
        line-height: 1.6;
    }

    nav { 
        display: flex; justify-content: space-between; align-items: center; 
        padding: 10px 5% !important; background: var(--container-bg); 
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

    .container { 
        width: 92%;
        max-width: 850px; 
        margin: 40px auto 50px; 
    }

    .header-page { 
        margin-bottom: 35px; 
        border-left: 5px solid #3498db;
        padding: 5px 20px;
    }
    .header-page h2 { font-size: 28px; font-weight: 800; margin: 0; letter-spacing: -0.5px; }

    .hero-score-card {
        background: linear-gradient(135deg, var(--container-bg) 0%, rgba(67, 97, 238, 0.05) 100%);
        border-radius: 24px;
        padding: 30px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-around;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .main-grade-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid <?php echo $g_color; ?>;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.03);
        box-shadow: 0 0 20px <?php echo $g_color; ?>44;
    }

    .grade-val { font-size: 3rem; font-weight: 800; color: <?php echo $g_color; ?>; line-height: 1; }
    .grade-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; opacity: 0.7; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
    }

    .stat-item { text-align: center; }
    .stat-value { display: block; font-size: 1.8rem; font-weight: 800; margin-bottom: -5px; }
    .stat-label { font-size: 0.75rem; font-weight: 600; opacity: 0.5; text-transform: uppercase; letter-spacing: 1px; }

    .q-card {
        background: var(--container-bg);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
    }
    .q-badge-container { display: flex; justify-content: flex-end; margin-bottom: 15px; }
    .q-badge {
        padding: 6px 12px; border-radius: 10px; font-size: 0.7rem; font-weight: 800;
        display: flex; align-items: center; gap: 6px;
    }
    .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

    .q-main-row { display: flex; gap: 15px; align-items: flex-start; }
    .q-number {
        background: var(--primary); color: white; width: 35px; height: 35px;
        border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-weight: 800; flex-shrink: 0; font-size: 0.9rem;
    }
    .q-content { flex: 1; }
    .q-text { font-size: 1rem; font-weight: 700; margin: 0 0 15px 0; color: var(--text-color); }

    .options-wrapper { display: grid; gap: 10px; }
    .option-item {
        padding: 12px 15px; border-radius: 12px; border: 1px solid var(--border-color);
        display: flex; align-items: center; background: var(--input-bg); font-size: 0.9rem;
    }
    .opt-label {
        width: 25px; height: 25px; background: var(--container-bg);
        border-radius: 6px; display: flex; align-items: center; justify-content: center;
        margin-right: 12px; font-weight: 700; font-size: 0.8rem; border: 1px solid var(--border-color);
    }
    .is-correct { border-color: var(--success) !important; background: rgba(16, 185, 129, 0.05) !important; color: var(--success); }
    .is-wrong { border-color: var(--danger) !important; background: rgba(239, 68, 68, 0.05) !important; color: var(--danger); }

    .explanation {
        margin-top: 15px; padding: 12px; background: rgba(16, 185, 129, 0.05);
        border-radius: 10px; font-size: 0.85rem; color: var(--success);
        display: flex; gap: 8px; border-left: 4px solid var(--success);
    }

    .nav-logo {
        height: 70px; 
        width: auto;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        filter: drop-shadow(0 0 10px rgba(52, 152, 219, 0.4)); 
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .container { width: 94%; margin-top: 20px; }
        .hero-score-card { flex-direction: column; gap: 25px; padding: 20px; text-align: center; }
        .stats-grid { gap: 20px; width: 100%; border-top: 1px solid var(--border-color); padding-top: 20px; }
        .stat-value { font-size: 1.4rem; }
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
        <h2>Review Hasil</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0 0; font-size: 14px;">Mata Kuliah: <b><?php echo htmlspecialchars($data_mk['nama_mk']); ?></b></p>
    </div>

    <div class="hero-score-card">
        <div class="main-grade-circle">
            <span class="grade-label">Grade</span>
            <span class="grade-val"><?php echo $grade; ?></span>
        </div>

        <div class="stats-grid">
            <div class="stat-item" style="color: var(--primary);">
                <span class="stat-value"><?php echo $skor_akhir; ?></span>
                <span class="stat-label">Total Skor</span>
            </div>
            <div class="stat-item" style="color: var(--success);">
                <span class="stat-value"><?php echo $hitung_benar; ?></span>
                <span class="stat-label">Benar</span>
            </div>
            <div class="stat-item" style="color: var(--danger);">
                <span class="stat-value"><?php echo $hitung_salah; ?></span>
                <span class="stat-label">Salah</span>
            </div>
        </div>
    </div>

    <?php 
    $no = 1;
    foreach($list_soal as $s): 
        $id_soal = $s['id_soal'];
        $user_ans = isset($jawaban_user[$id_soal]) ? $jawaban_user[$id_soal] : '-';
        $is_essay = empty($s['opsi_a']); 
        
        if (!$is_essay) {
            $correct_ans = strtolower($s['jawaban_benar']);
            $user_ans_lower = strtolower($user_ans);
            $is_user_correct = ($user_ans_lower === $correct_ans);
        } else {
            $is_user_correct = true; 
        }
    ?>
    <div class="q-card">
        <div class="q-badge-container">
            <div class="q-badge <?php echo $is_user_correct ? 'badge-success' : 'badge-danger'; ?>">
                <i class="fa-solid <?php echo $is_user_correct ? 'fa-check' : 'fa-xmark'; ?>"></i>
                <?php echo $is_essay ? 'ESAI' : ($is_user_correct ? 'BENAR' : 'SALAH'); ?>
            </div>
        </div>

        <div class="q-main-row">
            <div class="q-number"><?php echo $no++; ?></div>
            <div class="q-content">
                <p class="q-text"><?php echo nl2br(htmlspecialchars($s['pertanyaan'])); ?></p>
                
                <?php if (!$is_essay): ?>
                    <div class="options-wrapper">
                        <?php foreach(['a', 'b', 'c', 'd'] as $opt): 
                            $class = "";
                            if($opt === $correct_ans) $class = "is-correct";
                            if($opt === $user_ans_lower && !$is_user_correct) $class = "is-wrong";
                        ?>
                        <div class="option-item <?php echo $class; ?>">
                            <div class="opt-label"><?php echo strtoupper($opt); ?></div>
                            <div style="flex:1;"><?php echo htmlspecialchars($s["opsi_" . $opt]); ?></div>
                            <?php if($opt === $correct_ans): ?>
                                <i class="fa-solid fa-circle-check" style="color: var(--success); margin-left: 8px;"></i>
                            <?php elseif($opt === $user_ans_lower && !$is_user_correct): ?>
                                <i class="fa-solid fa-circle-xmark" style="color: var(--danger); margin-left: 8px;"></i>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="essay-review" style="display: grid; gap: 15px;">
                        <div style="padding: 15px; background: var(--input-bg); border-radius: 12px; border: 1px solid var(--border-color);">
                            <small style="font-weight: 800; color: var(--primary); display: block; margin-bottom: 5px;">Jawabamu:</small>
                            <div><?php echo nl2br(htmlspecialchars($user_ans)); ?></div>
                        </div>
                        <div style="padding: 15px; background: rgba(16, 185, 129, 0.05); border-radius: 12px; border: 1px solid var(--success);">
                            <small style="font-weight: 800; color: var(--success); display: block; margin-bottom: 5px;">Kunci Jawaban:</small>
                            <div><?php echo nl2br(htmlspecialchars($s['jawaban_benar'])); ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$is_essay && !$is_user_correct): ?>
                    <div class="explanation">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span>Jawaban Benar: <strong><?php echo strtoupper($correct_ans); ?>. <?php echo htmlspecialchars($s["opsi_" . $correct_ans]); ?></strong></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
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
</script>
</body>
</html>