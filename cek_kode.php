<?php
header('Content-Type: application/json');
include 'koneksi.php';

$kode_input = $_POST['kode'] ?? '';
$id_mk = $_POST['id_mk'] ?? '';

if (empty($kode_input) || empty($id_mk)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$query = mysqli_query($conn, "SELECT kode_akses FROM mata_kuliah WHERE id_mk = '$id_mk'");
$data = mysqli_fetch_assoc($query);

if ($data && $kode_input === $data['kode_akses']) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Kode Akses Salah!']);
}
exit;