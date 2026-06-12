<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=belum_login");
    exit;
}

if (isset($_POST['update_pw'])) {
    $id_user = $_POST['id_user'];
    $pw_baru = $_POST['password'];
    $pw_secure = md5($pw_baru); 
    $query_update = mysqli_query($conn, "UPDATE user SET password='$pw_secure' WHERE id='$id_user'");

    if ($query_update) {
        echo "<script>
                alert('Password user berhasil diperbarui!');
                window.location='dashboard_admin.php';
              </script>";
    } else {
        echo "<script>alert('Gagal memperbarui password: " . mysqli_error($conn) . "');</script>";
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $get_user = mysqli_query($conn, "SELECT * FROM user WHERE id='$id'");
    $data = mysqli_fetch_array($get_user);

    if (!$data) {
        header("location:dashboard_admin.php");
        exit;
    }
} else {
    header("location:dashboard_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password User - Admin Suite</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style_theme.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--body-bg); 
            color: var(--text-color); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .card-reset { 
            background: var(--container-bg); 
            padding: 40px; 
            border-radius: 16px; 
            border: 1px solid var(--border-color); 
            width: 100%; 
            max-width: 420px; 
            box-shadow: var(--card-shadow); 
            text-align: center;
        }
        .user-icon {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 15px;
        }
        h2 { margin-bottom: 5px; }
        p { opacity: 0.6; margin-bottom: 25px; }
        
        input { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 20px; 
            border-radius: 8px; 
            border: 1px solid var(--border-color); 
            background: var(--body-bg); 
            color: var(--text-color); 
            box-sizing: border-box; 
            font-size: 1rem;
        }
        .btn-save { 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 8px; 
            width: 100%; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.3s;
        }
        .btn-save:hover { background: #2980b9; }
        .btn-cancel { 
            display: inline-block; 
            margin-top: 20px; 
            color: #e74c3c; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="card-reset">
    <div class="user-icon"><i class="fa-solid fa-circle-user"></i></div>
    <h2>Atur Ulang Password</h2>
    <p>Mengubah password untuk user: <br><strong>@<?php echo $data['username']; ?></strong></p>
    
    <form action="" method="POST">
        <input type="hidden" name="id_user" value="<?php echo $data['id']; ?>">
        
        <input type="password" name="password" placeholder="Masukkan password baru..." required minlength="5" autofocus>
        
        <button type="submit" name="update_pw" class="btn-save">
            <i class="fa-solid fa-check-double"></i> Simpan Perubahan
        </button>
        
        <a href="dashboard_admin.php" class="btn-cancel">Batal dan Kembali</a>
    </form>
</div>

<script src="theme_script.js"></script>
</body>
</html>