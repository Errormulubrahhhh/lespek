<?php
session_start();
require_once "../connect.php";
header('Content-Type: application/json; charset=utf-8');

$bulan = isset($_GET['bulan']) ? trim($_GET['bulan']) : date('Y-m');

// Validasi format YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) {
    echo json_encode([
        "status" => "error",
        "message" => "Format bulan tidak valid"
    ]);
    exit;
}

// Query untuk ambil data per hari dalam bulan tersebut
$query = "
    SELECT DAY(tanggal) AS hari, COALESCE(SUM(total), 0) AS total_harian
    FROM transactions
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
    GROUP BY DAY(tanggal)
    ORDER BY hari ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $bulan);
$stmt->execute();
$result = $stmt->get_result();

// Hitung jumlah hari di bulan tersebut
$tgl_akhir = new DateTime($bulan . '-01');
$tgl_akhir->modify('last day of this month');
$hari_terakhir = (int)$tgl_akhir->format('d');

// Inisialisasi array dengan 0 untuk semua hari
$labels = [];
$values = [];
for ($i = 1; $i <= $hari_terakhir; $i++) {
    $labels[] = $i;
    $values[$i] = 0;
}

// Isi dengan data dari database
while ($row = $result->fetch_assoc()) {
    $values[(int)$row['hari']] = (int)$row['total_harian'];
}

// Convert ke array berurutan
$nilaiAkhir = array_values($values);

// Hitung total dan rata-rata bulan ini
$queryTotal = "
    SELECT 
        COALESCE(SUM(total), 0) AS total_bulan,
        COUNT(*) AS jml_transaksi
    FROM transactions
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
";

$stmtTotal = $conn->prepare($queryTotal);
$stmtTotal->bind_param("s", $bulan);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalResult = $resultTotal->fetch_assoc();

$stmt->close();
$stmtTotal->close();
$conn->close();

echo json_encode([
    "status" => "success",
    "bulan" => $bulan,
    "labels" => $labels,
    "values" => $nilaiAkhir,
    "total" => (int)$totalResult['total_bulan'],
    "jumlah_transaksi" => (int)$totalResult['jml_transaksi']
]);
?>
