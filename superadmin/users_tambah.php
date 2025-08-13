<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-user-plus"></i> Tambah Data Users</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="users.php">Tabel Users</a></li>
        <li class="breadcrumb-item active">Tambah Users</li>
      </ul>
    </div>

    <div class="card shadow p-4">
      <form action="proses_tambah_users.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">

          <!-- Kolom Kiri -->
<div class="col-md-6">
  <!-- Nama -->
  <div class="mb-3">
    <label for="nama" class="form-label">Nama Lengkap</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-user"></i>
      </span>
      <input type="text" name="nama" id="nama" class="form-control border-start-0 rounded-end" required>
    </div>
  </div>

  <!-- NIP -->
  <div class="mb-3">
    <label for="nip" class="form-label">NIP</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-id-card"></i>
      </span>
      <input type="text" name="nip" id="nip" class="form-control border-start-0 rounded-end">
    </div>
  </div>

  <!-- Jabatan -->
  <div class="mb-3">
    <label for="jabatan" class="form-label">Jabatan</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-briefcase"></i>
      </span>
      <select name="jabatan" id="jabatan" class="form-control border-start-0 rounded-end" required>
        <option value="" disabled selected>-- Pilih Jabatan --</option>
        <option value="Kepala Sekolah">Kepala Sekolah</option>
        <option value="Petugas Lab">Petugas Lab</option>
        <option value="Guru">Guru</option>
        <option value="Tata Usaha">Tata Usaha</option>
      </select>
    </div>
  </div>

  <!-- Jenis Kelamin -->
  <div class="mb-3">
    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-venus-mars"></i>
      </span>
      <select class="form-control border-start-0 rounded-end" name="jenis_kelamin" id="jenis_kelamin" required>
        <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
        <?php
        $result = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'jenis_kelamin'");
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

  <!-- Email -->
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-envelope"></i>
      </span>
      <input type="email" name="email" id="email" class="form-control border-start-0 rounded-end" required pattern="[a-zA-Z0-9._%+-]+@gmail\.com$" title="Gunakan email @gmail.com saja">
    </div>
  </div>

  <!-- No. HP -->
  <div class="mb-3">
    <label for="no_hp" class="form-label">No. HP</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-phone"></i>
      </span>
      <input type="text" name="no_hp" id="no_hp" class="form-control border-start-0 rounded-end" required pattern="^\+628[0-9]{7,13}$" title="Nomor HP harus dimulai dengan +628">
    </div>
  </div>

  <!-- Alamat -->
  <div class="mb-3">
    <label for="alamat" class="form-label">Alamat</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-map-marker-alt"></i>
      </span>
      <textarea name="alamat" id="alamat" rows="3" class="form-control border-start-0 rounded-end" required></textarea>
    </div>
  </div>
</div>

<!-- Kolom Kanan -->
<div class="col-md-6">
  <!-- Username -->
  <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-user-circle"></i>
      </span>
      <input type="text" name="username" id="username" class="form-control border-start-0 rounded-end" required>
    </div>
  </div>

  <!-- Password -->
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-lock"></i>
      </span>
      <input type="password" name="password" id="password" class="form-control border-start-0 rounded-end" required>
    </div>
  </div>

  <!-- Role -->
  <div class="mb-3">
    <label for="role" class="form-label">Role</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-user-tag"></i>
      </span>
      <select class="form-control border-start-0 rounded-end" name="role" id="role" required>
        <option value="" disabled selected>-- Pilih Role --</option>
        <?php
        $result = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role'");
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

  <!-- Verifikasi -->
  <div class="mb-3">
    <label for="verifikasi" class="form-label">Verifikasi</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-check-circle"></i>
      </span>
      <select class="form-control border-start-0 rounded-end" name="verifikasi" id="verifikasi" required>
        <option value="" disabled selected>-- Pilih Status --</option>
        <?php
        $result = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'verifikasi'");
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

  <!-- Tanggal Daftar -->
  <div class="mb-3">
    <label for="created_at" class="form-label">Tanggal Daftar</label>
    <div class="input-group">
      <span class="input-group-text bg-light text-dark border-end-0">
        <i class="fas fa-calendar-alt"></i>
      </span>
      <input type="date" name="created_at" id="created_at" class="form-control border-start-0 rounded-end" required>
    </div>
  </div>

  <!-- Foto -->
  <div class="mb-3">
    <label for="foto" class="form-label">Foto</label>
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
            <a href="users.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
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
