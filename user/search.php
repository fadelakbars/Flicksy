<?php
require 'db.php';

$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// Query untuk mengambil hasil pencarian
$stmt = $conn->prepare("SELECT * FROM videos WHERE judul LIKE :search_query OR deskripsi LIKE :search_query ORDER BY video_id DESC");
$stmt->bindValue(':search_query', '%' . $search_query . '%');
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Menampilkan hasil pencarian dalam format card
if ($videos) {
    foreach ($videos as $video) {
        echo '<div class="card">
                <a href="detail_video.php?video_id=' . $video['video_id'] . '">
                    <video width="100%" height="300" controls>
                        <source src="flicksy/AdminPhp/uploads/videos/' . htmlspecialchars($video['video_path']) . '" type="video/mp4">
                    </video>
                    <h3>' . htmlspecialchars($video['judul']) . '</h3>
                    <p>' . htmlspecialchars($video['deskripsi']) . '</p>
                </a>
            </div>';
    }
} else {
    echo '<p>No results found</p>';
}
?>
