<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-plus-circle"></i> Tambah Data Barang</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="barang.php">Tabel Barang</a></li>
        <li class="breadcrumb-item active">Tambah Barang</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <form action="proses_tambah_barang.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">

          <!-- Kolom Kiri -->
          <div class="col-md-6">
            <!-- Nama Barang -->
            <div class="mb-3">
              <label for="nama_barang" class="form-label">Nama Barang</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-box"></i>
                </span>
                <input type="text" name="nama_barang" id="nama_barang" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Merk -->
            <div class="mb-3">
              <label for="merk" class="form-label">Merk</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-industry"></i>
                </span>
                <input type="text" name="merk" id="merk" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Jumlah -->
            <div class="mb-3">
              <label for="kondisi_baik" class="form-label">Jumlah Kondisi Baik</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-sort-numeric-up"></i>
                </span>
                <input type="number" name="kondisi_baik" id="kondisi_baik" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="kondisi_rusak" class="form-label">Jumlah Kondisi Rusak</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-sort-numeric-up"></i>
                </span>
                <input type="number" name="kondisi_rusak" id="jukondisi_rusakmlah" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="kondisi_hilang" class="form-label">Jumlah Kondisi Hilang</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-sort-numeric-up"></i>
                </span>
                <input type="number" name="kondisi_hilang" id="kondisi_hilang" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Satuan -->
            <div class="mb-3">
              <label for="satuan" class="form-label">Satuan</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-balance-scale"></i>
                </span>
                <select class="form-control border-start-0 rounded-end" name="satuan" id="satuan" required>
                  <option value="" disabled selected>-- Pilih Satuan --</option>
                  <?php
                  $result = mysqli_query($conn, "SHOW COLUMNS FROM barang LIKE 'satuan'");
                  $row = mysqli_fetch_assoc($result);
                  if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enum_values = explode(",", $matches[1]);
                    foreach ($enum_values as $value) {
                      $val = trim($value, "'");
                      echo "<option value='$val'>$val</option>";
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
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-money-bill-wave"></i>
                </span>
                <input type="text" name="harga_beli" id="harga_beli" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">
          <!-- Spesifikasi -->
            <div class="mb-3">
              <label for="spesifikasi" class="form-label">Spesifikasi</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-info-circle"></i>
                </span>
                <textarea name="spesifikasi" id="spesifikasi" rows="3" class="form-control border-start-0 rounded-end" required></textarea>
              </div>
            </div>
            <!-- Kategori -->
            <div class="mb-3">
              <label for="kategori" class="form-label">Kategori</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-tags"></i>
                </span>
                <select class="form-control border-start-0 rounded-end" name="kategori" id="kategori" required>
                  <option value="" disabled selected>-- Pilih Kategori --</option>
                  <?php
                  $result = mysqli_query($conn, "SHOW COLUMNS FROM barang LIKE 'kategori'");
                  $row = mysqli_fetch_assoc($result);
                  if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enum_values = explode(",", $matches[1]);
                    foreach ($enum_values as $value) {
                      $val = trim($value, "'");
                      echo "<option value='$val'>$val</option>";
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
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-warehouse"></i>
                </span>
                <select class="form-control border-start-0 rounded-end" name="id_penyimpanan" id="id_penyimpanan" required>
                  <option value="" disabled selected>-- Pilih Lokasi --</option>
                  <?php
                  $lokasi = mysqli_query($conn, "SELECT * FROM penyimpanan");
                  while ($lok = mysqli_fetch_assoc($lokasi)) {
                    echo "<option value='{$lok['id_penyimpanan']}'>{$lok['nama_lokasi']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Nama Pemasok -->
            <div class="mb-3">
              <label for="id_pemasok" class="form-label">Nama Pemasok</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-truck"></i>
                </span>
                <select class="form-control border-start-0 rounded-end" name="id_pemasok" id="id_pemasok" required>
                  <option value="" disabled selected>-- Pilih Pemasok --</option>
                  <?php
                  $pemasok = mysqli_query($conn, "SELECT * FROM pemasok");
                  while ($row = mysqli_fetch_assoc($pemasok)) {
                    echo "<option value='{$row['id_pemasok']}'>{$row['nama_pemasok']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Tanggal Beli -->
            <div class="mb-3">
              <label for="tanggal_beli" class="form-label">Tanggal Beli</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date" name="tanggal_beli" id="tanggal_beli" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Foto Barang -->
            <div class="mb-3">
              <label for="foto" class="form-label">Foto Barang</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-camera"></i>
                </span>
                <input type="file" name="foto" id="foto" class="form-control border-start-0 rounded-end" accept="image/*">
              </div>
            </div>
          </div>

          <!-- Tombol Aksi -->
          <div class="col-12 tombol-kanan mt-3">
            <a href="barang.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
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
