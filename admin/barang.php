<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<!-- Section Tabel Barang -->
<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-boxes"></i> Data Barang</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Data Barang</li>
      </ul>
    </div>

    <div class="mb-4">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="barang_tambah.php" class="btn btn-success me-2">
          <i class="fas fa-plus"></i> Tambah Data
        </a>

        <button id="hapusSemua" class="btn btn-danger me-2">
          <i class="fas fa-trash-alt"></i> Hapus Semua
        </button>

      </div>
    </div>

    <!-- Tabel scroll container -->
    <div class="table-responsive">
<table id="tabelBarang" class="table table-bordered table-striped" width="100%">
  <thead style="background-color: #cccbcbff;">
    <tr class="text-nowrap text-center align-middle">
      <th><input type="checkbox" id="checkAll"></th>
      <th>No</th>
      <th>ID Barang</th>
      <th>Foto</th>
      <th>Nama Barang</th>
      <th>Merk</th>
      <th>Spesifikasi</th>
      <th>Harga Beli</th>
      <th>Jumlah</th>
      <th>Satuan</th>
      <th>Kondisi Baik</th>
      <th>Kondisi Rusak</th>
      <th>Kondisi Hilang</th>
      <th>Kategori</th>
      <th>Penyimpanan</th>
      <th>Nama Pemasok</th>
      <th>Tanggal Beli</th>
      <th>Tanggal Update</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = 1;

    // ✅ Gabungkan dua LEFT JOIN sekaligus
    $query = mysqli_query($conn, "SELECT b.*, ps.nama_pemasok, py.nama_lokasi 
                                  FROM barang b
                                  LEFT JOIN pemasok ps ON b.id_pemasok = ps.id_pemasok
                                  LEFT JOIN penyimpanan py ON b.id_penyimpanan = py.id_penyimpanan");

    while ($data = mysqli_fetch_assoc($query)) {
      echo "<tr>";
      echo "<td><input type='checkbox' class='checkItem' value='{$data['id_barang']}'></td>";
      echo "<td>{$no}</td>";
      echo "<td>{$data['id_barang']}</td>";

      $fotoFile = $data['foto'];
      $fotoPath = (!empty($fotoFile) && file_exists("../public/uploads/$fotoFile")) 
          ? "../public/uploads/$fotoFile" 
          : "../public/uploads/default.png";
      echo "<td><img src='$fotoPath' width='50' class='img-thumbnail'></td>";

      echo "<td>{$data['nama_barang']}</td>";
      echo "<td>{$data['merk']}</td>";
      echo "<td>{$data['spesifikasi']}</td>";
      echo "<td>Rp " . number_format($data['harga_beli'], 0, ',', '.') . "</td>";
      echo "<td>{$data['jumlah']}</td>";
      echo "<td>{$data['satuan']}</td>";
      echo "<td>{$data['kondisi_baik']}</td>";
      echo "<td>{$data['kondisi_rusak']}</td>";
      echo "<td>{$data['kondisi_hilang']}</td>";
      echo "<td>{$data['kategori']}</td>";
      echo "<td>{$data['nama_lokasi']}</td>";
      echo "<td>{$data['nama_pemasok']}</td>";
      echo "<td>" . date('Y-m-d', strtotime($data['tanggal_beli'])) . "</td>";
      echo "<td>" . date('Y-m-d', strtotime($data['tanggal_update'])) . "</td>";

      echo "<td>
              <div class='d-flex justify-content-center gap-2'>
                <a href='barang_view.php?id_barang={$data['id_barang']}' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i></a>
                <a href='barang_edit.php?id_barang={$data['id_barang']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                <a href='barang_hapus.php?id_barang={$data['id_barang']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin hapus?\")'><i class='fas fa-trash-alt'></i></a>
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

<!-- DataTables & jQuery CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- css -->
<style>
  .btn {
    margin-right: 8px;
  }
  .btn:last-child {
    margin-right: 0;
  }
</style>

<!-- Tambahan untuk Export -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
  $(document).ready(function () {
    var table = $('#tabelBarang').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 20, 25, 50, 100],
      dom: 'lBfrtip',
      buttons: [
        { extend: 'csv', className: 'd-none', title: 'Data Barang' },
        { extend: 'excel', className: 'd-none', title: 'Data Barang' },
        { extend: 'pdf', className: 'd-none', title: 'Data Barang' },
        { extend: 'print', className: 'd-none', title: 'Data Barang' }
      ]
    });

    // Export buttons handler
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

    // Check/Uncheck All
    $('#checkAll').click(function () {
      $('.checkItem').prop('checked', this.checked);
    });

    // Hapus Semua (barang)
    $('#hapusSemua').click(function () {
      var checked = $('.checkItem:checked');
      if (checked.length > 0) {
        if (confirm("Yakin ingin menghapus semua data barang yang dipilih?")) {
          var ids = [];
          checked.each(function () {
            ids.push($(this).val());
          });

          $.ajax({
            type: "POST",
            url: "hapus_semua.php",
            data: {
              id: ids,
              hapus_barang: true // Tambahkan agar cocok jika validasi PHP nanti ingin lebih ketat
            },
            dataType: "json",
            success: function (response) {
              if (response.status === 'success') {
                alert(response.message);
                location.reload();
              } else {
                alert(response.message);
              }
            },
            error: function () {
              alert("Terjadi kesalahan saat menghapus data.");
            }
          });
        }
      } else {
        alert("Pilih data yang ingin dihapus terlebih dahulu.");
      }
    });
  });
</script>
