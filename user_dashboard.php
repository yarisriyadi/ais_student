<?php 
include 'koneksi.php';
if ($_SESSION['role'] != 'user') header("Location: index.php");
?>
<h1>Selamat Datang, <?php echo $_SESSION['username']; ?></h1>
<h3>Pilih Mata Kuliah Ujian:</h3>
<ul>
    <?php
    $mk = mysqli_query($conn, "SELECT * FROM matakuliah");
    while($r = mysqli_fetch_assoc($mk)) {
        echo "<li>".$r['nama_mk']." <a href='ujian.php?id=".$r['id']."'>Mulai Ujian</a></li>";
    }
    ?>
</ul>
<a href="logout.php">Logout</a>