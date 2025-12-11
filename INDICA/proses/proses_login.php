<?php
session_start();
require_once "../connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Ambil user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 2");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Jika tidak ada user
    if (!$user) {
        $_SESSION['error_login'] = "Username atau password salah!";
        header("Location: ../login.php");
        exit;
    }

    // Verifikasi password
    if (password_verify($password, $user['password_hash'])) {
        // Login berhasil
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php"); // ⬅ MASUK KE HALAMAN UTAMA
        exit;
    } else {
        $_SESSION['error_login'] = "Username atau password salah!";
        header("Location: ../login.php");
        exit;
    }
}
