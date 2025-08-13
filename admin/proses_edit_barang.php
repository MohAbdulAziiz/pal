<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil & sanitasi data
    $id_barang_lama  = mysqli_real_escape_string($conn, $_POST['id_barang']);
    $nama_barang     = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kondisi_baik    = (int)$_POST['kondisi_baik'];
    $kondisi_rusak   = (int)$_POST['kondisi_rusak'];
    $kondisi_hilang  = (int)$_POST['kondisi_hilang'];
    $kategori        = mysqli_real_escape_string($conn, $_POST['kategori']);
    $kategori_lama   = mysqli_real_escape_string($conn, $_POST['kategori_lama']);
    $tanggal_beli    = mysqli_real_escape_string($conn, $_POST['tanggal_beli']);
    $satuan          = mysqli_real_escape_string($conn, $_POST['satuan']);
    $id_penyimpanan  = mysqli_real_escape_string($conn, $_POST['id_penyimpanan']);
    $id_pemasok      = mysqli_real_escape_string($conn, $_POST['id_pemasok']);
    $merk            = mysqli_real_escape_string($conn, $_POST['merk']);
    $spesifikasi     = mysqli_real_escape_string($conn, $_POST['spesifikasi']);
    $harga_beli      = mysqli_real_escape_string($conn, $_POST['harga_beli']);

    // Hitung jumlah total
    $jumlah  = $kondisi_baik + $kondisi_rusak + $kondisi_hilang;
    $kondisi = $jumlah;

    // Tanggal update otomatis (dengan jam, menit, detik)
    $tanggal_update = date('Y-m-d H:i:s');

    // Ambil foto lama
    $result_lama = mysqli_query($conn, "SELECT foto FROM barang WHERE id_barang = '$id_barang_lama'");
    $data_lama   = mysqli_fetch_assoc($result_lama);
    $foto_lama   = $data_lama['foto'] ?? '';

    $foto = $foto_lama;
    $upload_dir = '../public/uploads/';

    // Upload foto baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nama_file   = time() . '_' . basename($_FILES['foto']['name']);
        $target_file = $upload_dir . $nama_file;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
                unlink($upload_dir . $foto_lama);
            }
            $foto = $nama_file;
        }
    }

    // Jika kategori berubah → buat ID baru, hapus lama, insert ulang
    if ($kategori !== $kategori_lama) {
        // Buat prefix ID berdasarkan kategori baru
        switch ($kategori) {
            case 'Elektronik': $prefix = 'ELK'; break;
            case 'Furniture':  $prefix = 'FRT'; break;
            case 'Alat Tulis': $prefix = 'ATK'; break;
            case 'Dokumen':    $prefix = 'DKM'; break;
            default:           $prefix = 'UNK'; break;
        }

        $query_last = mysqli_query($conn, "SELECT id_barang FROM barang WHERE id_barang LIKE '$prefix%' ORDER BY id_barang DESC LIMIT 1");
        $last_id    = mysqli_fetch_assoc($query_last);
        $new_number = $last_id ? str_pad((int)substr($last_id['id_barang'], 3) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        $id_barang_baru = $prefix . $new_number;

        // Hapus data lama (pastikan tidak ada FK aktif di tabel lain)
        mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id_barang_lama'");

        // Insert data baru
        $query_insert = "
            INSERT INTO barang (
                id_barang, nama_barang, jumlah, kondisi, kondisi_baik, kondisi_rusak, kondisi_hilang,
                kategori, tanggal_beli, tanggal_update, satuan, id_penyimpanan, foto,
                id_pemasok, merk, spesifikasi, harga_beli
            ) VALUES (
                '$id_barang_baru', '$nama_barang', '$jumlah', '$kondisi_baik', '$kondisi_rusak', '$kondisi_hilang',
                '$kategori', '$tanggal_beli', '$tanggal_update', '$satuan', '$id_penyimpanan', '$foto',
                '$id_pemasok', '$merk', '$spesifikasi', '$harga_beli'
            )";

        if (mysqli_query($conn, $query_insert)) {
            echo "<script>alert('Kategori berubah. ID barang diganti menjadi $id_barang_baru'); window.location='barang.php';</script>";
        } else {
            echo "<script>alert('Gagal insert data baru: " . mysqli_error($conn) . "'); history.back();</script>";
        }

    } else {
        // Jika kategori sama → update data
        $query_update = "
            UPDATE barang SET
                nama_barang     = '$nama_barang',
                jumlah          = '$jumlah',
                kondisi_baik    = '$kondisi_baik',
                kondisi_rusak   = '$kondisi_rusak',
                kondisi_hilang  = '$kondisi_hilang',
                kategori        = '$kategori',
                tanggal_beli    = '$tanggal_beli',
                tanggal_update  = '$tanggal_update',
                satuan          = '$satuan',
                id_penyimpanan  = '$id_penyimpanan',
                foto            = '$foto',
                id_pemasok      = '$id_pemasok',
                merk            = '$merk',
                spesifikasi     = '$spesifikasi',
                harga_beli      = '$harga_beli'
            WHERE id_barang     = '$id_barang_lama'
        ";

        if (mysqli_query($conn, $query_update)) {
            echo "<script>alert('Data berhasil diperbarui!'); window.location='barang.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data: " . mysqli_error($conn) . "'); history.back();</script>";
        }
    }

} else {
    echo "<script>alert('Metode tidak diizinkan'); history.back();</script>";
}
?>
