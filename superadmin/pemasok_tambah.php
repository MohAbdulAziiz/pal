<?php
include 'header.php';
include '../koneksi.php';

// Generate ID otomatis: PMK0001, PMK0002, ...
function generateIdPemasok($conn) {
    $result = mysqli_query($conn, "SELECT MAX(id_pemasok) as max_id FROM pemasok");
    $data = mysqli_fetch_assoc($result);
    $lastId = $data['max_id'];

    $num = (int)substr($lastId, 3); // ambil angka setelah "PMK"
    $num++;
    return "PMK" . str_pad($num, 4, "0", STR_PAD_LEFT);
}

$id_pemasok = generateIdPemasok($conn);

// Proses Simpan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pemasok = mysqli_real_escape_string($conn, $_POST['id_pemasok']);
    $nama_pemasok = mysqli_real_escape_string($conn, $_POST['nama_pemasok']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $created_at = date("Y-m-d");

    $query = "INSERT INTO pemasok (id_pemasok, nama_pemasok, alamat, kota, telepon, email, keterangan, created_at) 
              VALUES ('$id_pemasok', '$nama_pemasok', '$alamat', '$kota', '$telepon', '$email', '$keterangan', '$created_at')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data pemasok berhasil ditambahkan'); window.location.href='pemasok.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan data: " . mysqli_error($conn) . "</div>";
    }
}
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-truck"></i> Tambah Data Pemasok</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="pemasok.php">Tabel Pemasok</a></li>
        <li class="breadcrumb-item active">Tambah Pemasok</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <form method="POST">
        <div class="row g-4">

          <!-- ID Pemasok -->
          <div class="col-md-6">
            <label class="form-label">ID Pemasok</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-id-badge"></i></span>
              <input type="text" name="id_pemasok" class="form-control border-start-0 rounded-end" value="<?= $id_pemasok ?>" readonly>
            </div>
          </div>

          <!-- Nama Pemasok -->
          <div class="col-md-6">
            <label class="form-label">Nama Pemasok</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-user"></i></span>
              <input type="text" name="nama_pemasok" class="form-control border-start-0 rounded-end" required>
            </div>
          </div>

          <!-- Alamat -->
          <div class="col-md-6">
            <label class="form-label">Alamat</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-map-marker-alt"></i></span>
              <textarea name="alamat" class="form-control border-start-0 rounded-end" rows="2" required></textarea>
            </div>
          </div>

          <!-- Kota -->
          <div class="col-md-6">
            <label class="form-label">Kota</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-city"></i></span>
              <input type="text" name="kota" class="form-control border-start-0 rounded-end" required>
            </div>
          </div>

          <!-- Telepon -->
          <div class="col-md-6">
            <label class="form-label">Telepon</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-phone"></i></span>
              <input type="text" name="telepon" class="form-control border-start-0 rounded-end" required>
            </div>
          </div>

          <!-- Email -->
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-envelope"></i></span>
              <input type="email" name="email" class="form-control border-start-0 rounded-end">
            </div>
          </div>

          <!-- Keterangan -->
          <div class="col-md-12">
            <label class="form-label">Keterangan</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-info-circle"></i></span>
              <textarea name="keterangan" class="form-control border-start-0 rounded-end" rows="3"></textarea>
            </div>
          </div>

          <!-- Tombol -->
          <div class="col-12 tombol-kanan mt-3">
            <a href="pemasok.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</section>

<style>
  .form-label {
    font-weight: 500;
  }
  .tombol-kanan {
    display: flex;
    justify-content: flex-end;
  }
  .tombol-kanan .btn {
    width: 150px;
    margin-right: 8px;
  }
  .tombol-kanan .btn:last-child {
    margin-right: 0;
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />