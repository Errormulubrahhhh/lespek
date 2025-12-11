<?php
require_once __DIR__ . "/../connect.php";

// Identitas user (dari session)
$nama   = $_SESSION['nama_Indica'] ?? 'User';
$user   = $_SESSION['username'] ?? '-';

// ==== QUERY PRODUK TERJUAL HARI INI ====
$q_produk_harian = mysqli_query($conn, "
    SELECT SUM(td.qty) AS total 
    FROM transaction_details td
    JOIN transactions t ON td.transaction_id = t.id
    WHERE DATE(t.tanggal) = CURDATE()
");
$produk_hari_ini = mysqli_fetch_assoc($q_produk_harian)['total'] ?? 0;

// ==== QUERY INCOME HARI INI ====
$q_income_harian = mysqli_query($conn, "
    SELECT SUM(total) AS total 
    FROM transactions
    WHERE DATE(tanggal) = CURDATE()
");
$income_hari_ini = mysqli_fetch_assoc($q_income_harian)['total'] ?? 0;
?>

<div class="max-w-4xl mx-auto mt-10" data-aos="fade-up">

    <h1 class="text-4xl font-extrabold text-amber-800 text-center mb-10">
        Profile Pengguna
    </h1>

    <!-- Card Identitas -->
    <div class="bg-white shadow-xl rounded-3xl p-8 mb-10 border border-amber-200">
        <h2 class="text-2xl font-bold text-gray-700 mb-6 flex items-center gap-3">
            <i class="bi bi-person-circle text-3xl text-amber-700"></i>
            Hai, ini kinerja kamu!
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-5 border rounded-2xl bg-amber-50">
                <p class="text-sm text-gray-500">Nama Lengkap</p>
                <p class="text-xl font-bold text-amber-800"><?= htmlspecialchars($nama) ?></p>
            </div>

            <div class="p-5 border rounded-2xl bg-amber-50">
                <p class="text-sm text-gray-500">Username</p>
                <p class="text-xl font-bold text-amber-800"><?= htmlspecialchars($user) ?></p>
            </div>
        </div>
    </div>

    <!-- Card Statistik Harian -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Income Hari Ini -->
        <div class="rounded-3xl shadow-xl p-8 text-white transform transition hover:scale-[1.03]"
             style="background: linear-gradient(135deg, #d97706, #b45309);">
            <h3 class="text-xl font-semibold opacity-90">Total Income Hari Ini</h3>
            <p class="text-4xl font-extrabold mt-3">
                Rp <?= number_format($income_hari_ini) ?>
            </p>
            <i class="bi bi-wallet2 text-6xl opacity-30 absolute right-6 bottom-6"></i>
        </div>

        <!-- Produk Terjual Hari Ini -->
        <div class="rounded-3xl shadow-xl p-8 text-white transform transition hover:scale-[1.03]"
             style="background: linear-gradient(135deg, #2563eb, #3b82f6);">
            <h3 class="text-xl font-semibold opacity-90">Produk Terjual Hari Ini</h3>
            <p class="text-4xl font-extrabold mt-3">
                <?= number_format($produk_hari_ini) ?> Produk
            </p>
            <i class="bi bi-box-seam text-6xl opacity-30 absolute right-6 bottom-6"></i>
        </div>

    </div>

</div>
