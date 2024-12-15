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
    // Hapus semua data download berdasarkan user_id
    $delete_query = "DELETE FROM video_downloads WHERE user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect kembali ke halaman download setelah berhasil menghapus
        header("Location: download.php");
        exit();
    } else {
        echo "Gagal menghapus riwayat download: " . $stmt->error;
    }
} else {
    echo "Akses tidak valid.";
}
?>
