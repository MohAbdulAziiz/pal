<?php 
include '../koneksi.php';

$id_peminjam      = $_POST['id_peminjam'] ?? '';
$id_barang        = $_POST['id_barang'] ?? '';
$tanggal_kembali  = $_POST['tanggal_kembali'] ?? date('Y-m-d');
$kondisi_baik     = (int) ($_POST['kondisi_baik'] ?? 0);
$kondisi_rusak    = (int) ($_POST['kondisi_rusak'] ?? 0);
$kondisi_hilang   = (int) ($_POST['kondisi_hilang'] ?? 0);
$status           = $_POST['status'] ?? 'Belum Verifikasi';
$jumlah_form      = (int) ($_POST['jumlah'] ?? 0);

// Validasi total pengembalian tidak melebihi jumlah pinjaman
$total_dikembalikan = $kondisi_baik + $kondisi_rusak + $kondisi_hilang;
if ($total_dikembalikan > $jumlah_form) {
    echo "<script>alert('Total barang dikembalikan melebihi jumlah pinjaman!'); window.location.href='pengembalian_tambah.php';</script>";
    exit;
}

// Ambil data peminjaman terakhir
$query_pinjam = mysqli_query($conn, "
    SELECT * FROM peminjaman 
    WHERE id_users = '$id_peminjam' 
      AND id_barang = '$id_barang' 
      AND status_peminjaman = 'Disetujui'
      AND id_peminjaman NOT IN (
          SELECT id_peminjaman FROM pengembalian WHERE status = 'Terverifikasi'
      )
    ORDER BY tanggal_pinjam DESC
    LIMIT 1
");

$data_pinjam = mysqli_fetch_assoc($query_pinjam);

if (!$data_pinjam) {
    echo "<script>alert('Peminjaman tidak ditemukan atau sudah dikembalikan.'); window.location.href='pengembalian_tambah.php';</script>";
    exit;
}

$id_peminjaman  = $data_pinjam['id_peminjaman'];
$tanggal_pinjam = $data_pinjam['tanggal_pinjam'];
$batas_waktu    = $data_pinjam['batas_waktu'] ?? $tanggal_pinjam;

// Generate ID otomatis
$query_last = mysqli_query($conn, "SELECT id_pengembalian FROM pengembalian ORDER BY id_pengembalian DESC LIMIT 1");
$data_last = mysqli_fetch_assoc($query_last);
if ($data_last) {
    $last_id = intval(substr($data_last['id_pengembalian'], 3));
    $new_id = 'PML' . str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);
} else {
    $new_id = 'PML0001';
}

// Hitung denda & keterlambatan
$selisih_hari = (strtotime($tanggal_kembali) - strtotime($batas_waktu)) / (60 * 60 * 24);
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
        foto,
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
        '$foto_name',
        '$kondisi_baik',
        '$kondisi_rusak',
        '$kondisi_hilang',
        '$tanggal_pinjam',
        '$tanggal_kembali',
        '$status',
        '$denda',
        '$keterangan'
    )
");

// Jika sukses dan status "Terverifikasi", update tabel barang
if ($insert) {
    if ($status === 'Terverifikasi') {
        $barang_q = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
        $barang = mysqli_fetch_assoc($barang_q);

        if ($barang) {
            // Tambahkan kembali barang ke kondisi masing-masing
            $kondisi_baik_total   = $barang['kondisi_baik'] + $kondisi_baik;
            $kondisi_rusak_total  = $barang['kondisi_rusak'] + $kondisi_rusak;
            $kondisi_hilang_total = $barang['kondisi_hilang'] + $kondisi_hilang;
            $jumlah_total         = $kondisi_baik_total + $kondisi_rusak_total + $kondisi_hilang_total;

            // Update data barang
            mysqli_query($conn, "
                UPDATE barang SET 
                    kondisi_baik = '$kondisi_baik_total',
                    kondisi_rusak = '$kondisi_rusak_total',
                    kondisi_hilang = '$kondisi_hilang_total',
                    jumlah = '$jumlah_total'
                WHERE id_barang = '$id_barang'
            ");
        }
    }

    echo "<script>alert('Data pengembalian berhasil disimpan.'); window.location.href='pengembalian.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "'); window.location.href='pengembalian_tambah.php';</script>";
}
?>
