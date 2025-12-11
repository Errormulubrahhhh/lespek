<?php session_start(); if(!isset($_SESSION['login'])){header("Location: ../login.php");exit;} ?>

<h2>Tambah User Baru</h2>

<?php if(isset($_SESSION['msg'])): ?>
  <div class="alert alert-info"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<form action="../proses/proses_tambah_user.php" method="POST" class="needs-validation" novalidate>
  <div class="mb-3">
    <label for="nama" class="form-label">Nama Lengkap</label>
    <input type="text" name="nama" id="nama" class="form-control" required>
    <div class="invalid-feedback">Nama wajib diisi.</div>
  </div>

  <div class="mb-3">
    <label for="username" class="form-label">Email (Username)</label>
    <input type="email" name="username" id="username" class="form-control" required>
    <div class="invalid-feedback">Email wajib diisi dan valid.</div>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" name="password" id="password" class="form-control" required>
    <div class="invalid-feedback">Password wajib diisi.</div>
  </div>

  <button type="submit" class="btn btn-primary">Tambah User</button>
</form>

<script>
  (() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
          e.preventDefault()
          e.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>
