<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "connect.php";

$page = $_GET['page'] ?? 'home';
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indica Kasir • <?= ucfirst($page) ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brown: {
                            500: '#92400e',
                            600: '#7c2d12',
                            700: '#652b0e',
                            800: '#451a03',
                            900: '#2d0f02',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="menuBtn" class="text-3xl text-amber-800 bg-white rounded-full p-3 shadow-lg">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <!-- SIDEBAR (Desain baru - Tailwind + Gradient Amber) -->
    <aside id="sidebar" class="w-64 bg-gradient-to-b from-amber-800 to-amber-900 text-white fixed inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl">
        <div class="p-6 text-center border-b border-amber-700">
            <h1 class="text-4xl font-bold tracking-wider">INDICA</h1>
            <p class="text-amber-200 text-sm mt-1">Coffe  & Roastery</p>
        </div>

        <nav class="mt-10 px-4">
            <a href="?page=home" 
               class="flex items-center px-6 py-4 rounded-lg mb-2 transition-all <?= $page=='home' ? 'bg-amber-700 shadow-lg' : 'hover:bg-amber-700' ?>">
                <i class="bi bi-speedometer2 text-2xl mr-4"></i>
                <span class="font-semibold">Dashboard</span>
            </a>
            <a href="?page=product" 
               class="flex items-center px-6 py-4 rounded-lg mb-2 transition-all <?= $page=='product' ? 'bg-amber-700 shadow-lg' : 'hover:bg-amber-700' ?>">
                <i class="bi bi-cup-hot-fill text-2xl mr-4"></i>
                <span class="font-semibold">Order Produk</span>
            </a>
            <a href="?page=report" 
               class="flex items-center px-6 py-4 rounded-lg mb-2 transition-all <?= $page=='report' ? 'bg-amber-700 shadow-lg' : 'hover:bg-amber-700' ?>">
                <i class="bi bi-clipboard-data text-2xl mr-4"></i>
                <span class="font-semibold">Laporan</span>
            </a>

            <hr class="my-8 border-amber-700">

            <a href="?page=profile" 
               class="flex items-center px-6 py-4 rounded-lg mb-2 transition-all hover:bg-amber-700">
                <i class="bi bi-person-circle text-2xl mr-4"></i>
                <span class="font-semibold">Profile</span>
            </a>
            <a href="logout.php" 
               class="flex items-center px-6 py-4 rounded-lg text-red-300 transition-all hover:bg-red-600 hover:text-white">
                <i class="bi bi-box-arrow-right text-2xl mr-4"></i>
                <span class="font-semibold">Keluar</span>
            </a>
        </nav>

        <div class="absolute bottom-6 left-0 right-0 text-center text-amber-300 text-xs">
            © <?= date('Y') ?> Indica Kasir
        </div>
    </aside>

    <!-- Overlay untuk mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 lg:ml-64 pt-16 lg:pt-8 px-6 lg:px-10">
        <!-- Header / User Info (di kanan atas) -->
        <div class="flex justify-end items-center mb-8">
            <div class="flex items-center gap-4 bg-white rounded-full shadow-lg px-6 py-3">
                <i class="bi bi-person-circle text-3xl text-amber-700"></i>
                <div>
                    <p class="text-sm text-gray-600">Selamat datang,</p>
                    <p class="font-bold text-amber-800"><?= htmlspecialchars($_SESSION['nama_Indica'] ?? '') ?></p>
                </div>
            </div>
        </div>

        <!-- Isi halaman -->
        <?php if (file_exists("pages/$page.php")): ?>
            <?php include "pages/$page.php"; ?>
        <?php else: ?>
            <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-8 py-6 rounded-xl" data-aos="zoom-in">
                <h3 class="text-2xl font-bold mb-2">Oops! Halaman tidak ditemukan</h3>
                <p>Kembali ke <a href="?page=home" class="underline font-bold">Dashboard</a></p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        // Mobile sidebar toggle
        const menuBtn = document.getElementById('menuBtn');
        const sidebar  = document.getElementById('sidebar');
        const overlay  = document.getElementById('overlay');

        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
</html>