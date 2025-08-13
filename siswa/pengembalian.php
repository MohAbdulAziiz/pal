<?php
session_start();
include 'header.php';
include '../koneksi.php';

// Cek apakah user sudah login dan punya session yang valid
if (!isset($_SESSION['id_users']) || empty($_SESSION['id_users'])) {
    header("Location: ../login.php");
    exit;
}

$id_users_login = $_SESSION['id_users'];
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-undo-alt"></i> Data Pengembalian Barang</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Pengembalian Barang</li>
      </ul>
    </div>

    <div class="mb-4">
      <a href="pengembalian_tambah.php" class="btn btn-success">
        <i class="fas fa-plus"></i> Form Pengembalian Barang
      </a>
    </div>

    <div class="table-responsive">
      <table id="tabelPengembalian" class="table table-bordered table-striped">
        <thead class="text-center text-nowrap align-middle" style="background-color: #cccbcbff;">
          <tr>
            <th>No</th>
            <th>ID Pengembalian</th>
            <th>ID Peminjaman</th>
            <th>Nama Peminjam</th>
            <th>Barang</th>
            <th>Foto</th>
            <th>Tanggal Pinjam</th>
            <th>Tgl Kembali (Peminjaman)</th>
            <th>Tgl Kembali (Pengembalian)</th>
            <th>Denda</th>
            <th>Status</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $query = mysqli_query($conn, "
            SELECT 
              pg.id_pengembalian,
              pg.foto,
              pg.tanggal_kembali AS tgl_kembali_pengembalian,
              pg.status,
              pg.keterangan,
              pg.denda,
              pj.id_peminjaman,
              pj.tanggal_pinjam,
              pj.tanggal_kembali AS tgl_kembali_peminjaman,
              b.nama_barang,
              u.nama AS nama_user
            FROM pengembalian pg
            LEFT JOIN peminjaman pj ON pg.id_peminjaman = pj.id_peminjaman
            LEFT JOIN barang b ON pj.id_barang = b.id_barang
            LEFT JOIN users u ON pj.id_users = u.id_users
            WHERE 
              pj.id_users = '$id_users_login' AND (
                pg.status != 'Terverifikasi'
                OR (
                  pg.status = 'Terverifikasi' 
                  AND TIMESTAMPDIFF(MINUTE, pg.tanggal_kembali, NOW()) <= 30
                )
              )
            ORDER BY pg.id_pengembalian DESC
          ");

          while ($data = mysqli_fetch_assoc($query)) {
              $foto_path = !empty($data['foto']) ? "../public/uploads/{$data['foto']}" : "#";

              $tanggal_pinjam = !empty($data['tanggal_pinjam']) ? date("d-m-Y", strtotime($data['tanggal_pinjam'])) : '-';
              $tgl_kembali_peminjaman = !empty($data['tgl_kembali_peminjaman']) ? date("d-m-Y", strtotime($data['tgl_kembali_peminjaman'])) : '-';
              $tgl_kembali_pengembalian = !empty($data['tgl_kembali_pengembalian']) ? date("d-m-Y", strtotime($data['tgl_kembali_pengembalian'])) : '-';

              $status_class = $data['status'] === 'Terverifikasi' ? 'btn btn-success btn-sm' : 'btn btn-warning btn-sm text-dark';
              $status_options = "<span class='{$status_class}'>{$data['status']}</span>";

              $keteranganClass = match ($data['keterangan']) {
                  'Tepat Waktu' => 'btn-success',
                  'Terlambat' => 'btn-danger',
                  default => 'btn-secondary',
              };

              echo "<tr>
                  <td class='text-center'>{$no}</td>
                  <td>{$data['id_pengembalian']}</td>
                  <td>{$data['id_peminjaman']}</td>
                  <td>{$data['nama_user']}</td>
                  <td>{$data['nama_barang']}</td>
                  <td><img src='{$foto_path}' width='60' class='img-thumbnail'></td>
                  <td>{$tanggal_pinjam}</td>
                  <td>{$tgl_kembali_peminjaman}</td>
                  <td>{$tgl_kembali_pengembalian}</td>
                  <td>Rp. " . number_format($data['denda'], 0, ',', '.') . "</td>
                  <td>{$status_options}</td>
                  <td><span class='btn btn-sm {$keteranganClass}'>{$data['keterangan']}</span></td>
                  <td>
                    <a href='pengembalian_view.php?id={$data['id_pengembalian']}' class='btn btn-primary btn-sm'>
                      <i class='fas fa-eye'></i>
                    </a>
                  </td>
                </tr>";
              $no++;
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Script & Styles -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<script>
  $(document).ready(function () {
    const status = new URLSearchParams(window.location.search).get('status');
    if (status === 'sukses') alert('Status berhasil diperbarui!');
    else if (status === 'gagal') alert('Gagal memperbarui status!');
    else if (status === 'invalid') alert('Status tidak valid!');

    $('#tabelPengembalian').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      order: [[0, 'asc']]
    });
  });
</script>

<style>
select.form-select.bg-success,
select.form-select.bg-warning {
  appearance: none;
  padding: 0.375rem 1.25rem;
  border-radius: 0.25rem;
  background-image: none;
}
</style>
