<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$video_id = intval($_POST['video_id']);

// Periksa apakah video sudah di-like
$query = "SELECT * FROM likes WHERE user_id = ? AND video_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $video_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Jika sudah di-like, hapus like
    $delete_query = "DELETE FROM likes WHERE user_id = ? AND video_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("ii", $user_id, $video_id);
    $stmt->execute();
    echo json_encode(['success' => true, 'liked' => false, 'message' => 'Like removed.']);
} else {
    // Jika belum di-like, tambahkan like
    $insert_query = "INSERT INTO likes (user_id, video_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ii", $user_id, $video_id);
    $stmt->execute();
    echo json_encode(['success' => true, 'liked' => true, 'message' => 'Video liked.']);
}
?>
