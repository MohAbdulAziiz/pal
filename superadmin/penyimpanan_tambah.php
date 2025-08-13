<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<?php
// Ambil ID penyimpanan terakhir
$query = mysqli_query($conn, "SELECT id_penyimpanan FROM penyimpanan ORDER BY id_penyimpanan DESC LIMIT 1");
$data = mysqli_fetch_assoc($query);

if ($data) {
    // Ambil angka dari format PNY0001
    $lastNumber = intval(substr($data['id_penyimpanan'], 3));
    $newNumber = $lastNumber + 1;
} else {
    $newNumber = 1; // Jika belum ada data sama sekali
}

// Format ID baru
$id_penyimpanan_baru = 'PNY' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <!-- Judul dan Breadcrumb -->
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-plus-circle"></i> Tambah Data Penyimpanan</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="penyimpanan.php">Tabel Penyimpanan</a></li>
        <li class="breadcrumb-item active">Tambah Penyimpanan</li>
      </ul>
    </div>

    <!-- Form Tambah Penyimpanan -->
    <div class="card shadow p-4">
      <form action="proses_tambah_penyimpanan.php" method="POST" enctype="multipart/form-data">
        <!-- ID penyimpanan otomatis -->
        <input type="hidden" name="id_penyimpanan" value="<?= $id_penyimpanan_baru; ?>">

<div class="row g-4">
  <!-- Kolom Nama Tempat -->
  <div class="col-md-6">
    <div class="mb-3">
      <label for="nama_lokasi" class="form-label">Nama Tempat</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
        <input type="text" name="nama_lokasi" id="nama_lokasi" class="form-control" required>
      </div>
    </div>
  </div>

  <!-- Kolom Foto Tempat -->
  <div class="col-md-6">
    <div class="mb-3">
      <label for="foto" class="form-label">Foto Tempat</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-image"></i></span>
        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
      </div>
    </div>
  </div>

  <!-- Kolom Deskripsi -->
  <div class="col-md-12">
    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-align-left"></i></span>
        <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control" required></textarea>
      </div>
    </div>
  </div>
</div>


        <!-- Tombol Aksi -->
        <div class="col-12 tombol-kanan mt-3">
            <a href="penyimpanan.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- Style Tambahan -->
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
