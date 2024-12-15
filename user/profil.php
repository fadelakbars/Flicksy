<?php
session_start();
require 'db.php';

// Cek jika user sudah login (dengan adanya session user_id)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");  // Mengarahkan ke halaman login jika belum login
    exit();
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Query untuk mengambil data pengguna
$sql_user = "SELECT * FROM users WHERE user_id = :user_id";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Query untuk mengambil data paket pengguna
$sql_package = "SELECT * FROM user_packages WHERE user_id = :user_id";
$stmt_package = $conn->prepare($sql_package);
$stmt_package->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_package->execute();
$package = $stmt_package->fetch(PDO::FETCH_ASSOC);
?>

<html>
 <head>
  <title>Flicksy</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
   body {
       background-color: #000;
       color: #fff;
       font-family: Arial, sans-serif;
       margin: 0;
       padding: 0;
   }
   .container {
       padding: 20px;
   }
   .header {
       display: flex;
       justify-content: space-between;
       align-items: center;
   }
   .header .title {
       color: #f0a500;
       font-size: 24px;
   }
   .header .icons {
       display: flex;
       gap: 20px;
   }
   .header .icons i {
       font-size: 24px;
   }
   .profile {
       display: flex;
       align-items: center;
       margin-top: 20px;
   }
   .profile img {
       border-radius: 50%;
       width: 50px;
       height: 50px;
       margin-right: 10px;
   }
   .profile .info {
       display: flex;
       align-items: center;
       margin-right: auto;
   }
   .profile .info p {
       margin: 5px 10px 5px 0;
   }
   .profile .actions {
       display: flex;
       gap: 10px;
   }
   .profile .actions button {
       background-color: #333;
       color: #fff;
       border: 1px solid #fff;
       padding: 5px 10px;
       cursor: pointer;
   }
   .package-info {
       margin-top: 20px;
       display: flex;
       justify-content: space-between;
   }
   .package-info .left,
   .package-info .right {
       width: 48%;
   }
   .package-info p {
       margin: 5px 0;
   }
   .menu {
       display: flex;
       justify-content: space-around;
       margin-top: 20px;
       padding-bottom: 10px;
   }
   .menu div {
       cursor: pointer;
       position: relative;
   }
   .menu div:first-child::after {
       content: '';
       display: block;
       width: 100%;
       height: 2px;
       background-color: #f0a500;
       position: absolute;
       bottom: -10px;
       left: 0;
   }
   .menu a {
    color: white; /* Mengubah warna teks menjadi putih */
    text-decoration: none; /* Menghapus garis bawah */

   }
   .custom-link {
    color: white; /* Mengubah warna teks menjadi putih */
    text-decoration: none; /* Menghapus garis bawah */
}

   .content {
       margin-top: 20px;
   }
   .content .section {
       margin-bottom: 20px;
   }
   .footer {
       position: fixed;
       bottom: 20px;
       right: 20px;
       display: flex;
       gap: 20px;
   }
   .footer i {
       font-size: 24px;
   }
  </style>
 </head>
 <body>
  <div class="container">
   <div class="header">
    <div class="title">Flicksy</div>
    <div class="icons">
     <i class="fas fa-download"></i>
     <i class="fas fa-history"></i>
     <i class="fas fa-star"></i>
     <i class="fas fa-search"></i>
     <i class="fas fa-user-circle"></i>
    </div>
   </div>

   <div class="profile">
    <img alt="Profile picture" height="50" src="<?= isset($user['profile_picture']) ? $user['profile_picture'] : 'default.jpg'; ?>" width="50"/>
    <div class="info">
    <p><strong>Nama:</strong> <?= isset($user['name']) ? $user['name'] : 'Tidak ditemukan'; ?></p>
    <p><strong>Email:</strong> <?= isset($user['email']) ? $user['email'] : 'Tidak ditemukan'; ?></p>
    <p><strong>Identitas Pengguna:</strong> <?= isset($user['user_id']) ? $user['user_id'] : 'Tidak ditemukan'; ?></p>
     <div class="actions">
      <button>Edit profil</button>
      <a href="dashboard.php"><button>Keluar</button></a>
     </div>
    </div>
   </div>

   <div class="package-info">
    <div class="left">
        <p>Akun Terdaftar: <?= isset($user['account_type']) ? $user['account_type'] : 'Tidak Ditemukan'; ?></p>
        <p>Kontak Email: <?= isset($user['email']) ? $user['email'] : 'Tidak Ditemukan'; ?></p>
        <p><strong>Identitas Pengguna:</strong> <?= isset($user['user_id']) ? $user['user_id'] : 'Tidak Ditemukan'; ?></p>
        <h2>Jenis Paket:</h2>
        <p>
            <?php
            if ($package) {
                echo isset($package['package_name']) ? $package['package_name'] : 'Tidak ada paket';
            } else {
                echo 'Tidak ada paket';
            }
            ?>
        </p>
    </div>
   </div>

   <div class="menu">
    <div>Paket saya</div>
    <div>
    <a href="favorit.php">Favorites</a>
    </div>
    <div>
    <a href="history.php">History</a>
    </div>
    <div>
        <a href="download.php">Downloads</a>
    </div>
   </div>

   <div class="content">
    <div class="section">
    <h3>Perincian Paket</h3>
    <p><strong>Bulan:</strong> <?= isset($package['duration']) ? $package['duration'] : 'Tidak ada informasi'; ?></p>
    <p><strong>Unduhan:</strong> <?= isset($package['downloads_limit']) ? $package['downloads_limit'] : 'Tidak ada informasi'; ?></p>
    </div>
   </div>

   <div class="footer">
    <i class="fab fa-facebook"></i>
    <i class="fab fa-instagram"></i>
    <i class="fab fa-youtube"></i>
    <i class="fab fa-telegram"></i>
    <i class="fab fa-twitter"></i>
   </div>
  </div>
 </body>
</html>
