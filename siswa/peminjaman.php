<?php
include 'header.php';
include '../koneksi.php';
// Pastikan user sudah login
if (!isset($_SESSION['id_users'])) {
    header("Location: ../login.php");
    exit;
}

$id_users_login = $_SESSION['id_users'];
?>

<!-- Section Peminjaman Barang -->
<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-hand-holding"></i> Data Peminjaman Barang</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Data Peminjaman</li>
      </ul>
    </div>

    <div class="mb-4">
      <a href="peminjaman_tambah.php" class="btn btn-success me-2">
        <i class="fas fa-plus"></i> Tambah Peminjaman
      </a>
    </div>

    <!-- Tabel -->
    <div class="table-responsive">
      <table id="tabelPeminjaman" class="table table-bordered table-striped" width="100%">
        <thead style="background-color: #cccbcbff;">
          <tr class="text-nowrap text-center align-middle">
            <th>No</th>
            <th>Id Peminjaman</th>
            <th>Nama Peminjam</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Kategori</th>
            <th>Status Peminjaman</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($conn, "
          SELECT pj.*, b.nama_barang, b.kategori, u.nama AS nama_user 
          FROM peminjaman pj 
          LEFT JOIN barang b ON pj.id_barang = b.id_barang 
          LEFT JOIN users u ON pj.id_users = u.id_users 
          WHERE pj.id_users = '$id_users_login'
            AND NOT EXISTS (
              SELECT 1 FROM pengembalian pg
              WHERE pg.id_peminjaman = pj.id_peminjaman
                AND pg.status = 'Terverifikasi'
            )
          ORDER BY pj.tanggal_pinjam DESC
        ");

        while ($data = mysqli_fetch_assoc($query)) {
          echo "<tr>";
          echo "<td>{$no}</td>";
          echo "<td>{$data['id_peminjaman']}</td>";
          echo "<td>{$data['nama_user']}</td>";
          echo "<td>{$data['nama_barang']}</td>";
          echo "<td>{$data['jumlah']}</td>";
          echo "<td>{$data['satuan']}</td>";
          echo "<td>{$data['kategori']}</td>";

          $statusClass = '';
          switch ($data['status_peminjaman']) {
            case 'Disetujui': $statusClass = 'btn-success'; break;
            case 'Menunggu': $statusClass = 'btn-warning'; break;
            case 'Ditolak': $statusClass = 'btn-danger'; break;
            case 'Dikembalikan': $statusClass = 'btn-primary'; break;
          }

          echo "<td><span class='btn btn-sm {$statusClass}' style='width: 100px; pointer-events: none;'>{$data['status_peminjaman']}</span></td>";
          echo "<td>" . date('d-m-Y', strtotime($data['tanggal_pinjam'])) . "</td>";
          echo "<td>" . date('d-m-Y', strtotime($data['tanggal_kembali'])) . "</td>";

          echo "<td class='text-center'>
                  <a href='peminjaman_view.php?id_peminjaman={$data['id_peminjaman']}' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i></a>
                </td>";
          echo "</tr>";
          $no++;
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Tambahan Export -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Inisialisasi DataTables -->
<script>
  $(document).ready(function () {
    $('#tabelPeminjaman').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 20, 50, 100],
    });
  });
</script>

<style>
  .btn {
    margin-right: 8px;
  }
  .btn:last-child {
    margin-right: 0;
  }
</style>
