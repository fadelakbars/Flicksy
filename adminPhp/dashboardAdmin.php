<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginAdmin.php");
    exit();
}

include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    // Cek direktori tujuan
    $target_dir = __DIR__ . "/../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // Buat folder jika belum ada
    }

    // Proses Video
    $video_ext = pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION);
    $video_name = "video_" . time() . "." . $video_ext;
    $video_file = $target_dir . $video_name;

    if (!move_uploaded_file($_FILES["video"]["tmp_name"], $video_file)) {
        die("Gagal mengunggah video.");
    }

    // Proses Thumbnail
    $thumbnile_ext = pathinfo($_FILES["thumbnile"]["name"], PATHINFO_EXTENSION);
    $thumbnail_name = "thumbnail_" . time() . "." . $thumbnile_ext;
    $thumbnail_file = $target_dir . $thumbnail_name;

    if (!move_uploaded_file($_FILES["thumbnile"]["tmp_name"], $thumbnail_file)) {
        die("Gagal mengunggah thumbnail.");
    }

    // Path relatif untuk database
    $relative_video_path = "uploads/" . $video_name;
    $relative_thumbnail_path = "uploads/" . $thumbnail_name;

    // Simpan ke database
    $query = "INSERT INTO videos (judul, deskripsi, video_path, thumbnile) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $judul, $deskripsi, $relative_video_path, $relative_thumbnail_path);

    if ($stmt->execute()) {
        header("Location: daftar_video.php");
        exit;
    } else {
        echo "Gagal menyimpan ke database: " . $stmt->error;
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
    <link rel="stylesheet" href="../css/dashboardAdmin.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Flicksy</h2>
        <a class="active" href="dashboardAdmin.php">Dashboard</a>
        <a href="daftar_video.php">Daftar Video</a>
        <div class="bottom-links">
            <a href="logoutAdmin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Upload Video</h1>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul">Judul :</label>
                    <input id="judul" name="judul" type="text" placeholder="Masukkan judul video" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi :</label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi video" required></textarea>
                </div>

                <div class="form-group">
                    <label for="video">Video :</label>
                    <input type="file" id="video" name="video" accept="video/*" required>
                </div>

                <div class="form-group">
                    <label for="thumbnile">Thumbnail :</label>
                    <input type="file" id="thumbnile" name="thumbnile" accept="image/*" required>
                </div>

                <button class="upload-btn" type="submit">
                    <i class="fas fa-upload"></i> Upload Video
                </button>
            </form>
        </div>
    </div>

    <!-- Modal untuk Edit Video -->

<!-- <script src="../js/dashboard_admin.js"></script> -->
</body>
</html>
