<?php
include '../koneksi.php';

if (!isset($_GET['id_penyimpanan'])) {
    header("Location: penyimpanan.php");
    exit;
}

include 'header.php';

$id = $_GET['id_penyimpanan'];
$query = mysqli_query($conn, "SELECT * FROM penyimpanan WHERE id_penyimpanan = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='penyimpanan.php';</script>";
    exit;
}

// Cek apakah foto tersedia dan file-nya ada
$fotoFile = $data['foto'];
$fotoPath = (!empty($fotoFile) && file_exists("../public/uploads/$fotoFile")) 
    ? "../public/uploads/$fotoFile" 
    : "../public/uploads/default.png";
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-eye"></i> Lihat Detail Penyimpanan</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="penyimpanan.php">Tabel Penyimpanan</a></li>
        <li class="breadcrumb-item active">View Penyimpanan</li>
      </ul>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row">
          <!-- Kolom Foto -->
          <div class="col-md-4 text-center mb-3 d-flex flex-column align-items-center">
            <img src="<?= $fotoPath ?>" width="100%" class="img-fluid rounded shadow-sm mb-3" alt="Foto Barang">
          </div>

          <!-- Kolom Detail -->
          <div class="col-md-8">
            <table class="table table-borderless">
              <tr>
                <th style="width: 35%;">Kode Penyimpanan</th>
                <td>: <?= htmlspecialchars($data['id_penyimpanan']); ?></td>
              </tr>
              <tr>
                <th style="width: 35%;">Nama Penyimpanan</th>
                <td>: <?= htmlspecialchars($data['nama_lokasi']); ?></td>
              </tr>
              <tr>
                <th>Deskripsi</th>
                <td>: <?= htmlspecialchars($data['deskripsi']); ?></td>
              </tr>
              <tr>
                <th>Tanggal Input</th>
                <td>: <?= date('d-m-Y', strtotime($data['created_at'])); ?></td>
              </tr>
            </table>
            <div class="w-100 d-flex justify-content-end mt-auto">
                <a href="penyimpanan.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
