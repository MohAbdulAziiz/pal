<?php 
session_start();
include '../koneksi.php';

$id_peminjam = $_SESSION['id_users'] ?? '';
$id_barang   = $_POST['id_barang'] ?? '';
$tanggal_kembali_input = $_POST['tanggal_kembali'] ?? date('Y-m-d');
$kondisi_baik   = (int) ($_POST['kondisi_baik'] ?? 0);
$kondisi_rusak  = (int) ($_POST['kondisi_rusak'] ?? 0);
$kondisi_hilang = (int) ($_POST['kondisi_hilang'] ?? 0);
$status         = $_POST['status'] ?? 'Belum Verifikasi';

// Validasi dasar
if (empty($id_peminjam) || empty($id_barang)) {
    echo "<script>alert('ID Peminjam dan ID Barang wajib diisi.'); window.location.href='pengembalian_tambah.php';</script>";
    exit;
}

// Ambil peminjaman yang masih memiliki sisa
$query_pinjam = mysqli_query($conn, "
    SELECT p.*, 
        IFNULL(SUM(pg.kondisi_baik + pg.kondisi_rusak + pg.kondisi_hilang), 0) AS total_kembali
    FROM peminjaman p
    LEFT JOIN pengembalian pg ON p.id_peminjaman = pg.id_peminjaman
    WHERE p.id_users = '$id_peminjam' 
      AND p.id_barang = '$id_barang' 
      AND p.status_peminjaman = 'Disetujui'
    GROUP BY p.id_peminjaman
    HAVING total_kembali < p.jumlah
    ORDER BY p.tanggal_pinjam ASC
    LIMIT 1
");

if (!$query_pinjam) {
    echo "<script>alert('Query gagal: " . mysqli_error($conn) . "'); window.location.href='pengembalian_tambah.php';</script>";
    exit;
}

$data_pinjam = mysqli_fetch_assoc($query_pinjam);
if (!$data_pinjam) {
    echo "<script>alert('Semua barang sudah dikembalikan.'); window.location.href='pengembalian_tambah.php';</script>";
    exit;
}

// Ambil info penting dari peminjaman
$id_peminjaman     = $data_pinjam['id_peminjaman'];
$tanggal_pinjam    = $data_pinjam['tanggal_pinjam'];
$tenggat_kembali   = $data_pinjam['tanggal_kembali'];
$jumlah_pinjam     = $data_pinjam['jumlah'];
$telah_kembali     = $data_pinjam['total_kembali'];

$sisa_dikembalikan = $jumlah_pinjam - $telah_kembali;
$jumlah_form       = $kondisi_baik + $kondisi_rusak + $kondisi_hilang;

// Validasi total pengembalian
if ($jumlah_form > $sisa_dikembalikan) {
    echo "<script>alert('Jumlah pengembalian melebihi sisa pinjaman.'); window.location.href='pengembalian_tambah.php';</script>";
    exit;
}

// Generate ID Pengembalian
$query_last = mysqli_query($conn, "SELECT id_pengembalian FROM pengembalian ORDER BY id_pengembalian DESC LIMIT 1");
$data_last = mysqli_fetch_assoc($query_last);
$last_id = ($data_last) ? intval(substr($data_last['id_pengembalian'], 3)) : 0;
$new_id = 'PML' . str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);

// Hitung denda
$selisih_hari = (strtotime($tanggal_kembali_input) - strtotime($tenggat_kembali)) / (60 * 60 * 24);
$denda = ($selisih_hari > 0) ? ($selisih_hari * 500) : 0;
$keterangan = ($selisih_hari > 0) ? 'Terlambat' : 'Tepat Waktu';

// Upload foto jika ada
$foto_name = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $foto_name = 'pengembalian_' . time() . '.' . $ext;
    $upload_path = '../public/uploads/' . $foto_name;
    move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path);
}

// Simpan pengembalian
$insert = mysqli_query($conn, "
    INSERT INTO pengembalian (
        id_pengembalian,
        id_peminjaman,
        id_users,
        id_barang,
        foto,
        jumlah,
        kondisi_baik,
        kondisi_rusak,
        kondisi_hilang,
        tanggal_pinjam,
        tanggal_kembali,
        status,
        denda,
        keterangan
    ) VALUES (
        '$new_id',
        '$id_peminjaman',
        '$id_peminjam',
        '$id_barang',
        '$foto_name',
        '$jumlah_form',
        '$kondisi_baik',
        '$kondisi_rusak',
        '$kondisi_hilang',
        '$tanggal_pinjam',
        '$tanggal_kembali_input',
        '$status',
        '$denda',
        '$keterangan'
    )
");

if ($insert) {
    // Jika sudah diverifikasi, update stok barang
    if ($status === 'Terverifikasi') {
        $barang_q = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
        $barang = mysqli_fetch_assoc($barang_q);

        if ($barang) {
            $update_q = mysqli_query($conn, "
                UPDATE barang SET 
                    kondisi_baik   = kondisi_baik + $kondisi_baik,
                    kondisi_rusak  = kondisi_rusak + $kondisi_rusak,
                    kondisi_hilang = kondisi_hilang + $kondisi_hilang,
                    jumlah         = jumlah + $jumlah_form
                WHERE id_barang = '$id_barang'
            ");
        }
    }

    echo "<script>alert('Data pengembalian berhasil disimpan.'); window.location.href='pengembalian.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "'); window.location.href='pengembalian_tambah.php';</script>";
}
?>
