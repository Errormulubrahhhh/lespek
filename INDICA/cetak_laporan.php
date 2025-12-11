<?php
require_once "connect.php";
require_once "vendor/autoload.php";   // ← ini yang penting!

use Dompdf\Dompdf;
use Dompdf\Options;

// Aktifkan gambar eksternal (untuk QRIS, logo, dll)
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

// Ambil tanggal
$mulai  = $_GET['mulai'] ?? date('Y-m-d', strtotime('-7 days'));
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Query transaksi
$query = "SELECT * FROM transactions WHERE DATE(tanggal) BETWEEN ? AND ? ORDER BY tanggal DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $mulai, $sampai);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 30px; }
        h1 { color: #8B4513; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #8B4513; color: white; padding: 12px; }
        td { padding: 10px; border: 1px solid #ccc; }
        .text-right { text-align: right; }
        .total { background: #f0f0f0; font-weight: bold; font-size: 1.2em; }
    </style>
</head>
<body>
    <h1>INDICA KASIR - Laporan Penjualan</h1>
    <p style="text-align:center;">
        Periode: ' . date('d M Y', strtotime($mulai)) . ' - ' . date('d M Y', strtotime($sampai)) . '
    </p>

    <table>
        <thead>
            <tr>
                <th width="80">ID</th>
                <th>Tanggal & Jam</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>';

while ($row = $result->fetch_assoc()) {
    $total += $row['total'];
    $html .= "<tr>
        <td>#{$row['id']}</td>
        <td>" . date('d/m/Y H:i', strtotime($row['tanggal'])) . "</td>
        <td>{$row['metode_pembayaran']}</td>
        <td class='text-right'>Rp " . number_format($row['total']) . "</td>
    </tr>";
}

$html .= '
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp ' . number_format($total) . '</td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align:right; margin-top:40px; color:#666;">
        Dicetak pada: ' . date('d/m/Y H:i') . '
    </p>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Indica_" . date('Ymd_His') . ".pdf", ["Attachment" => true]);