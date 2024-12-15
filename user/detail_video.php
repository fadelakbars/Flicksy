<?php
session_start();
include '../includes/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ambil ID video dari parameter URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID video tidak ditemukan.";
    exit();
}

$video_id = intval($_GET['id']);

// Ambil data video dari database
$query = "SELECT judul, deskripsi, video_path FROM videos WHERE video_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();
$video = $result->fetch_assoc();

if (!$video) {
    echo "Video tidak ditemukan!";
    exit();
}

$queryy = "SELECT * FROM videos ORDER BY created_at DESC LIMIT 15";
$resultt = $conn->query($queryy);
$videoss = $resultt->fetch_all(MYSQLI_ASSOC);

// Insert atau update ke watch history
if (isset($_SESSION['user_id']) && isset($video_id)) {
    $user_id = $_SESSION['user_id'];

    // Cek apakah data history sudah ada
    $check_query = "SELECT * FROM watch_history WHERE user_id = ? AND video_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $user_id, $video_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika sudah ada, update waktu tontonan
        $update_query = "UPDATE watch_history SET watched_at = NOW() WHERE user_id = ? AND video_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $user_id, $video_id);
        $stmt->execute();
    } else {
        // Jika belum ada, insert data baru
        $insert_query = "INSERT INTO watch_history (user_id, video_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $user_id, $video_id);
        $stmt->execute();
    }
}

// Cek apakah video sudah di-like
$like_query = "SELECT * FROM likes WHERE user_id = ? AND video_id = ?";
$stmt = $conn->prepare($like_query);
$stmt->bind_param("ii", $user_id, $video_id);
$stmt->execute();
$liked = $stmt->get_result()->num_rows > 0;

if (isset($_GET['video_id'])) {
    $video_id = intval($_GET['video_id']);
    $user_id = $_SESSION['user_id'];

    // Ambil path video dari database
    $query = "SELECT video_path FROM videos WHERE video_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $video_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();

    if ($video) {
        $video_path = "../" . $video['video_path'];

        // Cek apakah file video ada
        if (file_exists($video_path)) {
            // Catat video yang di-download
            $insert_query = "INSERT INTO downloads (user_id, video_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ii", $user_id, $video_id);
            $stmt->execute();

            // Header untuk mendownload file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($video_path) . '"');
            header('Content-Length: ' . filesize($video_path));
            flush();
            readfile($video_path);
            exit();
        } else {
            echo "File video tidak ditemukan.";
        }
    } else {
        echo "Video tidak valid.";
    }
} else {
    // echo "ID video tidak ditemukan.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($video['judul']); ?> - Detail Video</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/detail_video.css">
</head>
<body>
    <!-- Header -->
    <div class="header" style="text-align: center;">
        <a href="dashboard.php" style="text-decoration-line: none;">
            <h1>Flicksy</h1>
        </a>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Main Content -->
        <div class="main-content">
            <div class="video-container">
                <video controls autoplay>
                    <source src="../<?= htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                    Browser Anda tidak mendukung tag video.
                </video>
            </div>

            <!-- Video Footer -->
            <div class="video-footer">
                <div class="video-details">
                    <div class="title"><?= htmlspecialchars($video['judul']); ?></div>
                    <div class="synopsis"><?= htmlspecialchars($video['deskripsi']); ?></div>
                </div>
                <div class="video-actions">
                    <!-- <i class="fas fa-download" title="Download" id="download-btn" style="cursor: pointer;"></i> -->
                    <a href="download_video.php?video_id=<?= $video_id; ?>" title="Download">
                        <i class="fas fa-download" style="cursor: pointer;"></i>
                    </a>
                    <i class="fas fa-heart" 
                        title="Like" 
                        id="like-btn" 
                        style="cursor: pointer; color: <?= $liked ? '#e74c3c' : '#f39c12'; ?>;" 
                        data-video-id="<?= $video_id; ?>"></i>

                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="tabs">
                <div class="tab active">Video Terbaru</div>
            </div>
            <div class="video-list">
                <?php foreach ($videoss as $vid): ?>
                <a href="detail_video.php?id=<?= $vid['video_id']; ?>" style="text-decoration: none;">
                    <div class="video-item">
                        <img src="<?= htmlspecialchars('../' . $vid['thumbnile']); ?>" alt="Thumbnail">
                        <div class="video-title"><?= htmlspecialchars($vid['judul']); ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
document.getElementById("like-btn").addEventListener("click", function() {
    const likeBtn = this;
    const videoId = likeBtn.getAttribute("data-video-id");

    fetch("like_video.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `video_id=${videoId}`
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              likeBtn.style.color = data.liked ? "#e74c3c" : "#f39c12"; // Warna merah jika di-like
          } else {
              console.error(data.message);
          }
      }).catch(err => console.log(err));
});

    </script>
</body>
</html>
