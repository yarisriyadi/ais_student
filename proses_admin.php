<?php
// 1. Inisialisasi & Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 2. Proteksi Halaman
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=belum_login");
    exit;
}

// 3. Cek Aksi
if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];

    // --- AKSI: TAMBAH MATA KULIAH ---
    if ($aksi == "tambah_mk" && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama   = mysqli_real_escape_string($conn, $_POST['nama_mk']);
        $durasi = intval($_POST['durasi']);
        $tipe   = mysqli_real_escape_string($conn, $_POST['tipe_soal']);
        $desc   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        
        $query = "INSERT INTO mata_kuliah (nama_mk, deskripsi, durasi, tipe_soal) VALUES ('$nama', '$desc', '$durasi', '$tipe')";
        if (mysqli_query($conn, $query)) {
            header("location:admin_dashboard.php?status=sukses_mk");
        } else {
            header("location:admin_dashboard.php?status=gagal");
        }
        exit;
    }

    // --- AKSI: HAPUS MATA KULIAH (TAMBAHAN BARU) ---
    if ($aksi == "hapus_mk" && isset($_GET['id'])) {
        $id_mk = intval($_GET['id']);

        // Agar database bersih, hapus data terkait di tabel lain terlebih dahulu
        // 1. Hapus nilai ujian terkait MK ini
        mysqli_query($conn, "DELETE FROM nilai_ujian WHERE id_mk = $id_mk");
        // 2. Hapus soal terkait MK ini
        mysqli_query($conn, "DELETE FROM soal WHERE id_mk = $id_mk");
        
        // 3. Hapus Mata Kuliah utama
        $query = mysqli_query($conn, "DELETE FROM mata_kuliah WHERE id_mk = $id_mk");

        if ($query) {
            header("location:admin_dashboard.php?status=soal_dihapus"); // Menggunakan status yang sudah ada atau bisa buat baru
        } else {
            header("location:admin_dashboard.php?status=gagal");
        }
        exit;
    }

    // --- AKSI: TAMBAH SOAL BARU ---
    if ($aksi == "tambah_soal" && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_mk      = intval($_POST['id_mk']);
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $a          = mysqli_real_escape_string($conn, $_POST['a']);
        $b          = mysqli_real_escape_string($conn, $_POST['b']);
        $c          = mysqli_real_escape_string($conn, $_POST['c']);
        $d          = mysqli_real_escape_string($conn, $_POST['d']);
        $kunci      = mysqli_real_escape_string($conn, $_POST['jawaban']);

        $sql = "INSERT INTO soal (id_mk, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) 
                VALUES ('$id_mk', '$pertanyaan', '$a', '$b', '$c', '$d', '$kunci')";

        if (mysqli_query($conn, $sql)) {
            header("location:admin_dashboard.php?status=sukses_soal");
        } else {
            header("location:admin_dashboard.php?status=gagal");
        }
        exit;
    }

    // --- AKSI: EDIT SOAL ---
    if ($aksi == "edit_soal" && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_soal    = intval($_POST['id_soal']);
        $id_mk      = intval($_POST['id_mk']);
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $a          = mysqli_real_escape_string($conn, $_POST['a']);
        $b          = mysqli_real_escape_string($conn, $_POST['b']);
        $c          = mysqli_real_escape_string($conn, $_POST['c']);
        $d          = mysqli_real_escape_string($conn, $_POST['d']);
        $kunci      = mysqli_real_escape_string($conn, $_POST['jawaban']);
        $durasi_baru = intval($_POST['durasi_mk']);

        // Update data soal
        $sql_soal = "UPDATE soal SET 
                    id_mk = '$id_mk', 
                    pertanyaan = '$pertanyaan', 
                    opsi_a = '$a', 
                    opsi_b = '$b', 
                    opsi_c = '$c', 
                    opsi_d = '$d', 
                    jawaban_benar = '$kunci' 
                    WHERE id_soal = '$id_soal'";

        // Update durasi MK
        $sql_mk = "UPDATE mata_kuliah SET durasi = '$durasi_baru' WHERE id_mk = '$id_mk'";

        if (mysqli_query($conn, $sql_soal) && mysqli_query($conn, $sql_mk)) {
            header("location:admin_dashboard.php?status=soal_diupdate");
        } else {
            header("location:admin_dashboard.php?status=gagal");
        }
        exit;
    }

    // --- AKSI: HAPUS SOAL ---
    if ($aksi == "hapus_soal" && isset($_GET['id'])) {
        $id_soal = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM soal WHERE id_soal = ?");
        $stmt->bind_param("i", $id_soal);
        
        if ($stmt->execute()) {
            header("location:admin_dashboard.php?status=soal_dihapus");
        } else {
            header("location:admin_dashboard.php?status=gagal");
        }
        $stmt->close();
        exit;
    }

    // --- AKSI: HAPUS USER ---
    if ($aksi == 'hapus_user' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $session_id = $_SESSION['id_user'] ?? 0;

        if ($id == $session_id) {
            header("location:admin_dashboard.php?status=gagal_hapus_diri");
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("location:admin_dashboard.php?status=user_dihapus");
        } else {
            header("location:admin_dashboard.php?status=gagal");
        }
        $stmt->close();
        exit;
    }

    // --- AKSI: UBAH PASSWORD USER ---
    if ($aksi == 'ubah_password_user' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $pass_baru = mysqli_real_escape_string($conn, $_GET['pass']);
        $hashed_pass = password_hash($pass_baru, PASSWORD_DEFAULT); 
        
        $query = "UPDATE users SET password = '$hashed_pass' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            header("location:admin_dashboard.php?status=password_diupdate");
        } else {
            header("location:admin_dashboard.php?status=gagal");
        }
        exit;
    }
}

// Default redirect
header("location:admin_dashboard.php");
exit;