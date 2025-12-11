<?php
require_once "../connect.php";

// Baca JSON dari fetch()
$data = json_decode(file_get_contents("php://input"), true);

// Jika JSON gagal dibaca
if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON data"
    ]);
    exit;
}

$action = $data["action"] ?? "";
$nama   = $data["nama"] ?? "";
$kategori = $data["kategori_id"] ?? "";
$harga  = $data["harga"] ?? 0;
$harga_iced = $data["harga_iced"] ?? null;
$stok   = $data["stok"] ?? 0;
$has_variant = $data["has_variant"] ?? "no";

// --- TAMBAH MENU ---
if ($action === "add") {

    // Jika harga_iced kosong, set null
    $harga_iced_val = ($harga_iced && $harga_iced != "") ? "'".$harga_iced."'" : "NULL";
    
    $q = mysqli_query($conn,
        "INSERT INTO products (nama, kategori_id, harga, harga_iced, stok, has_variant) 
         VALUES ('$nama', '$kategori', '$harga', $harga_iced_val, '$stok', '$has_variant')"
    );

    if ($q) {
        echo json_encode([
            "status" => "success",
            "message" => "Menu berhasil ditambahkan"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menambah menu: " . $conn->error
        ]);
    }
    exit;
}

// --- EDIT MENU ---
if ($action === "update") {

    $id = $data["id"] ?? "";

    if (!$id) {
        echo json_encode([
            "status" => "error",
            "message" => "ID menu tidak ditemukan"
        ]);
        exit;
    }

    // Jika harga_iced kosong, set null
    $harga_iced_val = ($harga_iced && $harga_iced != "") ? "'".$harga_iced."'" : "NULL";

    $q = mysqli_query($conn,
        "UPDATE products 
         SET nama = '$nama', kategori_id = '$kategori', harga = '$harga', harga_iced = $harga_iced_val, stok = '$stok', has_variant = '$has_variant'
         WHERE id = '$id'"
    );

    if ($q) {
        echo json_encode([
            "status" => "success",
            "message" => "Menu berhasil diupdate"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal mengupdate menu: " . $conn->error
        ]);
    }
    exit;
}

// Jika action tidak dikenali
echo json_encode([
    "status" => "error",
    "message" => "Aksi tidak valid"
]);
exit;
?>
