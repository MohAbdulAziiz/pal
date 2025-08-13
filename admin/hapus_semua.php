<?php
include '../koneksi.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Permintaan tidak valid'];

// Fungsi hapus foto (jika ada)
function hapusFoto($path) {
    if (!empty($path) && file_exists($path)) {
        unlink($path);
    }
}

// Hapus Barang
if (!empty($_POST['hapus_barang']) && !empty($_POST['id']) && is_array($_POST['id'])) {
    foreach ($_POST['id'] as $id) {
        $id = mysqli_real_escape_string($conn, $id);
        mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id'");
    }
    $response = ['status' => 'success', 'message' => 'Data barang berhasil dihapus'];
}

// Hapus Penyimpanan
elseif (!empty($_POST['hapus_penyimpanan']) && !empty($_POST['id']) && is_array($_POST['id'])) {
    foreach ($_POST['id'] as $id) {
        $id = mysqli_real_escape_string($conn, $id);
        $foto_query = mysqli_query($conn, "SELECT foto FROM penyimpanan WHERE id_penyimpanan = '$id'");
        if ($foto_query && mysqli_num_rows($foto_query) > 0) {
            $foto = mysqli_fetch_assoc($foto_query)['foto'];
            hapusFoto("../public/uploads/$foto");
        }
        mysqli_query($conn, "DELETE FROM penyimpanan WHERE id_penyimpanan = '$id'");
    }
    $response = ['status' => 'success', 'message' => 'Data penyimpanan berhasil dihapus'];
}

// Hapus Users
elseif (!empty($_POST['hapus_users']) && !empty($_POST['id']) && is_array($_POST['id'])) {
    foreach ($_POST['id'] as $id) {
        $id = mysqli_real_escape_string($conn, $id);
        $foto_query = mysqli_query($conn, "SELECT foto FROM users WHERE id_users = '$id'");
        if ($foto_query && mysqli_num_rows($foto_query) > 0) {
            $foto = mysqli_fetch_assoc($foto_query)['foto'];
            hapusFoto("../public/uploads/$foto");
        }
        mysqli_query($conn, "DELETE FROM users WHERE id_users = '$id'");
    }
    $response = ['status' => 'success', 'message' => 'Data users berhasil dihapus'];
}

// Hapus Pemasok
elseif (!empty($_POST['hapus_pemasok']) && !empty($_POST['id']) && is_array($_POST['id'])) {
    foreach ($_POST['id'] as $id) {
        $id = mysqli_real_escape_string($conn, $id);
        // Langsung hapus dari database karena tidak ada kolom foto
        mysqli_query($conn, "DELETE FROM pemasok WHERE id_pemasok = '$id'");
    }
    $response = ['status' => 'success', 'message' => 'Data Pemasok Berhasil Dihapus'];
}

// Hapus Peminjaman
elseif (!empty($_POST['hapus_peminjaman']) && !empty($_POST['id']) && is_array($_POST['id'])) {
    foreach ($_POST['id'] as $id) {
        $id = mysqli_real_escape_string($conn, $id);

        // Ambil data barang yang dipinjam
        $detail_query = mysqli_query($conn, "SELECT id_barang, jumlah FROM peminjaman WHERE id_peminjaman = '$id'");
        if ($detail_query && mysqli_num_rows($detail_query) > 0) {
            while ($row = mysqli_fetch_assoc($detail_query)) {
                $id_barang = $row['id_barang'];
                $jumlah = $row['jumlah'];

                // Kembalikan jumlah ke kondisi_baik
                mysqli_query($conn, "UPDATE barang SET kondisi_baik = kondisi_baik + $jumlah WHERE id_barang = '$id_barang'");
            }
        }

        // Hapus data peminjaman
        mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman = '$id'");
    }
    $response = ['status' => 'success', 'message' => 'Data Peminjaman Berhasil Dihapus & Stok Barang Dikembalikan'];
}

echo json_encode($response);
exit;
?>
