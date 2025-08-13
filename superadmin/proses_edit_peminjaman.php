<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_peminjaman     = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    $id_users          = mysqli_real_escape_string($conn, $_POST['id_users']);
    $id_barang         = mysqli_real_escape_string($conn, $_POST['id_barang']);
    $jumlah_baru       = (int)$_POST['jumlah'];
    $tanggal_pinjam    = mysqli_real_escape_string($conn, $_POST['tanggal_pinjam']);
    $tanggal_kembali   = mysqli_real_escape_string($conn, $_POST['tanggal_kembali']);
    $status_peminjaman = mysqli_real_escape_string($conn, $_POST['status_peminjaman']);
    $keterangan        = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $created_at        = date('Y-m-d H:i:s');

    // Cek validitas user
    $cek_user = mysqli_query($conn, "SELECT id_users FROM users WHERE id_users = '$id_users'");
    if (mysqli_num_rows($cek_user) === 0) {
        echo "<script>alert('ID User tidak ditemukan.'); window.location='peminjaman.php';</script>";
        exit;
    }

    // Ambil data peminjaman lama
    $query_old = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    $data_old  = mysqli_fetch_assoc($query_old);

    if (!$data_old) {
        echo "<script>alert('Data peminjaman tidak ditemukan.'); window.location='peminjaman.php';</script>";
        exit;
    }

    $id_barang_lama = $data_old['id_barang'];
    $jumlah_lama    = (int)$data_old['jumlah'];

    // Cek barang baru
    $query_barang = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
    if (mysqli_num_rows($query_barang) === 0) {
        echo "<script>alert('ID Barang tidak ditemukan.'); window.location='peminjaman.php';</script>";
        exit;
    }

    $data_barang       = mysqli_fetch_assoc($query_barang);
    $stok_baik         = (int)$data_barang['kondisi_baik'];
    $stok_rusak        = (int)$data_barang['kondisi_rusak'];
    $stok_hilang       = (int)$data_barang['kondisi_hilang'];
    $kategori          = $data_barang['kategori'];
    $satuan            = $data_barang['satuan'];

    // Jika status peminjaman DITOLAK, kembalikan stok lama dan update langsung tanpa cek stok
    if (strtolower($status_peminjaman) === "ditolak") {
        // Kembalikan stok barang sesuai jumlah lama
        $barang_lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_barang_lama'"));
        $stok_kembali = (int)$barang_lama['kondisi_baik'] + $jumlah_lama;
        $total_baru   = $stok_kembali + (int)$barang_lama['kondisi_rusak'] + (int)$barang_lama['kondisi_hilang'];

        mysqli_query($conn, "UPDATE barang SET kondisi_baik = '$stok_kembali', jumlah = '$total_baru' WHERE id_barang = '$id_barang_lama'");

        // Update data peminjaman (status menjadi ditolak)
        $query_update = mysqli_query($conn, "
            UPDATE peminjaman SET
                id_users          = '$id_users',
                id_barang         = '$id_barang',
                kategori          = '$kategori',
                jumlah            = '$jumlah_baru',
                satuan            = '$satuan',
                tanggal_pinjam    = '$tanggal_pinjam',
                tanggal_kembali   = '$tanggal_kembali',
                status_peminjaman = '$status_peminjaman',
                keterangan        = '$keterangan',
                created_at        = '$created_at'
            WHERE id_peminjaman = '$id_peminjaman'
        ");

        if ($query_update) {
            echo "<script>alert('Peminjaman ditolak dan stok barang telah dikembalikan.'); window.location='peminjaman.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data.'); window.history.back();</script>";
        }
        exit;
    }

    // -------------------------
    // Jika status bukan "ditolak", lanjut proses normal
    // -------------------------

    // Jika barang berubah, kembalikan stok lama dulu
    if ($id_barang !== $id_barang_lama) {
        $barang_lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_barang_lama'"));
        $kondisi_baik_lama_kembali = (int)$barang_lama['kondisi_baik'] + $jumlah_lama;
        $total_lama = $kondisi_baik_lama_kembali + (int)$barang_lama['kondisi_rusak'] + (int)$barang_lama['kondisi_hilang'];
        mysqli_query($conn, "UPDATE barang SET kondisi_baik = '$kondisi_baik_lama_kembali', jumlah = '$total_lama' WHERE id_barang = '$id_barang_lama'");

        if ($jumlah_baru > $stok_baik) {
            echo "<script>alert('Jumlah pinjam melebihi stok barang. Maksimal: $stok_baik'); window.history.back();</script>";
            exit;
        }

        $stok_baik_baru = $stok_baik - $jumlah_baru;
        $total_baru     = $stok_baik_baru + $stok_rusak + $stok_hilang;

        $update_barang = mysqli_query($conn, "UPDATE barang SET kondisi_baik = '$stok_baik_baru', jumlah = '$total_baru' WHERE id_barang = '$id_barang'");
    } else {
        // Barang tidak berubah, cek selisih
        $selisih = $jumlah_baru - $jumlah_lama;

        if ($selisih > 0 && $selisih > $stok_baik) {
            echo "<script>alert('Jumlah pinjam melebihi stok barang. Maksimal tambahan: $stok_baik'); window.history.back();</script>";
            exit;
        }

        $stok_baik_baru = $stok_baik - $selisih;
        $total_baru     = $stok_baik_baru + $stok_rusak + $stok_hilang;

        $update_barang = mysqli_query($conn, "UPDATE barang SET kondisi_baik = '$stok_baik_baru', jumlah = '$total_baru' WHERE id_barang = '$id_barang'");
    }

    // Update data peminjaman
    $query_update = mysqli_query($conn, "
        UPDATE peminjaman SET
            id_users          = '$id_users',
            id_barang         = '$id_barang',
            kategori          = '$kategori',
            jumlah            = '$jumlah_baru',
            satuan            = '$satuan',
            tanggal_pinjam    = '$tanggal_pinjam',
            tanggal_kembali   = '$tanggal_kembali',
            status_peminjaman = '$status_peminjaman',
            keterangan        = '$keterangan',
            created_at        = '$created_at'
        WHERE id_peminjaman = '$id_peminjaman'
    ");

    if ($query_update && $update_barang) {
        echo "<script>alert('Data peminjaman berhasil diperbarui.'); window.location='peminjaman.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.'); window.history.back();</script>";
    }

} else {
    echo "<script>alert('Akses tidak valid.'); window.location='peminjaman.php';</script>";
}
?>
