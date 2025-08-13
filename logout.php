<?php
session_start();

// Hapus semua data session
$_SESSION = [];
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Logout</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #ffffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        text-align: center;
    }
    h1 {
        font-size: 55px;
        font-weight: bold;
        color: #0d6efd;
        margin-bottom: 20px;
    }
    p {
        font-size: 20px;
        color: #000000ff;
        margin-bottom: 10px;
    }
    .countdown {
        font-size: 24px;
        font-weight: bold;
        color: #ff0000ff;
    }
</style>
</head>
<body>
    <h1>Terima Kasih!</h1>
    <p>Anda telah berhasil logout dari sistem.</p>
    <p>Anda akan diarahkan ke halaman utama dalam 
       <span class="countdown" id="countdown">3</span> detik...</p>

<script>
    let counter = 3;
    const countdownElement = document.getElementById('countdown');

    const timer = setInterval(() => {
        counter--;
        countdownElement.textContent = counter;

        if (counter <= 0) {
            clearInterval(timer);
            window.location.href = "index.php"; // pindah ke halaman utama
        }
    }, 1000);
</script>
</body>
</html>
