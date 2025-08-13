<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-undo-alt"></i> Riwayat Pengembalian</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="riwayat_peminjaman.php">Riwayat Peminjaman</li></a>
        <li class="breadcrumb-item active">Riwayat Pengembalian</li>
      </ul>
    </div>

    <div class="table-responsive">
      <table id="tabelPengembalian" class="table table-bordered table-striped">
        <thead style="background-color: #ccc; white-space: nowrap;">
          <tr class="text-center">
            <th>No</th>
            <th>ID Pengembalian</th>
            <th>ID Peminjaman</th>
            <th>Foto</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Denda</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $q3 = mysqli_query($conn, "
            SELECT pg.id_pengembalian, pg.id_peminjaman, pg.foto,
                   pj.tanggal_pinjam, pg.tanggal_kembali, pg.denda,
                   pg.keterangan, pg.status
            FROM pengembalian pg
            JOIN peminjaman pj ON pg.id_peminjaman = pj.id_peminjaman
            WHERE pg.status = 'Terverifikasi'
            ORDER BY pg.tanggal_kembali DESC
          ");
          while ($d = mysqli_fetch_assoc($q3)) {
            $foto = (!empty($d['foto']) && file_exists("../public/uploads/{$d['foto']}"))
              ? "../public/uploads/{$d['foto']}"
              : "../public/uploads/default.png";

            $status = $d['status'] === 'Terverifikasi'
              ? "<span class='btn btn-success btn-sm'>Terverifikasi</span>"
              : "<span class='btn btn-warning btn-sm text-dark'>Belum Verifikasi</span>";

            echo "<tr>
              <td>{$no}</td>
              <td>{$d['id_pengembalian']}</td>
              <td>{$d['id_peminjaman']}</td>
              <td><img src='{$foto}' width='50' class='img-thumbnail'></td>
              <td>" . date('d-m-Y', strtotime($d['tanggal_pinjam'])) . "</td>
              <td>" . date('d-m-Y', strtotime($d['tanggal_kembali'])) . "</td>
              <td>Rp. " . number_format($d['denda'], 0, ',', '.') . "</td>
              <td>{$d['status']}</td>
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
  $('#tabelPengembalian').DataTable({
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
