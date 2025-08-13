<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-box-open"></i> Riwayat Peminjaman</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="riwayat_pengembalian.php">Riwayat Pengembalian</li></a>
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
            <th>Status</th>
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
              LEFT JOIN pengembalian pg ON pj.id_peminjaman = pg.id_peminjaman
              JOIN users u ON pj.id_users = u.id_users
              JOIN barang b ON pj.id_barang = b.id_barang
              WHERE 
                (pj.status_peminjaman = 'Disetujui' AND pg.status = 'Terverifikasi')
                OR pj.status_peminjaman = 'Ditolak'
              ORDER BY pj.tanggal_pinjam DESC
          ");
          while ($d = mysqli_fetch_assoc($q2)) {
            $statusBtn = '';
            if ($d['status_peminjaman'] === 'Disetujui') {
                $statusBtn = "<span class='btn btn-success btn-sm'>Disetujui</span>";
            } elseif ($d['status_peminjaman'] === 'Ditolak') {
                $statusBtn = "<span class='btn btn-danger btn-sm'>Ditolak</span>";
            } else {
                $statusBtn = "<span class='btn btn-secondary btn-sm'>{$d['status_peminjaman']}</span>";
            }
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
              <td>{$statusBtn}</td>
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

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


<script>
$(document).ready(function(){
  $('#tabelPeminjaman').DataTable({
    responsive: true,
    dom: 'lBfrtip',
    buttons: [
      { extend: 'csv', className: 'd-none' },
      { extend: 'excel', className: 'd-none' },
      { extend: 'pdf', className: 'd-none' },
      { extend: 'print', className: 'd-none' }
    ]
  });
});
</script>
