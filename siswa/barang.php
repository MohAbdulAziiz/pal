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

    <!-- Tabel scroll container -->
    <div class="table-responsive">
<table id="tabelBarang" class="table table-bordered table-striped" width="100%">
  <thead style="background-color: #cccbcbff;">
    <tr class="text-nowrap text-center align-middle">
      <th>No</th>
      <th>ID Barang</th>
      <th>Foto</th>
      <th>Nama Barang</th>
      <th>Kategori</th>
      <th>Merk</th>
      <th>Spesifikasi</th>
      <th>Harga Beli</th>
      <th>Jumlah</th>
      <th>Satuan</th>
      <th>Penyimpanan</th>
      <th>Tanggal Beli</th>
      <th>Tanggal Update</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = 1;

    $query = mysqli_query($conn, "SELECT b.*, ps.nama_pemasok, py.nama_lokasi 
                                  FROM barang b
                                  LEFT JOIN pemasok ps ON b.id_pemasok = ps.id_pemasok
                                  LEFT JOIN penyimpanan py ON b.id_penyimpanan = py.id_penyimpanan");

    while ($data = mysqli_fetch_assoc($query)) {
      echo "<tr>";
      echo "<td>{$no}</td>";
      echo "<td>{$data['id_barang']}</td>";
      $fotoFile = $data['foto'];
      $fotoPath = (!empty($fotoFile) && file_exists("../public/uploads/$fotoFile")) 
          ? "../public/uploads/$fotoFile" 
          : "../public/uploads/default.png";
      echo "<td><img src='$fotoPath' width='50' class='img-thumbnail'></td>";

      echo "<td>{$data['nama_barang']}</td>";
      echo "<td>{$data['kategori']}</td>";
      echo "<td>{$data['merk']}</td>";
      echo "<td>{$data['spesifikasi']}</td>";
      echo "<td>Rp " . number_format($data['harga_beli'], 0, ',', '.') . "</td>";
      echo "<td>{$data['jumlah']}</td>";
      echo "<td>{$data['satuan']}</td>";
      echo "<td>{$data['nama_lokasi']}</td>";
      echo "<td>" . date('Y-m-d', strtotime($data['tanggal_beli'])) . "</td>";
      echo "<td>" . date('Y-m-d', strtotime($data['tanggal_update'])) . "</td>";

      echo "<td>
              <div class='d-flex justify-content-center gap-2'>
                <a href='barang_view.php?id_barang={$data['id_barang']}' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i></a>
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
    });

  });
</script>
