<?php
session_start();
include '../includes/db.php';

$error = '';

// Proses form login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data admin dari database
    $query = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Cek password (disini plaintext, gunakan hash jika diperlukan)
        if ($password === $admin['password']) {
            // Simpan informasi login ke session
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_email'] = $admin['email'];
            header("Location: dashboardAdmin.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/adminLogin.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <i class="fas fa-user-shield"></i>
            <h1>Login Admin</h1>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <form method="POST" action="">
            <h2>Silakan Masuk</h2>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
            
            <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
            <div class="error">Email atau password salah!</div>
        </form>
    </div>
</body>
</html>
