<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Metode tidak valid.'); window.history.back();</script>";
    exit;
}

// Ambil input
$nama        = trim($_POST['nama']);
$username    = trim($_POST['username']);
$email       = trim($_POST['email']);
$password    = $_POST['password'];
$no_hp       = trim($_POST['no_hp']);
$alamat      = trim($_POST['alamat']);
$jabatan     = trim($_POST['jabatan']);
$nip         = trim($_POST['nip']);
$role        = $_POST['role'];
$verifikasi  = $_POST['verifikasi'];
$created_at  = $_POST['created_at'];

// Validasi Email harus @gmail.com
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
    echo "<script>alert('Email harus menggunakan domain @gmail.com'); window.history.back();</script>";
    exit;
}

// Validasi No HP harus +628...
if (!preg_match('/^\+628[0-9]{7,13}$/', $no_hp)) {
    echo "<script>alert('Nomor HP harus diawali dengan +628 dan angka selanjutnya'); window.history.back();</script>";
    exit;
}

// Cek duplikat username/email
$cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
if (mysqli_num_rows($cek) > 0) {
    $existing = mysqli_fetch_assoc($cek);
    if ($existing['username'] === $username) {
        echo "<script>alert('Username sudah digunakan. Silakan gunakan yang lain.'); window.history.back();</script>";
    } else {
        echo "<script>alert('Email sudah terdaftar. Gunakan email lain.'); window.history.back();</script>";
    }
    exit;
}

// Generate ID Users Otomatis
$prefix = "USR";
$result = mysqli_query($conn, "SELECT id_users FROM users ORDER BY id_users DESC LIMIT 1");
if (mysqli_num_rows($result) > 0) {
    $lastId = mysqli_fetch_assoc($result)['id_users'];
    $lastNumber = (int)substr($lastId, 3); // Ambil angka setelah prefix 'USR'
    $newNumber = $lastNumber + 1;
    $id_users = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
} else {
    $id_users = $prefix . "0001";
}

// Enkripsi password
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

// Upload Foto
$fotoPath = 'default.png';
if (!empty($_FILES['foto']['name'])) {
    $uploadDir = '../public/uploads/';
    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; 

    $original = basename($_FILES['foto']['name']);
    $safe = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $original);
    $fileName = time() . '_' . $safe;

    $tmp = $_FILES['foto']['tmp_name'];
    $type = $_FILES['foto']['type'];
    $size = $_FILES['foto']['size'];
    $path = $uploadDir . $fileName;

    if (!in_array($type, $allowed)) {
        echo "<script>alert('Jenis file tidak didukung.'); window.history.back();</script>";
        exit;
    }

    if ($size > $maxSize) {
        echo "<script>alert('Ukuran file melebihi 2MB.'); window.history.back();</script>";
        exit;
    }

    if (move_uploaded_file($tmp, $path)) {
        $fotoPath = $fileName;
    } else {
        echo "<script>alert('Gagal upload foto.'); window.history.back();</script>";
        exit;
    }
}

// Simpan ke database
$query = "INSERT INTO users 
(id_users, nama, username, email, password, no_hp, alamat, jabatan, nip, role, verifikasi, created_at, foto)
VALUES 
('$id_users', '$nama', '$username', '$email', '$hashPassword', '$no_hp', '$alamat', '$jabatan', '$nip', '$role', '$verifikasi', '$created_at', '$fotoPath')";

if (mysqli_query($conn, $query)) {
    header("Location: users.php?success=tambah");
    exit;
} else {
    echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "'); window.history.back();</script>";
}
?>
