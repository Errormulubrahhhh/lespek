<?php
require_once "../connect.php";

// Ambil JSON yang dikirim lewat fetch()
$data = json_decode(file_get_contents("php://input"), true);

// Validasi JSON
if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON data"
    ]);
    exit;
}

$id        = $data['id'] ?? "";
$nama      = $data['nama'] ?? "";
$kategori  = $data['kategori_id'] ?? "";
$harga     = $data['harga'] ?? "";
$stok      = $data['stok'] ?? "";

// Cek ID
if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID tidak ditemukan"
    ]);
    exit;
}

// Query update
$q = mysqli_query($conn,
    "UPDATE products SET 
        nama='$nama',
        kategori_id='$kategori',
        harga='$harga',
        stok='$stok'
     WHERE id='$id'"
);

// Response JSON
if ($q) {
    echo json_encode([
        "status" => "success",
        "message" => "Menu berhasil diperbarui"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal update"
    ]);
}
?>
