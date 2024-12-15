<?php
session_start();
include '../includes/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hapus semua data like berdasarkan user_id
    $delete_query = "DELETE FROM likes WHERE user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect kembali ke halaman favorit setelah berhasil menghapus
        header("Location: favorit.php");
        exit();
    } else {
        echo "Gagal menghapus semua suka: " . $stmt->error;
    }
} else {
    echo "Akses tidak valid.";
}
?>
