<?php
require_once "../connect.php";
header('Content-Type: application/json; charset=utf-8');

// Baca JSON dari request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "ID produk tidak ditemukan"
    ]);
    exit;
}

$product_id = intval($data['id']);

// Cek apakah produk ada
$checkStmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
$checkStmt->bind_param("i", $product_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Produk tidak ditemukan"
    ]);
    $checkStmt->close();
    exit;
}

$checkStmt->close();

// Hapus produk
$deleteStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$deleteStmt->bind_param("i", $product_id);

if ($deleteStmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Produk berhasil dihapus"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menghapus produk: " . $deleteStmt->error
    ]);
}

$deleteStmt->close();
$conn->close();
?>
