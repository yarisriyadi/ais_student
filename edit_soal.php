<?php
include 'koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=belum_login");
    exit;
}

if (!isset($_GET['id'])) {
    header("location:admin_dashboard.php");
    exit;
}

$id_soal = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM soal WHERE id_soal = '$id_soal'");
$data = mysqli_fetch_array($query);

if (!$data) {
    echo "Soal tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Soal - AIS QUIZ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style_theme.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: var(--body-bg); color: var(--text-color); margin: 0; }
        .container { padding: 2rem 5%; max-width: 800px; margin: auto; }
        .card { background: var(--container-bg); border-radius: 12px; border: 1px solid var(--border-color); padding: 30px; box-shadow: var(--card-shadow); }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--body-bg); color: var(--text-color); box-sizing: border-box; }
        .btn-group { display: flex; gap: 10px; }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; text-decoration: none; text-align: center; flex: 1; }
        .btn-save { background: #3498db; color: white; }
        .btn-back { background: #95a5a6; color: white; }
        h2 { color: #3498db; margin-top: 0; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
    </style>
    <script>
        // Fungsi untuk menyembunyikan/menampilkan opsi berdasarkan tipe soal
        function checkType() {
            var tipe = document.getElementById("tipe_soal").value;
            var opsiSection = document.getElementById("opsi_section");
            var kunciSection = document.getElementById("kunci_section");
            
            if (tipe === "essai") {
                opsiSection.style.display = "none";
                kunciSection.style.display = "none";
            } else {
                opsiSection.style.display = "grid";
                kunciSection.style.display = "block";
            }
        }
    </script>
</head>
<body onload="checkType()">

<div class="container">
    <div class="card">
        <h2><i class="fa-solid fa-pen-to-square"></i> Edit Soal</h2>
        <form action="proses_admin.php?aksi=edit_soal" method="POST">
            <input type="hidden" name="id_soal" value="<?php echo $data['id_soal']; ?>">

            <label>Mata Kuliah</label>
            <select name="id_mk" required>
                <?php
                $mk_list = mysqli_query($conn, "SELECT * FROM mata_kuliah");
                while($m = mysqli_fetch_array($mk_list)) {
                    $selected = ($m['id_mk'] == $data['id_mk']) ? "selected" : "";
                    echo "<option value='".$m['id_mk']."' $selected>".$m['nama_mk']."</option>";
                }
                ?>
            </select>
            <label><i class="fa-solid fa-clock"></i> Durasi Pengerjaan (Menit)</label>
<input type="number" name="durasi_mk" value="<?php echo $data['durasi']; ?>" min="1" required>
<small style="display:block; margin-top:-10px; margin-bottom:15px; color: #7f8c8d;">
    *Mengubah ini akan merubah durasi untuk semua soal di mata kuliah ini.
</small>

            <label>Tipe Soal</label>
            <select name="tipe_soal" id="tipe_soal" onchange="checkType()" required>
                <option value="pg" <?php if($data['tipe_soal'] == 'pg') echo 'selected'; ?>>Pilihan Ganda</option>
                <option value="essai" <?php if($data['tipe_soal'] == 'essai') echo 'selected'; ?>>Essai</option>
            </select>

            <label>Pertanyaan</label>
            <textarea name="pertanyaan" rows="4" required><?php echo $data['pertanyaan']; ?></textarea>

            <!-- Bagian Opsi Jawaban -->
            <div id="opsi_section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label>Opsi A</label>
                    <input type="text" name="a" value="<?php echo $data['opsi_a']; ?>">
                </div>
                <div>
                    <label>Opsi B</label>
                    <input type="text" name="b" value="<?php echo $data['opsi_b']; ?>">
                </div>
                <div>
                    <label>Opsi C</label>
                    <input type="text" name="c" value="<?php echo $data['opsi_c']; ?>">
                </div>
                <div>
                    <label>Opsi D</label>
                    <input type="text" name="d" value="<?php echo $data['opsi_d']; ?>">
                </div>
            </div>

            <!-- Bagian Kunci Jawaban -->
            <div id="kunci_section">
                <label>Kunci Jawaban</label>
                <select name="jawaban">
                    <option value="a" <?php if($data['jawaban'] == 'a') echo 'selected'; ?>>Kunci: A</option>
                    <option value="b" <?php if($data['jawaban'] == 'b') echo 'selected'; ?>>Kunci: B</option>
                    <option value="c" <?php if($data['jawaban'] == 'c') echo 'selected'; ?>>Kunci: C</option>
                    <option value="d" <?php if($data['jawaban'] == 'd') echo 'selected'; ?>>Kunci: D</option>
                </select>
            </div>

            <div class="btn-group">
                <a href="admin_dashboard.php" class="btn btn-back">Batal</a>
                <button type="submit" class="btn btn-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="theme_script.js"></script>
</body>
</html>