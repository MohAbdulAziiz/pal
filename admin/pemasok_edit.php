<?php
include 'header.php';
include '../koneksi.php';

// Jika tidak ada ID, kembali
if (!isset($_GET['id_pemasok'])) {
    header("Location: pemasok.php");
    exit;
}

$id_pemasok = $_GET['id_pemasok'];

// Ambil data pemasok
$query = mysqli_query($conn, "SELECT * FROM pemasok WHERE id_pemasok = '$id_pemasok'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='pemasok.php';</script>";
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pemasok = mysqli_real_escape_string($conn, $_POST['nama_pemasok']);
    $alamat       = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kota         = mysqli_real_escape_string($conn, $_POST['kota']);
    $telepon      = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email        = mysqli_real_escape_string($conn, $_POST['email']);
    $keterangan   = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $created_at   = mysqli_real_escape_string($conn, $_POST['created_at']);


    $queryUpdate = "UPDATE pemasok SET 
        nama_pemasok = '$nama_pemasok',
        alamat = '$alamat',
        kota = '$kota',
        telepon = '$telepon',
        email = '$email',
        keterangan = '$keterangan',
        created_at = '$created_at'
    WHERE id_pemasok = '$id_pemasok'";

    if (mysqli_query($conn, $queryUpdate)) {
        echo "<script>alert('Data pemasok berhasil diperbarui'); window.location.href='pemasok.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
    }
}
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-edit"></i> Edit Data Pemasok</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="pemasok.php">Tabel Pemasok</a></li>
        <li class="breadcrumb-item active">Edit Pemasok</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <form method="POST">
        <input type="hidden" name="id_pemasok" value="<?= $data['id_pemasok']; ?>">
        <div class="row g-4">

          <!-- Nama Pemasok -->
          <div class="col-md-6">
            <label class="form-label">Nama Pemasok</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user"></i></span>
              <input type="text" name="nama_pemasok" class="form-control" value="<?= $data['nama_pemasok']; ?>" required>
            </div>
          </div>

          <!-- Alamat -->
          <div class="col-md-6">
            <label class="form-label">Alamat</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
              <textarea name="alamat" class="form-control" rows="2" required><?= $data['alamat']; ?></textarea>
            </div>
          </div>

          <!-- Kota -->
          <div class="col-md-6">
            <label class="form-label">Kota</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-city"></i></span>
              <input type="text" name="kota" class="form-control" value="<?= $data['kota']; ?>" required>
            </div>
          </div>

          <!-- Telepon -->
          <div class="col-md-6">
            <label class="form-label">Telepon</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-phone"></i></span>
              <input type="text" name="telepon" class="form-control" value="<?= $data['telepon']; ?>" required>
            </div>
          </div>

          <!-- Email -->
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-envelope"></i></span>
              <input type="email" name="email" class="form-control" value="<?= $data['email']; ?>">
            </div>
          </div>

          <!-- Keterangan -->
          <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
              <textarea name="keterangan" class="form-control" rows="2"><?= $data['keterangan']; ?></textarea>
            </div>
          </div>

            <!-- Tanggal Buat -->
            <div class="col-md-6">
            <label class="form-label">Tanggal Input</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" name="created_at" class="form-control" value="<?= date('Y-m-d', strtotime($data['created_at'])); ?>" required>
            </div>
            </div>

          <!-- Tombol -->
          <div class="col-12 tombol-kanan mt-3">
            <a href="pemasok.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</section>

<style>
  .form-label { font-weight: 500; }
  .tombol-kanan { display: flex; justify-content: flex-end; }
  .tombol-kanan .btn { width: 150px; margin-right: 8px; }
  .tombol-kanan .btn:last-child { margin-right: 0; }
</style>
