<?php
session_start();
include 'koneksi.php';

$message = '';
$message_class = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email    = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  $query = "SELECT * FROM users WHERE email='$email'";
  $result = mysqli_query($conn, $query);

  $login_success = false;

  if (mysqli_num_rows($result) > 0) {
    while ($user = mysqli_fetch_assoc($result)) {

      if (password_verify($password, $user['password'])) {

        if (isset($user['verifikasi']) && strtolower($user['verifikasi']) === 'belum terverifikasi') {
          $_SESSION['login_message'] = "Akun Anda Belum di Verifikasi Super Admin.";
          $_SESSION['message_class'] = "bg-yellow-100 text-yellow-700 px-4 py-3 rounded text-sm text-left font-semibold";
          header("Location: login.php");
          exit;
        }

        // Set session sesuai akun yang cocok
        $_SESSION['id_users'] = $user['id_users'];
        $_SESSION['email']    = $user['email'];
        $_SESSION['role']     = $user['role'];

        $_SESSION['login_message'] = "Login Berhasil !!!";
        $_SESSION['message_class'] = "bg-green-100 text-green-700 px-4 py-3 rounded text-sm text-left font-semibold";

        switch ($user['role']) {
          case 'Super Admin':
            $_SESSION['redirect_to'] = "superadmin/dashboard.php";
            break;
          case 'Admin':
            $_SESSION['redirect_to'] = "admin/dashboard.php";
            break;
          case 'Siswa':
            $_SESSION['redirect_to'] = "siswa/dashboard.php";
            break;
          default:
            $_SESSION['login_message'] = "Role tidak dikenali. Hubungi admin.";
            $_SESSION['message_class'] = "bg-red-100 text-red-700 px-4 py-3 rounded text-sm text-left font-semibold";
            session_destroy();
            header("Location: login.php");
            exit;
        }

        $login_success = true;
        header("Location: login.php");
        exit;
      }
    }

    if (!$login_success) {
      $_SESSION['login_message'] = "Password salah!";
      $_SESSION['message_class'] = "bg-red-100 text-red-700 px-4 py-3 rounded text-sm text-left font-semibold";
      header("Location: login.php");
      exit;
    }
  } else {
    $_SESSION['login_message'] = "Email tidak terdaftar!";
    $_SESSION['message_class'] = "bg-red-100 text-red-700 px-4 py-3 rounded text-sm text-left font-semibold";
    header("Location: login.php");
    exit;
  }
}

// Ambil pesan dari session
if (isset($_SESSION['login_message'])) {
  $message = $_SESSION['login_message'];
  $message_class = $_SESSION['message_class'];
  $redirect_to = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : '';

  unset($_SESSION['login_message'], $_SESSION['message_class'], $_SESSION['redirect_to']);
}
?>


<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | System Inventory</title>
  <link rel="icon" type="image/png" href="public/smart.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
          <h2 class="text-3xl font-bold text-gray-800">Login to System Inventory</h2>
          <p class="text-sm text-gray-500 mt-2">Masukkan email dan password Anda</p>
        </div>

        <?php if (!empty($message)) : ?>
          <div class="<?php echo $message_class; ?> text-center mt-4" id="message-box">
            <?php echo $message; ?>
          </div>

          <?php if (!empty($redirect_to)) : ?>
            <script>
              setTimeout(function () {
                window.location.href = "<?php echo $redirect_to; ?>";
              }, 2000);
            </script>
          <?php endif; ?>
        <?php endif; ?>

        <form class="space-y-6" method="POST" action="">

          <!-- Email -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="relative mt-1">
              <span class="absolute left-0 top-0 bottom-0 flex items-center">
                <div class="w-10 h-full bg-gray-200 flex items-center justify-center border border-r-0 border-gray-300 rounded-l-md">
                  <i class="fas fa-envelope text-gray-600 text-sm"></i>
                </div>
              </span>
              <input id="email" name="email" type="email" required
                class="w-full pl-12 pr-4 py-2 h-[40px] border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-400" />
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

      <div class="mb-4">
        <label class="flex items-center">
          <input type="checkbox" name="terms" required 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
          <span class="ml-2 text-sm text-gray-600">
            Apakah data LOGIN sudah sesuai?
            </a>
          </span>
        </label>
      </div>

          <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">Login</button>

          <p class="text-center text-sm text-gray-600">
            Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Register di sini</a><br>
            <a href="index.php" class="text-blue-600 hover:underline">Kembali</a>
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
