<?php

session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginAdmin.php");
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

if (isset($_GET['delete'])) {
    $video_id = intval($_GET['delete']); // Pastikan ID adalah integer

    // Ambil path video dan thumbnail berdasarkan ID
    $query = "SELECT video_path, thumbnile FROM videos WHERE video_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $video_id);
    $stmt->execute();
    $stmt->bind_result($video_path, $thumbnail_path);
    $stmt->fetch();
    $stmt->close();

    // Hapus file fisik video dan thumbnail jika ada
    if (file_exists($video_path)) {
        unlink($video_path);
    }

    if (file_exists($thumbnail_path)) {
        unlink($thumbnail_path);
    }

    // Hapus data dari database
    $delete_query = "DELETE FROM videos WHERE video_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $video_id);

    if ($delete_stmt->execute()) {
        header("Location: daftar_video.php"); // Redirect ke halaman daftar video
        exit();
    } else {
        echo "Gagal menghapus video: " . $delete_stmt->error;
    }
}

if (isset($_POST['update_video'])) {
    $video_id = $_POST['video_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    // Handle file upload jika ada
    $update_query = "UPDATE videos SET judul = ?, deskripsi = ?";

    if (!empty($_FILES['video']['name'])) {
        $video_path = 'uploads/' . time() . "_" . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], "../" . $video_path);
        $update_query .= ", video_path = '$video_path'";
    }

    if (!empty($_FILES['thumbnile']['name'])) {
        $thumbnail_path = 'uploads/' . time() . "_" . basename($_FILES['thumbnile']['name']);
        move_uploaded_file($_FILES['thumbnile']['tmp_name'], "../" . $thumbnail_path);
        $update_query .= ", thumbnile = '$thumbnail_path'";
    }

    $update_query .= " WHERE video_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $judul, $deskripsi, $video_id);

    if ($stmt->execute()) {
        header("Location: daftar_video.php");
        exit;
    } else {
        echo "Gagal memperbarui data: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Flicksy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/daftar_video.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Flicksy</h2>
        <a href="dashboardAdmin.php">Dashboard</a>
        <a href="daftar_video.php" class="active">Daftar Video</a>
        <div class="bottom-links">
            <a href="logoutAdmin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="search-bar">
            <form method="GET" action="daftar_video.php" style="display: flex; gap: 10px;">
                <input type="text" name="q" placeholder="Cari video..." value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" />
                <button type="submit" class="search-btn" style="padding: 10px 15px; background-color: #f39c12; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                    Cari
                </button>
            </form>
        </div>

        <!-- Data Dummy -->
        <?php foreach ($videos as $video): ?>
        <div class="video-item">
            <div class="video-thumbnail" onclick="openVideoModal('<?= htmlspecialchars('../' . $video['video_path']); ?>')">
                <img src="<?= htmlspecialchars('../' . $video['thumbnile']); ?>" alt="Thumbnail <?= htmlspecialchars($video['judul']); ?>" />
            </div>


            <div class="video-info">
                <h3><?= htmlspecialchars($video['judul']); ?></h3>
                <p><?= htmlspecialchars($video['deskripsi'] ?? 'Deskripsi tidak tersedia'); ?></p>
            </div>
            <div class="video-actions">
                <a href="javascript:void(0);" onclick="openEditModal('<?= $video['video_id']; ?>', '<?= addslashes($video['judul']); ?>', '<?= addslashes($video['deskripsi']); ?>')">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="daftar_video.php?delete=<?= $video['video_id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus video ini?');">
                    <i class="fas fa-trash-alt" style="color: red;"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<!-- Modal Video Player -->
<div id="videoModal" class="modal">
    <div class="modal-content">
        <span class="close close-video" onclick="closeVideoModal()">&times;</span>
        <video id="modalVideo" controls>
            <source id="modalVideoSource" src="" type="video/mp4">
            Browser Anda tidak mendukung pemutar video.
        </video>
    </div>
</div>

<!-- Modal Edit Video -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close close-edit">&times;</span>
        <h2>Edit Video</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" id="video_id" name="video_id">

            <div class="form-group">
                <label for="edit_judul">Judul</label>
                <input type="text" id="edit_judul" name="judul" placeholder="Masukkan judul video" required>
            </div>

            <div class="form-group">
                <label for="edit_deskripsi">Deskripsi</label>
                <textarea id="edit_deskripsi" name="deskripsi" placeholder="Masukkan deskripsi video" required></textarea>
            </div>

            <div class="form-group">
                <label for="edit_video">Video Baru (Opsional)</label>
                <input type="file" id="edit_video" name="video" accept="video/*">
            </div>

            <div class="form-group">
                <label for="edit_thumbnile">Thumbnail Baru (Opsional)</label>
                <input type="file" id="edit_thumbnile" name="thumbnile" accept="image/*">
            </div>

            <button type="submit" name="update_video">Simpan Perubahan</button>
        </form>
    </div>
</div>



    <script src="../js/dashboard_admin.js"></script>
</body>
</html>
