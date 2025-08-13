<?php
session_start(); // Harus di baris pertama sebelum output apapun
include '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_users'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit;
}

$id_users = $_SESSION['id_users'];

// Proses ubah password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_lama        = mysqli_real_escape_string($conn, $_POST['password_lama']);
    $password_baru        = mysqli_real_escape_string($conn, $_POST['password_baru']);
    $konfirmasi_password  = mysqli_real_escape_string($conn, $_POST['konfirmasi_password']);

    // Ambil password lama dari database
    $query = mysqli_query($conn, "SELECT password FROM users WHERE id_users = '$id_users'");
    $user  = mysqli_fetch_assoc($query);

    if (!$user || !password_verify($password_lama, $user['password'])) {
        $_SESSION['pesan'] = "<div class='alert alert-danger'>Password lama salah!</div>";
    } elseif ($password_baru !== $konfirmasi_password) {
        $_SESSION['pesan'] = "<div class='alert alert-warning'>Password baru dan konfirmasi tidak cocok.</div>";
    } else {
        $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
        $update    = mysqli_query($conn, "UPDATE users SET password = '$hash_baru' WHERE id_users = '$id_users'");

        if ($update) {
            $_SESSION['pesan'] = "<div class='alert alert-success'>Password berhasil diubah.</div>";
        } else {
            $_SESSION['pesan'] = "<div class='alert alert-danger'>Gagal mengubah password.</div>";
        }
    }

    // Redirect untuk mencegah form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Setelah proses, baru tampilkan halaman
include 'header.php';
?>

<!-- ========== HALAMAN HTML ========= -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-cogs"></i> Pengaturan</h1>
        <p>Kelola akun dan preferensi Anda</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Pengaturan</li>
      </ul>
    </div>

    <?php
    if (isset($_SESSION['pesan'])) {
        echo $_SESSION['pesan'];
        unset($_SESSION['pesan']);
    }
    ?>

    <ul class="list-group list-group-flush">
      <!-- Ubah Password -->
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <i class="fas fa-key me-2 text-primary"></i> Ubah Password
        </div>
        <button class="btn btn-outline-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#ubahPasswordModal" style="width: 100px; ">Edit</button>
      </li>
    </ul>
  </div>
</section>

<!-- Modal: Ubah Password -->
<div class="modal fade" id="ubahPasswordModal" tabindex="-1" aria-labelledby="ubahPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ubahPasswordModalLabel">Ubah Password</h5>
        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Tutup" style="position: absolute; top: 10px; right: 10px;">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">

        <div class="mb-3">
          <label for="password_lama" class="form-label">Password Lama</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" name="password_lama" id="password_lama" required>
            <span class="input-group-text toggle-password" onclick="togglePassword('password_lama', this)">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>

        <div class="mb-3">
          <label for="password_baru" class="form-label">Password Baru</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-key"></i></span>
            <input type="password" class="form-control" name="password_baru" id="password_baru" required>
            <span class="input-group-text toggle-password" onclick="togglePassword('password_baru', this)">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>

        <div class="mb-3">
          <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
            <input type="password" class="form-control" name="konfirmasi_password" id="konfirmasi_password" required>
            <span class="input-group-text toggle-password" onclick="togglePassword('konfirmasi_password', this)">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>

     </div>
<div class="modal-footer d-flex gap-2">
  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
    <i class="fas fa-times-circle me-1"></i> Batal
  </button>
  <button type="submit" class="btn btn-primary">
    <i class="fas fa-save me-1"></i> Simpan
  </button>
</div>

    </form>
  </div>
</div>

<!-- Script untuk toggle password -->
<script>
function togglePassword(inputId, iconElement) {
  const input = document.getElementById(inputId);
  const icon = iconElement.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  body { overflow-x: hidden; }
</style>
