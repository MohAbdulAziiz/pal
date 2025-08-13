<?php
include '../koneksi.php';

if (!isset($_GET['id_barang'])) {
    header("Location: barang.php");
    exit;
}

include 'header.php';

$id_barang = $_GET['id_barang'];
$query = mysqli_query($conn, "SELECT b.*, p.nama_lokasi, s.nama_pemasok 
FROM barang b 
LEFT JOIN penyimpanan p ON b.id_penyimpanan = p.id_penyimpanan
LEFT JOIN pemasok s ON b.id_pemasok = s.id_pemasok
WHERE b.id_barang = '$id_barang'");
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
        <h1><i class="fas fa-edit"></i> Edit Data Barang</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="barang.php">Tabel Barang</a></li>
        <li class="breadcrumb-item active">Edit Barang</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <form action="proses_edit_barang.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_barang" value="<?= $data['id_barang']; ?>">
        <input type="hidden" name="kategori_lama" value="<?= $data['kategori']; ?>">
        <div class="row g-4">

          <!-- Kolom Kiri -->
          <div class="col-md-6">
            <!-- Nama Barang -->
            <div class="mb-3">
              <label for="nama_barang" class="form-label">Nama Barang</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-box"></i></span>
                <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="<?= $data['nama_barang']; ?>" required>
              </div>
            </div>

            <!-- Merk -->
            <div class="mb-3">
              <label for="merk" class="form-label">Merk</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                <input type="text" name="merk" id="merk" class="form-control" value="<?= $data['merk']; ?>" required>
              </div>
            </div>

            <!-- Jumlah -->
            <div class="mb-3">
              <label for="kondisi_baik" class="form-label">Jumlah Kondisi Baik</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                <input type="number" name="kondisi_baik" id="kondisi_baik" class="form-control" value="<?= $data['kondisi_baik']; ?>" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="kondisi_rusak" class="form-label">Jumlah Kondisi Rusak</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                <input type="number" name="kondisi_rusak" id="kondisi_rusak" class="form-control" value="<?= $data['kondisi_rusak']; ?>" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="kondisi_hilang" class="form-label">Jumlah Kondisi Hilang</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                <input type="number" name="kondisi_hilang" id="kondisi_hilang" class="form-control" value="<?= $data['kondisi_hilang']; ?>" required>
              </div>
            </div>

            <!-- Satuan -->
            <div class="mb-3">
              <label for="satuan" class="form-label">Satuan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                <select class="form-control" name="satuan" id="satuan" required>
                  <option disabled>-- Pilih Satuan --</option>
                  <?php
                  $result = mysqli_query($conn, "SHOW COLUMNS FROM barang LIKE 'satuan'");
                  $row = mysqli_fetch_assoc($result);
                  if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enum_values = explode(",", $matches[1]);
                    foreach ($enum_values as $value) {
                      $val = trim($value, "'");
                      $selected = ($val == $data['satuan']) ? 'selected' : '';
                      echo "<option value='$val' $selected>$val</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Harga Beli -->
            <div class="mb-3">
              <label for="harga_beli" class="form-label">Harga Beli</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                <input type="number" name="harga_beli" id="harga_beli" class="form-control" value="<?= $data['harga_beli']; ?>" required>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">
          <!-- Spesifikasi -->
            <div class="mb-3">
              <label for="spesifikasi" class="form-label">Spesifikasi</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                <textarea name="spesifikasi" id="spesifikasi" rows="3" class="form-control" required><?= $data['spesifikasi']; ?></textarea>
              </div>
            </div>

            <!-- Tanggal Beli -->
            <div class="mb-3">
              <label for="tanggal_beli" class="form-label">Tanggal Beli</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" name="tanggal_beli" id="tanggal_beli" class="form-control" value="<?= date('Y-m-d', strtotime($data['tanggal_beli'])); ?>" required>
              </div>
            </div>

            <!-- Kategori -->
            <div class="mb-3">
              <label for="kategori" class="form-label">Kategori</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-tags"></i></span>
                <select class="form-control" name="kategori" id="kategori" required>
                  <option disabled>-- Pilih Kategori --</option>
                  <?php
                  $result = mysqli_query($conn, "SHOW COLUMNS FROM barang LIKE 'kategori'");
                  $row = mysqli_fetch_assoc($result);
                  if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enum_values = explode(",", $matches[1]);
                    foreach ($enum_values as $value) {
                      $val = trim($value, "'");
                      $selected = ($val == $data['kategori']) ? 'selected' : '';
                      echo "<option value='$val' $selected>$val</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Lokasi Penyimpanan -->
            <div class="mb-3">
              <label for="id_penyimpanan" class="form-label">Lokasi Penyimpanan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                <select class="form-control" name="id_penyimpanan" id="id_penyimpanan" required>
                  <option disabled>-- Pilih Lokasi --</option>
                  <?php
                  $lokasi = mysqli_query($conn, "SELECT * FROM penyimpanan");
                  while ($lok = mysqli_fetch_assoc($lokasi)) {
                    $selected = ($lok['id_penyimpanan'] == $data['id_penyimpanan']) ? 'selected' : '';
                    echo "<option value='{$lok['id_penyimpanan']}' $selected>{$lok['nama_lokasi']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Nama Pemasok -->
            <div class="mb-3">
              <label for="id_pemasok" class="form-label">Nama Pemasok</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-truck"></i></span>
                <select class="form-control" name="id_pemasok" id="id_pemasok" required>
                  <option disabled>-- Pilih Pemasok --</option>
                  <?php
                  $lokasi = mysqli_query($conn, "SELECT * FROM pemasok");
                  while ($lok = mysqli_fetch_assoc($lokasi)) {
                    $selected = ($lok['id_pemasok'] == $data['id_pemasok']) ? 'selected' : '';
                    echo "<option value='{$lok['id_pemasok']}' $selected>{$lok['nama_pemasok']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Foto -->
            <div class="mb-3">
              <label for="foto" class="form-label">Foto Barang (opsional)</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-camera"></i></span>
                <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
              </div>
              <?php if ($data['foto']): ?>
                <small>Foto saat ini: <br><img src="../public/uploads/<?= $data['foto']; ?>" width="100" class="img-thumbnail mt-2"></small>
              <?php endif; ?>
            </div>
          </div>

          <!-- Tombol Aksi -->
          <div class="col-12 tombol-kanan mt-3">
            <a href="barang.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Data</button>
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
