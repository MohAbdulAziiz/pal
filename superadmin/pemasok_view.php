<?php
include '../koneksi.php';

if (!isset($_GET['id_pemasok'])) {
    header("Location: pemasok.php");
    exit;
}

include 'header.php';

$id = $_GET['id_pemasok'];

$query = mysqli_query($conn, "SELECT * FROM pemasok WHERE id_pemasok = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='pemasok.php';</script>";
    exit;
}
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-eye"></i> Lihat Detail Pemasok</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="pemasok.php">Tabel Pemasok</a></li>
        <li class="breadcrumb-item active">View Pemasok</li>
      </ul>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-borderless">
              <tr><th style="width: 35%;">ID Pemasok</th><td>: <?= htmlspecialchars($data['id_pemasok']); ?></td></tr>
              <tr><th>Nama Pemasok</th><td>: <?= htmlspecialchars($data['nama_pemasok']); ?></td></tr>
              <tr><th>Alamat</th><td>: <?= nl2br(htmlspecialchars($data['alamat'])); ?></td></tr>
              <tr><th>Kota</th><td>: <?= htmlspecialchars($data['kota']); ?></td></tr>
              <tr><th>Telepon</th><td>: <?= htmlspecialchars($data['telepon']); ?></td></tr>
              <tr><th>Email</th><td>: <?= htmlspecialchars($data['email']); ?></td></tr>
              <tr><th>Keterangan</th><td>: <?= nl2br(htmlspecialchars($data['keterangan'])); ?></td></tr>
              <tr><th>Tanggal Input</th><td>: <?= date('d-m-Y', strtotime($data['created_at'])); ?></td></tr>
            </table>
            <div class="w-100 d-flex justify-content-end mt-4">
              <a href="pemasok.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
