<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Metode tidak valid.'); window.history.back();</script>";
    exit;
}

// Ambil dan amankan data dari form
$id_penyimpanan  = mysqli_real_escape_string($conn, $_POST['id_penyimpanan']);
$nama_barang     = mysqli_real_escape_string($conn, $_POST['nama_barang']);
$kondisi_baik    = (int)mysqli_real_escape_string($conn, $_POST['kondisi_baik']);
$kondisi_rusak   = (int)mysqli_real_escape_string($conn, $_POST['kondisi_rusak']);
$kondisi_hilang  = (int)mysqli_real_escape_string($conn, $_POST['kondisi_hilang']);
$satuan          = mysqli_real_escape_string($conn, $_POST['satuan']);
$kategori        = strtolower(mysqli_real_escape_string($conn, $_POST['kategori']));
$tanggal_beli      = mysqli_real_escape_string($conn, $_POST['tanggal_beli']);
$id_pemasok      = mysqli_real_escape_string($conn, $_POST['id_pemasok']);
$merk            = mysqli_real_escape_string($conn, $_POST['merk']);
$spesifikasi     = mysqli_real_escape_string($conn, $_POST['spesifikasi']);
$harga_beli      = mysqli_real_escape_string($conn, $_POST['harga_beli']);

// Hitung total jumlah
$jumlah = $kondisi_baik + $kondisi_rusak + $kondisi_hilang;

// Validasi lokasi penyimpanan
$cekLokasi = mysqli_query($conn, "SELECT 1 FROM penyimpanan WHERE id_penyimpanan = '$id_penyimpanan'");
if (mysqli_num_rows($cekLokasi) == 0) {
    echo "<script>alert('Lokasi penyimpanan tidak valid.'); window.history.back();</script>";
    exit;
}

// Buat ID Barang otomatis
switch ($kategori) {
    case 'elektronik': $prefix = 'ELK'; break;
    case 'furniture': $prefix = 'PRT'; break;
    case 'alat tulis': $prefix = 'ATK'; break;
    case 'dokumen': $prefix = 'DKM'; break;
}

$cekLast = mysqli_query($conn, "
    SELECT id_barang FROM barang
    WHERE id_barang LIKE '$prefix%'
    ORDER BY CAST(SUBSTRING(id_barang, LENGTH('$prefix') + 1) AS UNSIGNED) DESC
    LIMIT 1
");

if (mysqli_num_rows($cekLast) > 0) {
    $last = mysqli_fetch_assoc($cekLast)['id_barang'];
    $num = intval(substr($last, strlen($prefix))) + 1;
    $id_barang = $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
} else {
    $id_barang = $prefix . '0001';
}

// Default foto
$fotoPath = 'default.png';

// Proses upload foto jika ada
if (!empty($_FILES['foto']['name'])) {
    $uploadDir = '../public/uploads/';
    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $maxSize = 2 * 1024 * 1024;

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
$query = "INSERT INTO barang 
(id_barang, id_penyimpanan, nama_barang, jumlah, kondisi_hilang, kondisi_rusak, kondisi_baik, satuan, kategori, tanggal_beli, foto, id_pemasok, merk, spesifikasi, harga_beli)
VALUES 
('$id_barang', '$id_penyimpanan', '$nama_barang', '$jumlah', '$kondisi_hilang', '$kondisi_rusak', '$kondisi_baik', '$satuan', '$kategori', '$tanggal_beli', '$fotoPath', '$id_pemasok', '$merk', '$spesifikasi', '$harga_beli')";

if (mysqli_query($conn, $query)) {
    header("Location: barang.php?success=tambah");
    exit;
} else {
    echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "'); window.history.back();</script>";
}
?>
