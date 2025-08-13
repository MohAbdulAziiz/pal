<?php
include '../koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: pengembalian.php");
    exit;
}

include 'header.php';

$id = $_GET['id'];

// Ambil data pengembalian + barang + user
$query = mysqli_query($conn, "
    SELECT 
        pg.*,
        b.nama_barang,
        b.merk,
        b.foto AS foto_barang,
        u.nama AS nama_user
    FROM pengembalian pg
    LEFT JOIN barang b ON pg.id_barang = b.id_barang
    LEFT JOIN users u ON pg.id_users = u.id_users
    WHERE pg.id_pengembalian = '$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='pengembalian.php';</script>";
    exit;
}

// Cek foto barang
$fotoFile = $data['foto'];
$fotoPath = (!empty($fotoFile) && file_exists("../public/uploads/$fotoFile")) 
    ? "../public/uploads/$fotoFile" 
    : "../public/uploads/default.png";
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-eye"></i> Lihat Detail Pengembalian</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="pengembalian.php">Tabel Pengembalian</a></li>
        <li class="breadcrumb-item active">View Pengembalian</li>
      </ul>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row">
          <!-- Foto Barang -->
          <div class="col-md-4 text-center mb-3 d-flex flex-column align-items-center">
            <img src="<?= $fotoPath ?>" width="100%" class="img-fluid rounded shadow-sm mb-3" alt="Foto Barang">
          </div>

          <!-- Detail Pengembalian -->
          <div class="col-md-8">
            <table class="table table-borderless">
              <tr><th style="width: 35%;">ID Pengembalian</th><td>: <?= htmlspecialchars($data['id_pengembalian']); ?></td></tr>
              <tr><th>ID Peminjaman</th><td>: <?= htmlspecialchars($data['id_peminjaman']); ?></td></tr>
              <tr><th>Nama Barang</th><td>: <?= htmlspecialchars($data['nama_barang']); ?></td></tr>
              <tr><th>Merk</th><td>: <?= htmlspecialchars($data['merk']); ?></td></tr>
              <tr><th>Dipinjam Oleh</th><td>: <?= htmlspecialchars($data['nama_user']); ?></td></tr>
              <tr><th>Jumlah Dikembalikan</th><td>: <?= htmlspecialchars($data['jumlah']); ?></td></tr>
              <tr><th>Kondisi Baik</th><td>: <?= htmlspecialchars($data['kondisi_baik']); ?></td></tr>
              <tr><th>Kondisi Rusak</th><td>: <?= htmlspecialchars($data['kondisi_rusak']); ?></td></tr>
              <tr><th>Kondisi Hilang</th><td>: <?= htmlspecialchars($data['kondisi_hilang']); ?></td></tr>
              <tr><th>Tanggal Pinjam</th><td>: <?= date('d-m-Y', strtotime($data['tanggal_pinjam'])); ?></td></tr>
              <tr><th>Tanggal Kembali</th><td>: <?= date('d-m-Y', strtotime($data['tanggal_kembali'])); ?></td></tr>
              <tr><th>Status</th><td>: <span class="badge bg-<?= $data['status'] === 'Terverifikasi' ? 'success' : 'warning text-dark' ?>"><?= htmlspecialchars($data['status']); ?></span></td></tr>
              <tr><th>Denda</th><td>: Rp. <?= number_format($data['denda'], 0, ',', '.'); ?></td></tr>
              <tr><th>Keterangan</th><td>: <?= nl2br(htmlspecialchars($data['keterangan'])); ?></td></tr>
            </table>

            <div class="w-100 d-flex justify-content-end mt-auto">
              <a href="pengembalian.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
