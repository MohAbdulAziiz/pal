<?php
include 'header.php';
include '../koneksi.php';

$id_user = $_SESSION['id_users']; // pastikan session ini sudah di-set saat login

// Hitung data berdasarkan user login
$total_peminjaman   = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total 
                         FROM peminjaman 
                         WHERE status_peminjaman = 'Disetujui' 
                         AND id_users = '$id_user'")
)['total'];

$total_pengembalian = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total 
                         FROM pengembalian 
                         WHERE status = 'Terverifikasi' 
                         AND id_users = '$id_user'")
)['total'];

// Ambil lokasi user (opsional, kalau tidak dipakai bisa dihapus)
$ip = $_SERVER['REMOTE_ADDR'];
$location_data = @file_get_contents("http://ipinfo.io/{$ip}/json");
$location_json = json_decode($location_data, true);
$city = $location_json['city'] ?? 'Tidak Diketahui';
$region = $location_json['region'] ?? '';
$country = $location_json['country'] ?? '';
?>


<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ul>
    </div>

<div class="row">
  <!-- KIRI: 4 Card (Users, Barang, Pemasok, Penyimpanan) -->
<div class="col-lg-6">
  <div class="row">

    <!-- Peminjaman -->
    <div class="col-md-6 mb-4">
      <div class="small-box text-white p-3 rounded shadow-sm" style="background: linear-gradient(135deg, #fd7e14, #994908ff);">
        <div class="inner">
          <h3><?= $total_peminjaman; ?></h3>
          <p>Data Peminjaman</p>
        </div>
        <div class="icon"><i class="fas fa-hand-holding"></i></div>
        <a href="peminjaman.php" class="d-block text-center py-2 mt-3 rounded" style="background-color: rgba(0,0,0,0.2); color: #fff;">
          Lihat Detail <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

    <!-- Pengembalian -->
    <div class="col-md-6 mb-4">
      <div class="small-box text-white p-3 rounded shadow-sm" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
        <div class="inner">
          <h3><?= $total_pengembalian; ?></h3>
          <p>Total Pengembalian</p>
        </div>
        <div class="icon"><i class="fas fa-undo-alt"></i></div>
        <a href="pengembalian.php" class="d-block text-center py-2 mt-3 rounded" style="background-color: rgba(0,0,0,0.2); color: #fff;">
          Lihat Detail <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

  </div>
</div>


<div class="col-lg-6 mb-4">
<?php
$card_height = "150px"; // Tinggi card
setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian'); // Set bahasa Indonesia
date_default_timezone_set('Asia/Jakarta'); // Pastikan zona waktu benar
?>

<div class="card border-0 shadow-sm" 
     style="background: linear-gradient(135deg, #007bff, #004085); color: #fff; height: <?= $card_height; ?>;">
  <div class="card-body d-flex align-items-center" style="height: 100%;">
    <div class="row w-100 align-items-center">

      <!-- Kolom kiri: Informasi Waktu -->
      <div class="col-md-8 text-start">
        <h5 id="greeting" class="mb-2"></h5>
        <h3 id="digitalClock" class="mb-1 fw-bold"></h3>
        <p class="mb-1">
          <?= strftime("%A, %d %B %Y"); ?>
        </p>
      </div>

      <!-- Kolom kanan: Jam Analog -->
      <div class="col-md-4 text-center">
        <canvas id="analogClock" 
                width="100" 
                height="100" 
                style="background-color: #fff; border-radius: 50%;"></canvas>
      </div>

    </div>
  </div>
</div>

<script>
// Digital Clock & Greeting
function updateClock() {
  const now = new Date();
  document.getElementById("digitalClock").textContent =
    now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

  let hour = now.getHours();
  let greeting = "Selamat Malam 🌙";
  if (hour >= 5 && hour < 12) greeting = "Selamat Pagi 🌅";
  else if (hour >= 12 && hour < 15) greeting = "Selamat Siang ☀️";
  else if (hour >= 15 && hour < 18) greeting = "Selamat Sore 🌇";
  
  document.getElementById("greeting").textContent = greeting;
}
setInterval(updateClock, 1000);
updateClock();
</script>
</div>

    </div>
  </div>
</section>

<!-- Style Tambahan -->
<style>
  .small-box {
    position: relative;
    overflow: hidden;
    min-height: 120px;
  }

  .small-box .icon {
    position: absolute;
    top: 10px;
    right: 15px;
    z-index: 0;
    font-size: 55px;
    opacity: 0.2;
  }

  .small-box-footer {
    display: block;
    position: relative;
    padding: 5px 0;
    text-align: center;
    font-weight: 500;
    z-index: 1;
    text-decoration: none;
  }

  .small-box .inner h3 {
    font-size: 32px;
    margin: 0;
    font-weight: bold;
  }

  .small-box .inner p {
    margin: 0;
    font-size: 16px;
  }
  .bg-indigo { background-color: #6610f2 !important; }
.bg-pink   { background-color: #e83e8c !important; }
.bg-orange { background-color: #fd7e14 !important; }
.bg-teal   { background-color: #20c997 !important; }
.bg-purple { background-color: #6f42c1 !important; }
.bg-bluegray { background-color: #6c757d !important; }
.bg-cyan   { background-color: #0dcaf0 !important; }

</style>
<script>
// Script Jam Analog
const canvas = document.getElementById("analogClock");
const ctx = canvas.getContext("2d");
const radius = canvas.height / 2;
ctx.translate(radius, radius);

function drawClock() {
  drawFace(ctx, radius);
  drawNumbers(ctx, radius);
  drawTime(ctx, radius);
}

function drawFace(ctx, radius) {
  ctx.beginPath();
  ctx.arc(0, 0, radius, 0, 2 * Math.PI);
  ctx.fillStyle = "white";
  ctx.fill();
  
  ctx.strokeStyle = "#333";
  ctx.lineWidth = radius * 0.05;
  ctx.stroke();
  
  ctx.beginPath();
  ctx.arc(0, 0, radius * 0.05, 0, 2 * Math.PI);
  ctx.fillStyle = "#333";
  ctx.fill();
}

function drawNumbers(ctx, radius) {
  ctx.font = radius * 0.15 + "px Arial";
  ctx.textBaseline = "middle";
  ctx.textAlign = "center";
  for (let num = 1; num <= 12; num++) {
    let ang = num * Math.PI / 6;
    ctx.rotate(ang);
    ctx.translate(0, -radius * 0.85);
    ctx.rotate(-ang);
    ctx.fillText(num.toString(), 0, 0);
    ctx.rotate(ang);
    ctx.translate(0, radius * 0.85);
    ctx.rotate(-ang);
  }
}

function drawTime(ctx, radius) {
  const now = new Date();
  let hour = now.getHours();
  let minute = now.getMinutes();
  let second = now.getSeconds();
  
  // Hour
  hour = hour % 12;
  hour = (hour * Math.PI / 6) +
         (minute * Math.PI / (6 * 60)) +
         (second * Math.PI / (360 * 60));
  drawHand(ctx, hour, radius * 0.5, radius * 0.07);

  // Minute
  minute = (minute * Math.PI / 30) + (second * Math.PI / (30 * 60));
  drawHand(ctx, minute, radius * 0.8, radius * 0.07);

  // Second
  second = (second * Math.PI / 30);
  drawHand(ctx, second, radius * 0.9, radius * 0.02, "red");
}

function drawHand(ctx, pos, length, width, color = "#333") {
  ctx.beginPath();
  ctx.lineWidth = width;
  ctx.lineCap = "round";
  ctx.strokeStyle = color;
  ctx.moveTo(0, 0);
  ctx.rotate(pos);
  ctx.lineTo(0, -length);
  ctx.stroke();
  ctx.rotate(-pos);
}

setInterval(drawClock, 1000);
drawClock();
</script>