<?php
include '../koneksi.php'; // Pastikan koneksi ke database

// Cek apakah parameter id tersedia di URL
if (isset($_GET['id_barang'])) {
    $id = $_GET['id_barang'];

    // Query untuk menghapus data berdasarkan ID
    $query = "DELETE FROM barang WHERE id_barang = '$id'";

    if (mysqli_query($conn, $query)) {
        // Redirect ke halaman barang setelah berhasil hapus
        header("Location: barang.php?status=success");
    } else {
        // Redirect jika gagal
        header("Location: barang.php?status=error");
    }
} else {
    // Redirect jika tidak ada ID
    header("Location: barang.php?status=invalid");
}
?>
