<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<!-- Section Tabel Pemasok -->
<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-truck"></i> Data Pemasok</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Data Pemasok</li>
      </ul>
    </div>

    <div class="mb-4 d-flex align-items-center gap-2 flex-wrap">
      <a href="pemasok_tambah.php" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Pemasok
      </a>

      <button id="hapusSemua" class="btn btn-danger">
        <i class="fas fa-trash-alt"></i> Hapus Semua
      </button>
    </div>

    <!-- Tabel Pemasok -->
    <div class="table-responsive">
      <table id="tabelPemasok" class="table table-bordered table-striped" width="100%">
        <thead style="background-color: #cccbcb;">
          <tr class="text-center align-middle text-nowrap">
            <th><input type="checkbox" id="checkAll"></th>
            <th>No</th>
            <th>ID Pemasok</th>
            <th>Nama Pemasok</th>
            <th>Alamat</th>
            <th>Kota</th>
            <th>Telepon</th>
            <th>Email</th>
            <th>Keterangan</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $query = mysqli_query($conn, "SELECT * FROM pemasok");
          while ($data = mysqli_fetch_assoc($query)) {
          ?>
            <tr>
              <td class="text-center"><input type="checkbox" class="checkItem" value="<?= $data['id_pemasok']; ?>"></td>
              <td><?= $no++; ?></td>
              <td><?= $data['id_pemasok']; ?></td>
              <td><?= $data['nama_pemasok']; ?></td>
              <td><?= $data['alamat']; ?></td>
              <td><?= $data['kota']; ?></td>
              <td><?= $data['telepon']; ?></td>
              <td><?= $data['email']; ?></td>
              <td><?= $data['keterangan']; ?></td>
              <td><?= date('Y-m-d', strtotime($data['created_at'])); ?></td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <a href="pemasok_view.php?id_pemasok=<?= $data['id_pemasok']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                  <a href="pemasok_edit.php?id_pemasok=<?= $data['id_pemasok']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                  <a href="pemasok_hapus.php?id_pemasok=<?= $data['id_pemasok']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus?')"><i class="fas fa-trash-alt"></i></a>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- DataTables & Export -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- CSS Tambahan -->
<style>
  .btn {
    margin-right: 8px;
  }

  .btn:last-child {
    margin-right: 0;
  }
</style>

<!-- Script: DataTable & Tools -->
<script>
  $(document).ready(function () {
    var table = $('#tabelPemasok').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 20, 25, 50, 100],
      dom: 'lBfrtip',
      buttons: [
        { extend: 'csv', className: 'd-none', title: 'Data Pemasok' },
        { extend: 'excel', className: 'd-none', title: 'Data Pemasok' },
        { extend: 'pdf', className: 'd-none', title: 'Data Pemasok' },
        { extend: 'print', className: 'd-none', title: 'Data Pemasok' }
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

    $('#checkAll').on('click', function () {
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
            hapus_pemasok: true
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
