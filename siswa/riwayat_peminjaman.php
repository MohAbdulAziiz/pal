<?php 
include 'header.php'; 
include '../koneksi.php';

$id_user_login = $_SESSION['id_users']; // pastikan session 'id_users' sudah diset saat login
?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-box-open"></i> Riwayat Peminjaman</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="riwayat_pengembalian.php">Riwayat Pengembalian</a></li>
        <li class="breadcrumb-item active">Riwayat Peminjaman</li>
      </ul>
    </div>

    <div class="table-responsive">
      <table id="tabelPeminjaman" class="table table-bordered table-striped" width="100%">
        <thead style="background-color: #ccc; white-space: nowrap;">
          <tr class="text-center">
            <th>No</th>
            <th>ID Pinjam</th>
            <th>ID Barang</th>
            <th>ID User</th>
            <th>User</th>
            <th>Barang</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $q2 = mysqli_query($conn, "
            SELECT 
              pj.id_peminjaman, pj.id_barang, pj.id_users, u.nama AS nama_user, 
              b.nama_barang, b.kategori, pj.jumlah, b.satuan, 
              pj.tanggal_pinjam, pj.tanggal_kembali, pj.status_peminjaman, pj.keterangan
            FROM peminjaman pj
            INNER JOIN pengembalian pg ON pj.id_peminjaman = pg.id_peminjaman
            JOIN users u ON pj.id_users = u.id_users
            JOIN barang b ON pj.id_barang = b.id_barang
            WHERE pj.status_peminjaman = 'Disetujui'
              AND pg.status = 'Terverifikasi'
              AND pj.id_users = '$id_user_login'
            ORDER BY pj.tanggal_pinjam DESC
          ");
          while ($d = mysqli_fetch_assoc($q2)) {
            echo "<tr>
              <td>{$no}</td>
              <td>{$d['id_peminjaman']}</td>
              <td>{$d['id_barang']}</td>
              <td>{$d['id_users']}</td>
              <td>{$d['nama_user']}</td>
              <td>{$d['nama_barang']}</td>
              <td>{$d['kategori']}</td>
              <td>{$d['jumlah']}</td>
              <td>{$d['satuan']}</td>
              <td>" . date('Y-m-d', strtotime($d['tanggal_pinjam'])) . "</td>
              <td>" . date('Y-m-d', strtotime($d['tanggal_kembali'])) . "</td>
              <td>{$d['keterangan']}</td>
            </tr>";
            $no++;
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- DataTables Styles & Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function(){
  $('#tabelPeminjaman').DataTable({
    responsive: true
  });
});
</script>
