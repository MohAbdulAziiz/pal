<?php
// Mulai session hanya jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../koneksi.php'; 

// Cek apakah user sudah login dan rolenya adalah 'Siswa'
if (!isset($_SESSION['id_users']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Super Admin') {
    // Jika tidak sesuai, arahkan ke halaman akses ditolak
    header("Location: /pal/akses-ditolak.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$id_users = $_SESSION['id_users'];

// Ambil data pengguna dari database
$query = mysqli_query($conn, "SELECT nama, role, foto FROM users WHERE id_users = '$id_users'");
if (!$query || mysqli_num_rows($query) === 0) {
    // Jika data user tidak ditemukan, logout paksa
    header("Location: /pal/logout.php");
    exit;
}

$user = mysqli_fetch_assoc($query);

// Validasi bahwa data role dari database cocok dengan session
if ($user['role'] !== $_SESSION['role']) {
    // Jika tidak cocok, logout untuk keamanan
    header("Location: /pal/logout.php");
    exit;
}

// Penentuan path foto
$fotoFolder = "../public/uploads/";
$fotoURL = "/pal/public/uploads/";
$fotoDefault = "/pal/public/users_default.png";

// Gunakan foto dari database jika ada dan file fisiknya tersedia
if (!empty($user['foto']) && file_exists($fotoFolder . $user['foto'])) {
    $fotoPathForHTML = $fotoURL . $user['foto'];
} else {
    $fotoPathForHTML = $fotoDefault;
}
?>

<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Inventory | SMK Ma'arif Terpadu Cicalengka</title>
  <link rel="icon" type="image/png" href="../public/smart.png">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
 <style>
  .sidebar {
  transition: width 0.3s ease, transform 0.3s ease;
}

#mobileOverlay {
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
}

#mobileOverlay.active {
  opacity: 1;
  pointer-events: all;
}

    body, html {
      margin: 0;
      height: 100%;
      overflow: hidden;
    }

    .navbar-custom {
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 60px;
      background-color: #3c74f2;
      color: white;
      display: flex;
      align-items: center;
      z-index: 1060;
      padding: 0 15px;
      transition: width 0.3s ease, transform 0.3s ease;
    }

    .sidebar {
      position: fixed;
      padding: 20px 15px;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background-color: #0d0d1f;
      color: white;
      overflow: hidden;
      z-index: 1050;
      transition: width 0.3s ease, transform 0.3s ease;
      scrollbar-width: none;         /* Firefox */
    }
    .sidebar::-webkit-scrollbar {
      width: 0px;                   /* Chrome, Safari */
    }

    .main-content {
      margin-left: 250px;
      margin-top: 60px;
      height: calc(100vh - 60px);
      overflow-y: auto;
      padding: 20px;
      background-color: #f8f9fa;
    }

    .sidebar-collapsed {
      width: 70px;
    }

    .sidebar-collapsed ~ .navbar-custom {
      left: 70px;
    }

    .sidebar-collapsed ~ .main-content {
      margin-left: 70px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      margin: 10px 0;
    }

    .sidebar ul li a {
      color: white;
      padding: 8px 10px;
      display: flex;
      align-items: center;
      border-radius: 5px;
      text-decoration: none;
    }

    .sidebar ul li a:hover {
      background-color: rgba(255,255,255,0.1);
    }

    .sidebar ul li a i {
      width: 25px;
      text-align: center;
    }

    .sidebar-collapsed .sidebar-text {
      display: none;
    }

    .sidebar-menu {
        max-height: calc(100vh - 230px); /* 230px untuk avatar dan padding atas-bawah */
        overflow-y: auto;
        padding-right: 5px;
        scrollbar-width: none;        /* Firefox */
      }

      .sidebar-menu::-webkit-scrollbar {
        width: 0px;                  /* Chrome, Safari */
      }

      .app-menu__item.active {
      position: relative;
      background-color: rgba(255, 255, 255, 0.1);
      font-weight: bold;
    }

    .app-menu__item.active::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 4px;
      background-color: #3c74f2;
      border-top-left-radius: 5px;
      border-bottom-left-radius: 5px;
    }

    .toggle-btn,
    .user-btn {
      background: none;
      border: none;
      color: white;
      font-size: 20px;
      margin-right: 10px;
    }

    .dropdown-menu {
      background-color:rgb(255, 255, 255);
      border: none;
    }

    .dropdown-item:hover {
      background-color:rgb(255, 255, 255);
    }

    .sidebar-collapsed .text-center img {
      width: 50px !important;
      height: 50px !important;
    }

    .sidebar-collapsed .text-center p,
    .sidebar-collapsed .text-center small {
      display: none;
    }

    .sidebar.sidebar-visible-mobile {
      z-index: 1061;
    }

@media (max-width: 992px) {
  .sidebar {
    transform: translateX(-100%);
    position: fixed;
    left: 0;
    top: 0;
    transition: transform 0.3s ease;
  }

  .sidebar.sidebar-visible-mobile {
    transform: translateX(0%);
  }

  .navbar-custom {
    left: 0 !important;
    transition: left 0.3s ease;
  }

  .main-content {
    margin-left: 0 !important;
  }

  #mobileOverlay {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0,0,0,0.4);
    z-index: 1040;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }

  #mobileOverlay.active {
    opacity: 1;
    pointer-events: all;
  }
}
    .sidebar-collapsed .sidebar-text {
  display: none;
}

/* Smooth transition for all key elements */
.navbar-custom,
.sidebar,
.main-content {
  transition: all 0.3s ease;
}

/* Hapus garis/border hitam di ikon user dan hamburger */
.user-btn:focus, .toggle-btn:focus {
  outline: none;
  box-shadow: none;
}

/* Hover efek untuk hamburger */
.toggle-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 5px;
  cursor: pointer;
}

/* Class otomatis untuk layar kecil */
@media (max-width: 992px) {
  body.sidebar-hidden .sidebar {
    transform: translateX(-100%);
  }

  body.sidebar-hidden .navbar-custom {
    left: 0 !important;
  }

  body.sidebar-hidden .main-content {
    margin-left: 0 !important;
  }
}

.transition-chevron {
  transition: transform 0.3s ease;
}

/* Saat aktif, putar chevron ke bawah */
#submenuLaporan.show + #chevronLaporan,
[data-expanded='true'] #chevronLaporan {
  transform: rotate(90deg);
}

  </style>

</head>
<body>

<div id="mobileOverlay"></div>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <!-- Profil Pengguna -->
    <div class="text-center mb-4">
      <img src="../public/huhu.png" class="rounded-circle mb-2" width="130" height="130" style="object-fit: cover;">
      <p class="app-sidebar__user-name mb-0 sidebar-text">Sistem Inventaris</p>
      <small class="text-muted sidebar-text"><p class="app-sidebar__user-role mb-0">SMK Ma'arif Terpadu Cicalengka</p></small>
    </div>
    <hr class="bg-light">

    <!-- Menu Navigasi -->
<div class="sidebar-menu">
  <ul>
    <ul>
      <li class="text-uppercase font-weight-bold small mb-2 sidebar-text">Dashboard</li>
      <li>
        <a href="dashboard.php" class="app-menu__item <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
          <i class="fas fa-tachometer-alt"></i> 
          <span class="sidebar-text ml-2">Dashboard</span>
        </a>
      </li>
      <hr class="bg-light">

      <!-- Manajemen Inventaris -->
      <li class="text-uppercase font-weight-bold small mb-2 sidebar-text">MASTER DATA</li>

      <li>
        <a href="users.php" class="app-menu__item <?= ($current_page === 'users.php' || $current_page === 'users_tambah.php' || $current_page === 'users_edit.php' || $current_page === 'users_view.php') ? 'active' : '' ?>">
          <i class="fas fa-users"></i> 
          <span class="sidebar-text ml-2">User</span>
        </a>
      </li>

      <li>
        <a href="barang.php" class="app-menu__item <?= ($current_page === 'barang.php' || $current_page === 'barang_tambah.php' || $current_page === 'barang_view.php' || $current_page == 'barang_edit.php') ? 'active' : '' ?>">
          <i class="fas fa-boxes"></i> 
          <span class="sidebar-text ml-2">Data Barang</span>
        </a>
      </li>

      <li>
        <a href="penyimpanan.php" class="app-menu__item <?= ($current_page === 'penyimpanan.php'|| $current_page === 'penyimpanan_tambah.php' || $current_page === 'penyimpanan_view.php' || $current_page == 'penyimpanan_edit.php') ? 'active' : '' ?>">
          <i class="fas fa-warehouse"></i> 
          <span class="sidebar-text ml-2">Penyimpanan</span>
        </a>
      </li>

      <li>
        <a href="pemasok.php" class="app-menu__item <?= ($current_page === 'pemasok.php'|| $current_page === 'pemasok.php' || $current_page === 'pemasok.php' || $current_page == 'pemasok.php') ? 'active' : '' ?>">
          <i class="fas fa-truck"></i> 
          <span class="sidebar-text ml-2">Pemasok</span>
        </a>
      </li>
      <hr class="bg-light">

      <!-- Manajemen Inventaris -->
      <li class="text-uppercase font-weight-bold small mb-2 sidebar-text">TRANSAKSI</li>
      <li>
        <a href="peminjaman.php" class="app-menu__item <?= ($current_page === 'peminjaman.php' || $current_page === 'peminjaman_tambah.php' || $current_page === 'peminjaman_edit.php' || $current_page === 'peminjaman_view.php') ? 'active' : '' ?>">
          <i class="fas fa-hand-holding"></i> 
          <span class="sidebar-text ml-2">Peminjaman</span>
        </a>
      </li>

      <li>
        <a href="pengembalian.php" class="app-menu__item <?= ($current_page === 'pengembalian.php' || $current_page === 'pengembalian_tambah.php' || $current_page === 'pengembalian_view.php') ? 'active' : '' ?>">
          <i class="fas fa-undo-alt"></i>
          <span class="sidebar-text ml-2">Pengembalian</span>
        </a>
      </li>
      <hr class="bg-light">

      <!-- Manajemen Inventaris -->
      <li class="text-uppercase font-weight-bold small mb-2 sidebar-text">LAPORAN</li>

      <li>
        <a href="riwayat_peminjaman.php" class="app-menu__item <?= ($current_page === 'riwayat_peminjaman.php' || $current_page === 'riwayat_pengembalian.php') ? 'active' : '' ?>">
          <i class="fas fa-history"></i>
          <span class="sidebar-text ml-2">Riwayat</span>
        </a>
      </li>

      <li class="dropdown-submenu">
        <a href="#submenuLaporan" class="app-menu__item d-flex align-items-center <?= in_array($current_page, ['laporan_harian.php', 'laporan_mingguan.php', 'laporan_bulanan.php', 'laporan_tahunan.php']) ? 'active' : '' ?>" data-toggle="collapse" role="button" aria-expanded="<?= in_array($current_page, ['laporan_harian.php', 'laporan_mingguan.php', 'laporan_bulanan.php', 'laporan_tahunan.php']) ? 'true' : 'false' ?>" aria-controls="submenuLaporan">
          <i class="fas fa-file-alt"></i>
          <span class="sidebar-text ml-2">Laporan</span>
          <i class="fas ml-auto transition-chevron <?= in_array($current_page, ['laporan_harian.php', 'laporan_mingguan.php', 'laporan_bulanan.php', 'laporan_tahunan.php']) ? 'fa-chevron-down' : 'fa-chevron-right' ?>" id="chevronLaporan"></i>
        </a>
        <ul class="collapse list-unstyled ml-4 <?= in_array($current_page, ['laporan_harian.php', 'laporan_mingguan.php', 'laporan_bulanan.php', 'laporan_tahunan.php']) ? 'show' : '' ?>" id="submenuLaporan">
          <li>
            <a href="laporan_harian.php" class="app-menu__item <?= ($current_page === 'laporan_harian.php') ? 'active' : '' ?>">
              <i class="fas fa-calendar-day"></i>
              <span class="sidebar-text ml-2">Harian</span>
            </a>
          </li>
          <li>
            <a href="laporan_mingguan.php" class="app-menu__item <?= ($current_page === 'laporan_mingguan.php') ? 'active' : '' ?>">
              <i class="fas fa-calendar-week"></i>
              <span class="sidebar-text ml-2">Mingguan</span>
            </a>
          </li>
          <li>
            <a href="laporan_bulanan.php" class="app-menu__item <?= ($current_page === 'laporan_bulanan.php') ? 'active' : '' ?>">
              <i class="fas fa-calendar-alt"></i>
              <span class="sidebar-text ml-2">Bulanan</span>
            </a>
          </li>
        </ul>
      </li>

      <hr class="bg-light">

      <li class="text-uppercase font-weight-bold small mb-2 sidebar-text">Pengaturan</li>
      <li>
        <a href="pengaturan.php" class="app-menu__item <?= ($current_page === 'pengaturan.php') ? 'active' : '' ?>">
          <i class="fas fa-cogs"></i> 
          <span class="sidebar-text ml-2">Pengaturan</span>
        </a>
      </li>
      <li>
        <a href="/pal/logout.php" onclick="return confirm('Yakin ingin keluar?')" class="app-menu__item">
          <i class="fas fa-sign-out-alt"></i> 
          <span class="sidebar-text ml-2">Keluar</span>
        </a>
      </li>
      <br>
    </ul>
  </div>
</div>

<!-- Main content -->
<div class="main-content">
  <div class="navbar-custom d-flex justify-content-between align-items-center px-3">
    <!-- Tombol Toggle Sidebar -->
    <button class="toggle-btn" id="toggleSidebar">
      <i class="fas fa-bars"></i>
    </button>

    <div class="ml-auto d-flex align-items-center">
      <!-- Ikon & Profil User -->
      <div class="dropdown">
        <button class="user-btn d-flex align-items-center" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background:none;border:none;">
          <img src="<?= htmlspecialchars($fotoPathForHTML) ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
          <div class="ml-2 text-left d-none d-md-block">
            <p class="mb-0" style="font-size: 14px; font-weight: 600;"><?= htmlspecialchars($user['nama']) ?></p>
            <small class="text-black"><?= htmlspecialchars($user['role']) ?></small>
          </div>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
          <a class="dropdown-item" href="profile.php">
            <i class="fas fa-user-circle mr-2"></i> Profile
          </a>
          <a class="dropdown-item" href="pengaturan.php">
            <i class="fas fa-cogs mr-2"></i> Pengaturan
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="/pal/logout.php" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
        </div>
      </div>
    </div>

    <div class="text-left mt-5">

    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const sidebar = document.getElementById('sidebar');
  const mobileOverlay = document.getElementById('mobileOverlay');
  const toggleBtn = document.getElementById('toggleSidebar');
  const navbar = document.querySelector('.navbar-custom');
  const mainContent = document.querySelector('.main-content');

  // Toggle sidebar saat tombol diklik
  toggleBtn.addEventListener('click', function () {
    const isSmallScreen = window.innerWidth <= 992;

    if (isSmallScreen) {
      const isVisible = sidebar.classList.contains('sidebar-visible-mobile');
      sidebar.classList.toggle('sidebar-visible-mobile', !isVisible);
      mobileOverlay.classList.toggle('active', !isVisible);
    } else {
      sidebar.classList.toggle('sidebar-collapsed');

      if (sidebar.classList.contains('sidebar-collapsed')) {
        navbar.style.left = '70px';
        mainContent.style.marginLeft = '70px';
      } else {
        navbar.style.left = '250px';
        mainContent.style.marginLeft = '250px';
      }
    }
  });

  // Klik di luar sidebar → hide sidebar mobile
  mobileOverlay.addEventListener('click', function () {
    sidebar.classList.remove('sidebar-visible-mobile');
    mobileOverlay.classList.remove('active');
  });

  // Tangani tampilan saat layar di-resize
  function handleResponsiveSidebar() {
    const isSmallScreen = window.innerWidth <= 992;

    if (isSmallScreen) {
      sidebar.classList.remove('sidebar-collapsed');
      sidebar.classList.remove('sidebar-visible-mobile');
      mobileOverlay.classList.remove('active');
      navbar.style.left = '0';
      mainContent.style.marginLeft = '0';
    } else {
      sidebar.classList.remove('sidebar-visible-mobile');
      mobileOverlay.classList.remove('active');
      if (sidebar.classList.contains('sidebar-collapsed')) {
        navbar.style.left = '70px';
        mainContent.style.marginLeft = '70px';
      } else {
        navbar.style.left = '250px';
        mainContent.style.marginLeft = '250px';
      }
    }
  }

  // Inisialisasi saat halaman dibuka dan di-resize
  window.addEventListener('resize', handleResponsiveSidebar);
  window.addEventListener('load', handleResponsiveSidebar);
</script>
  <script>
  $('#submenuLaporan').on('show.bs.collapse', function () {
    $('#chevronLaporan').removeClass('fa-chevron-right').addClass('fa-chevron-down');
  });

  $('#submenuLaporan').on('hide.bs.collapse', function () {
    $('#chevronLaporan').removeClass('fa-chevron-down').addClass('fa-chevron-right');
  });
</script>
</body>
</html>