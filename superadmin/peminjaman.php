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
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="peminjaman_tambah.php" class="btn btn-success me-2">
          <i class="fas fa-plus"></i> Tambah Peminjaman
        </a>

        <button id="hapusSemua" class="btn btn-danger me-2">
          <i class="fas fa-trash-alt"></i> Hapus Semua
        </button>
      </div>
    </div>

    <!-- Tabel -->
    <div class="table-responsive">
    <table id="tabelPeminjaman" class="table table-bordered table-striped" width="100%">
      <thead style="background-color: #cccbcbff;">
        <tr class="text-nowrap text-center align-middle">
          <th><input type="checkbox" id="checkAll"></th>
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
  WHERE NOT EXISTS (
    SELECT 1 FROM pengembalian pg
    WHERE pg.id_peminjaman = pj.id_peminjaman
      AND pg.status = 'Terverifikasi'
  )
  AND NOT (
    pj.status_peminjaman = 'Ditolak'
    AND TIMESTAMPDIFF(MINUTE, pj.created_at, NOW()) > 30
  )
  ORDER BY pj.tanggal_pinjam DESC
");

while ($data = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    echo "<td><input type='checkbox' class='checkItem' value='{$data['id_peminjaman']}'></td>";
    echo "<td>{$no}</td>";
    echo "<td>{$data['id_peminjaman']}</td>";
    echo "<td>{$data['nama_user']}</td>";
    echo "<td>{$data['nama_barang']}</td>";
    echo "<td>{$data['jumlah']}</td>";
    echo "<td>{$data['satuan']}</td>";
    echo "<td>{$data['kategori']}</td>";

    $statusClass = '';
    if ($data['status_peminjaman'] == 'Disetujui') $statusClass = 'btn-success';
    elseif ($data['status_peminjaman'] == 'Menunggu') $statusClass = 'btn-warning';
    elseif ($data['status_peminjaman'] == 'Ditolak') $statusClass = 'btn-danger';
    elseif ($data['status_peminjaman'] == 'Dikembalikan') $statusClass = 'btn-primary';

    echo "<td><span class='btn btn-sm {$statusClass}' style='width: 100px; pointer-events: none;'>{$data['status_peminjaman']}</span></td>";
    echo "<td>" . date('d-m-Y', strtotime($data['tanggal_pinjam'])) . "</td>";
    echo "<td>" . date('d-m-Y', strtotime($data['tanggal_kembali'])) . "</td>";

    echo "<td>
            <div class='d-flex justify-content-center gap-2'>
              <a href='peminjaman_view.php?id_peminjaman={$data['id_peminjaman']}' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i></a>
              <a href='peminjaman_edit.php?id_peminjaman={$data['id_peminjaman']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
              <a href='peminjaman_hapus.php?id_peminjaman={$data['id_peminjaman']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin hapus peminjaman ini?\")'><i class='fas fa-trash-alt'></i></a>
            </div>
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

<!-- DataTables & Script sama seperti sebelumnya -->

<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
  .btn {
    margin-right: 8px;
  }
  .btn:last-child {
    margin-right: 0;
  }
</style>

<!-- Tambahan Export & Script -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
  $(document).ready(function () {
    var table = $('#tabelPeminjaman').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 20, 25, 50, 100],
      dom: 'lBfrtip',
      buttons: [
        { extend: 'csv', className: 'd-none', title: 'Data Peminjaman' },
        { extend: 'excel', className: 'd-none', title: 'Data Peminjaman' },
        { extend: 'pdf', className: 'd-none', title: 'Data Peminjaman' },
        { extend: 'print', className: 'd-none', title: 'Data Peminjaman' }
      ]
    });

    $('#btn-csv').click(function (e) {
      e.preventDefault();
      table.button(0).trigger();
    });
    $('#btn-excel').click(function (e) {
      e.preventDefault();
      table.button(1).trigger();
    });
    $('#btn-pdf').click(function (e) {
      e.preventDefault();
      table.button(2).trigger();
    });
    $('#btn-print').click(function (e) {
      e.preventDefault();
      table.button(3).trigger();
    });

    $('#checkAll').click(function () {
      $('.checkItem').prop('checked', this.checked);
    });

 $('#hapusSemua').on('click', function () {
      var checked = $('.checkItem:checked');
      if (checked.length === 0) {
        alert("Pilih data yang ingin dihapus terlebih dahulu.");
        return;
      }

      if (confirm("Yakin ingin menghapus semua data pemasok yang dipilih?")) {
        var ids = checked.map(function () {
          return $(this).val();
        }).get();

        $.ajax({
          type: "POST",
          url: "hapus_semua.php",
          data: {
            id: ids,
            hapus_peminjaman: true
          },
          dataType: "json",
          success: function (response) {
            alert(response.message);
            if (response.status === 'success') {
              location.reload();
            }
          },
          error: function () {
            alert("Terjadi kesalahan saat menghapus data.");
          }
        });
      }
    });
  });
</script>
