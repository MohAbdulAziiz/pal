<?php
include '../koneksi.php'; // Pastikan koneksi ke database

// Cek apakah parameter id tersedia di URL
if (isset($_GET['id_pemasok'])) {
    $id = $_GET['id_pemasok'];

    // Query untuk menghapus data berdasarkan ID
    $query = "DELETE FROM pemasok WHERE id_pemasok = '$id'";

    if (mysqli_query($conn, $query)) {
        // Redirect ke halaman barang setelah berhasil hapus
        header("Location: pemasok.php?status=success");
    } else {
        // Redirect jika gagal
        header("Location: pemasok.php?status=error");
    }
} else {
    // Redirect jika tidak ada ID
    header("Location: pemasok.php?status=invalid");
}
?>
