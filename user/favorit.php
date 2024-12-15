<?php
session_start();
include '../includes/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query untuk mengambil video yang sudah di-like
$query = "SELECT v.video_id, v.judul, v.deskripsi, v.video_path 
          FROM likes l 
          JOIN videos v ON l.video_id = v.video_id 
          WHERE l.user_id = ?
          ORDER BY l.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorites = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flicksy - Favorite Videos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/dashboard_user.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php">
            <h1>Flicksy</h1>
        </a>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="favorit.php" class="active"><i class="fas fa-heart"></i> Favorites</a>
        <a href="download.php"><i class="fas fa-download"></i> Downloads</a>
        <a href="history.php"><i class="fas fa-history"></i> History</a>
        <div class="bottom-links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <!-- Content -->
    <div class="content">
        <div class="highlight">
            <h2>Favorite Videos</h2>
            <form method="POST" action="delete_likes.php" style="margin-bottom: 15px;">
                <button type="submit" name="delete_likes" style="
                        padding: 10px 15px; 
                        background-color: #e74c3c; 
                        color: #fff; 
                        border: none; 
                        border-radius: 5px; 
                        cursor: pointer;">
                    Hapus Semua Suka
                </button>
            </form>
            <div class="cards">
                <?php if (count($favorites) > 0): ?>
                    <?php foreach ($favorites as $video): ?>
                    <div class="card">
                        <a href="detail_video.php?id=<?= $video['video_id']; ?>">
                            <video width="100%" height="200" controls>
                                <source src="../<?= htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                            </video>
                            <h3><?= htmlspecialchars($video['judul']); ?></h3>
                            <p><?= htmlspecialchars($video['deskripsi']); ?></p>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada video favorit yang ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
