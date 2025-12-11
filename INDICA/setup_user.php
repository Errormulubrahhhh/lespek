<?php
require_once "connect.php";

// Hapus user lama (opsional)
$conn->query("DELETE FROM users WHERE username = 'admin'");

// Generate hash password
$password = "password";
$hash = password_hash($password, PASSWORD_BCRYPT);

// Insert user baru
$stmt = $conn->prepare("INSERT INTO users (name, username, password_hash, role) VALUES (?, ?, ?, ?)");
$name = "Admin";
$username = "admin";
$role = "admin";

$stmt->bind_param("ssss", $name, $username, $hash, $role);

if ($stmt->execute()) {
    echo "<div style='background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px;'>";
    echo "<h2>✓ User Berhasil Dibuat!</h2>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> password</p>";
    echo "<p><strong>Hash:</strong> " . $hash . "</p>";
    echo "<p><a href='login.php' style='color: white; text-decoration: underline;'>→ Kembali ke Login</a></p>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f44336; color: white; padding: 20px; text-align: center; border-radius: 5px;'>";
    echo "<h2>✗ Error!</h2>";
    echo "<p>" . $conn->error . "</p>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>
