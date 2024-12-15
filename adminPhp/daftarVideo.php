<?php
$servername = "localhost"; 
$username = "root";       
$password = "";           
$dbname = "flicksy";     

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "SELECT video_path FROM videos WHERE video_id = $delete_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
 
        unlink($row['video_path']);
    }

    $sql = "DELETE FROM videos WHERE video_id = $delete_id";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Video berhasil dihapus');</script>";
} else {
    echo "<script>alert('Error saat menghapus video: " . $conn->error . "');</script>";
}

}

$sql = "SELECT * FROM videos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Flicksy</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
      rel="stylesheet"
    />
    <style>
      body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #d1b18a;
      }
      .sidebar {
        width: 250px;
        background-color: #c18a2b;
        height: 100vh;
        position: fixed;
        padding: 20px;
        box-sizing: border-box;
      }
      .sidebar h1 {
        color: white;
        font-size: 24px;
        margin-bottom: 20px;
      }
      .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        margin: 10px 0;
        font-size: 18px;
      }
      .sidebar a i {
        margin-right: 10px;
      }
      .sidebar .bottom-links {
        position: absolute;
        bottom: 20px;
        width: 100%;
      }
      .sidebar .bottom-links a {
        display: flex;
        align-items: center;
      }
      .sidebar .bottom-links a i {
        margin-right: 10px;
      }
      .content {
        margin-left: 250px;
        padding: 20px;
      }
      .search-bar {
        display: flex;
        align-items: center;
        background-color: #e0d4c3;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
      }
      .search-bar input {
        border: none;
        background: none;
        outline: none;
        flex: 1;
        padding: 5px;
      }
      .search-bar i {
        margin-right: 10px;
      }
      .video-item {
        display: flex;
        background-color: #e0d4c3;
        border-radius: 5px;
        margin-bottom: 20px;
        padding: 10px;
        align-items: center;
      }
      .video-item .video-thumbnail {
        width: 100px;
        height: 100px;
        background-color: #333;
        border-radius: 5px;
        margin-right: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 14px;
      }
      .video-item .video-info {
        flex: 1;
      }
      .video-item .video-info h3 {
        margin: 0;
        font-size: 20px;
      }
      .video-item .video-info p {
        margin: 5px 0 0;
        font-size: 14px;
      }
      .video-item .video-actions {
        display: flex;
        align-items: center;
      }
      .video-item .video-actions i {
        margin-left: 10px;
        cursor: pointer;
      }
    </style>
  </head>
  <body>
    <div class="sidebar">
      <h1>Flicksy</h1>
      <a href="dashboardAdmin.php">Dashboard</a>
      <a href="#">Daftar Video</a>
      <div class="bottom-links">
        <a href="#">
          <i class="fas fa-cog"> </i>
          Pengaturan
        </a>
        <a href="logoutAdmin.php">
          <i class="fas fa-sign-out-alt"> </i>
          Logout
        </a>
      </div>
    </div>
    <div class="content">
      <div class="search-bar">
        <i class="fas fa-search"> </i>
        <input placeholder="Cari..." type="text" />
      </div>

      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $video_file = $row['video_path']; 
            $thumbnail = "assets/thumbnails/" . basename($video_file) . ".jpg"; // Lokasi thumbnail
            
            // Menampilkan video item
            echo '
            <div class="video-item">
              <div class="video-thumbnail">
                <span>Video</span>
              </div>
              <div class="video-info">
                <h3>' . $row['judul'] . '</h3>
                <p>' . $row['deskripsi'] . '</p>
              </div>
              <div class="video-actions">
                <!-- Edit video mengarah ke dashboardAdmin.php dengan ID video -->
                <a href="dashboardAdmin.php?id=' . $row['video_id'] . '"><i class="fas fa-edit"> </i></a>
                <a href="?delete=' . $row['video_id'] . '"><i class="fas fa-trash-alt"> </i></a>
              </div>
            </div>';
        }
      } else {
        echo "Tidak ada video ditemukan.";
      }
      ?>
    </div>
  </body>
</html>

<?php
$conn->close();
?>
