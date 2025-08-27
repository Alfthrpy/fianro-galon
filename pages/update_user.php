<?php
require_once __DIR__ . '/../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    $id       = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($id <= 0 || $username === '') {
        throw new Exception("Data tidak valid");
    }

    if ($password !== '') {
        // Update username + password
        $hash = $password;
        $stmt = $conn->prepare("UPDATE user SET username=?, password=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $hash, $id);
    } else {
        // Update username saja
        $stmt = $conn->prepare("UPDATE user SET username=? WHERE id=?");
        $stmt->bind_param("si", $username, $id);
    }

    $stmt->execute();

    header("Location: ../index.php?page=edit_user&id=$id&success=1");
    exit;

} catch (Throwable $e) {
    echo "Gagal update user: " . htmlspecialchars($e->getMessage());
}
