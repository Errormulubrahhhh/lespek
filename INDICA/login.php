<?php
session_start();
require_once "connect.php";

// Tangkap pesan error kalau ada
$error_msg = $_SESSION['error_login'] ?? '';
unset($_SESSION['error_login']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Kedai Indica | Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6F4E37, #D2B48C);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: #3E2723;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            color: #fff;
        }

        h3 {
            margin-bottom: 25px;
            font-weight: 700;
            color: #FFD54F;
            text-align: center;
        }

        .form-control {
            background-color: #5D4037;
            border: none;
            color: #FFE0B2;
        }

        .form-control:focus {
            background-color: #6D4C41;
            color: #fff;
            box-shadow: 0 0 8px #FFD54F;
            border: none;
        }

        label {
            color: #FFECB3;
        }

        .btn-primary {
            background-color: #A0522D;
            border: none;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #6B3E1A;
        }

        .form-check-label {
            color: #FFECB3;
        }

        .input-group-text {
            background-color: #5D4037;
            border: none;
            color: #FFD54F;
            cursor: pointer;
        }

        .error-msg {
            background-color: #b71c1c;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 0 8px #ff5252;
        }

        .success-msg {
            background-color: #1b5e20;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 0 8px #4caf50;
        }

        .btn-daftar {
            background-color: #FFD54F;
            border: none;
            color: #3E2723;
            font-weight: 600;
            margin-top: 15px;
        }

        .btn-daftar:hover {
            background-color: #FFC107;
            color: #3E2723;
        }

        .modal-login {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-login.active {
            display: flex;
        }

        .modal-content {
            background: #3E2723;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            color: #fff;
        }

        .modal-content h4 {
            color: #FFD54F;
            margin-bottom: 20px;
            text-align: center;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #FFD54F;
            cursor: pointer;
            background: none;
            border: none;
        }

        .close-modal:hover {
            color: #FFC107;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <h3><i class="bi bi-cup-hot-fill"></i> Kedai Indica</h3>

        <?php if($error_msg): ?>
            <div class="error-msg"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form class="needs-validation" novalidate action="proses/proses_login.php" method="POST">

            <!-- GANTI EMAIL MENJADI USERNAME -->
            <div class="form-floating mb-3">
                <input name="username" type="text" class="form-control" id="floatingInput" placeholder="Username" required>
                <label for="floatingInput">Username</label>
                <div class="invalid-feedback">Masukkan username.</div>
            </div>

            <div class="form-floating mb-3">
                <div class="input-group">
                    <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword" class="visually-hidden">Password</label>
                    <span class="input-group-text" id="togglePassword" title="Tampilkan / sembunyikan password">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </span>
                    <div class="invalid-feedback">Masukkan password.</div>
                </div>
            </div>

            <div class="form-check text-start mb-3">
                <input class="form-check-input" type="checkbox" value="remember-me" id="checkDefault">
                <label class="form-check-label" for="checkDefault">Ingat saya</label>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit" name="submit_validate">
                Login
            </button>

            <button class="btn btn-daftar w-100 py-2" type="button" id="btnDaftar">
                <i class="bi bi-person-plus"></i> Daftar Akun Baru
            </button>

            <p class="mt-3 text-center text-warning">&copy; 2025 Kedai Indica</p>

        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modal Daftar -->
    <div id="modalDaftar" class="modal-login">
        <div class="modal-content">
            <button class="close-modal" id="closeDaftar">&times;</button>
            <h4><i class="bi bi-person-plus-fill"></i> Daftar Akun Baru</h4>

            <div id="msgDaftar"></div>

            <form id="formDaftar" novalidate>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="namaDaftar" class="form-control" placeholder="Nama Anda" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" id="usernameDaftar" class="form-control" placeholder="Username (min 3 karakter)" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" id="passwordDaftar" class="form-control" placeholder="Password (min 6 karakter)" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" id="passwordConfirmDaftar" class="form-control" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn btn-warning w-100 py-2 fw-bold">
                    Daftar
                </button>
            </form>

            <p class="mt-3 text-center text-muted">
                Sudah punya akun? <button type="button" id="btnKembaliLogin" style="background:none; border:none; color:#FFD54F; cursor:pointer; text-decoration:underline;">Login di sini</button>
            </p>
        </div>
    </div>

    <!-- Show/hide password -->
    <script>
        // Modal Control
        const modalDaftar = document.getElementById('modalDaftar');
        const btnDaftar = document.getElementById('btnDaftar');
        const closeDaftar = document.getElementById('closeDaftar');
        const btnKembaliLogin = document.getElementById('btnKembaliLogin');
        const formDaftar = document.getElementById('formDaftar');
        const msgDaftar = document.getElementById('msgDaftar');

        btnDaftar.addEventListener('click', () => {
            modalDaftar.classList.add('active');
            formDaftar.reset();
            msgDaftar.innerHTML = '';
        });

        closeDaftar.addEventListener('click', () => {
            modalDaftar.classList.remove('active');
        });

        btnKembaliLogin.addEventListener('click', () => {
            modalDaftar.classList.remove('active');
        });

        // Click outside modal to close
        window.addEventListener('click', (e) => {
            if (e.target === modalDaftar) {
                modalDaftar.classList.remove('active');
            }
        });

        // Form submit
        formDaftar.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = document.getElementById('namaDaftar').value.trim();
            const username = document.getElementById('usernameDaftar').value.trim();
            const password = document.getElementById('passwordDaftar').value;
            const passwordConfirm = document.getElementById('passwordConfirmDaftar').value;

            // Clear previous message
            msgDaftar.innerHTML = '';

            try {
                const res = await fetch('proses/proses_daftar_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: name,
                        username: username,
                        password: password,
                        password_confirm: passwordConfirm
                    })
                });

                const json = await res.json();

                if (json.status === 'success') {
                    msgDaftar.innerHTML = `<div style="background-color: #1b5e20; color: white; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: 600;">✓ ${json.message}</div>`;
                    formDaftar.reset();
                    
                    // Close modal setelah 2 detik
                    setTimeout(() => {
                        modalDaftar.classList.remove('active');
                    }, 2000);
                } else {
                    msgDaftar.innerHTML = `<div style="background-color: #b71c1c; color: white; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: 600;">✗ ${json.message}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                msgDaftar.innerHTML = `<div style="background-color: #b71c1c; color: white; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: 600;">✗ Koneksi error!</div>`;
            }
        });

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('floatingPassword');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', () => {
            if(passwordInput.type === 'password'){
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });

        // Bootstrap validation
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', e => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        })();
    </script>

</body>
</html>
