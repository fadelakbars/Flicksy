<?php
// Anda dapat menambahkan logika PHP di sini jika diperlukan, seperti pengecekan login atau lainnya
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flicksy Logo Page</title>
  <link rel="stylesheet" href="utama.css">
  <script>
    // Mengarahkan pengguna ke halaman index.php setelah 3 detik
    setTimeout(function() {
      window.location.href = "index.php"; 
    }, 3000);
  </script>
</head>
<body>
  <div class="logo-screen">
    <div class="logo-container">
        <img src="tv-logo.png" alt="TV logo" class="tv-logo">
    </div>
  </div>
</body>
</html>
