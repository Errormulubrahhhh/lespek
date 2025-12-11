<?php
require_once "connect.php";

// === DATA AMAN ===
$hari_ini = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total), 0) AS hari_ini FROM transactions WHERE DATE(tanggal) = CURDATE()"
))['hari_ini'];

$totalAll = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS jml, COALESCE(SUM(total),0) AS grand FROM transactions"
));

$jml_transaksi = $totalAll['jml'];
$omzet_keseluruhan = $totalAll['grand'];

// === GRAFIK 7 HARI ===
$query7hari = "
    SELECT DATE(tanggal) AS hari, COALESCE(SUM(total),0) AS total_harian
    FROM transactions
    WHERE tanggal >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(tanggal)
    ORDER BY hari ASC
";
$res = mysqli_query($conn, $query7hari);

$labels = [];
$dataPenjualan = [];

for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d/m', strtotime($tgl));
    $dataPenjualan[$tgl] = 0;
}
while ($row = mysqli_fetch_assoc($res)) {
    $dataPenjualan[$row['hari']] = (int)$row['total_harian'];
}
$nilaiPenjualan = array_values($dataPenjualan);

// === DATA BULAN SAAT INI ===
$bulan_saat_ini = date('Y-m');
$queryBulan = "
    SELECT DAY(tanggal) AS hari, COALESCE(SUM(total),0) AS total_harian
    FROM transactions
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
    GROUP BY DAY(tanggal)
    ORDER BY hari ASC
";

$stmt = $conn->prepare($queryBulan);
$stmt->bind_param("s", $bulan_saat_ini);
$stmt->execute();
$resBulan = $stmt->get_result();

$labelsBulan = [];
$dataBulan = [];
$hari_terakhir = (int)date('t'); // Jumlah hari di bulan ini

for ($i = 1; $i <= $hari_terakhir; $i++) {
    $labelsBulan[] = $i;
    $dataBulan[$i] = 0;
}

while ($row = mysqli_fetch_assoc($resBulan)) {
    $dataBulan[(int)$row['hari']] = (int)$row['total_harian'];
}
$nilaiDataBulan = array_values($dataBulan);
$stmt->close();

// === HISTORI ===
$histori = mysqli_query($conn, "SELECT * FROM transactions ORDER BY tanggal DESC");
?>

<h2 class="text-3xl font-bold text-amber-800 mb-6">Laporan Penjualan</h2>

<!-- RINGKASAN -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

    <div class="bg-amber-700 text-white rounded-xl shadow p-6 text-center">
        <h3 class="text-lg mb-1">Pendapatan Hari Ini</h3>
        <p class="text-3xl font-bold">Rp <?= number_format($hari_ini) ?></p>
        <p class="text-sm opacity-80 mt-1"><?= date('d F Y') ?></p>
    </div>

    <div class="bg-white rounded-xl shadow p-6 text-center border border-gray-200">
        <h3 class="text-lg mb-1 text-gray-700">Total Transaksi</h3>
        <p class="text-3xl font-bold text-amber-800"><?= $jml_transaksi ?> kali</p>
    </div>

    <div class="bg-gradient-to-br from-amber-700 to-amber-900 text-white rounded-xl shadow p-6 text-center">
        <h3 class="text-lg mb-1">Omzet Keseluruhan</h3>
        <p class="text-3xl font-bold">Rp <?= number_format($omzet_keseluruhan) ?></p>
    </div>
</div>

<!-- GRAFIK -->
<div class="bg-white shadow rounded-xl p-6 mb-10 border border-gray-200">
    <h3 class="font-bold text-lg text-amber-800 mb-4">Penjualan 7 Hari Terakhir</h3>
    <canvas id="chart7hari" height="100"></canvas>
</div>

<!-- GRAFIK BULANAN -->
<div class="bg-white shadow rounded-xl p-6 mb-10 border border-gray-200">
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-lg text-amber-800">Laporan Penjualan Bulanan</h3>
        <div class="flex gap-2 items-center">
            <label class="text-gray-700 font-semibold">Pilih Bulan:</label>
            <input type="month" id="bulanPilih" value="<?= date('Y-m') ?>" class="border border-gray-300 rounded px-3 py-2">
        </div>
    </div>
    <canvas id="chartBulan" height="100"></canvas>
</div>

<!-- HISTORI -->
<div class="bg-white shadow rounded-xl border border-gray-200">
    <div class="flex justify-between items-center bg-amber-700 text-white px-6 py-3 rounded-t-xl">
        <h3 class="font-bold">Histori Transaksi</h3>
        <div class="flex gap-2">
            <input type="date" id="mulai" class="border border-gray-300 text-black rounded px-2 py-1">
            <input type="date" id="sampai" class="border border-gray-300 text-black rounded px-2 py-1">
            <button onclick="cetakPDF()" class="bg-white text-black px-3 py-1 rounded">Cetak PDF</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-900 text-white">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody>

                <?php while ($h = mysqli_fetch_assoc($histori)): ?>
                <tr class="border-b">
                    <td class="px-4 py-3 font-bold">#<?= $h['id'] ?></td>
                    <td class="px-4 py-3"><?= date('d/m/Y H:i', strtotime($h['tanggal'])) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-white text-sm
                            <?= $h['metode_pembayaran']=='QRIS' ? 'bg-green-600' : 'bg-blue-600' ?>">
                            <?= $h['metode_pembayaran'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-amber-800">
                        Rp <?= number_format($h['total']) ?>
                    </td>
                </tr>
                <?php endwhile; ?>

                <?php if (mysqli_num_rows($histori) == 0): ?>
                <tr>
                    <td colspan="4" class="text-center py-4 text-gray-500">
                        Belum ada transaksi.
                    </td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- CHART SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart 7 hari
const ctx = document.getElementById('chart7hari').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Penjualan (Rp)',
            data: <?= json_encode($nilaiPenjualan) ?>,
            backgroundColor: '#A0522D',
            borderColor: '#8B4513',
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});

// Chart Bulanan
let chartBulan = null;

async function loadChartBulan(bulan) {
    try {
        // Fetch dari api file dengan path absolute
        const res = await fetch('/INDICA/api/data_bulanan.php?bulan=' + bulan);
        const data = await res.json();

        if (data.status !== 'success') {
            alert('Error: ' + data.message);
            return;
        }

        const ctx = document.getElementById('chartBulan').getContext('2d');
        
        // Destroy chart lama jika ada
        if (chartBulan) {
            chartBulan.destroy();
        }

        // Buat chart baru
        chartBulan = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Pendapatan Harian (Rp)',
                    data: data.values,
                    borderColor: '#A0522D',
                    backgroundColor: 'rgba(160, 82, 45, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#A0522D',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading data:', error);
        alert('Gagal memuat data bulan!');
    }
}

// Event listener untuk pilih bulan
document.getElementById('bulanPilih').addEventListener('change', (e) => {
    loadChartBulan(e.target.value);
});

// Load chart bulanan saat halaman dibuka
loadChartBulan('<?= date("Y-m") ?>');

function cetakPDF() {
    const m = document.getElementById('mulai').value;
    const s = document.getElementById('sampai').value;
    if (!m || !s) return alert("Masukkan tanggal!");
    window.open(`cetak_laporan.php?mulai=${m}&sampai=${s}`, "_blank");
}
</script>
