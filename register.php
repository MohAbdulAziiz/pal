<?php
session_start();
include 'koneksi.php';

$message = '';
if (isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
  $username   = mysqli_real_escape_string($conn, $_POST['username']);
  $email      = mysqli_real_escape_string($conn, $_POST['email']);
  $no_hp      = mysqli_real_escape_string($conn, $_POST['no_hp']);
  $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role       = mysqli_real_escape_string($conn, $_POST['role']);
  $verifikasi = 'Belum Terverifikasi';

  // Validasi Email dan No HP
  if (!str_ends_with($email, '@gmail.com')) {
    $_SESSION['message'] = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm font-bold text-left'>
                              Email harus menggunakan domain @gmail.com.
                            </div>";
  } elseif (!preg_match('/^\+628[0-9]{8,}$/', $no_hp)) {
    $_SESSION['message'] = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm font-bold text-left'>
                              Nomor HP harus diawali dengan +628 dan terdiri dari angka.
                            </div>";
  } else {
    // Cek apakah username atau email sudah digunakan
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
      $_SESSION['message'] = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm font-bold text-left'>
                                Username atau email sudah digunakan.
                              </div>";
    } else {
      // Buat ID otomatis USR0001, USR0002, dst.
      $getLastID = mysqli_query($conn, "SELECT id_users FROM users ORDER BY id_users DESC LIMIT 1");
      if (mysqli_num_rows($getLastID) > 0) {
        $lastRow = mysqli_fetch_assoc($getLastID);
        $lastNum = (int)substr($lastRow['id_users'], 3);
        $newID = 'USR' . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
      } else {
        $newID = 'USR0001';
      }

      // Query insert
      $query = "INSERT INTO users (id_users, nama, username, email, no_hp, password, role, verifikasi)
                VALUES ('$newID', '$nama', '$username', '$email', '$no_hp', '$password', '$role', '$verifikasi')";

      if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "<div class='bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded text-sm font-bold text-left'>
                                  Pendaftaran berhasil! Silakan login.
                                </div>";
      } else {
        $_SESSION['message'] = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm font-bold text-left'>
                                  Gagal mendaftar: " . mysqli_error($conn) . "
                                </div>";
      }
    }
  }
  // Redirect untuk mencegah re-submit
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}
?>

<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | System Inventory</title>
  <link rel="icon" type="image/png" href="public/smart.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
<style>
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .fade-in {
    animation: fadeIn 0.8s ease-in-out;
  }

  html, body {
    height: 100%;
    margin: 0;
    overflow: hidden; /* cegah scroll */
  }

  @media (max-width: 1024px) {
    .container-responsive {
      flex-direction: column;
    }

    .left-panel {
      width: 100% !important;
      height: auto !important;
      padding: 2rem 1.5rem;
    }

    .bg-image {
      display: none;
    }
  }
  ::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: rgba(100, 100, 100, 0.3);
  border-radius: 4px;
}

</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


</head>
<body class="min-h-screen flex bg-cover bg-center fade-in" style="background-image: url('public/smart.jpeg');">
  <div class="flex flex-row h-full w-full container-responsive">

    <!-- Left: Login Form Card -->
<div class="left-panel w-full md:w-3/5 lg:w-2/5 min-h-screen flex items-center justify-center bg-white bg-opacity-40 backdrop-blur-md shadow-xl overflow-y-auto">
  <div class="shadow-lg p-6 max-w-md w-full animate__animated animate__fadeIn overflow-y-auto max-h-[90vh] rounded-md">

        <div class="mb-6 text-center">
          <h2 class="text-3xl font-bold text-gray-800">Regsiter to System Inventory</h2>
          <p class="text-sm text-gray-500 mt-2">Masukkan Data Anda Dengan Sesuai</p>
        </div>
      <?php if (!empty($message)) echo $message; ?>
      <form class="space-y-6" method="POST" action="">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Nama Lengkap -->
        <div>
          <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
          <div class="relative mt-1">
            <span class="absolute inset-y-0 left-0 flex items-center">
              <div class="w-10 h-full bg-gray-200 flex items-center justify-center border border-r-0 border-gray-300 rounded-l-md">
                <i class="fas fa-user text-gray-600 text-sm"></i>
              </div>
            </span>
            <input id="nama" name="nama" type="text" required
              class="w-full pl-12 pr-4 py-2 h-10 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
          </div>
        </div>

        <!-- Username -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
          <div class="relative mt-1">
            <span class="absolute inset-y-0 left-0 flex items-center">
              <div class="w-10 h-full bg-gray-200 flex items-center justify-center border border-r-0 border-gray-300 rounded-l-md">
                <i class="fas fa-user-tag text-gray-600 text-sm"></i>
              </div>
            </span>
            <input id="username" name="username" type="text" required
              class="w-full pl-12 pr-4 py-2 h-10 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
          </div>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <div class="relative mt-1">
            <span class="absolute inset-y-0 left-0 flex items-center">
              <div class="w-10 h-full bg-gray-200 flex items-center justify-center border border-r-0 border-gray-300 rounded-l-md">
                <i class="fas fa-envelope text-gray-600 text-sm"></i>
              </div>
            </span>
            <input id="email" name="email" type="email" required
              class="w-full pl-12 pr-4 py-2 h-10 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
          </div>
        </div>

        <!-- No WhatsApp -->
        <div>
          <label for="no_hp" class="block text-sm font-medium text-gray-700">No. WhatsApp</label>
          <div class="relative mt-1">
            <span class="absolute inset-y-0 left-0 flex items-center">
              <div class="w-10 h-full bg-gray-200 flex items-center justify-center border border-r-0 border-gray-300 rounded-l-md">
                <i class="fab fa-whatsapp text-gray-600 text-sm"></i>
              </div>
            </span>
            <input id="no_hp" name="no_hp" type="tel" placeholder="+62..." required
              class="w-full pl-12 pr-4 py-2 h-10 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
          </div>
        </div>

      <!-- Password -->
      <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <div class="relative mt-1">
          <span class="absolute left-0 top-0 bottom-0 flex items-center">
            <div class="w-10 h-full bg-gray-200 flex items-center justify-center border border-r-0 border-gray-300 rounded-l-md">
              <i class="fas fa-lock text-gray-600 text-sm"></i>
            </div>
          </span>

          <input id="password" name="password" type="password" required
            class="w-full pl-12 pr-10 py-2 h-[40px] border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-400" />

          <!-- Eye icon -->
          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" onclick="togglePassword()">
            <i id="togglePasswordIcon" class="fas fa-eye text-gray-600"></i>
          </span>
        </div>
      </div>

        <!-- Role -->
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700">Pilih Role</label>
          <div class="relative mt-1">
            <span class="absolute inset-y-0 left-0 flex items-center">
              <div class="w-10 h-full bg-gray-200 flex items-center justify-center border border-r-0 border-gray-300 rounded-l-md">
                <i class="fas fa-user-cog text-gray-600 text-sm"></i>
              </div>
            </span>
            <select id="role" name="role" required
              class="w-full pl-12 pr-4 py-2 h-10 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400">
              <option value="">-- Pilih Role --</option>
              <option value="Super Admin">Super Admin</option>
              <option value="Admin">Admin</option>
              <option value="Siswa">Siswa</option>
            </select>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="flex items-center">
          <input type="checkbox" name="terms" required 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
          <span class="ml-2 text-sm text-gray-600">
            Apakah data register sudah sesuai?
            </a>
          </span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="terms" required 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
          <span class="ml-2 text-sm text-gray-600">
            Saya telah membaca dan menyetujui Syarat & Ketentuan
            </a>
          </span>
        </label>
      </div>

        <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700">
          Daftar
        </button>
        <p class="text-center text-sm text-gray-600">
          Sudah punya akun? <a href="login.php" class="text-blue-600 hover:underline">Login di sini</a>
        <br><a href="index.php" class="text-blue-600 hover:underline">Kembali</a>
        </p>
      </form>
    </div>
  </div>
</div>
</body>
</html>

<script>
  function togglePassword() {
    const passwordInput = document.getElementById("password");
    const icon = document.getElementById("togglePasswordIcon");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    } else {
      passwordInput.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    }
  }

  document.querySelector('form').addEventListener('submit', function(e) {
  const rememberCheckbox = document.querySelector('input[name="remember"]');
  if (!rememberCheckbox.checked) {
    e.preventDefault();
    alert('Silakan centang "Remember me" terlebih dahulu.');
  }
});

</script>
