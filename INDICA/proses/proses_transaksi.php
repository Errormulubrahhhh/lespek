<?php
// proses/proses_transaksi.php - perbaikan: menerima form-urlencoded atau JSON, menggunakan mysqli dari connect.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connect.php'; // connect.php menyediakan $conn (mysqli)

// Terima data: bisa JSON di body atau form-urlencoded ($_POST)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !is_array($data)) {
    // fallback ke _POST
    $data = $_POST;
}

// Validasi sederhana
if (!isset($data['metode']) || !isset($data['total']) || !isset($data['detail'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Ambil nilai
$metode = $conn->real_escape_string($data['metode']);
$total = (int)$data['total'];
$detail_raw = $data['detail'];

// Jika detail dikirim sebagai JSON string (dari localStorage), decode lagi
if (is_string($detail_raw)) {
    $detail = json_decode(urldecode($detail_raw), true);
    if (!is_array($detail)) {
        // coba langsung json_decode tanpa urldecode
        $detail = json_decode($detail_raw, true);
    }
} else {
    $detail = $detail_raw;
}

if (!is_array($detail) || count($detail) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Detail transaksi kosong atau tidak valid']);
    exit;
}

// Mulai transaksi MySQL
$conn->begin_transaction();

try {
    // user_id dari session, jika ada
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    // Simpan ke tabel transactions
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, metode_pembayaran, total, status, catatan) VALUES (?, ?, ?, 'berhasil', ?)");
    $catatan = null;
    $stmt->bind_param("isis", $user_id, $metode, $total, $catatan);
    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan transaksi: " . $stmt->error);
    }
    $transactionId = $conn->insert_id;
    $stmt->close();

        // Simpan detail dan update stok
    $stmtDetail = $conn->prepare("INSERT INTO transaction_details 
        (transaction_id, product_id, nama_produk, harga, jumlah, subtotal, variant) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmtUpdateStock = $conn->prepare("UPDATE products SET stok = stok - ? WHERE id = ? AND stok >= ?");
    $stmtStockLog = $conn->prepare("INSERT INTO stock_logs (product_id, tipe, jumlah, keterangan) VALUES (?, 'keluar', ?, ?)");

    foreach ($detail as $item) {
        $product_id = isset($item['id']) ? (int)$item['id'] : null;
        $nama = $item['nama'] ?? '';
        $harga = (int)($item['harga'] ?? 0);
        $qty = (int)($item['qty'] ?? 1);
        $subtotal = $harga * $qty;
        $variant = $item['variant'] ?? null;

        // INSERT DETAIL (7 kolom, 7 parameter - tambah variant)
        $stmtDetail->bind_param("iisiiis", $transactionId, $product_id, $nama, $harga, $qty, $subtotal, $variant);
        if (!$stmtDetail->execute()) {
            throw new Exception("Gagal menyimpan detail: " . $stmtDetail->error);
        }

        // Update stok
        if ($product_id) {
            $stmtUpdateStock->bind_param("iii", $qty, $product_id, $qty);
            if (!$stmtUpdateStock->execute() || $stmtUpdateStock->affected_rows === 0) {
                throw new Exception("Stok produk ID $product_id tidak mencukupi");
            }

            // Log stok
            $keterangan = "Penjualan (transaksi #$transactionId)";
            $stmtStockLog->bind_param("iis", $product_id, $qty, $keterangan);
            $stmtStockLog->execute(); // tidak perlu cek error, opsional
        }
    }

    // Commit jika semua sukses
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Transaksi gagal: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
    exit;
}
