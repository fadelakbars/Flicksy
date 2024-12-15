<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "flicksy"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM films ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
   
    $film = $result->fetch_assoc();

   
    $sql_episodes = "SELECT * FROM episodes WHERE film_id = " . $film['id'];
    $episodes_result = $conn->query($sql_episodes);
} else {
    echo "No film found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }
        .video-container .play-button {
            position: absolute;
            bottom: 10px;
            left: 10px;
            font-size: 24px;
            color: white;
        }
        .title {
            font-size: 32px;
            margin: 20px 0 10px;
        }
        .subtitle {
            font-size: 14px;
            color: gray;
            margin-bottom: 20px;
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
        .episode-list {
            list-style: none;
            padding: 0;
        }
        .episode-list li {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #333;
            border-radius: 10px;
        }
        .episode-list .episode-info {
            flex: 1;
        }
        .episode-list .episode-info .episode-title {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .episode-list .episode-info .episode-time {
            font-size: 12px;
            color: gray;
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
                <div class="play-button">
                    <i class="fas fa-play"></i>
                </div>
            </div>
            <div class="title">
                <?php echo $film['title']; ?>
            </div>
            <div class="subtitle">
                Episode <?php echo $film['current_episode']; ?>
            </div>
            <div class="synopsis">
                <?php echo $film['synopsis']; ?>
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
                    Daftar Episode
                </div>
                <div>
                    Rekomendasikan
                </div>
            </div>
            <ul class="episode-list">
                <?php if ($episodes_result->num_rows > 0): ?>
                    <?php while($episode = $episodes_result->fetch_assoc()): ?>
                        <li>
                            <div class="episode-info">
                                <div class="episode-title">
                                    <?php echo $episode['title']; ?>
                                </div>
                                <div class="episode-time">
                                    <?php echo $episode['duration']; ?>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No episodes available</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
