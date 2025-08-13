<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<html>

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Sistem Inventory | SMK Ma'arif Terpadu Cicalengka</title>
  <link rel="icon" type="image/png" href="public/smart.png">
  <meta name="description" content="Sistem Informasi Inventaris Barang di SMK Ma'arif Terpadu Cicalengka. Efisien, mudah, dan terstruktur.">
  <meta name="keywords" content="inventory barang, inventaris sekolah, SMK Ma'arif Cicalengka, sistem inventaris">

  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    /* Garis bawah pada menu aktif */
.navmenu a.active {
  position: relative;
  font-weight: bold;
  color: #0d6efd; /* Warna biru atau sesuai tema */
}

.navmenu a.active::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 2px;
  background-color: #0d6efd;
  left: 0;
  bottom: -5px;
}

/* Tombol login aktif (jika ingin diberi efek juga) */
.btn-getstarted.active {
  background-color: #0d6efd;
  color: #fff;
  border: 1px solid #0d6efd;
}

  </style>
</head>

<body class="index-page">
  <!-- Header -->
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center">
      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <img src="public/smart.png" alt="Logo SMK">
        <h1 class="sitename">Sistem Inventory</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Beranda</a></li>
          <li><a href="galeri.php" class="<?= $current_page === 'galeri.php' ? 'active' : '' ?>">Galeri</a></li>
          <li><a href="contact.php" class="<?= $current_page === 'contact.php' ? 'active' : '' ?>">Kontak</a></li>
        </ul>
      </nav>
      <a class="btn-getstarted <?= $current_page === 'login.php' ? 'active' : '' ?>" href="login.php">Login</a>
    </div>
  </header>

  <main class="main">