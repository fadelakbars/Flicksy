<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/db.php';

$keyword = isset($_GET['q']) ? "%" . $_GET['q'] . "%" : "";

// Query untuk pengambilan data video
if (!empty($keyword)) {
    // Jika ada input keyword, cari video berdasarkan judul atau deskripsi
    $query = "SELECT * FROM videos WHERE judul LIKE ? OR deskripsi LIKE ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    $videos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Jika tidak ada keyword, tampilkan semua video
    $query = "SELECT * FROM videos ORDER BY created_at DESC";
    $result = $conn->query($query);
    $videos = $result->fetch_all(MYSQLI_ASSOC);
}

?>

<html>
<head>
    <title>Flicksy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/dashboard_user.css">
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">
            <h1>Flicksy</h1>
        </a>
        <a href="dashboard.php" class="active"><i class="fas fa-home"></i>Home</a>
        <a href="favorit.php"><i class="fas fa-heart"></i> Favorites</a>
        <a href="download.php"><i class="fas fa-download"></i> Downloads</a>
        <a href="history.php"><i class="fas fa-history"></i> History</a>
        <div class="bottom-links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    
    <div class="content">
        <div class="top-bar">
            <input id="search-input" placeholder="Search..." type="text" />
            <div class="icons">
                <a href="profile.php"><i class="fas fa-user-circle"></i></a>
            </div>
        </div>
    
        <div class="highlight">
            <h2>Today's Highlight</h2>
            <div class="cards">
            <?php foreach ($videos as $video): ?>
                <div class="card">
                    <a href="detail_video.php?id=<?= $video['video_id']; ?>">
                        <video width="100%" height="200" controls>
                            <source src="<?= htmlspecialchars('../' . $video['video_path']); ?>" type="video/mp4">
                        </video>
                        <h3><?= htmlspecialchars($video['judul']); ?></h3>
                        <p><?= htmlspecialchars($video['deskripsi'] ?? 'Deskripsi tidak tersedia'); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="../js/dashboard_user.js"></script>
    
</body>
</html>
