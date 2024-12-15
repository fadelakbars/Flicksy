<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flicksy - User Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/user_profile.css">
</head>
<body>
    <div class="container">
        <!-- Header -->

        <!-- Profile Section -->
        <div class="profile">
            <img alt="Profile picture" src="../img/profile.jpg" />
            <div class="info">
                <p><strong>Nama:</strong> John Doe</p>
                <p><strong>Email:</strong> johndoe@example.com</p>
            </div>
            <div class="actions">
                <a href="dashboard.html"><button>Keluar</button></a>
            </div>
        </div>

        <!-- Form Edit Profile -->
        <div class="edit-profile">
            <h2>Edit Profil</h2>
            <form action="#" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username baru" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan email baru" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password baru" required>
                </div>
                <div class="form-group">
                    <label for="photo">Foto Profil</label>
                    <input type="file" id="photo" name="photo" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
