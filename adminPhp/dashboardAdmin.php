<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: loginAdmin.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flicksy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $gender = $_POST['gender'];
    $deskripsi = $_POST['deskripsi'];
    $resolusi = $_POST['resolusi'];
    $jenis = $_POST['jenis'];

    if ($_FILES['video']['size'] > 1024 * 1024 * 1024) { // 1GB
        $error = "File terlalu besar! Maksimum ukuran file adalah 1GB.";
    } else {
        if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
            $video_name = $_FILES['video']['name'];
            $video_tmp_name = $_FILES['video']['tmp_name'];
            $video_ext = pathinfo($video_name, PATHINFO_EXTENSION);
            $allowed_ext = ['mp4', 'avi', 'mov', 'mkv'];

            if (!in_array($video_ext, $allowed_ext)) {
                $error = "Hanya file video (MP4, AVI, MOV, MKV) yang diperbolehkan!";
            } else {
                $video_upload_dir = "uploads/videos/";

                if (!file_exists($video_upload_dir)) {
                    mkdir($video_upload_dir, 0777, true);
                }

                $video_new_name = uniqid() . '.' . $video_ext;
                $video_upload_path = $video_upload_dir . $video_new_name;

                if (move_uploaded_file($video_tmp_name, $video_upload_path)) {
                    chmod($video_upload_path, 0644);

                    $stmt = $conn->prepare("INSERT INTO videos (judul, genre, deskripsi, resolusi, jenis, video_path) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $judul, $gender, $deskripsi, $resolusi, $jenis, $video_upload_path);

                    if ($stmt->execute()) {
                        $success = "Video berhasil diupload!";
                        header("Location: dashboardAdmin.php");
                        exit();
                    } else {
                        $error = "Gagal menyimpan data!";
                    }
                    $stmt->close();
                } else {
                    $error = "Gagal mengupload video!";
                }
            }
        } else {
            $error = "Video harus di-upload!";
        }
    }
}

$folderPath = 'uploads/videos/';

if (is_dir($folderPath)) {
    if (chmod($folderPath, 0755)) {
    } else {
        echo "Gagal mengubah hak akses folder.";
    }
} else {
    echo "Folder tidak ditemukan.";
}
$videos = [];
$result = $conn->query("SELECT * FROM videos ORDER BY video_id DESC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Flicksy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #e0d7c6;
        }
        .sidebar {
            width: 200px;
            background-color: #c18d2b;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
        }
        .sidebar h2 {
            color: white;
            margin: 0 0 20px 0;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .sidebar .logout,
        .sidebar .settings {
            position: absolute;
            bottom: 20px;
            width: 100%;
        }
        .sidebar .settings {
            bottom: 60px;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .form-container {
            background-color: #c18d2b;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            color: white;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
        }
        .form-container input,
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
        }
        .upload-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #c18d2b;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .upload-btn:hover {
            background-color: #a47521;
        }
        .videos {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .videos video {
            width: 100%;
            border: none;
            outline: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Flicksy</h2>
        <a href="#">Dashboard</a>
        <a href="daftarVideo.php">Daftar Video</a>
        <div class="settings">
            <a href="#">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </div>
        <div class="logout">
            <a href="logoutAdmin.php">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>

    <div class="content">
        <?php if ($error) { echo "<p style='color:red;'>$error</p>"; } ?>
        <?php if ($success) { echo "<p style='color:green;'>$success</p>"; } ?>

        <!-- Form Upload -->
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <label for="judul">Judul :</label>
                <input id="judul" name="judul" type="text" required /><br>

                <label for="gender">Genre :</label>
                <select id="gender" name="gender" required>
                    <option>Romance</option>
                    <option>Horror</option>
                    <option>Action</option>
                    <option>Comedy</option>
                </select><br>

                <label for="deskripsi">Deskripsi :</label>
                <textarea id="deskripsi" name="deskripsi" required></textarea><br>

                <label for="resolusi">Resolusi :</label>
                <select id="resolusi" name="resolusi" required>
                    <option>720p</option>
                    <option>1080p</option>
                    <option>480p</option>
                    <option>240p</option>
                </select><br>

                <label for="jenis">Jenis :</label>
                <select id="jenis" name="jenis" required>
                    <option>Private</option>
                    <option>Public</option>
                </select><br>

                <label for="video">Video:</label>
                <input type="file" id="video" name="video" accept="video/*" required><br>

                <button class="upload-btn" type="submit">
                    <i class="fas fa-upload"></i> Upload Video
                </button>
            </form>
        </div>
    </div>
</body>
</html>
