<?php
session_start();
include '../includes/db.php';

if (isset($_GET['video_id']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $video_id = intval($_GET['video_id']);

    // Ambil path video
    $query = "SELECT video_path FROM videos WHERE video_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $video_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();

    if ($video) {
        // Catat aksi download
        $insert_query = "INSERT INTO video_downloads (user_id, video_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $user_id, $video_id);
        $stmt->execute();

        // Download file
        $file = '../' . $video['video_path'];
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
        exit();
    } else {
        echo "Video tidak ditemukan.";
    }
}
?>
