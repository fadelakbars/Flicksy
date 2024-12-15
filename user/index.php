<?php
session_start();
include '../includes/db.php';

$error_message = "";

// Proses registrasi
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Validasi input tidak kosong
    if (!empty($username) && !empty($email) && !empty($_POST['password'])) {
        // Cek apakah email sudah terdaftar
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email sudah digunakan!";
        } else {
            // Insert data ke tabel users
            $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $error_message = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error_message = "Terjadi kesalahan saat mendaftar.";
            }
        }
    } else {
        $error_message = "Harap isi semua kolom.";
    }
}

// Proses login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Cek data user berdasarkan email
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Simpan session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Password salah!";
            }
        } else {
            $error_message = "Email tidak ditemukan!";
        }
    } else {
        $error_message = "Harap isi semua kolom.";
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
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<div class="modal-overlay"></div>
<div class="login-modal">
    <img height="150" src="../img/4.jpg" width="400"/>
    <h2 id="form-title">Daftar</h2> <!-- Tambahkan ID ini -->
    <div class="alt-login">
        <span>Atau</span>
        <span class="alt-login-right">
            <a href="#" id="toggle-form">Masuk</a> <!-- Tambahkan ID ini -->
        </span>
    </div>
    <!-- Form Registrasi -->
    <form method="POST" action="" id="register-form">
        <input placeholder="Masukkan Nama Anda" type="text" name="username"/>
        <input placeholder="Masukkan email Anda" type="email" name="email"/>
        <input placeholder="Silahkan masukkan kata sandi Anda" type="password" name="password"/>
        <button type="submit" name="register">Daftar</button>
    </form>
    <!-- Form Login -->
    <form method="POST" action="" id="login-form" style="display: none;">
        <input placeholder="Masukkan email Anda" type="email" name="email"/>
        <input placeholder="Silahkan masukkan kata sandi Anda" type="password" name="password"/>
        <button type="submit" name="login">Masuk</button>
        
    </form>
    
</div>


<script src="../js/script.js"></script>

</body>
</html>
