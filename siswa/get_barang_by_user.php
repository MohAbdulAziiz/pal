<?php
include '../koneksi.php';

$id_users = $_GET['id_users'] ?? '';
$id_barang = $_GET['id_barang'] ?? '';

header('Content-Type: application/json');

if (!empty($id_barang)) {
    // Jika id_barang tersedia, ambil jumlah peminjaman terakhir
    $query = mysqli_query($conn, "
        SELECT jumlah
        FROM peminjaman
        WHERE id_users = '$id_users'
          AND id_barang = '$id_barang'
          AND status_peminjaman = 'Disetujui'
        ORDER BY tanggal_pinjam DESC
        LIMIT 1
    ");

    $result = mysqli_fetch_assoc($query);

    echo json_encode([
        'jumlah' => $result['jumlah'] ?? null
    ]);
} else {
    // Jika id_barang tidak ada, ambil daftar barang yang belum dikembalikan
    $data = [];
    $query = mysqli_query($conn, "
        SELECT DISTINCT b.id_barang, b.nama_barang
        FROM peminjaman p
        JOIN barang b ON p.id_barang = b.id_barang
        WHERE p.id_users = '$id_users'
          AND p.status_peminjaman = 'Disetujui'
          AND p.id_peminjaman NOT IN (
              SELECT id_peminjaman 
              FROM pengembalian 
              WHERE status = 'Terverifikasi'
          )
    ");

    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    echo json_encode($data);
}
?>
