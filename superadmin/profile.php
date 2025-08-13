<?php
include 'header.php';
include '../koneksi.php';

if (!isset($_SESSION['id_users'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit;
}

$id_users = $_SESSION['id_users'];

// Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);

    // Ambil data lama (untuk hapus foto lama jika diganti)
    $oldData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM users WHERE id_users = '$id_users'"));
    $fotoLama = $oldData['foto'];

    // Default foto tetap
    $fotoBaru = $fotoLama;

    // Jika ada upload foto baru
    if (!empty($_FILES['foto']['name'])) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['foto']['name'];
        $fileTmp  = $_FILES['foto']['tmp_name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowedExt)) {
            $newFileName = 'user_' . $id_users . '_' . time() . '.' . $fileExt;
            $uploadPath = '../public/uploads/' . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                // Hapus foto lama jika bukan default
                if (!empty($fotoLama) && file_exists("../public/uploads/$fotoLama") && $fotoLama !== 'default-user.png') {
                    unlink("../public/uploads/$fotoLama");
                }
                $fotoBaru = $newFileName;
            }
        }
    }

    // Update database
    $update = mysqli_query($conn, "UPDATE users SET 
        nama='$nama',
        nip='$nip',
        username='$username',
        email='$email',
        no_hp='$no_hp',
        alamat='$alamat',
        jabatan='$jabatan',
        foto='$fotoBaru'
        WHERE id_users='$id_users'");

    if ($update) {
        echo "<script>alert('Profil berhasil diperbarui'); window.location.href='profile.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui profil');</script>";
    }
}

// Pastikan id_users ada dan aman
$id_users = isset($id_users) ? mysqli_real_escape_string($conn, $id_users) : 0;

// Ambil Data
$query = mysqli_query($conn, "SELECT * FROM users WHERE id_users = '$id_users'");
$data  = mysqli_fetch_assoc($query);

// Path default foto profil
$defaultFoto = '../public/users_default.png';

// Tentukan foto yang akan digunakan
$fotoPath = (!empty($data['foto']) && file_exists('../public/uploads/' . $data['foto'])) 
    ? '../public/uploads/' . $data['foto'] 
    : $defaultFoto;
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title mb-4">
      <div>
        <h1><i class="fas fa-user-circle"></i> Profil Saya</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Profil</li>
      </ul>
    </div>

    <div class="row justify-content-center">
      <!-- Foto dan Role -->
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm p-4 text-center">
          <img id="fotoPreview" 
               src="<?= htmlspecialchars($fotoPath); ?>" 
               alt="Foto Profil" 
               class="img-fluid rounded-circle mx-auto d-block mb-3" 
               style="width: 200px; height: 200px; object-fit: cover;">
          <!-- Input Foto -->
          <div id="fotoUploadWrapper" class="d-none">
            <input type="file" name="foto" id="fotoInput" class="form-control mb-3" accept="image/*" form="profileForm">
          </div>

          <h5 class="mb-3"><?= htmlspecialchars($data['nama']); ?></h5>
          <div class="mb-2 text-start">
            <label class="form-label fw-bold">Role</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($data['role']); ?>" readonly>
          </div>
          <div class="text-start">
            <label class="form-label fw-bold">Verifikasi</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($data['verifikasi']); ?>" readonly>
          </div>
          <div class="text-start">
            <label class="form-label fw-bold">Jabatan</label>
            <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($data['jabatan']); ?>" readonly form="profileForm">
          </div>
        </div>
      </div>

      <!-- Detail Profil -->
      <div class="col-md-8 mb-4">
        <div class="card shadow-sm p-4">
          <h4 class="mb-4"><i class="fas fa-user"></i> Detail Profil</h4>
          <form id="profileForm" method="post" enctype="multipart/form-data">
            <div class="row mb-3">
              <div class="col-md-4 fw-bold">ID Pengguna</div>
              <div class="col-md-8">
                <input type="text" name="id_users" class="form-control" value="<?= htmlspecialchars($data['id_users']); ?>" readonly>
              </div>
            </div>
            <?php
              $fields = [
                'nama' => 'Nama Lengkap',
                'nip' => 'NIP',
                'username' => 'Username',
                'email' => 'Email',
                'no_hp' => 'No Handphone'
              ];

              foreach ($fields as $name => $label) {
                  $type = $name === 'email' ? 'email' : 'text';
                  echo '
                  <div class="row mb-3">
                    <div class="col-md-4 fw-bold">' . $label . '</div>
                    <div class="col-md-8">
                      <input type="' . $type . '" name="' . $name . '" class="form-control" value="' . htmlspecialchars($data[$name]) . '" readonly>
                    </div>
                  </div>';
              }
            ?>
            <div class="row mb-3">
              <div class="col-md-4 fw-bold">Alamat</div>
              <div class="col-md-8">
                <textarea name="alamat" class="form-control" rows="2" readonly><?= htmlspecialchars($data['alamat']); ?></textarea>
              </div>
            </div>
              <div class="row mb-4">
                <div class="col-md-4 fw-bold">Tanggal Input</div>
                <div class="col-md-8">
                  <input type="text" class="form-control" 
                        value="<?= date('d-m-Y', strtotime($data['created_at'])); ?>" 
                        readonly 
                        data-readonly="true">
                </div>
              </div>
              <!-- Tombol -->
              <div class="text-end">
                <button type="button" class="btn btn-primary me-2" id="editBtn" style="min-width: 120px;">
                  <i class="fas fa-edit"></i> Edit Profil
                </button>
                <button type="submit" class="btn btn-success me-2 d-none" id="saveBtn" style="min-width: 120px;">
                  <i class="fas fa-save"></i> Simpan Data
                </button>
                <button type="button" class="btn btn-danger d-none" id="cancelBtn" style="min-width: 120px;">
                  <i class="fas fa-times"></i> Batal
                </button>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  body { overflow-x: hidden; }
  .card { border-radius: 15px; }
</style>

<script>
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const cancelBtn = document.getElementById('cancelBtn');
const form = document.getElementById('profileForm');
const fotoUploadWrapper = document.getElementById('fotoUploadWrapper');
const fotoInput = document.getElementById('fotoInput');
const fotoPreview = document.getElementById('fotoPreview');

// Simpan nilai awal
const originalValues = {};
form.querySelectorAll('input, textarea').forEach(input => {
  originalValues[input.name] = input.value;
});
let originalFoto = fotoPreview.src;

editBtn.addEventListener('click', () => {
  const inputs = form.querySelectorAll('input, textarea');
  inputs.forEach(input => {
    if (input.name !== 'id_users' && !input.dataset.readonly) {
      input.removeAttribute('readonly');
    }
  });
  fotoUploadWrapper.classList.remove('d-none');
  editBtn.classList.add('d-none');
  saveBtn.classList.remove('d-none');
  cancelBtn.classList.remove('d-none');
});

// Tombol Batal
cancelBtn.addEventListener('click', () => {
  // Kembalikan nilai awal
  form.querySelectorAll('input, textarea').forEach(input => {
    if (input.name in originalValues) {
      input.value = originalValues[input.name];
      input.setAttribute('readonly', true);
    }
  });

  // Kembalikan foto
  fotoPreview.src = originalFoto;
  fotoInput.value = "";

  // Kembalikan tombol
  fotoUploadWrapper.classList.add('d-none');
  saveBtn.classList.add('d-none');
  cancelBtn.classList.add('d-none');
  editBtn.classList.remove('d-none');
});

// Preview foto saat dipilih
fotoInput.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (file) {
    fotoPreview.src = URL.createObjectURL(file);
  }
});

</script>
