<?php
// Tangkap filter
$tanggalMulai   = $_GET['tanggal_mulai'] ?? '';
$tanggalSelesai = $_GET['tanggal_selesai'] ?? '';
$kategori       = $_GET['kategori'] ?? '';
$export         = $_GET['export'] ?? '';

$isFilterValid = !empty($_GET['bulan']) && !empty($_GET['tahun']) && !empty($kategori);

// Mapping file export
$fileMap = [
'pdf' => [
    'Users'        => 'pdfbulanan/users_pdf.php',
    'Barang'       => 'pdfbulanan/barang_pdf.php',
    'Pemasok'      => 'pdfbulanan/pemasok_pdf.php',
    'Penyimpanan'  => 'pdfbulanan/penyimpanan_pdf.php',
    'Peminjaman'   => 'pdfbulanan/peminjaman_pdf.php',
    'Pengembalian' => 'pdfbulanan/pengembalian_pdf.php'
],
  'excel' => [
    'Users' => 'excelbulanan/users_excel.php',
    'Barang' => 'excelbulanan/barang_excel.php',
    'Pemasok' => 'excelbulanan/pemasok_excel.php',
    'Penyimpanan' => 'excelbulanan/penyimpanan_excel.php',
    'Peminjaman' => 'excelbulanan/peminjaman_excel.php',
    'Pengembalian' => 'excelbulanan/pengembalian_excel.php'
  ]
];

// Mapping kategori ke file print
$printFileMap = [
  'Users' => 'printbulanan/users_print.php',
  'Barang' => 'printbulanan/barang_print.php',
  'Pemasok' => 'printbulanan/pemasok_print.php',
  'Penyimpanan' => 'printbulanan/penyimpanan_print.php',
  'Peminjaman' => 'printbulanan/peminjaman_print.php',
  'Pengembalian' => 'printbulanan/pengembalian_print.php'
];

$printFile = $printFileMap[$kategori] ?? '';

// Redirect jika export diminta dan filter valid
if ($isFilterValid && in_array($export, ['pdf', 'excel'])) {
  $targetFile = $fileMap[$export][$kategori] ?? '';
  if ($targetFile) {
    $params = http_build_query([
      'bulan' => $_GET['bulan'],
      'tahun' => $_GET['tahun'],
      'kategori' => $kategori
    ]);
    header("Location: $targetFile?$params");
    exit;
  }
}

include 'header.php';
include '../koneksi.php';
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-file-alt"></i> Data Laporan Bulanan</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Laporan</li>
      </ul>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="row row-cols-lg-auto g-3 align-items-end mb-4">
      <div class="col">
        <label for="bulan" class="form-label">Bulan</label>
        <select class="form-control" name="bulan" required>
          <option value="">-- Pilih Bulan --</option>
          <?php
          for ($i = 1; $i <= 12; $i++) {
            $selected = ($i == ($_GET['bulan'] ?? '')) ? 'selected' : '';
            echo "<option value=\"$i\" $selected>" . date('F', mktime(0, 0, 0, $i, 10)) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="col">
        <label for="tahun" class="form-label">Tahun</label>
        <select class="form-control" name="tahun" required>
          <option value="">-- Pilih Tahun --</option>
          <?php
          $yearNow = date('Y');
          for ($i = $yearNow; $i >= $yearNow - 10; $i--) {
            $selected = ($i == ($_GET['tahun'] ?? '')) ? 'selected' : '';
            echo "<option value=\"$i\" $selected>$i</option>";
          }
          ?>
        </select>
      </div>

      <div class="col">
        <label for="kategori" class="form-label">Kategori</label>
        <select class="form-control" name="kategori" required>
          <option value="">-- Pilih Data --</option>
          <?php
          $kategoriOptions = ['Users', 'Barang', 'Pemasok', 'Penyimpanan', 'Peminjaman', 'Pengembalian'];
          foreach ($kategoriOptions as $option) {
            $selected = ($option == ($_GET['kategori'] ?? '')) ? 'selected' : '';
            echo "<option value=\"$option\" $selected>$option</option>";
          }
          ?>
        </select>
      </div>

      <div class="col">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-filter"></i> Lihat
        </button>
      </div>
    </form>

<?php if ($isFilterValid): ?>
<style>
    /* Tambahan jarak antar tombol */
    .export-buttons .btn {
        margin-right: 6px;
        margin-bottom: 6px;
    }
</style>

<div class="mb-3 export-buttons d-flex flex-wrap">
<a href="?<?= http_build_query([
    'bulan' => $_GET['bulan'],
    'tahun' => $_GET['tahun'],
    'kategori' => $kategori,
    'export' => 'pdf'
]) ?>" class="btn btn-danger" target="_blank">
    <i class="fas fa-print"></i> Cetak / PDF
</a>


    <a href="?<?= http_build_query([
        'bulan' => $_GET['bulan'],
        'tahun' => $_GET['tahun'],
        'kategori' => $kategori,
        'export' => 'excel'
    ]) ?>" class="btn btn-success" target="_blank">
        <i class="fas fa-file-excel"></i> Excel
    </a>
</div>
<?php endif; ?>

<!-- Tabel -->
<div class="table-responsive mt-3">
    <table id="tabelLaporan" class="table table-bordered table-striped table-hover align-middle">
        <thead style="background-color: #f5f5f5;">
            <tr class="text-center">
                <?php
                $kategori = $_GET['kategori'] ?? '';
                if ($kategori === 'Users') {
                    echo '<th>No</th><th>ID</th><th>Username</th><th>Email</th><th>No HP</th><th>Nama</th><th>Jenis Kelamin</th><th>Alamat</th><th>Jabatan</th><th>NIP</th><th>Role</th><th>Verifikasi</th>';
                } elseif ($kategori === 'Barang') {
                    echo '<th>No</th><th>ID</th><th>Nama Barang</th><th>Jumlah</th><th>Satuan</th><th>Baik</th><th>Rusak</th><th>Hilang</th><th>Kategori</th><th>Penyimpanan</th><th>Pemasok</th><th>Merk</th><th>Spesifikasi</th><th>Harga Beli</th><th>Tanggal Beli</th>';
                } elseif ($kategori === 'Pemasok') {
                    echo '<th>No</th><th>ID</th><th>Nama</th><th>Alamat</th><th>Kota</th><th>Telepon</th><th>Email</th><th>Keterangan</th>';
                } elseif ($kategori === 'Penyimpanan') {
                    echo '<th>No</th><th>ID</th><th>Nama Tempat</th><th>Deskripsi</th>';
                } elseif ($kategori === 'Peminjaman') {
                    echo '<th>No</th><th>ID</th><th>ID Barang</th><th>ID User</th><th>Kategori</th><th>Jumlah</th><th>Satuan</th><th>Tgl Pinjam</th><th>Batas Waktu</th><th>Status</th><th>Keterangan</th>';
                } elseif ($kategori === 'Pengembalian') {
                    echo '<th>No</th><th>ID</th><th>ID Peminjaman</th><th>ID Barang</th><th>ID Users</th><th>Tanggal Pinjam</th><th>Tanggal Kembali</th><th>Denda</th><th>Status</th><th>Keterangan</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody>

<?php
function tanggalFilter($field) {
  $bulan  = $_GET['bulan'] ?? '';
  $tahun  = $_GET['tahun'] ?? '';

  if (!empty($bulan) && !empty($tahun)) {
    return "WHERE MONTH($field) = '$bulan' AND YEAR($field) = '$tahun'";
  }
  return '';
}

function formatTanggal($tanggal) {
  if (!$tanggal || $tanggal == '0000-00-00') return '-';
  return date('d-m-Y', strtotime($tanggal));
}

$no = 1;
  switch ($kategori) {
case 'Users':
  $query = mysqli_query($conn, "SELECT * FROM users " . tanggalFilter('created_at'));
  while ($d = mysqli_fetch_assoc($query)) {
    echo "<tr>
    <td>{$no}</td>
            <td>{$d['id_users']}</td>
            <td>{$d['username']}</td>
            <td>{$d['email']}</td>
            <td>{$d['no_hp']}</td>
            <td>{$d['nama']}</td>
            <td>{$d['jenis_kelamin']}</td>
            <td>{$d['alamat']}</td>
            <td>{$d['jabatan']}</td>
            <td>{$d['nip']}</td>
            <td>{$d['role']}</td>
            <td>{$d['verifikasi']}</td>
          </tr>";
      $no++;
  }
  break;

case 'Barang':
  $query = mysqli_query($conn, "SELECT * FROM barang " . tanggalFilter('tanggal_beli'));
  while ($d = mysqli_fetch_assoc($query)) {
    echo "<tr>
    <td>{$no}</td>
            <td>{$d['id_barang']}</td>
            <td>{$d['nama_barang']}</td>
            <td>{$d['jumlah']}</td>
            <td>{$d['satuan']}</td>
            <td>{$d['kondisi_baik']}</td>
            <td>{$d['kondisi_rusak']}</td>
            <td>{$d['kondisi_hilang']}</td>
            <td>{$d['kategori']}</td>
            <td>{$d['id_penyimpanan']}</td>
            <td>{$d['id_pemasok']}</td>
            <td>{$d['merk']}</td>
            <td>{$d['spesifikasi']}</td>
            <td>{$d['harga_beli']}</td>
            <td>" . formatTanggal($d['tanggal_beli']) . "</td>
          </tr>";
              $no++;
  }
  break;

  
case 'Penyimpanan':
  $query = mysqli_query($conn, "SELECT * FROM penyimpanan " . tanggalFilter('created_at'));
  while ($d = mysqli_fetch_assoc($query)) {
    echo "<tr>
    <td>{$no}</td>
            <td>{$d['id_penyimpanan']}</td>
            <td>{$d['nama_lokasi']}</td>
            <td>{$d['deskripsi']}</td>
          </tr>";
              $no++;
  }
  break;

case 'Pemasok':
  $query = mysqli_query($conn, "SELECT * FROM pemasok " . tanggalFilter('created_at'));
  while ($d = mysqli_fetch_assoc($query)) {
    echo "<tr>
    <td>{$no}</td>
            <td>{$d['id_pemasok']}</td>
            <td>{$d['nama_pemasok']}</td>
            <td>{$d['alamat']}</td>
            <td>{$d['kota']}</td>
            <td>{$d['telepon']}</td>
            <td>{$d['email']}</td>
            <td>{$d['keterangan']}</td>
          </tr>";
              $no++;
  }
  break;

case 'Peminjaman':
  $query = mysqli_query($conn, "SELECT * FROM peminjaman " . tanggalFilter('created_at'));
  while ($d = mysqli_fetch_assoc($query)) {
    echo "<tr>
    <td>{$no}</td>
            <td>{$d['id_peminjaman']}</td>
            <td>{$d['id_barang']}</td>
            <td>{$d['id_users']}</td>
            <td>{$d['kategori']}</td>
            <td>{$d['jumlah']}</td>
            <td>{$d['satuan']}</td>
            <td>" . formatTanggal($d['tanggal_pinjam']) . "</td>
            <td>" . formatTanggal($d['tanggal_kembali']) . "</td>
            <td>{$d['status_peminjaman']}</td>
            <td>{$d['keterangan']}</td>
          </tr>";
              $no++;
  }
  break;

case 'Pengembalian':
  $query = mysqli_query($conn, "SELECT * FROM pengembalian " . tanggalFilter('tanggal_kembali'));
  while ($d = mysqli_fetch_assoc($query)) {
    echo "<tr>
    <td>{$no}</td>
            <td>{$d['id_pengembalian']}</td>
            <td>{$d['id_peminjaman']}</td>
            <td>{$d['id_barang']}</td>
            <td>{$d['id_users']}</td>
            <td>" . formatTanggal($d['tanggal_pinjam']) . "</td>
            <td>" . formatTanggal($d['tanggal_kembali']) . "</td>
            <td>Rp. " . number_format($d['denda'], 0, ',', '.') . "</td>
            <td>{$d['status']}</td>
            <td>{$d['keterangan']}</td>
          </tr>";
              $no++;
  }
  break;

  }
?>
</tbody>

      </table>
    </div>
  </div>
</section>

<!-- DataTables & Export CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons Extension -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Export Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
  $(document).ready(function () {
    var table = $('#tabelLaporan').DataTable({
      responsive: true,
      pageLength: 10,
      dom: 'lBfrtip',
      buttons: [
        { extend: 'pdf', className: 'd-none', title: 'Data Laporan' },
        { extend: 'excel', className: 'd-none', title: 'Data Laporan' },
        { extend: 'csv', className: 'd-none', title: 'Data Laporan' },
        { extend: 'print', className: 'd-none', title: 'Data Laporan' }
      ]
    });

    $('#btn-pdf').click(() => table.button(0).trigger());
    $('#btn-excel').click(() => table.button(1).trigger());
    $('#btn-csv').click(() => table.button(2).trigger());
    $('#btn-print').click(() => table.button(3).trigger());
  });
</script>
