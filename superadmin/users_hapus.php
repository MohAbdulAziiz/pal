<?php
include '../koneksi.php'; // Pastikan koneksi ke database

// Cek apakah parameter id tersedia di URL
if (isset($_GET['id_users'])) {
    $id = $_GET['id_users'];

    // Query untuk menghapus data berdasarkan ID
    $query = "DELETE FROM users WHERE id_users = '$id'";

    if (mysqli_query($conn, $query)) {
        // Redirect ke halaman barang setelah berhasil hapus
        header("Location: users.php?status=success");
    } else {
        // Redirect jika gagal
        header("Location: users.php?status=error");
    }
} else {
    // Redirect jika tidak ada ID
    header("Location: users.php?status=invalid");
}
?>
