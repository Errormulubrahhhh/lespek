let keranjang = JSON.parse(localStorage.getItem('keranjangIndica')) || [];

function tambahKeKeranjang(produk) {
  const existing = keranjang.find(item => item.id === produk.id);
  if (existing) {
    existing.qty += 1;
  } else {
    keranjang.push({ ...produk, qty: 1 });
  }
  localStorage.setItem('keranjangIndica', JSON.stringify(keranjang));
  renderKeranjang();
  AOS.refresh(); // Refresh animasi
}

function updateQty(index, qty) {
  keranjang[index].qty = parseInt(qty);
  if (keranjang[index].qty <= 0) keranjang.splice(index, 1);
  localStorage.setItem('keranjangIndica', JSON.stringify(keranjang));
  renderKeranjang();
}

function hapus(index) {
  keranjang.splice(index, 1);
  localStorage.setItem('keranjangIndica', JSON.stringify(keranjang));
  renderKeranjang();
}

function kosongkanKeranjang() {
  keranjang = [];
  localStorage.removeItem('keranjangIndica');
  renderKeranjang();
}

function renderKeranjang() {
  const tbody = document.querySelector('#tabelKeranjang tbody');
  const jmlEl = document.getElementById('jmlItem');
  const totalEl = document.getElementById('totalBayar');
  
  if (keranjang.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Keranjang kosong</td></tr>';
    jmlEl.textContent = 0;
    totalEl.textContent = 0;
    return;
  }

  tbody.innerHTML = keranjang.map((p, i) => {
    const subtotal = p.harga * p.qty;
    return `
      <tr>
        <td>${p.nama}</td>
        <td>Rp ${p.harga.toLocaleString()}</td>
        <td><input type="number" class="form-control form-control-sm" min="1" value="${p.qty}" onchange="updateQty(${i}, this.value)" style="width: 80px;"></td>
        <td>Rp ${subtotal.toLocaleString()}</td>
        <td><button class="btn btn-danger btn-sm" onclick="hapus(${i})"><i class="bi bi-trash"></i></button></td>
      </tr>
    `;
  }).join('');

  const total = keranjang.reduce((sum, p) => sum + (p.harga * p.qty), 0);
  const jml = keranjang.reduce((sum, p) => sum + p.qty, 0);
  
  jmlEl.textContent = jml;
  totalEl.textContent = total.toLocaleString();
}

function prosesBayar(metode) {
  if (keranjang.length === 0) return alert('Keranjang kosong!');
  
  const total = keranjang.reduce((sum, p) => sum + (p.harga * p.qty), 0);
  const detail = JSON.stringify(keranjang);

  fetch('proses/proses_transaksi.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `metode=${metode}&total=${total}&detail=${encodeURIComponent(detail)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert('Transaksi berhasil! ' + data.message);
      kosongkanKeranjang();
    } else {
      alert('Gagal: ' + data.message);
    }
  });
}

// Init on load
document.addEventListener('DOMContentLoaded', renderKeranjang);