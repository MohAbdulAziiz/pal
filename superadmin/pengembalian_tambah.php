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
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-undo"></i> Tambah Data Pengembalian</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="pengembalian.php">Tabel Pengembalian</a></li>
        <li class="breadcrumb-item active">Tambah Pengembalian</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <form action="proses_tambah_pengembalian.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">

          <!-- Kolom Kiri -->
          <div class="col-md-6">
            <!-- ID Peminjam -->
            <div class="mb-3">
              <label for="id_peminjam" class="form-label">Nama Peminjam</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-user"></i>
                </span>
                <select class="form-control border-start-0 rounded-end" name="id_peminjam" id="id_peminjam" required onchange="loadBarangByPeminjam(this.value)">
                  <option value="" disabled selected>-- Pilih Peminjam --</option>
                  <?php
                  $peminjam_q = mysqli_query($conn, "
                    SELECT DISTINCT u.id_users, u.nama
                    FROM peminjaman p
                    JOIN users u ON p.id_users = u.id_users
                    WHERE p.status_peminjaman = 'Disetujui'
                      AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)
                  ");
                  while ($u = mysqli_fetch_assoc($peminjam_q)) {
                    echo "<option value='{$u['id_users']}'>{$u['nama']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- ID Barang -->
            <div class="mb-3">
              <label for="id_barang" class="form-label">Nama Barang</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-box"></i>
                </span>
                <select class="form-control border-start-0 rounded-end" name="id_barang" id="id_barang" required>
                  <option value="" disabled selected>-- Pilih Barang --</option>
                </select>
              </div>
            </div>

            <!-- Tanggal Kembali -->
            <div class="mb-3">
              <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control border-start-0 rounded-end" required>
              </div>
            </div>

            <!-- Foto -->
            <div class="mb-3">
              <label for="foto" class="form-label">Foto Pengembalian</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-camera"></i>
                </span>
                <input type="file" name="foto" id="foto" class="form-control border-start-0 rounded-end" accept="image/*">
              </div>
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Jumlah</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                <input type="number" name="jumlah" class="form-control" value="<?= $data['jumlah']; ?>" readonly>
              </div>
            </div>
            
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

            <!-- Status -->
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-dark border-end-0">
                  <i class="fas fa-check-circle"></i>
                </span>
                <select class="form-control border-start-0 rounded-end" name="status" id="status" required>
                  <option value="" disabled selected>-- Pilih Status --</option>
                  <option value="Terverifikasi">Terverifikasi</option>
                  <option value="Belum Verifikasi">Belum Verifikasi</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Tombol Aksi -->
          <div class="col-12 tombol-kanan mt-3">
            <a href="pengembalian.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</section>

<!-- SCRIPT AJAX -->
<script>
function loadBarangByPeminjam(id_users) {
  const barangSelect = document.getElementById('id_barang');
  barangSelect.innerHTML = '<option selected disabled>Loading...</option>';
  document.querySelector('input[name="jumlah"]').value = ''; // Reset jumlah saat pilih peminjam

  fetch('get_barang_by_user.php?id_users=' + id_users)
    .then(response => response.json())
    .then(data => {
      barangSelect.innerHTML = '<option value="" disabled selected>-- Pilih Barang --</option>';
      if (data.length === 0) {
        barangSelect.innerHTML += '<option disabled>Tidak ada barang</option>';
      } else {
        data.forEach(item => {
          barangSelect.innerHTML += `<option value="${item.id_barang}">${item.nama_barang}</option>`;
        });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      barangSelect.innerHTML = '<option disabled>Error loading data</option>';
    });
}

document.getElementById('id_barang').addEventListener('change', function() {
  const id_users = document.getElementById('id_peminjam').value;
  const id_barang = this.value;

  fetch(`get_barang_by_user.php?id_users=${id_users}&id_barang=${id_barang}`)
    .then(response => response.json())
    .then(data => {
      document.querySelector('input[name="jumlah"]').value = data.jumlah || '';
    })
    .catch(error => {
      console.error('Gagal mengambil jumlah:', error);
      document.querySelector('input[name="jumlah"]').value = '';
    });
});
</script>


<!-- STYLE -->
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