<?php
include '../koneksi.php';

if (!isset($_GET['id_peminjaman'])) {
    header("Location: peminjaman.php");
    exit;
}

include 'header.php';

$id = $_GET['id_peminjaman'];

// Ambil data peminjaman + data barang + user
$query = mysqli_query($conn, "
    SELECT 
        p.*,
        b.nama_barang,
        b.merk,
        b.foto,
        u.nama
    FROM peminjaman p
    LEFT JOIN barang b ON p.id_barang = b.id_barang
    LEFT JOIN users u ON p.id_users = u.id_users
    WHERE p.id_peminjaman = '$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='peminjaman.php';</script>";
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
        <h1><i class="fas fa-eye"></i> Lihat Detail Peminjaman</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="peminjaman.php">Tabel Peminjaman</a></li>
        <li class="breadcrumb-item active">View Peminjaman</li>
      </ul>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row">
          <!-- Foto Barang -->
          <div class="col-md-4 text-center mb-3 d-flex flex-column align-items-center">
            <img src="<?= $fotoPath ?>" width="100%" class="img-fluid rounded shadow-sm mb-3" alt="Foto Barang">
          </div>

          <!-- Detail Peminjaman -->
          <div class="col-md-8">
            <table class="table table-borderless">
              <tr><th style="width: 35%;">Kode Peminjaman</th><td>: <?= htmlspecialchars($data['id_peminjaman']); ?></td></tr>
              <tr><th>Nama Barang</th><td>: <?= htmlspecialchars($data['nama_barang']); ?></td></tr>
              <tr><th>Merk</th><td>: <?= htmlspecialchars($data['merk']); ?></td></tr>
              <tr><th>Dipinjam Oleh</th><td>: <?= htmlspecialchars($data['nama']); ?></td></tr>
              <tr><th>Kategori</th><td>: <?= htmlspecialchars($data['kategori']); ?></td></tr>
              <tr><th>Jumlah</th><td>: <?= htmlspecialchars($data['jumlah']); ?> <?= htmlspecialchars($data['satuan']); ?></td></tr>
              <tr><th>Tanggal Pinjam</th><td>: <?= date('d-m-Y', strtotime($data['tanggal_pinjam'])); ?></td></tr>
              <tr><th>Tenggat</th><td>: <?= date('d-m-Y', strtotime($data['tanggal_kembali'])); ?></td></tr>
              <tr><th>Status Peminjaman</th><td>: <?= ucfirst(htmlspecialchars($data['status_peminjaman'])); ?></td></tr>
              <tr><th>Keterangan</th><td>: <?= nl2br(htmlspecialchars($data['keterangan'])); ?></td></tr>
              <tr><th>Dicatat Pada</th><td>: <?= date('d-m-Y H:i', strtotime($data['created_at'])); ?></td></tr>
            </table>

            <div class="w-100 d-flex justify-content-end mt-auto">
              <a href="peminjaman.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
