<?php 
include 'header.php'; 
include '../koneksi.php';

// Ambil id user dari session
$id_user_login = $_SESSION['id_users'];

// Ambil data barang dari database
$barangData = mysqli_fetch_all(mysqli_query($conn, "SELECT id_barang, kategori, satuan FROM barang"), MYSQLI_ASSOC);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<?php
  // Ambil data barang dan user dari database
  $barangData = mysqli_fetch_all(mysqli_query($conn, "SELECT id_barang, kategori, satuan FROM barang"), MYSQLI_ASSOC);
  $userData = mysqli_fetch_all(mysqli_query($conn, "SELECT id_users FROM users"), MYSQLI_ASSOC);
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-hand-holding"></i> Form Peminjaman Barang</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="peminjaman.php">Tabel Peminjaman</a></li>
        <li class="breadcrumb-item active">Tambah Peminjaman</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_GET['error']) ?>
        </div>
      <?php endif; ?>

  <form action="proses_tambah_peminjaman.php" method="POST">
    <div class="row g-4">

          <!-- Kolom Kiri -->
          <div class="col-md-6">
            <!-- Nama Barang -->
            <div class="mb-3">
              <label for="id_barang" class="form-label">ID Barang</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-barcode"></i></span>
                <input type="text" name="id_barang" id="id_barang" class="form-control border-start-0 rounded-end" required>
              </div>
              <small id="barang-error" class="text-danger d-none">ID Barang tidak tersedia</small>
            </div>

            <!-- Kategori -->
            <div class="mb-3">
              <label for="kategori" class="form-label">Kategori</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-tags"></i></span>
                <input type="text" name="kategori" id="kategori" class="form-control border-start-0 rounded-end" readonly>
              </div>
            </div>

            <!-- Jumlah -->
            <div class="mb-3">
              <label for="jumlah" class="form-label">Jumlah</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-sort-numeric-up"></i></span>
                <input type="number" name="jumlah" id="jumlah" min="1" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Satuan -->
            <div class="mb-3">
              <label for="satuan" class="form-label">Satuan</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-balance-scale"></i></span>
                <input type="text" name="satuan" id="satuan" class="form-control border-start-0 rounded-end" readonly>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">
            <!-- ID Users -->
            <div class="mb-3">
              <label for="id_users" class="form-label">ID Peminjam</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-id-badge"></i></span>
                <input type="text" name="id_users" id="id_users" class="form-control border-start-0 rounded-end" value="<?= $id_user_login ?>" readonly>
              </div>
            </div>

            <!-- Tanggal Pinjam -->
            <div class="mb-3">
              <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-calendar-day"></i></span>
                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Tanggal Kembali -->
            <div class="mb-3">
              <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-calendar-day"></i></span>
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Keterangan -->
            <div class="mb-3">
              <label for="keterangan" class="form-label">Keterangan</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0"><i class="fas fa-comment-dots"></i></span>
                <textarea name="keterangan" id="keterangan" class="form-control border-start-0 rounded-end" rows="2"></textarea>
              </div>
            </div>
          </div>

          <!-- Tombol Aksi -->
          <div class="col-12 tombol-kanan mt-3">
            <a href="peminjaman.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
<script>
  const barangData = <?= json_encode($barangData) ?>;

  document.addEventListener('DOMContentLoaded', function () {
    const idBarangInput = document.getElementById('id_barang');
    const kategoriInput = document.getElementById('kategori');
    const satuanInput = document.getElementById('satuan');
    const barangError = document.getElementById('barang-error');

    function validateBarang() {
      const idBarang = idBarangInput.value.trim();
      const barang = barangData.find(b => b.id_barang === idBarang);

      if (barang) {
        kategoriInput.value = barang.kategori;
        satuanInput.value = barang.satuan;
        barangError.classList.add('d-none');
      } else {
        kategoriInput.value = '';
        satuanInput.value = '';
        barangError.classList.remove('d-none');
      }
    }

    idBarangInput.addEventListener('input', validateBarang);
  });
</script>

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