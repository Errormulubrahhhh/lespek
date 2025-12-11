<?php
require_once "../connect.php";
header('Content-Type: application/json; charset=utf-8');

// Baca JSON dari request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "Data tidak valid"
    ]);
    exit;
}

$name = trim($data['name'] ?? '');
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$password_confirm = trim($data['password_confirm'] ?? '');

// Validasi
if (empty($name) || empty($username) || empty($password) || empty($password_confirm)) {
    echo json_encode([
        "status" => "error",
        "message" => "Semua field harus diisi"
    ]);
    exit;
}

if (strlen($username) < 3) {
    echo json_encode([
        "status" => "error",
        "message" => "Username minimal 3 karakter"
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        "status" => "error",
        "message" => "Password minimal 6 karakter"
    ]);
    exit;
}

if ($password !== $password_confirm) {
    echo json_encode([
        "status" => "error",
        "message" => "Password tidak sesuai"
    ]);
    exit;
}

// Cek username sudah ada
$checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$checkStmt->bind_param("s", $username);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Username sudah digunakan"
    ]);
    $checkStmt->close();
    exit;
}

$checkStmt->close();

// Hash password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Insert user baru
$insertStmt = $conn->prepare("INSERT INTO users (name, username, password_hash, role) VALUES (?, ?, ?, 'employee')");
$insertStmt->bind_param("sss", $name, $username, $password_hash);

if ($insertStmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Akun berhasil dibuat! Silakan login."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal membuat akun: " . $insertStmt->error
    ]);
}

$insertStmt->close();
$conn->close();
?>
