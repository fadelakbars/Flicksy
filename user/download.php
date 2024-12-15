<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil daftar video yang di-download
$query = "SELECT v.judul, v.deskripsi, v.video_path, d.downloaded_at
          FROM video_downloads d
          JOIN videos v ON d.video_id = v.video_id
          WHERE d.user_id = ?
          ORDER BY d.downloaded_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$downloads = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Download History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/dashboard_user.css">
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php"><h1>Flicksy</h1></a>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="favorit.php"><i class="fas fa-heart"></i> Favorites</a>
        <a href="download.php" class="active"><i class="fas fa-download"></i> Downloads</a>
        <a href="history.php"><i class="fas fa-history"></i> History</a>
        <div class="bottom-links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="content">
        <h2>Video yang Sudah Di-Download</h2>

        <form method="POST" action="delete_downloads.php" style="margin-bottom: 15px;">
        <button type="submit" name="delete_downloads" style="
                padding: 10px 15px; 
                background-color: #e74c3c; 
                color: #fff; 
                border: none; 
                border-radius: 5px; 
                cursor: pointer;">
            Hapus Semua Download
        </button>
    </form>
    
        <div class="cards">
            <?php foreach ($downloads as $download): ?>
                <div class="card">
                    <video width="100%" height="200" controls>
                        <source src="../<?= htmlspecialchars($download['video_path']); ?>" type="video/mp4">
                    </video>
                    <h3><?= htmlspecialchars($download['judul']); ?></h3>
                    <p><?= htmlspecialchars($download['deskripsi']); ?></p>
                    <!-- <small>Di-download pada: <?= $download['downloaded_at']; ?></small> -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
