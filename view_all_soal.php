<?php
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    exit;
}

$id_mk = $_GET['id_mk'];
$mk_info = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id_mk = '$id_mk'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Soal - <?php echo $mk_info['nama_mk']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_theme.css">
    <script>
        const savedTheme = localStorage.getItem('selected-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--body-bg); color: var(--text-color); margin: 0; padding: 2rem 5%; }
        .container { max-width: 1000px; margin: auto; }
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: var(--container-bg); border-radius: 20px; border: 1px solid var(--border-color); padding: 24px; box-shadow: var(--card-shadow); }
        .btn-back { text-decoration: none; color: var(--text-color); font-weight: 700; font-size: 14px; opacity: 0.7; }
        .btn-back:hover { opacity: 1; color: #4361ee; }
        .table-res { width: 100%; border-collapse: collapse; }
        .table-res th { text-align: left; padding: 15px; border-bottom: 2px solid var(--border-color); opacity: 0.7; font-size: 13px; }
        .table-res td { padding: 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
        .btn-action { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; text-decoration: none; margin-right: 5px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-area">
        <div>
            <a href="admin_dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
            <h1 style="margin: 10px 0 0 0;"><?php echo $mk_info['nama_mk']; ?></h1>
            <p style="opacity: 0.6; margin: 5px 0;">Menampilkan semua daftar soal</p>
        </div>
        <div style="text-align: right;">
            <span style="background: #4361ee; color: white; padding: 8px 16px; border-radius: 12px; font-weight: 700;">
                <?php 
                $count = mysqli_num_rows(mysqli_query($conn, "SELECT id_soal FROM soal WHERE id_mk = '$id_mk'"));
                echo $count; 
                ?> Soal
            </span>
        </div>
    </div>

    <div class="card">
        <div style="overflow-x: auto;">
            <table class="table-res">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Pertanyaan</th>
                        <th width="100">Tipe</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $all_soal = mysqli_query($conn, "SELECT * FROM soal WHERE id_mk = '$id_mk' ORDER BY id_soal ASC");
                    $no = 1;
                    while($s = mysqli_fetch_array($all_soal)):
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($s['pertanyaan']); ?></td>
                        <td><small style="font-weight: 700; opacity: 0.7;"><?php echo strtoupper($mk_info['tipe_soal']); ?></small></td>
                        <td style="display: flex;">
                            <a href="edit_soal.php?id=<?php echo $s['id_soal']; ?>" class="btn-action" style="background: #f1c40f; color: black;"><i class="fa-solid fa-pen"></i></a>
                            <a href="proses_admin.php?aksi=hapus_soal&id=<?php echo $s['id_soal']; ?>&redirect=view_all&id_mk=<?php echo $id_mk; ?>" class="btn-action" style="background: #e74c3c; color: white;" onclick="return confirm('Hapus soal ini?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>