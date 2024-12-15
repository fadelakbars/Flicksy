<?php
include '../includes/db.php';

// Ambil keyword search dari parameter GET
$keyword = isset($_GET['q']) ? "%" . $_GET['q'] . "%" : "";

// Query SQL untuk mencari video berdasarkan judul atau deskripsi
$query = "SELECT * FROM videos WHERE judul LIKE ? OR deskripsi LIKE ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $keyword, $keyword);
$stmt->execute();

$result = $stmt->get_result();
$videos = $result->fetch_all(MYSQLI_ASSOC);

// Return hasil dalam format JSON
header('Content-Type: application/json');
echo json_encode($videos);
?>
