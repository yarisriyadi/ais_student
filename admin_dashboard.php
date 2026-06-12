<?php
include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=belum_login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AIS QUIZ</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('selected-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <link rel="stylesheet" href="style_theme.css">
    <style>
        * {
            transition: background 0.4s ease, color 0.4s ease, border-color 0.4s ease;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--body-bg); 
            color: var(--text-color); 
            margin: 0; 
            padding-bottom: 80px; 
        }
        .nav-admin { 
            background: var(--container-bg); 
            padding: 1rem 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid var(--border-color); 
            position: sticky; 
            top: 0; 
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .tab-nav { 
            background: var(--container-bg); 
            padding: 0 5%; 
            display: flex; 
            gap: 20px; 
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto; 
            scrollbar-width: none;
        }
        .tab-nav::-webkit-scrollbar { display: none; }
        .tab-item { 
            padding: 18px 10px; 
            cursor: pointer; 
            font-weight: 700; 
            opacity: 0.6; 
            border-bottom: 3px solid transparent; 
            white-space: nowrap;
            font-size: 14px;
            color: var(--text-color);
        }
        .tab-item.active { 
            opacity: 1; 
            border-color: #4361ee; 
            color: #4361ee; 
        }
        .tab-content { display: none; padding: 2rem 5%; max-width: 1300px; margin: auto; }
        .tab-content.active { display: block; }
        .grid-header { 
            display: grid; 
            grid-template-columns: 1fr 2fr; 
            gap: 25px; 
        }
        .card { 
            background: var(--container-bg); 
            border-radius: 20px; 
            border: 1px solid var(--border-color); 
            padding: 24px; 
            box-shadow: var(--card-shadow); 
            margin-bottom: 25px; 
        }
        input, select, textarea { 
            width: 100%; 
            padding: 14px; 
            margin-bottom: 15px; 
            border-radius: 12px; 
            border: 1px solid var(--border-color); 

            background: var(--input-bg); 

            color: var(--text-color); 

            font-size: 14px;

            font-family: inherit;

        }



        .btn-submit { 

            padding: 14px; 

            border: none; 

            border-radius: 12px; 

            cursor: pointer; 

            font-weight: 700; 

            width: 100%; 

            color: white;

            text-transform: uppercase;

            letter-spacing: 0.5px;

        }



        /* Table Responsive */

        .table-res-container { overflow-x: auto; }

        .table-res { width: 100%; border-collapse: collapse; margin-top: 15px; min-width: 600px; }

        .table-res th { text-align: left; padding: 15px; border-bottom: 2px solid var(--border-color); opacity: 0.7; font-size: 13px; }

        .table-res td { padding: 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; }



        /* Badge & Buttons */

        .badge-mk { background: #4361ee; color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }

        .btn-action { 

            width: 36px; height: 36px; 

            display: inline-flex; align-items: center; justify-content: center;

            border-radius: 10px; text-decoration: none; margin-right: 5px; 

            border: none; cursor: pointer; transition: 0.3s;

        }



        /* Theme Switcher Style (Sama dengan login.php) */

        .theme-switcher { position: fixed; bottom: 25px; left: 25px; z-index: 1001; }

        .theme-btn {

            width: 48px; height: 48px; border-radius: 14px;

            border: 1px solid var(--border-color); background: var(--container-bg);

            color: var(--text-color); cursor: pointer; display: flex;

            align-items: center; justify-content: center; box-shadow: var(--card-shadow);

        }

        .theme-btn:hover { 

            transform: translateY(-3px); 

            background-image: linear-gradient(90deg, #4361ee, #4cc9f0, #4361ee);

            color: white; border-color: transparent;

        }



        /* Responsive Mobile */

        @media (max-width: 768px) {

            .grid-header { grid-template-columns: 1fr; }

            .nav-admin { padding: 1rem 4%; }

            .nav-admin span { display: none; } /* Sembunyikan text "ADMIN" di mobile */

            .tab-nav { padding: 0 4%; }

        }

    </style>

</head>

<body>



<!-- Theme Switcher (Kiri Bawah) -->

<div class="theme-switcher">

    <button class="theme-btn" onclick="toggleTheme()" title="Ganti Tema">

        <i id="theme-icon" class="fa-solid"></i>

    </button>

</div>



<nav class="nav-admin">

    <div style="font-weight: 800; font-size: 1.3rem; color: #4361ee;">

        AIS <span style="color: var(--text-color);">QUIZ ADMIN</span>

    </div>

    <div>

        <a href="logout.php" style="color: #e74c3c; font-weight: 700; text-decoration: none; font-size: 14px;">

            <i class="fa-solid fa-right-from-bracket"></i> Keluar

        </a>

    </div>

</nav>



<div class="tab-nav">

    <div class="tab-item active" onclick="openTab(event, 'input-tab')"><i class="fa-solid fa-plus-circle"></i> Input Data</div>
    <div class="tab-item" onclick="openTab(event, 'soal-tab')"><i class="fa-solid fa-book"></i> Management Soal</div>
    <div class="tab-item" onclick="openTab(event, 'nilai-tab')"><i class="fa-solid fa-chart-simple"></i> Laporan Nilai</div>
    <div class="tab-item" onclick="openTab(event, 'user-tab')"><i class="fa-solid fa-users"></i> User</div>

</div>



<div id="input-tab" class="tab-content active">

    <!-- Notifikasi SweetAlert di-handle di bagian bawah script -->
    <?php if (isset($_GET['status'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const status = "<?php echo $_GET['status']; ?>";
                if (status === 'sukses_mk') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Mata Kuliah Berhasil Disimpan!', confirmButtonColor: '#4361ee' });
                } else if (status === 'sukses_soal') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Soal Berhasil Diposting!', confirmButtonColor: '#4361ee' });
                } else if (status === 'soal_diupdate') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Perubahan Soal Berhasil Disimpan!', confirmButtonColor: '#4361ee' });
                } else if (status === 'user_dihapus') {
                    Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'User Berhasil Dihapus!', confirmButtonColor: '#4361ee' });
                } else if (status === 'soal_dihapus') {
                    Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Soal Telah Dihapus dari Sistem!', confirmButtonColor: '#4361ee' });
                } else if (status === 'sukses_hapus_mk') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Mata Kuliah dan seluruh soal di dalamnya telah dihapus.', confirmButtonColor: '#4361ee' });
                }
                // Membersihkan URL
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        </script>
    <?php endif; ?>



    <div class="grid-header">

        <!-- Form MK -->

        <div class="card">

            <h3 style="margin-top:0;"><i class="fa-solid fa-folder-plus"></i> Aturan MK</h3>

            <form action="proses_admin.php?aksi=tambah_mk" method="POST">

                <input type="text" name="nama_mk" placeholder="Nama Mata Kuliah" required>

                <label style="font-size: 11px; font-weight: 700; opacity: 0.6; display:block; margin-bottom: 5px;">DURASI (MENIT)</label>

                <input type="number" name="durasi" placeholder="60" required>

                <select name="tipe_soal">

                    <option value="pg">Pilihan Ganda</option>

                    <option value="essai">Essai</option>

                </select>

                <textarea name="deskripsi" placeholder="Instruksi Ujian..."></textarea>

                <button type="submit" class="btn-submit" style="background: #2ecc71;">Simpan MK</button>

            </form>

        </div>



        <!-- Form Soal -->

        <div class="card">

            <h3 style="margin-top:0;"><i class="fa-solid fa-file-signature"></i> Buat Soal</h3>

            <form action="proses_admin.php?aksi=tambah_soal" method="POST">

                <select name="id_mk" required>

                    <option value="" disabled selected>Pilih Mata Kuliah</option>

                    <?php

                    $mk_list = mysqli_query($conn, "SELECT * FROM mata_kuliah");

                    while($m = mysqli_fetch_array($mk_list)) {

                        echo "<option value='".$m['id_mk']."'>".$m['nama_mk']."</option>";

                    }

                    ?>

                </select>

                <select name="tipe_soal_input" id="tipe_soal" onchange="checkType()">

                    <option value="pg">Pilihan Ganda (PG)</option>

                    <option value="essai">Essai</option>

                </select>

                <textarea name="pertanyaan" placeholder="Tulis soal di sini..." required style="height: 100px;"></textarea>

                

                <div id="opsi_container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">

                    <input type="text" name="a" placeholder="Opsi A">

                    <input type="text" name="b" placeholder="Opsi B">

                    <input type="text" name="c" placeholder="Opsi C">

                    <input type="text" name="d" placeholder="Opsi D">

                </div>



                <div id="kunci_container">

                    <select name="jawaban">

                        <option value="" disabled selected>Kunci Jawaban</option>

                        <option value="a">A</option><option value="b">B</option>

                        <option value="c">C</option><option value="d">D</option>

                    </select>

                </div>

                <button type="submit" class="btn-submit" style="background: #4361ee;">Posting Soal</button>

            </form>

        </div>

    </div>

</div>



<!-- Tab Management Soal -->
<div id="soal-tab" class="tab-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;"><i class="fa-solid fa-layer-group"></i> Management Soal</h2>
    </div>

    <?php
    $mk_groups = mysqli_query($conn, "SELECT * FROM mata_kuliah");
    while($group = mysqli_fetch_array($mk_groups)):
        $id_mk_now = $group['id_mk'];
        // Query dibatasi hanya 5 data terbaru
        $soal_query = mysqli_query($conn, "SELECT * FROM soal WHERE id_mk = '$id_mk_now' ORDER BY id_soal DESC LIMIT 5");
        $total_soal = mysqli_num_rows(mysqli_query($conn, "SELECT id_soal FROM soal WHERE id_mk = '$id_mk_now'"));
    ?>
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div>
                <h4 style="margin: 0; color: #4361ee;"><?php echo $group['nama_mk']; ?></h4>
                <small style="opacity: 0.6;"><?php echo strtoupper($group['tipe_soal']); ?> | <?php echo $group['durasi']; ?> Menit</small>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge-mk"><?php echo $total_soal; ?> Soal</span>
                <!-- Tombol Hapus Mata Kuliah -->
                <button type="button" 
                        class="btn-action" 
                        style="background: #e74c3c; color: white; width: 30px; height: 30px; font-size: 12px; border:none; cursor:pointer;" 
                        onclick="confirmDelete('proses_admin.php?aksi=hapus_mk&id=<?php echo $id_mk_now; ?>', 'Hapus Mata Kuliah ini beserta seluruh soal di dalamnya?')">
                     <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="table-res-container">
            <table class="table-res">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Pertanyaan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    if(mysqli_num_rows($soal_query) > 0){
                        while($s = mysqli_fetch_array($soal_query)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo (strlen($s['pertanyaan']) > 70) ? substr($s['pertanyaan'], 0, 70).'...' : $s['pertanyaan']; ?></td>
                        <td>
                            <a href="edit_soal.php?id=<?php echo $s['id_soal']; ?>" class="btn-action" style="background: #f1c40f; color: black;"><i class="fa-solid fa-pen"></i></a>
                            <button type="button" 
                                    class="btn-action" 
                                    style="background: #e74c3c; color: white; border:none; cursor:pointer;" 
                                    onclick="confirmDelete('proses_admin.php?aksi=hapus_soal&id=<?php echo $s['id_soal']; ?>', 'Hapus soal ini?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; opacity:0.5;'>Belum ada soal.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Lihat Semua -->
        <?php if($total_soal > 5): ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="view_all_soal.php?id_mk=<?php echo $id_mk_now; ?>" class="btn-submit" style="background: var(--input-bg); color: #4361ee; border: 1px solid #4361ee; text-decoration: none; padding: 8px 20px; font-size: 12px; display: inline-block; width: auto;">
                Lihat Semua Soal <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>
    
    <!-- Tab Laporan Nilai -->
<div id="nilai-tab" class="tab-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;"><i class="fa-solid fa-graduation-cap"></i> Hasil Ujian Mahasiswa</h2>
        <div style="display: flex; gap: 10px;">
            <!-- Tombol Generate Excel Baru -->
            <a href="export_excel.php" class="btn-action" style="background: #27ae60; color: white; width: auto; padding: 0 15px; font-size: 12px; text-decoration: none; display: flex; align-items: center; border-radius: 10px; height: 36px;">
                <i class="fa-solid fa-file-excel"></i> &nbsp; Export Excel
            </a>
            
            <button onclick="window.location.reload()" class="btn-action" style="background: #4361ee; color: white; width: auto; padding: 0 15px; font-size: 12px;">
                <i class="fa-solid fa-sync"></i> Refresh Data
            </button>
        </div>
    </div>

    <div class="card">
        <div class="table-res-container">
            <table class="table-res">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Mahasiswa</th>
                        <th>Mata Kuliah</th>
                        <th>Benar/Salah</th>
                        <th>Skor</th>
                        <th>Grade</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query disesuaikan dengan nama tabel 'nilai_ujian' dan kolom pada gambar Anda
                    $query_nilai = mysqli_query($conn, "SELECT n.*, u.nama_lengkap, m.nama_mk 
                                                      FROM nilai_ujian n
                                                      JOIN users u ON n.id_user = u.id
                                                      JOIN mata_kuliah m ON n.id_mk = m.id_mk
                                                      ORDER BY n.tanggal_ujian DESC");
                    
                    $no_n = 1;
                    if(mysqli_num_rows($query_nilai) > 0) {
                        while($n = mysqli_fetch_array($query_nilai)):
                            // Logika penentuan Grade
                            $skor = $n['skor'];
                            $grade = 'E';
                            $color = '#e74c3c';

                            if($skor >= 85) { $grade = 'A'; $color = '#2ecc71'; }
                            else if($skor >= 75) { $grade = 'B'; $color = '#3498db'; }
                            else if($skor >= 60) { $grade = 'C'; $color = '#f1c40f'; }
                            else if($skor >= 40) { $grade = 'D'; $color = '#e67e22'; }
                        	else if($skor >= 30) { $grade = 'E'; $color = '#ef4444'; }
                    ?>
                    <tr>
                        <td><?php echo $no_n++; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($n['nama_lengkap']); ?></strong>
                        </td>
                        <td><span class="badge-mk"><?php echo $n['nama_mk']; ?></span></td>
                        <td>
                            <span style="color: #2ecc71;">B: <?php echo $n['jumlah_benar']; ?></span> / 
                            <span style="color: #e74c3c;">S: <?php echo $n['jumlah_salah']; ?></span>
                        </td>
                        <td><strong style="font-size: 1.1rem;"><?php echo $skor; ?></strong></td>
                        <td>
                            <span style="background: <?php echo $color; ?>; color: white; padding: 4px 10px; border-radius: 6px; font-weight: 800;">
                                <?php echo $grade; ?>
                            </span>
                        </td>
                        <td style="font-size: 12px; opacity: 0.8;">
                            <?php echo date('d M Y', strtotime($n['tanggal_ujian'])); ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding: 40px; opacity:0.5;'>Belum ada data nilai di tabel nilai_ujian.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<!-- Tab User -->

<div id="user-tab" class="tab-content">

    <h2><i class="fa-solid fa-users"></i> Manajemen User</h2>

    <div class="card">

        <div class="table-res-container">

            <table class="table-res">

                <thead>

                    <tr>

                        <th width="50">No</th>

                        <th>Nama</th>

                        <th>Username</th>

                        <th>Role</th>

                        <th width="120">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    <?php 

                    $user_query = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");

                    $no_u = 1;

                    while($u = mysqli_fetch_array($user_query)): 

                    ?>

                    <tr>

                        <td><?php echo $no_u++; ?></td>

                        <td><?php echo htmlspecialchars($u['nama_lengkap']); ?></td>

                        <td><?php echo htmlspecialchars($u['username']); ?></td>

                        <td><span class="badge-mk" style="background: <?php echo $u['role']=='admin'?'#e74c3c':'#4361ee'; ?>"><?php echo strtoupper($u['role']); ?></span></td>

                        <td>

                            <button onclick="changePassword(<?php echo $u['id']; ?>, '<?php echo $u['username']; ?>')" class="btn-action" style="background: #f1c40f; color: black;"><i class="fa-solid fa-key"></i></button>

                            <?php if($u['id'] != $_SESSION['id']): ?>

                            <button type="button" 
                                    class="btn-action" 
                                    style="background: #e74c3c; color: white; border:none; cursor:pointer;" 
                                    onclick="confirmDelete('proses_admin.php?aksi=hapus_user&id=<?php echo $u['id']; ?>', 'Hapus user ini?')">
                                <i class="fa-solid fa-user-minus"></i>
                            </button>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>



<script src="theme_script.js"></script>

<script>

    function openTab(evt, tabName) {

        var i, tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tab-content");

        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; tabcontent[i].classList.remove("active"); }

        tablinks = document.getElementsByClassName("tab-item");

        for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }

        document.getElementById(tabName).style.display = "block";

        document.getElementById(tabName).classList.add("active");

        evt.currentTarget.className += " active";

    }



    function checkType() {

        var tipe = document.getElementById("tipe_soal").value;

        document.getElementById("opsi_container").style.display = (tipe === "essai") ? "none" : "grid";

        document.getElementById("kunci_container").style.display = (tipe === "essai") ? "none" : "block";

    }



    function changePassword(id, username) {

        Swal.fire({
            title: 'Ubah Password',
            text: 'Masukkan password baru untuk ' + username,
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            confirmButtonColor: '#4361ee',
            showLoaderOnConfirm: true,
            preConfirm: (newPass) => {
                if (!newPass) {
                    Swal.showValidationMessage('Password tidak boleh kosong');
                }
                return newPass;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "proses_admin.php?aksi=ubah_password_user&id=" + id + "&pass=" + encodeURIComponent(result.value);
            }
        });

    }

    // Fungsi Konfirmasi Hapus SweetAlert
    function confirmDelete(url, text) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4361ee',
            cancelButtonColor: '#e74c3c',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }



    // Theme Icon Sync (Sama dengan login.php)

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

        window.toggleTheme = function() {

            originalToggle();

            syncThemeIcon();

        };

    });

</script>

</body>

</html>