<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flicksy";

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$video_id = isset($_GET['video_id']) ? (int)$_GET['video_id'] : null;

if ($video_id) {
    $stmt = $conn->prepare("SELECT * FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $video_id);
    $stmt->execute();
    $result_video = $stmt->get_result();
    if ($result_video && $result_video->num_rows > 0) {
        $video = $result_video->fetch_assoc();
    } else {
        $video = null;
    }
    $stmt->close();
} else {
    echo "Video ID tidak ditemukan.";
    exit;
}

if (!$video) {
    echo "Video tidak ditemukan.";
    exit;
}

$video_filename = $video['video_path'];

$video_url = '/Flicksy/AdminPhp/uploads/videos/' . htmlspecialchars(basename($video_filename));
$valid_path = realpath($_SERVER['DOCUMENT_ROOT'] . $video_url);

if (!$valid_path || !file_exists($valid_path)) {
    echo "Video file not found. Checked path: <br>";
    echo "Checked Path: " . htmlspecialchars($valid_path) . " - NOT FOUND<br>";
    echo "<h3>Video Database Info:</h3>";
    print_r($video);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flicksy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: row;
            padding: 20px;
            justify-content: space-between;
        }
        .main-content {
            flex: 3;
            padding-right: 20px;
        }
        .sidebar {
            flex: 1;
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
        }
        .header {
            font-size: 24px;
            color: orange;
            margin-bottom: 20px;
        }
        .video-container {
            position: relative;
            width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .video-container video {
            width: 100%;
            max-width: 900px;
            height: auto;
            border-radius: 10px;
        }
        .title {
            font-size: 32px;
            margin: 20px 0 10px;
        }
        .synopsis {
            font-size: 16px;
            line-height: 1.5;
        }
        .sidebar .menu {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .sidebar .menu i {
            font-size: 20px;
        }
        .sidebar .tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .sidebar .tabs div {
            cursor: pointer;
            padding: 10px;
            border-bottom: 2px solid transparent;
        }
        .sidebar .tabs .active {
            border-bottom: 2px solid orange;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="main-content">
        <div class="header">
            Flicksy
        </div>

        <div class="video-container">
            <?php if ($video && file_exists($valid_path)): ?>
                <video controls autoplay>
                    <source src="<?= $video_url ?>" type="video/mp4">
                    Browser Anda tidak mendukung tag video.
                </video>
                <div class="title"><?= htmlspecialchars($video['judul']); ?></div>
                <div class="synopsis"><?= htmlspecialchars($video['deskripsi']); ?></div>
            <?php else: ?>
                <p>Video tidak ditemukan atau tidak dapat diakses.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="sidebar">
        <div class="menu">
            <i class="fas fa-download"></i>
            <i class="fas fa-history"></i>
            <i class="fas fa-heart"></i>
            <i class="fas fa-search"></i>
            <i class="fas fa-ellipsis-h"></i>
        </div>
        <div class="tabs">
            <div class="active">
                Video Terbaru
            </div>
            <div>
                Rekomendasikan
            </div>
        </div>
    </div>
</div>

<script>
    const video = document.querySelector('video');
    video?.addEventListener('canplay', function() {
        this.muted = false;
    });
</script>
</body>
</html>

<?php
$conn->close();
?>
