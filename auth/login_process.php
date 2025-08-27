<?php
session_start();
require_once "../config/db.php";

// Ambil input dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Cegah SQL injection
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// Query user dari database
$sql = "SELECT * FROM user WHERE username = '$username' LIMIT 1";
$result = $conn->query($sql);

// Cek apakah user ditemukan
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Versi non-hashed (kamu bisa ganti nanti ke password_hash)
    if ($user['password'] === $password) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id']; 
        header("Location: ../index.php?page=dashboard");
        exit;
    } else {
        echo "salah password";
        exit;
    }
} else {
    header("Location: ../index.php?page=login&error=1");
    exit;
}
?>
