<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan sanitasi data
    $id_users   = mysqli_real_escape_string($conn, $_POST['id_users']);
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp      = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $alamat     = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jabatan    = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $nip        = mysqli_real_escape_string($conn, $_POST['nip']);
    $role       = mysqli_real_escape_string($conn, $_POST['role']);
    $jenis_kelamin       = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $verifikasi = mysqli_real_escape_string($conn, $_POST['verifikasi']);

    $upload_dir = '../public/uploads/';

    // Ambil foto lama dari DB
    $result_lama = mysqli_query($conn, "SELECT foto FROM users WHERE id_users = '$id_users'");
    $data_lama   = mysqli_fetch_assoc($result_lama);
    $foto_lama   = $data_lama['foto'];
    $foto        = $foto_lama;

    // Cek apakah ada upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (!in_array($ext, $allowed_ext)) {
            echo "<script>alert('Format gambar tidak valid. Gunakan jpg, jpeg, png, atau webp.'); history.back();</script>";
            exit;
        }

        if ($_FILES['foto']['size'] > $max_size) {
            echo "<script>alert('Ukuran foto maksimal 5MB.'); history.back();</script>";
            exit;
        }

        $nama_file_baru = time() . '_' . basename($_FILES['foto']['name']);
        $target_file = $upload_dir . $nama_file_baru;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
                unlink($upload_dir . $foto_lama);
            }
            $foto = $nama_file_baru;
        }
    }

    // Validasi email tidak digunakan user lain
    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND id_users != '$id_users'");
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email sudah digunakan oleh pengguna lain.'); window.location.href = 'users_edit.php?id=$id_users';</script>";
        exit;
    }

    // Update data user
    $update = mysqli_query($conn, "UPDATE users SET
        nama        = '$nama',
        email       = '$email',
        no_hp       = '$no_hp',
        username    = '$username',
        alamat      = '$alamat',
        jabatan     = '$jabatan',
        nip         = '$nip',
        role        = '$role',
        verifikasi  = '$verifikasi',
        foto        = '$foto',
        jenis_kelamin = '$jenis_kelamin'
        WHERE id_users = '$id_users'
    ");

    if ($update) {
        echo "<script>alert('Data pengguna berhasil diperbarui.'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data pengguna.'); history.back();</script>";
    }
} else {
    echo "<script>alert('Metode tidak diizinkan.'); history.back();</script>";
}
?>
