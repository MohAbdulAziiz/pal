<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penyimpanan = isset($_POST['id_penyimpanan']) ? mysqli_real_escape_string($conn, $_POST['id_penyimpanan']) : '';
    $nama_lokasi = isset($_POST['nama_lokasi']) ? mysqli_real_escape_string($conn, $_POST['nama_lokasi']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, $_POST['deskripsi']) : '';

    // Ambil data lama (terutama untuk foto)
    $query = mysqli_query($conn, "SELECT foto FROM penyimpanan WHERE id_penyimpanan = '$id_penyimpanan'");
    if (!$query) {
        die("Query error: " . mysqli_error($conn));
    }

    $data_lama = mysqli_fetch_assoc($query);
    $foto_lama = $data_lama['foto'];

    $foto = $foto_lama; // Default pakai foto lama

    // Cek apakah user upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['name'] !== '') {
        $nama_file = time() . '_' . basename($_FILES['foto']['name']);
        $target_dir = '../public/uploads/';
        $target_file = $target_dir . $nama_file;

        // Pindahkan file baru
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            // Hapus foto lama jika ada dan file-nya memang ada di folder
            if (!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
                unlink($target_dir . $foto_lama);
            }
            $foto = $nama_file;
        } else {
            echo "<script>alert('Gagal mengunggah foto baru!'); history.back();</script>";
            exit;
        }
    }

    // Update data ke database
    $update = mysqli_query($conn, "UPDATE penyimpanan SET
        nama_lokasi = '$nama_lokasi',
        deskripsi = '$deskripsi',
        foto = '$foto'
        WHERE id_penyimpanan = '$id_penyimpanan'
    ");

    if ($update) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='penyimpanan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!'); history.back();</script>";
    }
} else {
    echo "<script>alert('Metode tidak diizinkan'); history.back();</script>";
}
?>
