<?php
require_once "connect.php";

// Statistik
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM transactions"))['t'] ?? 0;
$total_income = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS t FROM transactions"))['t'] ?? 0;
$produk_terjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS t FROM transaction_details"))['t'] ?? 0;

$rata = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT AVG(x.total_harian) AS r FROM (
        SELECT DATE(tanggal) AS tgl, SUM(total) AS total_harian
        FROM transactions
        WHERE tanggal >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(tanggal)
    ) x
"))['r'] ?? 0;
?>

<!-- SEMUA STYLE HANYA BERLAKU DI DALAM .home-wrapper -->
<style>
.home-wrapper {
    padding: 25px 30px;
    font-family: "Inter", sans-serif;
    color: #222;
    background: #f7f8fa;
}

/* HEADER */
.home-wrapper .header-title {
    font-size: 35px;
    font-weight: 800;
    letter-spacing: -1px;
    background: linear-gradient(90deg, rgb(120,36,0), #ff8ccf, #9b5dff);
    -webkit-background-clip: text;
    color: transparent;
}

.home-wrapper .subtitle {
    font-size: 18px;
    color: #777;
    margin-top: -4px;
    margin-bottom: 35px;
}

/* GRID */
.home-wrapper .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

/* CARD */
.home-wrapper .card {
    background: rgba(255,255,255,0.75);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(200,200,200,0.4);
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    transition: 0.25s ease;
}
.home-wrapper .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 30px rgba(0,0,0,0.08);
}

/* ICON */
.home-wrapper .card-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
}

.icon-gradient-1 {
    background: linear-gradient(135deg, #ff8cab, #ff6db7);
}
.icon-gradient-2 {
    background: linear-gradient(135deg, #ffb46c, #ff9f4f);
}
.icon-gradient-3 {
    background: linear-gradient(135deg, #6bb6ff, #4891ff);
}
.icon-gradient-4 {
    background: linear-gradient(135deg, #a173ff, #8b4dff);
}

.home-wrapper .card-icon svg {
    width: 24px;
    height: 24px;
    color: #fff;
}

/* TEXT */
.home-wrapper .card-title {
    font-size: 15px;
    color: #666;
    font-weight: 600;
    margin-bottom: 4px;
}
.home-wrapper .card-value {
    font-size: 28px;
    font-weight: 800;
    color: #222;
}

/* SECTION */
.home-wrapper .section-title {
    margin-top: 50px;
    margin-bottom: 20px;
    font-size: 22px;
    font-weight: 700;
}

/* SHORTCUT */
.home-wrapper .shortcut-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px,1fr));
    gap: 20px;
}

.home-wrapper .shortcut-box {
    background: #ffffff;
    border-radius: 18px;
    padding: 22px;
    border: 1px solid #e7e7e7;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    transition: 0.25s;
    text-decoration: none;
    color: #222;
}

.home-wrapper .shortcut-box:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.07);
}

.home-wrapper .shortcut-title {
    margin-top: 12px;
    font-size: 17px;
    font-weight: 700;
}
</style>

<!-- WRAPPER -->
<div class="home-wrapper">

    <h1 class="header-title">Dashboard INDICA</h1>
    <div class="subtitle">Coffee & Roastery</div>

    <!-- STATISTIK -->
    <div class="stats-grid">

        <div class="card">
            <div class="card-icon icon-gradient-1">
                <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="card-title">Total Transaksi</div>
            <div class="card-value"><?= number_format($total_transaksi) ?></div>
        </div>

        <div class="card">
            <div class="card-icon icon-gradient-2">
                <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 10h18M7 15h2m4 0h2m4-6V5a2 2 0 00-2-2H5a2 2 0 00-2 2v4"/>
                </svg>
            </div>
            <div class="card-title">Total Pendapatan</div>
            <div class="card-value">Rp <?= number_format($total_income) ?></div>
        </div>

        <div class="card">
            <div class="card-icon icon-gradient-3">
                <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3v18h18M7 13l3 3 7-7"/>
                </svg>
            </div>
            <div class="card-title">Produk Terjual</div>
            <div class="card-value"><?= number_format($produk_terjual) ?></div>
        </div>

        <div class="card">
            <div class="card-icon icon-gradient-4">
                <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
            </div>
            <div class="card-title">Rata-rata Harian</div>
            <div class="card-value">Rp <?= number_format($rata) ?></div>
        </div>

    </div>

    <!-- SHORTCUT -->
    <div class="section-title">Akses Cepat</div>

    <div class="shortcut-grid">
        <a href="?page=report" class="shortcut-box">
            <div class="card-icon icon-gradient-3">
                <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 6h16M4 10h16M4 14h10M4 18h16"/>
                </svg>
            </div>
            <div class="shortcut-title">Laporan Penjualan</div>
        </a>

        <a href="?page=product" class="shortcut-box">
            <div class="card-icon icon-gradient-1">
                <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 6l8 4-8 4-8-4 8-4z"/>
                </svg>
            </div>
            <div class="shortcut-title">Kelola Produk</div>
        </a>
    </div>

</div>
