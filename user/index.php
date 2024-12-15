<?php
session_start();
require 'db.php';

// Cek jika form registrasi dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Ambil data dari form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($email) || empty($password)) {
        echo "<script>alert('Harap isi semua kolom.');</script>";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid.');</script>";
    } else {
        // Hash password untuk keamanan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Periksa apakah email sudah ada di database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email sudah terdaftar. Silakan gunakan email lain.');</script>";
        } else {
            // Masukkan pengguna baru ke database
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                // Ambil user_id yang baru saja disimpan
                $user_id = $conn->lastInsertId();

                // Simpan user_id dan email di session setelah berhasil registrasi
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $email;

                echo "<script>
                        alert('Registrasi berhasil. Selamat datang di Flicksy!');
                        window.location.href = 'dashboard.php';
                      </script>";
                exit();
            } else {
                echo "<script>alert('Registrasi gagal. Silakan coba lagi.');</script>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }
        .highlight .card p {
            font-size: 14px;
            margin: 10px;
            color: #666;
        }
        .icon-button {
            position: absolute;
            top: 10px;
            font-size: 24px;
            color: #000;
            cursor: pointer;
        }
        .icon-button.left {
            left: 10px;
        }
        .icon-button.right {
            left: 40px;
        }
        .login-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .login-modal img {
            width: 100%;
            border-radius: 10px 10px 0 0;
        }
        .login-modal h2 {
            margin: 20px 0;
            font-size: 24px;
            display: inline-block;
        }
        .login-modal .alt-login {
            display: inline-block;
            float: right;
            margin-top: 20px;
        }
        .login-modal .email-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
        }
        .login-modal input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-modal button {
            width: 100%;
            padding: 10px;
            background-color: #b8860b;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
        }
        .login-modal .google-btn {
            background-color: white;
            color: black;
            border: 1px solid #ccc;
        }
        .login-modal .google-btn i {
            margin-right: 10px;
        }
        .login-modal .alt-login a {
            color: #b8860b;
            text-decoration: none;
        }
        .login-modal .email-section a {
            color: #C18D2B;
            text-decoration: none;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
  </style>

</head>
<body>
<div class="sidebar">
    <h1>Flicksy</h1>
    <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="#"><i class="fas fa-home"></i> Home</a>
    <a href="#"><i class="fas fa-heart"></i> Favorites</a>
    <a href="#"><i class="fas fa-download"></i> Downloads</a>
    <a href="#"><i class="fas fa-history"></i> History</a>
    <div class="bottom-links">
        <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <a href="#"><i class="fas fa-cog"></i> Settings</a>
    </div>
</div>
<div class="content">
    <div class="top-bar" style="background-image: url('image.png');">
        <i class="fas fa-angle-left icon-button left"></i>
        <i class="fas fa-angle-right icon-button right"></i>
        <input placeholder="Search" type="text"/>
        <div class="icons">
            <i class="fas fa-bell"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>
    <div class="highlight">
        <h2>Today's Highlight</h2>
        <div class="filter">
            <select>
                <option>Semua Tahun</option>
            </select>
        </div>
        <div class="cards">
            <div class="card">
                <img src="https://storage.googleapis.com/a1aa/image/me4O3T47V8QpDKAsGRaRSkyki10fY8DEsotOV3N085a7SLzTA.jpg" />
                <h3>VINCENZO</h3>
                <p>Park Joo-hyung was adopted by an Italian family at the age of eight and sent to Italy.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Login -->
<div class="modal-overlay"></div>
<div class="login-modal">
    <img height="150" src="4.jpg" width="400"/>
    <h2>Daftar</h2>
    <div class="alt-login">
        <span>Atau</span>
        <span class="alt-login-right"><a href="#">Masuk</a></span>
    </div>
    <form method="POST" action="index.php">
        <button class="google-btn" type="submit" name="google-login">
            <i class="fab fa-google"></i> Daftar dengan Google
        </button>
        <div class="email-section">
            <span>Email</span>
            <span class="alt-login-right"><a href="#" style="color: #C18D2B;">Daftar dengan nomor ponsel</a></span>
        </div>
        <input placeholder="Masukkan email Anda" type="email" name="email"/>
        <input placeholder="Silahkan masukkan kata sandi Anda" type="password" name="password"/>
        <button type="submit" name="register">Daftar</button>
    </form>
</div>
</body>
</html>
