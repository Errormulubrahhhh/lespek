<?php
require_once "connect.php";

// Ambil produk
$minuman_query = mysqli_query($conn, "SELECT * FROM products WHERE kategori_id = '1' ORDER BY nama");
$makanan_query = mysqli_query($conn, "SELECT * FROM products WHERE kategori_id = '2' ORDER BY nama");

// Ambil histori hari ini
$today = date('Y-m-d');
$histori_query = mysqli_query($conn, "SELECT tanggal, metode_pembayaran, total FROM transactions WHERE DATE(tanggal) = '$today' ORDER BY tanggal DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Product Order - INDICA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

<div class="container-fluid py-6 max-w-7xl mx-auto">

    <!-- Judul -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold text-gray-800 mb-2">Product Order</h1>
        <p class="text-6xl font-bold text-amber-600">INDICA</p>
    </div>

    <!-- Tombol Tambah Menu Baru -->
    <div class="text-center mb-12">
        <button id="btnTambahMenu" class="bg-blue-600 text-white font-bold text-2xl px-10 py-4 rounded-xl hover:bg-blue-700 transition transform hover:scale-105">
            Tambah Menu Baru
        </button>
    </div>

    <!-- MENU MINUMAN -->
    <section class="mb-20">
        <h2 class="text-4xl font-extrabold text-amber-700 text-center mb-10">Minuman</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
            <?php while ($p = mysqli_fetch_assoc($minuman_query)): ?>
                <?php $safe = htmlspecialchars(json_encode($p), ENT_QUOTES); ?>
                <div class="product-card group cursor-pointer relative" data-produk='<?= $safe ?>'>
                    <!-- Tombol Edit -->
                    <button class="edit-btn absolute top-2 right-2 bg-gray-700 text-white px-3 py-1 rounded-full text-sm hover:bg-gray-800 z-20">Edit</button>

                    <!-- Card Produk: klik area/tombol untuk tambah ke keranjang -->
                    <div class="add-area bg-white rounded-2xl shadow-xl border-2 border-amber-100 hover:border-amber-400 p-6 text-center transition-all hover:shadow-2xl hover:-translate-y-3">
                        <h5 class="font-bold text-gray-800 text-lg mb-3"><?= htmlspecialchars($p['nama']) ?></h5>
                        <p class="text-amber-600 font-extrabold text-2xl mb-2">Rp <?= number_format($p['harga']) ?></p>
                        <?php if($p['has_variant'] === 'yes' && $p['harga_iced']): ?>
                            <p class="text-xs text-gray-500 mb-3">Hot: Rp <?= number_format($p['harga']) ?> | Iced: Rp <?= number_format($p['harga_iced']) ?></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 mb-3">Stok: <?= intval($p['stok']) ?></p>
                        <?php endif; ?>
                        <button class="add-btn w-full bg-amber-600 text-white font-bold py-3 rounded-xl hover:bg-amber-700 transition transform hover:scale-105">Tambah</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- MENU MAKANAN -->
    <section class="mb-20">
        <h2 class="text-4xl font-extrabold text-amber-700 text-center mb-10">Makanan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
            <?php while ($p = mysqli_fetch_assoc($makanan_query)): ?>
                <?php $safe = htmlspecialchars(json_encode($p), ENT_QUOTES); ?>
                <div class="product-card group cursor-pointer relative" data-produk='<?= $safe ?>'>
                    <!-- Tombol Edit -->
                    <button class="edit-btn absolute top-2 right-2 bg-gray-700 text-white px-3 py-1 rounded-full text-sm hover:bg-gray-800 z-20">Edit</button>

                    <!-- Card Produk -->
                    <div class="add-area bg-white rounded-2xl shadow-xl border-2 border-amber-100 hover:border-amber-400 p-6 text-center transition-all hover:shadow-2xl hover:-translate-y-3">
                        <h5 class="font-bold text-gray-800 text-lg mb-3"><?= htmlspecialchars($p['nama']) ?></h5>
                        <p class="text-amber-600 font-extrabold text-2xl mb-2">Rp <?= number_format($p['harga']) ?></p>
                        <?php if($p['has_variant'] === 'yes' && $p['harga_iced']): ?>
                            <p class="text-xs text-gray-500 mb-3">Hot: Rp <?= number_format($p['harga']) ?> | Iced: Rp <?= number_format($p['harga_iced']) ?></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 mb-3">Stok: <?= intval($p['stok']) ?></p>
                        <?php endif; ?>
                        <button class="add-btn w-full bg-amber-600 text-white font-bold py-3 rounded-xl hover:bg-amber-700 transition transform hover:scale-105">Tambah</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- KERANJANG + TOTAL -->
    <div class="grid lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-10">
                <div class="bg-gradient-to-r from-amber-700 to-amber-900 text-white p-6 text-center">
                    <h3 class="text-3xl font-bold">Keranjang Pesanan</h3>
                </div>
                <div class="p-8">
                    <table class="w-full text-center" id="tabelKeranjang">
                        <thead class="bg-amber-100 text-amber-900 font-bold">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <p id="keranjangKosong" class="text-center text-gray-500 text-xl py-12 italic">Keranjang kosong</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl p-10 text-center">
                <p class="text-3xl text-gray-600 mb-4">Total Harus Dibayar</p>
                <p id="totalBayar" class="text-6xl font-extrabold text-amber-700 mb-10">Rp 0</p>
                <div class="flex justify-center gap-8">
                    <button id="btnQRIS" class="bg-amber-600 text-white font-bold text-2xl px-16 py-5 rounded-xl hover:bg-amber-700 transition transform hover:scale-105">Bayar QRIS</button>
                    <button id="btnCash" class="bg-amber-600 text-white font-bold text-2xl px-16 py-5 rounded-xl hover:bg-amber-700 transition transform hover:scale-105">Bayar Cash</button>
                </div>
            </div>
        </div>

        <!-- Panel Pembayaran -->
        <div id="panelPembayaran" class="bg-white rounded-3xl shadow-2xl p-10 hidden">
            <div id="pembayaranQRIS" class="text-center hidden">
                <h3 class="text-4xl font-bold text-amber-700 mb-8">Scan QRIS</h3>
                <img src="../qris.jpeg" class="mx-auto w-80 rounded-2xl shadow-2xl mb-8" alt="QRIS">
                <button id="selesaiQRIS" class="bg-green-600 text-white font-bold text-3xl px-20 py-6 rounded-xl hover:bg-green-700 transition transform hover:scale-105">Selesai Bayar QRIS</button>
            </div>
            <div id="pembayaranCash" class="text-center hidden">
                <h3 class="text-4xl font-bold text-amber-700 mb-8">Pembayaran Tunai</h3>
                <p class="text-5xl font-extrabold text-amber-600 mb-8" id="harusBayar">Rp 0</p>
                <input type="number" id="inputUang" placeholder="Masukkan uang" class="w-full max-w-md mx-auto px-8 py-6 text-3xl text-center rounded-xl border-4 border-amber-400 mb-8">
                <p id="infoKembalian" class="text-4xl font-bold mb-10 min-h-20"></p>
                <button id="selesaiCash" class="bg-green-600 text-white font-bold text-3xl px-20 py-6 rounded-xl hover:bg-green-700 opacity-50 cursor-not-allowed" disabled>Selesai Bayar Cash</button>
            </div>
        </div>
    </div>

    <!-- Histori Penjualan Hari Ini -->
    <div class="mt-20 bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gray-900 to-black text-white p-6 text-center">
            <h3 class="text-3xl font-bold">Histori Penjualan Hari Ini</h3>
        </div>
        <div class="p-8">
            <table class="w-full text-center">
                <thead class="bg-amber-100 text-amber-900 font-bold text-lg">
                    <tr><th class="py-4">Waktu</th><th>Metode</th><th>Total</th></tr>
                </thead>
                <tbody id="historiBody">
                    <?php while($h = mysqli_fetch_assoc($histori_query)): ?>
                    <tr class="hover:bg-amber-50">
                        <td class="py-4"><?= date('H:i', strtotime($h['tanggal'])) ?></td>
                        <td><span class="px-5 py-2 rounded-full text-sm font-bold <?= $h['metode_pembayaran']=='QRIS'?'bg-green-100 text-green-800':'bg-blue-100 text-blue-800' ?>"><?= $h['metode_pembayaran'] ?></span></td>
                        <td class="font-bold text-amber-700 text-xl">Rp <?= number_format($h['total']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Pilihan Variant (Iced/Hot) -->
<div id="variantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-3xl shadow-2xl p-10 w-full max-w-md text-center">
        <h3 id="variantTitle" class="text-3xl font-bold text-amber-700 mb-8">Pilih Varian</h3>
        <p id="variantDesc" class="text-xl text-gray-600 mb-12"></p>
        <div class="flex justify-center gap-6">
            <button id="variantHot" class="bg-red-600 text-white font-bold text-2xl px-12 py-6 rounded-xl hover:bg-red-700">🔥 HOT</button>
            <button id="variantIced" class="bg-blue-600 text-white font-bold text-2xl px-12 py-6 rounded-xl hover:bg-blue-700">🧊 ICED</button>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Update Menu -->
<div id="modalMenu" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-3xl shadow-2xl p-10 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h3 id="modalTitle" class="text-3xl font-bold text-amber-700 mb-8 text-center">Tambah Menu Baru</h3>
        <form id="formMenu">
            <input type="hidden" id="menuId" name="id">
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Nama Menu</label>
                <input type="text" id="namaMenu" name="nama" class="w-full px-4 py-3 rounded-xl border-2 border-amber-300 focus:border-amber-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Kategori</label>
                <select id="kategoriMenu" name="kategori_id" class="w-full px-4 py-3 rounded-xl border-2 border-amber-300 focus:border-amber-500" required>
                    <option value="1">Minuman</option>
                    <option value="2">Makanan</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Harga (Hot/Default)</label>
                <input type="number" id="hargaMenu" name="harga" class="w-full px-4 py-3 rounded-xl border-2 border-amber-300 focus:border-amber-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <input type="checkbox" id="hasVariant" name="has_variant"> Pilihan Iced/Hot
                </label>
                <p class="text-sm text-gray-600">Centang jika menu ini ada pilihan Iced dan Hot</p>
            </div>
            <div id="variantFields" class="mb-6 hidden">
                <label class="block text-gray-700 font-bold mb-2">Harga Iced</label>
                <input type="number" id="hargaIced" name="harga_iced" class="w-full px-4 py-3 rounded-xl border-2 border-amber-300 focus:border-amber-500" placeholder="Jika berbeda dari Hot">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Stok</label>
                <input type="number" id="stokMenu" name="stok" class="w-full px-4 py-3 rounded-xl border-2 border-amber-300 focus:border-amber-500" required>
            </div>
            <div class="flex justify-center gap-6">
                <button type="submit" class="bg-amber-600 text-white font-bold text-xl px-10 py-4 rounded-xl hover:bg-amber-700">Simpan</button>
                <button type="button" id="btnTutupModal" class="bg-gray-600 text-white font-bold text-xl px-10 py-4 rounded-xl hover:bg-gray-700">Batal</button>
                <button type="button" id="btnHapusMenu" class="bg-red-600 text-white font-bold text-xl px-10 py-4 rounded-xl hover:bg-red-700 hidden">Hapus Menu</button>
            </div>
        </form>
    </div>
</div>

<script>
// Util kecil
function safeParse(jsonStr) {
    try {
        return JSON.parse(jsonStr);
    } catch (e) {
        console.error("safeParse error:", e, jsonStr);
        return null;
    }
}
function escapeHtml(unsafe) {
    if (typeof unsafe !== 'string') return unsafe;
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}
const Rupiah = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// Semua inisialisasi pakai DOMContentLoaded agar elemen ada
document.addEventListener('DOMContentLoaded', () => {
    console.log("product.php script init");

    // ======= KERANJANG =======
    const Keranjang = {
        items: JSON.parse(localStorage.getItem('keranjangIndica')) || [],

        render() {
            try {
                const tbody = document.querySelector('#tabelKeranjang tbody');
                const kosong = document.getElementById('keranjangKosong');
                tbody.innerHTML = '';

                if (this.items.length === 0) {
                    kosong.classList.remove('hidden');
                    this.updateTotal();
                    return;
                }

                kosong.classList.add('hidden');
                this.items.forEach((item, i) => {
                    const subtotal = item.harga * item.qty;
                    const variantLabel = item.variant ? ` (${item.variant})` : '';
                    tbody.innerHTML += `
                        <tr>
                            <td class="py-4">${i + 1}</td>
                            <td class="font-medium">${escapeHtml(item.nama)}${variantLabel}</td>
                            <td class="flex justify-center items-center gap-2">
                                <button onclick="window.Keranjang.kurang(${i})" class="bg-gray-300 w-8 h-8 rounded-full text-lg">-</button>
                                <span class="w-12 text-center font-bold">${item.qty}</span>
                                <button onclick="window.Keranjang.tambahLagi(${i})" class="bg-amber-600 text-white w-8 h-8 rounded-full text-lg">+</button>
                            </td>
                            <td>${Rupiah(item.harga)}</td>
                            <td class="font-bold">${Rupiah(subtotal)}</td>
                            <td><button onclick="window.Keranjang.hapus(${i})" class="bg-red-600 text-white px-3 py-1 rounded">Hapus</button></td>
                        </tr>`;
                });
                this.updateTotal();
            } catch (e) {
                console.error("Keranjang.render error:", e);
            }
        },

        updateTotal() {
            const total = this.items.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            const tb = document.getElementById('totalBayar');
            const hb = document.getElementById('harusBayar');
            if (tb) tb.textContent = Rupiah(total);
            if (hb) hb.textContent = Rupiah(total);
        },

        tambah(produk, variant = null) {
            try {
                produk.id = parseInt(produk.id);
                produk.harga = parseFloat(produk.harga);
                produk.stok = parseInt(produk.stok) || 0;

                // Jika produk punya variant dan belum ada pilihan, buka modal
                if (produk.has_variant === 'yes' && !variant) {
                    window._selectedProdukForVariant = produk;
                    document.getElementById('variantModal').classList.remove('hidden');
                    return;
                }

                // Update harga jika pilih iced
                if (variant === 'Iced' && produk.harga_iced) {
                    produk.harga = parseFloat(produk.harga_iced);
                }

                const key = `${produk.id}_${variant || 'none'}`;
                const ada = this.items.find(x => x.id === produk.id && (x.variant || 'none') === (variant || 'none'));
                
                if (ada) {
                    ada.qty += 1;
                } else {
                    this.items.push({ ...produk, qty: 1, variant: variant });
                }
                this.simpan();
                this.render();
            } catch (e) {
                console.error("tambah error:", e, produk);
            }
        },

        tambahLagi(i) {
            this.items[i].qty++;
            this.simpan();
            this.render();
        },

        kurang(i) {
            if (this.items[i].qty > 1) {
                this.items[i].qty--;
            } else {
                this.items.splice(i, 1);
            }
            this.simpan();
            this.render();
        },

        hapus(i) {
            this.items.splice(i, 1);
            this.simpan();
            this.render();
        },

        simpan() {
            localStorage.setItem('keranjangIndica', JSON.stringify(this.items));
        },

        kosongkan() {
            this.items = [];
            localStorage.removeItem('keranjangIndica');
            this.render();
        }
    };

    // expose to global so inline handlers (generated below) can call if needed
    window.Keranjang = Keranjang;

    // ======= MENU (Tambah/Edit) =======
    const Menu = {
        openModal(mode, data = {}) {
            const modal = document.getElementById('modalMenu');
            const title  = document.getElementById('modalTitle');
            const form   = document.getElementById('formMenu');
            const hasVariantCheck = document.getElementById('hasVariant');
            const variantFields = document.getElementById('variantFields');
            const btnHapus = document.getElementById('btnHapusMenu');

            if (mode === "add") {
                title.textContent = "Tambah Menu Baru";
                form.reset();
                document.getElementById("menuId").value = "";
                hasVariantCheck.checked = false;
                variantFields.classList.add('hidden');
                btnHapus.classList.add('hidden'); // Hide tombol hapus saat tambah
            } else if (mode === "edit") {
                title.textContent = "Edit Menu";
                document.getElementById("menuId").value      = data.id ?? "";
                document.getElementById("namaMenu").value    = data.nama ?? "";
                document.getElementById("kategoriMenu").value= data.kategori_id ?? "1";
                document.getElementById("hargaMenu").value   = data.harga ?? "";
                document.getElementById("stokMenu").value    = data.stok ?? 0;
                hasVariantCheck.checked = (data.has_variant === 'yes');
                document.getElementById("hargaIced").value   = data.harga_iced ?? "";
                if (data.has_variant === 'yes') {
                    variantFields.classList.remove('hidden');
                } else {
                    variantFields.classList.add('hidden');
                }
                btnHapus.classList.remove('hidden'); // Show tombol hapus saat edit
            }

            modal.classList.remove("hidden");
        },

        editFromElement(el) {
            const json = el.closest('.product-card')?.dataset?.produk ?? null;
            const data = safeParse(json);
            if (!data) return alert("Data produk rusak, lihat console");
            this.openModal("edit", data);
        }
    };

    // expose Menu to window for debugging if needed
    window.Menu = Menu;

    // tombol modal & form handlers
    const btnTambahMenu = document.getElementById("btnTambahMenu");
    const btnTutupModal = document.getElementById("btnTutupModal");
    const btnHapusMenu = document.getElementById("btnHapusMenu");
    const formMenu = document.getElementById("formMenu");
    const hasVariantCheck = document.getElementById("hasVariant");
    const variantFields = document.getElementById("variantFields");

    if (btnTambahMenu) btnTambahMenu.addEventListener("click", () => Menu.openModal("add"));
    if (btnTutupModal) btnTutupModal.addEventListener("click", () => document.getElementById('modalMenu').classList.add('hidden'));

    // Tombol Hapus di dalam modal
    if (btnHapusMenu) {
        btnHapusMenu.addEventListener("click", async () => {
            const menuId = document.getElementById("menuId").value;
            const namaMenu = document.getElementById("namaMenu").value;

            if (!menuId) return alert("ID menu tidak ditemukan");

            if (!confirm(`Hapus menu "${namaMenu}"?\n\nTindakan ini tidak bisa dibatalkan!`)) {
                return;
            }

            try {
                const res = await fetch("proses/proses_hapus_produk.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: parseInt(menuId) })
                });
                const json_res = await res.json();

                if (json_res.status === "success") {
                    alert(`Menu "${namaMenu}" berhasil dihapus`);
                    document.getElementById('modalMenu').classList.add('hidden');
                    location.reload();
                } else {
                    alert("Gagal: " + (json_res.message || "Error tidak diketahui"));
                }
            } catch (error) {
                console.error("delete error:", error);
                alert("Koneksi error!");
            }
        });
    }

    // Toggle variant fields
    if (hasVariantCheck) {
        hasVariantCheck.addEventListener('change', function() {
            if (this.checked) {
                variantFields.classList.remove('hidden');
            } else {
                variantFields.classList.add('hidden');
            }
        });
    }

    if (formMenu) {
        formMenu.addEventListener("submit", async (e) => {
            e.preventDefault();
            const form = new FormData(e.target);
            const data = Object.fromEntries(form);
            const action = data.id ? "update" : "add";
            const hasVariant = document.getElementById('hasVariant').checked;

            const payload = {
                action: action,
                id: data.id || "",
                nama: data.nama,
                kategori_id: data.kategori_id,
                harga: data.harga,
                harga_iced: hasVariant ? data.harga_iced : "",
                stok: data.stok,
                has_variant: hasVariant ? "yes" : "no"
            };

            try {
                const res = await fetch("proses/proses_tambah_produk.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();

                if (json.status === "success") {
                    alert(`Menu berhasil ${action === "add" ? "ditambahkan" : "diupdate"}`);
                    location.reload();
                } else {
                    alert("Gagal: " + (json.message || "Error tidak diketahui"));
                }
            } catch (error) {
                console.error("form submit error:", error);
                alert("Gagal terhubung ke server!");
            }
        });
    }

    // ======= Pasang event listener ke setiap produk (Add + Edit) =======
    document.querySelectorAll('.product-card').forEach(card => {
        // tombol tambah
        const btnAdd = card.querySelector('.add-btn');
        if (btnAdd) {
            btnAdd.addEventListener('click', (ev) => {
                ev.stopPropagation();
                const json = card.dataset.produk ?? null;
                const produk = safeParse(json);
                if (!produk) {
                    console.error("Produk JSON invalid:", json);
                    return alert("Produk rusak — lihat console");
                }
                Keranjang.tambah(produk);
            });
        }

        // tombol edit
        const btnEdit = card.querySelector('.edit-btn');
        if (btnEdit) {
            btnEdit.addEventListener('click', (ev) => {
                ev.stopPropagation();
                Menu.editFromElement(btnEdit);
            });
        }

        // juga klik pada seluruh area card untuk tambah (opsional)
        const area = card.querySelector('.add-area');
        if (area) {
            area.addEventListener('click', () => {
                const json = card.dataset.produk ?? null;
                const produk = safeParse(json);
                if (!produk) {
                    console.error("Produk JSON invalid:", json);
                    return;
                }
                Keranjang.tambah(produk);
            });
        }
    });

    // ======= Variant Modal Handlers =======
    const variantHotBtn = document.getElementById('variantHot');
    const variantIcedBtn = document.getElementById('variantIced');
    
    if (variantHotBtn) {
        variantHotBtn.addEventListener('click', () => {
            if (window._selectedProdukForVariant) {
                Keranjang.tambah(window._selectedProdukForVariant, 'Hot');
                document.getElementById('variantModal').classList.add('hidden');
                window._selectedProdukForVariant = null;
            }
        });
    }

    if (variantIcedBtn) {
        variantIcedBtn.addEventListener('click', () => {
            if (window._selectedProdukForVariant) {
                Keranjang.tambah(window._selectedProdukForVariant, 'Iced');
                document.getElementById('variantModal').classList.add('hidden');
                window._selectedProdukForVariant = null;
            }
        });
    }

    // ======= Histori helper =======
    function tambahKeHistori(waktu, metode, total) {
        const tbody = document.getElementById('historiBody');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-amber-50';
        tr.innerHTML = `
            <td class="py-4">${waktu}</td>
            <td><span class="px-5 py-2 rounded-full text-sm font-bold ${metode === 'QRIS' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">${metode}</span></td>
            <td class="font-bold text-amber-700 text-xl">${Rupiah(total)}</td>
        `;
        if (tbody) tbody.prepend(tr);
    }

    // ======= Kembalian Cash logic =======
    const inputUang = document.getElementById('inputUang');
    if (inputUang) {
        inputUang.addEventListener('input', function () {
            const total = Keranjang.items.reduce((s, i) => s + i.harga * i.qty, 0);
            const bayar = parseInt(this.value) || 0;
            const info = document.getElementById('infoKembalian');
            const btn = document.getElementById('selesaiCash');

            if (bayar >= total && total > 0) {
                const kembali = bayar - total;
                info.innerHTML = kembali === 0
                    ? '<span class="text-green-600 text-4xl">Uang Pas!</span>'
                    : `<span class="text-green-600 text-4xl">Kembalian: ${Rupiah(kembali)}</span>`;
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else if (bayar > 0) {
                info.innerHTML = `<span class="text-red-600 text-4xl">Kurang ${Rupiah(total - bayar)}</span>`;
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                info.textContent = '';
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }

    // ======= Tombol bayar =======
    const btnQRIS = document.getElementById('btnQRIS');
    const btnCash = document.getElementById('btnCash');
    if (btnQRIS) btnQRIS.addEventListener('click', () => {
        if (Keranjang.items.length === 0) return alert('Keranjang kosong!');
        document.getElementById('panelPembayaran').classList.remove('hidden');
        document.getElementById('pembayaranQRIS').classList.remove('hidden');
        document.getElementById('pembayaranCash').classList.add('hidden');
    });
    if (btnCash) btnCash.addEventListener('click', () => {
        if (Keranjang.items.length === 0) return alert('Keranjang kosong!');
        document.getElementById('panelPembayaran').classList.remove('hidden');
        document.getElementById('pembayaranCash').classList.remove('hidden');
        document.getElementById('pembayaranQRIS').classList.add('hidden');
        document.getElementById('harusBayar').textContent = Rupiah(Keranjang.items.reduce((s,i)=>s+i.harga*i.qty,0));
    });

    // ======= Simpan transaksi =======
    async function simpanTransaksi(metode) {
        const total = Keranjang.items.reduce((s, i) => s + i.harga * i.qty, 0);
        if (total === 0) return alert('Keranjang kosong!');

        try {
            const res = await fetch("proses/proses_transaksi.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    metode: metode,
                    total: total,
                    detail: Keranjang.items
                })
            }).then(r => r.json());

            if (res.status === "success") {
                const waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                tambahKeHistori(waktu, metode, total);
                Keranjang.kosongkan();

                // Tutup panel
                document.getElementById('panelPembayaran').classList.add('hidden');
                document.getElementById('pembayaranQRIS').classList.add('hidden');
                document.getElementById('pembayaranCash').classList.add('hidden');
                if (inputUang) inputUang.value = '';
                document.getElementById('infoKembalian').textContent = '';
                const selesaiCash = document.getElementById('selesaiCash');
                if (selesaiCash) {
                    selesaiCash.disabled = true;
                    selesaiCash.classList.add('opacity-50','cursor-not-allowed');
                }

                alert(`Transaksi ${metode} berhasil! Total: ${Rupiah(total)}`);
            } else {
                alert("Gagal: " + (res.message || "Error tidak diketahui"));
            }
        } catch (err) {
            console.error(err);
            alert("Koneksi error!");
        }
    }

    // Tombol selesai bayar
    const selesaiQRIS = document.getElementById('selesaiQRIS');
    const selesaiCash = document.getElementById('selesaiCash');
    if (selesaiQRIS) selesaiQRIS.addEventListener('click', () => simpanTransaksi('QRIS'));
    if (selesaiCash) selesaiCash.addEventListener('click', () => simpanTransaksi('Cash'));

    // Render keranjang saat halaman dibuka
    try {
        Keranjang.render();
    } catch (e) {
        console.error("Initial render error:", e);
    }

    // expose helpers for debugging
    window._debug = { Keranjang, Menu, safeParse };
});
</script>

</body>
</html>
