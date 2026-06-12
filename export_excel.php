<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    exit;
}

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Nilai_Per_Matakuliah_".date('d-m-Y').".xls");
?>

<center>
    <h2>LAPORAN HASIL UJIAN MAHASISWA</h2>
</center>

<table border="1">
    <thead>
        <tr style="background-color: #e0e0e0;">
            <th>No</th>
            <th>Nama Mahasiswa</th>
            <th>Benar</th>
            <th>Salah</th>
            <th>Skor</th>
            <th>Grade</th>
            <th>Tanggal Ujian</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query_nilai = mysqli_query($conn, "SELECT n.*, u.nama_lengkap, m.nama_mk 
                                            FROM nilai_ujian n
                                            JOIN users u ON n.id_user = u.id
                                            JOIN mata_kuliah m ON n.id_mk = m.id_mk
                                            ORDER BY m.nama_mk ASC, n.skor DESC");
        
        $current_mk = "";
        $no = 1;

        while($n = mysqli_fetch_array($query_nilai)) {
            if ($current_mk != $n['nama_mk']) {
                $current_mk = $n['nama_mk'];
                $no = 1; // Reset nomor urut untuk mata kuliah baru
                ?>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="7" style="font-weight: bold; text-align: left; padding: 10px;">
                        MATA KULIAH: <?php echo strtoupper($current_mk); ?>
                    </td>
                </tr>
                <?php
            }

            // Logika Grade
            $skor = $n['skor'];
            $grade = 'E';
            if($skor >= 85) $grade = 'A';
            else if($skor >= 75) $grade = 'B';
            else if($skor >= 60) $grade = 'C';
            else if($skor >= 40) $grade = 'D';
            ?>
            <tr>
                <td align="center"><?php echo $no++; ?></td>
                <td><?php echo $n['nama_lengkap']; ?></td>
                <td align="center"><?php echo $n['jumlah_benar']; ?></td>
                <td align="center"><?php echo $n['jumlah_salah']; ?></td>
                <td align="center"><?php echo $n['skor']; ?></td>
                <td align="center"><?php echo $grade; ?></td>
                <td><?php echo date('d-m-Y', strtotime($n['tanggal_ujian'])); ?></td>
            </tr>
            <?php 
        } 
        ?>
    </tbody>
</table>