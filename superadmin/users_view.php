<?php
include '../koneksi.php';

if (!isset($_GET['id_users'])) {
    header("Location: users.php");
    exit;
}

include 'header.php';

$id = $_GET['id_users'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id_users = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='users.php';</script>";
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
        <h1><i class="fas fa-eye"></i> Lihat Detail Pengguna</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="users.php">Tabel Pengguna</a></li>
        <li class="breadcrumb-item active">View Pengguna</li>
      </ul>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row">
          <!-- Foto -->
          <div class="col-md-4 text-center mb-3 d-flex flex-column align-items-center">
            <img src="<?= $fotoPath ?>" width="100%" class="img-fluid rounded shadow-sm mb-3" alt="Foto Pengguna">
          </div>

          <!-- Detail -->
          <div class="col-md-8">
            <table class="table table-borderless">
              <tr><th style="width: 35%;">ID Pengguna</th><td>: <?= htmlspecialchars($data['id_users']); ?></td></tr>
              <tr><th>Nama Lengkap</th><td>: <?= htmlspecialchars($data['nama']); ?></td></tr>
              <tr><th>Email</th><td>: <?= htmlspecialchars($data['email']); ?></td></tr>
              <tr><th>Nomor HP</th><td>: <?= htmlspecialchars($data['no_hp']); ?></td></tr>
              <tr><th>Jenis Kelamin</th><td>: <?= htmlspecialchars($data['jenis_kelamin']); ?></td></tr>
              <tr><th>Alamat</th><td>: <?= nl2br(htmlspecialchars($data['alamat'])); ?></td></tr>
              <tr><th>Jabatan</th><td>: <?= htmlspecialchars($data['jabatan']); ?></td></tr>
              <tr><th>NIP</th><td>: <?= htmlspecialchars($data['nip']); ?></td></tr>
              <tr><th>Username</th><td>: <?= htmlspecialchars($data['username']); ?></td></tr>
              <tr><th>Role</th><td>: <?= htmlspecialchars($data['role']); ?></td></tr>
              <tr><th>Verifikasi</th><td>: <?= htmlspecialchars($data['verifikasi']); ?></td></tr>
              <tr><th>Tanggal Input</th><td>: <?= date('d-m-Y', strtotime($data['created_at'])); ?></td></tr>
            </table>

            <div class="w-100 d-flex justify-content-end mt-auto">
              <a href="users.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
