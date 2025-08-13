<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang         = $_POST['id_barang'];
    $id_users          = $_POST['id_users'];
    $tanggal_pinjam    = $_POST['tanggal_pinjam'];
    $tanggal_kembali   = $_POST['tanggal_kembali']; // input manual dari form
    $status_peminjaman = 'Menunggu'; // default status
    $keterangan        = $_POST['keterangan'];
    $created_at        = date('Y-m-d H:i:s');
    $jumlah_pinjam     = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;

    if ($jumlah_pinjam <= 0) {
        $msg = "Jumlah pinjam harus lebih dari 0.";
        header("Location: peminjaman_tambah.php?error=" . urlencode($msg));
        exit;
    }

    // Validasi barang
    $query_barang = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
    if (!$query_barang || mysqli_num_rows($query_barang) == 0) {
        $msg = "Barang tidak ditemukan.";
        header("Location: peminjaman_tambah.php?error=" . urlencode($msg));
        exit;
    }

    $data_barang   = mysqli_fetch_assoc($query_barang);
    $stok_baik     = intval($data_barang['kondisi_baik']);
    $stok_rusak    = intval($data_barang['kondisi_rusak']);
    $stok_hilang   = intval($data_barang['kondisi_hilang']);
    $stok_total    = $stok_baik + $stok_rusak + $stok_hilang;

    if ($jumlah_pinjam > $stok_baik) {
        $msg = "Jumlah pinjam melebihi stok kondisi baik. Maksimal yang bisa dipinjam adalah $stok_baik unit.";
        header("Location: peminjaman_tambah.php?error=" . urlencode($msg));
        exit;
    }

    // Generate ID peminjaman
    $query_last    = mysqli_query($conn, "SELECT id_peminjaman FROM peminjaman ORDER BY id_peminjaman DESC LIMIT 1");
    $last          = mysqli_fetch_assoc($query_last);
    $last_number   = ($last && isset($last['id_peminjaman'])) ? intval(substr($last['id_peminjaman'], 3)) : 0;
    $next_number   = $last_number + 1;
    $id_peminjaman = 'PMJ' . str_pad($next_number, 4, '0', STR_PAD_LEFT);

    // Update stok barang
    $stok_baik_baru    = $stok_baik - $jumlah_pinjam;
    $stok_total_baru   = $stok_baik_baru + $stok_rusak + $stok_hilang;
    $kategori          = $data_barang['kategori'];
    $satuan            = $data_barang['satuan'];

    $update_barang = mysqli_query($conn, "UPDATE barang 
        SET kondisi_baik = '$stok_baik_baru', jumlah = '$stok_total_baru' 
        WHERE id_barang = '$id_barang'");

    // Simpan data peminjaman
    $query_peminjaman = mysqli_query($conn, "INSERT INTO peminjaman (
        id_peminjaman, id_barang, id_users, kategori, jumlah, satuan,
        tanggal_pinjam, tanggal_kembali, status_peminjaman, keterangan, created_at
    ) VALUES (
        '$id_peminjaman', '$id_barang', '$id_users', '$kategori', '$jumlah_pinjam', '$satuan',
        '$tanggal_pinjam', '$tanggal_kembali', '$status_peminjaman', '$keterangan', '$created_at'
    )");

    // Jika status pengembalian sudah diverifikasi (dari input lain), maka kembalikan stok
    if (isset($_POST['status_pengembalian']) && $_POST['status_pengembalian'] === 'Terverifikasi') {
        $kembali_baik   = isset($_POST['kembali_baik']) ? intval($_POST['kembali_baik']) : 0;
        $kembali_rusak  = isset($_POST['kembali_rusak']) ? intval($_POST['kembali_rusak']) : 0;
        $kembali_hilang = isset($_POST['kembali_hilang']) ? intval($_POST['kembali_hilang']) : 0;

        // Update kembali stok di tabel barang
        $update_stok_kembali = mysqli_query($conn, "UPDATE barang SET 
            kondisi_baik = kondisi_baik + $kembali_baik,
            kondisi_rusak = kondisi_rusak + $kembali_rusak,
            kondisi_hilang = kondisi_hilang + $kembali_hilang,
            jumlah = jumlah + $kembali_baik + $kembali_rusak + $kembali_hilang
            WHERE id_barang = '$id_barang'");
    }

    if ($query_peminjaman && $update_barang) {
        header("Location: peminjaman.php?success=1");
    } else {
        $msg = "Gagal menyimpan data.";
        header("Location: peminjaman_tambah.php?error=" . urlencode($msg));
    }
}
?>
