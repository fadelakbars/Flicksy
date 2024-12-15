<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $_SESSION['user_email']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM videos ORDER BY video_id DESC");
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
    <title>Flicksy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #d2b48c;
        }
        .sidebar {
            width: 250px;
            background-color: #b8860b;
            height: 100vh;
            position: fixed;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .sidebar h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .sidebar a i {
            margin-right: 10px;
            color: #000;
        }
        .sidebar .bottom-links {
            margin-top: auto;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-size: cover;
            background-position: center;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            height: 300px;
            position: relative;
            margin-top: -20px;
            margin-left: -20px;
            margin-right: -20px;
        }
        .top-bar input {
            width: 300px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            position: absolute;
            top: 10px;
            left: 60px;
        }
        .top-bar .icons {
            display: flex;
            align-items: center;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .top-bar .icons i {
            margin-left: 10px;
            font-size: 20px;
            color: #000;
        }
        .highlight {
            margin-top: 20px;
        }
        .highlight h2 {
            font-size: 24px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .highlight .filter {
            display: inline-block;
            margin-left: 20px;
        }
        .highlight .filter select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #d2b48c;
        }
        .highlight .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .highlight .card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 200px;
        }
        .highlight .card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .highlight .card h3 {
            font-size: 18px;
            margin: 10px;
            color: #000000; 
        }
        .highlight .card p {
            font-size: 14px;
            margin: 10px;
            color: #333333; 
        }
        .highlight .card a {
            text-decoration: none; 
        }

        .highlight .card a:hover {
            text-decoration: none; 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h1>Flicksy</h1>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="favorit.php"><i class="fas fa-heart"></i> Favorites</a>
        <a href="download.php"><i class="fas fa-download"></i> Downloads</a>
        <a href="history.php"><i class="fas fa-history"></i> History</a>
        <div class="bottom-links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <a href="#"><i class="fas fa-cog"></i> Settings</a>
        </div>
    </div>
    <div class="content">
        <div class="top-bar" style="background-image: url('image.png');">
            <input id="search-input" placeholder="Search" type="text" onkeyup="searchVideos()" />
            <div class="icons">
                <i class="fas fa-bell"></i>
                <a href="profil.php"><i class="fas fa-user-circle"></i></a>
            </div>
        </div>

        <div class="highlight">
            <h2>Today's Highlight</h2>

            <div class="filter">
                <select>
                    <option>Semua Tahun</option>
                </select>
            </div>

            <div class="cards" id="video-cards">
                <?php foreach ($videos as $video): ?>
                    <div class="card" class="video-card">
                        <a href="detail_video.php?video_id=<?= $video['video_id']; ?>">
                            <video width="100%" height="300" controls>
                                <source src="flicksy/AdminPhp/uploads/videos/<?= htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                            </video>
                            <h3><?= htmlspecialchars($video['judul']); ?></h3>
                            <p><?= htmlspecialchars($video['deskripsi']); ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function searchVideos() {
            var searchQuery = document.getElementById('search-input').value.toLowerCase();
            var videos = document.querySelectorAll('.video-card');

            videos.forEach(function(video) {
                var title = video.querySelector('h3').textContent.toLowerCase();
                var description = video.querySelector('p').textContent.toLowerCase();

                if (title.includes(searchQuery) || description.includes(searchQuery)) {
                    video.style.display = '';
                } else {
                    video.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
