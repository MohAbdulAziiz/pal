<?php
include '../koneksi.php';

if (!isset($_GET['id_barang'])) {
    header("Location: barang.php");
    exit;
}

include 'header.php';

$id = $_GET['id_barang'];

$query = mysqli_query($conn, "
    SELECT 
        b.*, 
        p.nama_lokasi, 
        s.nama_pemasok
    FROM barang b
    LEFT JOIN penyimpanan p ON b.id_penyimpanan = p.id_penyimpanan
    LEFT JOIN pemasok s ON b.id_pemasok = s.id_pemasok
    WHERE b.id_barang = '$id'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='barang.php';</script>";
    exit;
}

$fotoFile = $data['foto'];
$fotoPath = (!empty($fotoFile) && file_exists("../public/uploads/$fotoFile")) 
    ? "../public/uploads/$fotoFile" 
    : "../public/uploads/default.png";
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-eye"></i> Lihat Detail Barang</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="barang.php">Tabel Barang</a></li>
        <li class="breadcrumb-item active">View Barang</li>
      </ul>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row">
          <!-- Foto -->
          <div class="col-md-4 text-center mb-3 d-flex flex-column align-items-center">
            <img src="<?= $fotoPath ?>" width="100%" class="img-fluid rounded shadow-sm mb-3" alt="Foto Barang">
          </div>

          <!-- Detail -->
          <div class="col-md-8">
            <table class="table table-borderless">
              <tr><th style="width: 35%;">Kode Barang</th><td>: <?= htmlspecialchars($data['id_barang']); ?></td></tr>
              <tr><th>Nama Barang</th><td>: <?= htmlspecialchars($data['nama_barang']); ?></td></tr>
              <tr><th>Merk</th><td>: <?= htmlspecialchars($data['merk']); ?></td></tr>
              <tr><th>Spesifikasi</th><td>: <?= nl2br(htmlspecialchars($data['spesifikasi'])); ?></td></tr>
              <tr><th>Kategori</th><td>: <?= htmlspecialchars($data['kategori']); ?></td></tr>
              <tr><th>Jumlah</th><td>: <?= htmlspecialchars($data['jumlah']); ?> <?= htmlspecialchars($data['satuan']); ?></td></tr>
              <tr><th>Jumlah Baik</th><td>: <?= htmlspecialchars($data['kondisi_baik']); ?></td></tr>
              <tr><th>Jumlah Rusak</th><td>: <?= htmlspecialchars($data['kondisi_rusak']); ?></td></tr>
              <tr><th>Jumlah Hilang</th><td>: <?= htmlspecialchars($data['kondisi_hilang']); ?></td></tr>
              <tr><th>Harga Beli</th><td>: Rp <?= number_format($data['harga_beli'], 0, ',', '.'); ?></td></tr>
              <tr><th>Pemasok</th><td>: <?= htmlspecialchars($data['nama_pemasok']); ?></td></tr>
              <tr><th>Lokasi (Penyimpanan)</th><td>: <?= htmlspecialchars($data['nama_lokasi']); ?></td></tr>
              <tr><th>Tanggal Beli</th><td>: <?= date('d-m-Y', strtotime($data['tanggal_beli'])); ?></td></tr>
            </table>
            <div class="w-100 d-flex justify-content-end mt-auto">
              <a href="barang.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
