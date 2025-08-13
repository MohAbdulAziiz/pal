<?php
include '../koneksi.php'; // Pastikan koneksi ke database

// Cek apakah parameter id tersedia di URL
if (isset($_GET['id_penyimpanan'])) {
    $id = $_GET['id_penyimpanan'];

    // Query untuk menghapus data berdasarkan ID
    $query = "DELETE FROM penyimpanan WHERE id_penyimpanan = '$id'";

    if (mysqli_query($conn, $query)) {
        // Redirect ke halaman barang setelah berhasil hapus
        header("Location: penyimpanan.php?status=success");
    } else {
        // Redirect jika gagal
        header("Location: penyimpanan.php?status=error");
    }
} else {
    // Redirect jika tidak ada ID
    header("Location: penyimpanan.php?status=invalid");
}
?>
