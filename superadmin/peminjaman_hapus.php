<?php
include '../koneksi.php'; // Pastikan koneksi ke database

// Cek apakah parameter id tersedia di URL
if (isset($_GET['id_peminjaman'])) {
    $id = $_GET['id_peminjaman'];

    // Ambil data peminjaman terlebih dahulu
    $query_get = mysqli_query($conn, "SELECT id_barang, jumlah FROM peminjaman WHERE id_peminjaman = '$id'");
    $data = mysqli_fetch_assoc($query_get);

    if ($data) {
        $id_barang     = $data['id_barang'];
        $jumlah_pinjam = intval($data['jumlah']);

        // Ambil data kondisi saat ini dari barang
        $query_barang = mysqli_query($conn, "SELECT kondisi_baik, kondisi_rusak, kondisi_hilang FROM barang WHERE id_barang = '$id_barang'");
        $barang = mysqli_fetch_assoc($query_barang);

        if ($barang) {
            $kondisi_baik  = intval($barang['kondisi_baik']) + $jumlah_pinjam; // kembalikan barang yang dipinjam
            $kondisi_rusak = intval($barang['kondisi_rusak']);
            $kondisi_hilang= intval($barang['kondisi_hilang']);

            $jumlah_total = $kondisi_baik + $kondisi_rusak + $kondisi_hilang;

            // Update kondisi_baik dan jumlah di tabel barang
            $update_barang = mysqli_query($conn, "UPDATE barang SET 
                kondisi_baik = '$kondisi_baik', 
                jumlah = '$jumlah_total' 
                WHERE id_barang = '$id_barang'");

            // Hapus data peminjaman
            $query_delete = mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman = '$id'");

            if ($query_delete && $update_barang) {
                header("Location: peminjaman.php?status=success");
            } else {
                header("Location: peminjaman.php?status=delete_error");
            }
        } else {
            header("Location: peminjaman.php?status=barang_not_found");
        }
    } else {
        header("Location: peminjaman.php?status=not_found");
    }
} else {
    header("Location: peminjaman.php?status=invalid");
}
?>
