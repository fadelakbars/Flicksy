<?php
session_start();

// Sertakan koneksi database
include '../includes/db.php'; // Pastikan path-nya benar sesuai struktur folder Anda

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat tontonan dari database
$query = "SELECT v.judul, v.deskripsi, v.video_path, h.watched_at 
          FROM watch_history h
          JOIN videos v ON h.video_id = v.video_id
          WHERE h.user_id = ?
          ORDER BY h.watched_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$history = $result->fetch_all(MYSQLI_ASSOC);

if (isset($_POST['delete_all'])) {
    $user_id = $_SESSION['user_id'];

    // Query untuk menghapus semua history berdasarkan user_id
    $delete_all_query = "DELETE FROM watch_history WHERE user_id = ?";
    $stmt = $conn->prepare($delete_all_query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect ke halaman history setelah berhasil menghapus semua data
        header("Location: history.php");
        exit();
    } else {
        echo "Gagal menghapus semua history: " . $stmt->error;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Tontonan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard_user.css">
</head>
<body>

<style>
    .history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.delete-all-btn {
    background-color: #e74c3c;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.delete-all-btn:hover {
    background-color: #c0392b;
}

</style>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php"><h1>Flicksy</h1></a>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="favorit.php"><i class="fas fa-heart"></i> Favorites</a>
        <a href="download.php"><i class="fas fa-download"></i> Downloads</a>
        <a href="history.php" class="active"><i class="fas fa-history"></i> History</a>
        <div class="bottom-links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>Riwayat Tontonan</h2>
        <div class="history-header">
            <form method="POST" action="history.php" >
                <button type="submit" name="delete_all" class="delete-all-btn">Hapus Semua Riwayat</button>
            </form>
        </div>
        <div class="cards">
            <?php if (count($history) > 0): ?>
                <?php foreach ($history as $item): ?>
                    <div class="card">
                        <video width="100%" height="200" controls>
                            <source src="../<?= htmlspecialchars($item['video_path']); ?>" type="video/mp4">
                        </video>
                        <h3><?= htmlspecialchars($item['judul']); ?></h3>
                        <p><?= htmlspecialchars($item['deskripsi']); ?></p>
                        <!-- <small>Ditonton pada: <?= htmlspecialchars($item['watched_at']); ?></small> -->
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada riwayat tontonan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
