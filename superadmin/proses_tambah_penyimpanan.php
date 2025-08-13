<?php
include '../koneksi.php';

// Ambil dan bersihkan input
$nama_lokasi = mysqli_real_escape_string($conn, $_POST['nama_lokasi']);
$deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
$created_at  = date('Y-m-d H:i:s');

// Ambil ID penyimpanan terakhir
$getLastId = mysqli_query($conn, "SELECT id_penyimpanan FROM penyimpanan ORDER BY id_penyimpanan DESC LIMIT 1");
$dataLast  = mysqli_fetch_assoc($getLastId);

if ($dataLast) {
    $lastNumber = intval(substr($dataLast['id_penyimpanan'], 3));
    $newNumber  = $lastNumber + 1;
} else {
    $newNumber  = 1;
}

$id_penyimpanan = 'PNY' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

// Default foto jika tidak upload
$fotoPath = 'default.png';

// Jika ada upload foto
if (!empty($_FILES['foto']['name'])) {
    $uploadDir    = '../public/uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $maxFileSize  = 2 * 1024 * 1024;

    $originalName = basename($_FILES['foto']['name']);
    $safeName     = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);
    $fileName     = time() . '_' . $safeName;

    $fileTmp    = $_FILES['foto']['tmp_name'];
    $fileType   = $_FILES['foto']['type'];
    $fileSize   = $_FILES['foto']['size'];
    $targetPath = $uploadDir . $fileName;

    if (!in_array($fileType, $allowedTypes)) {
        echo "<script>alert('Jenis file tidak didukung. Hanya JPG, JPEG, PNG, dan WEBP.'); window.history.back();</script>";
        exit;
    }

    if ($fileSize > $maxFileSize) {
        echo "<script>alert('Ukuran file terlalu besar. Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    if (move_uploaded_file($fileTmp, $targetPath)) {
        $fotoPath = $fileName;
    } else {
        echo "<script>alert('Gagal mengunggah foto.'); window.history.back();</script>";
        exit;
    }
}

// Simpan ke database
$query = "INSERT INTO penyimpanan (id_penyimpanan, nama_lokasi, deskripsi, created_at, foto)
          VALUES ('$id_penyimpanan', '$nama_lokasi', '$deskripsi', '$created_at', '$fotoPath')";

if (mysqli_query($conn, $query)) {
    header("Location: penyimpanan.php?success=tambah");
    exit;
} else {
    echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "'); window.history.back();</script>";
}
?>
