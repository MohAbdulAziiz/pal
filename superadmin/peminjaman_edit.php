<?php
session_start();
include 'header.php';
include '../koneksi.php';

// Cek apakah user sudah login dan punya session yang valid
if (!isset($_SESSION['id_users']) || empty($_SESSION['id_users'])) {
    header("Location: ../login.php");
    exit;
}

$id_users_login = $_SESSION['id_users'];

$id_error = $barang_error = "";
$valid = true;

if (!isset($_GET['id_peminjaman'])) {
    echo "<script>alert('ID Peminjaman tidak ditemukan.'); window.location.href='peminjaman.php';</script>";
    exit;
}

$id_peminjaman = $_GET['id_peminjaman'];

// Ambil data peminjaman
$query = mysqli_query($conn, "
    SELECT pj.*, b.nama_barang, b.kategori AS kategori_barang, b.satuan AS satuan_barang 
    FROM peminjaman pj 
    LEFT JOIN barang b ON pj.id_barang = b.id_barang 
    WHERE pj.id_peminjaman = '$id_peminjaman'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data peminjaman tidak ditemukan.'); window.location.href='peminjaman.php';</script>";
    exit;
}

  // Ambil data barang dan user dari database
  $barangData = mysqli_fetch_all(mysqli_query($conn, "SELECT id_barang, kategori, satuan FROM barang"), MYSQLI_ASSOC);
  $userData = mysqli_fetch_all(mysqli_query($conn, "SELECT id_users FROM users"), MYSQLI_ASSOC);
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-edit"></i> Edit Data Peminjaman</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="peminjaman.php">Tabel Peminjaman</a></li>
        <li class="breadcrumb-item active">Edit Peminjaman</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_GET['error']) ?>
        </div>
      <?php endif; ?>

      <form action="proses_edit_peminjaman.php" method="POST">
        <input type="hidden" name="id_peminjaman" value="<?= $data['id_peminjaman']; ?>">

        <div class="row g-4">
          <div class="col-md-6">
            <!-- ID User -->
<div class="mb-3">
  <label class="form-label">ID User (Peminjam)</label>
  <div class="input-group">
    <span class="input-group-text"><i class="fas fa-user"></i></span>
    <input type="text" name="id_users" id="id_users" class="form-control" value="<?= $data['id_users']; ?>" required>
  </div>
  <small id="user-error" class="text-danger d-none">ID User tidak tersedia</small>
</div>

            <!-- ID Barang -->
<div class="mb-3">
  <label class="form-label">ID Barang</label>
  <div class="input-group">
    <span class="input-group-text"><i class="fas fa-box"></i></span>
    <input type="text" name="id_barang" id="id_barang" class="form-control" value="<?= $data['id_barang']; ?>" required>
  </div>
  <small id="barang-error" class="text-danger d-none">ID Barang tidak tersedia</small>
</div>

            <!-- Jumlah -->
            <div class="mb-3">
              <label class="form-label">Jumlah</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                <input type="number" name="jumlah" class="form-control" value="<?= $data['jumlah']; ?>" required>
              </div>
            </div>

            <!-- Kategori (readonly) -->
            <div class="mb-3">
              <label class="form-label">Kategori</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-tags"></i></span>
                <input type="text" id="kategori" class="form-control" value="<?= $data['kategori_barang']; ?>" readonly>
              </div>
            </div>

            <!-- Satuan (readonly) -->
            <div class="mb-3">
              <label class="form-label">Satuan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                <input type="text" id="satuan" class="form-control" value="<?= $data['satuan_barang']; ?>" readonly>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">

            <!-- Tanggal Pinjam -->
            <div class="mb-3">
              <label class="form-label">Tanggal Pinjam</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                <input type="date" name="tanggal_pinjam" class="form-control"
                  value="<?= date('Y-m-d', strtotime($data['tanggal_pinjam'])) ?>" required>
              </div>
            </div>

            <!-- Tanggal Kembali -->
            <div class="mb-3">
              <label class="form-label">Tanggal Kembali</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                <input type="date" name="tanggal_kembali" class="form-control"
                  value="<?= date('Y-m-d', strtotime($data['tanggal_kembali'])) ?>" required>
              </div>
            </div>

            <!-- Status Peminjaman -->
            <div class="mb-3">
              <label class="form-label">Status Peminjaman</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                <select name="status_peminjaman" class="form-control" required>
                  <option value="Disetujui" <?= $data['status_peminjaman'] == 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                  <option value="Menunggu" <?= $data['status_peminjaman'] == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                  <option value="Ditolak" <?= $data['status_peminjaman'] == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
              </div>
            </div>

            <!-- Keterangan -->
            <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                <textarea name="keterangan" class="form-control" rows="3"><?= $data['keterangan']; ?></textarea>
              </div>
            </div>
          </div>

          <!-- Tombol Aksi -->
          <div class="col-12 tombol-kanan mt-3">
            <a href="peminjaman.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-success" <?= !$valid ? 'disabled' : '' ?>><i class="fas fa-save"></i> Update Data</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<script>
  const barangData = <?= json_encode($barangData) ?>;
  const userData = <?= json_encode($userData) ?>;

document.addEventListener('DOMContentLoaded', function () {
  const idBarangInput = document.getElementById('id_barang');
  const kategoriInput = document.getElementById('kategori');
  const satuanInput = document.getElementById('satuan');
  const barangError = document.getElementById('barang-error');

  const idUserInput = document.getElementById('id_users');
  const userError = document.getElementById('user-error');

  const submitBtn = document.getElementById('submit-btn');

  function validateForm() {
    const idBarang = idBarangInput.value.trim();
    const idUser = idUserInput.value.trim();

    const barang = barangData.find(b => b.id_barang === idBarang);
    const user = userData.find(u => u.id_users === idUser);

    // Validasi barang
    if (barang) {
      kategoriInput.value = barang.kategori;
      satuanInput.value = barang.satuan;
      barangError.classList.add('d-none');
    } else {
      kategoriInput.value = '';
      satuanInput.value = '';
      barangError.classList.remove('d-none');
    }

    // Validasi user
    if (user) {
      userError.classList.add('d-none');
    } else {
      userError.classList.remove('d-none');
    }

    // Nonaktifkan tombol submit jika ada kesalahan
    submitBtn.disabled = !(barang && user);
  }

  idBarangInput.addEventListener('input', validateForm);
  idUserInput.addEventListener('input', validateForm);

  // Trigger validasi awal
  validateForm();
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
