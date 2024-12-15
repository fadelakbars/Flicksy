<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flicksy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM admins WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if ($password == $admin['password']) {
                session_start();
                $_SESSION['admin'] = $admin['email'];

                header("Location: dashboardAdmin.php");
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Akun tidak ditemukan!";
        }
        $stmt->close();
    } else {
        $error = "Email dan password wajib diisi!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #d3d3d3;
            padding: 20px;
            text-align: left;
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            position: absolute;
            bottom: -40px;
            left: 20px;
            background-color: #f5f5f5;
            padding: 0 10px;
        }
        .container {
            max-width: 400px;
            margin: 70px auto;
            text-align: center;
        }
        .container button,
        .container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 25px;
            font-size: 16px;
        }
        .container button {
            background-color: #d4a24c;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .container button:hover {
            background-color: #b88a3a;
        }
        .container label {
            display: inline-block;
            margin-bottom: 5px;
            text-align: left;
            margin-left: 10px;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Login Admin</h1>
    </div>
    <div class="container">
        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
            <input type="password" name="password" placeholder="Silahkan masukkan kata sandi Anda" required>
            <button type="submit">Login</button>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>