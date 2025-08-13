<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>403 - Akses Ditolak</title>
  <link rel="icon" type="image/png" href="public/smart.png">
  <meta http-equiv="refresh" content="5;url=login.php">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f0f0;
      color: #333;
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .box {
      background-color: #e2e3e5;
      border: 1px solid #d6d8db;
      border-radius: 8px;
      padding: 40px;
      max-width: 500px;
      width: 90%;
      text-align: center;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h1 {
      font-size: 48px;
      margin: 0;
      color: #6c757d;
    }
    h2 {
      margin-top: 20px;
      font-size: 24px;
    }
    .countdown {
      font-weight: bold;
      font-size: 24px;
      color: #555;
    }
    a {
      color: #007bff;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="box">
    <h1>403</h1>
    <h2>Maaf, Anda harus login terlebih dahulu.</h2>
    <p>Anda akan diarahkan ke halaman login dalam <span class="countdown" id="counter">5</span> detik...</p>
    <p><a href="login.php">Klik di sini</a> jika tidak diarahkan otomatis.</p>
  </div>

  <script>
    let count = 5;
    const counterElement = document.getElementById("counter");
    const interval = setInterval(() => {
      count--;
      if (count <= 0) {
        clearInterval(interval);
      } else {
        counterElement.textContent = count;
      }
    }, 1000);
  </script>
</body>
</html>
