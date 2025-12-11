<?php
// proses/proses_tambah_user.php
session_start();
require_once __DIR__ . '/../connect.php';

// Cek otorisasi jika perlu
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { ... }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'staff';

if (!$name || !$username || !$password) {
    echo json_encode(['success'=>false,'message'=>'Semua field wajib diisi']);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, username, password_hash, role) VALUES (:name, :username, :password_hash, :role)");
    $stmt->execute([
        ':name' => $name,
        ':username' => $username,
        ':password_hash' => $passwordHash,
        ':role' => $role
    ]);
    echo json_encode(['success'=>true,'message'=>'User berhasil ditambahkan']);
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) { // duplicate entry
        echo json_encode(['success'=>false,'message'=>'Username sudah digunakan']);
    } else {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'Terjadi kesalahan server']);
    }
}
