<?php include 'header.php'; ?>
<?php include '../koneksi.php'; ?>

<!-- Section Tabel Users -->
<section class="dashboard-section py-4" style="background-color: #ffffff;">
  <div class="container-fluid">
    <div class="app-title">
      <div>
        <h1><i class="fas fa-users"></i> Data Users</h1>
        <p>System Inventory | SMK Ma'arif Terpadu Cicalengka</p>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Data Users</li>
      </ul>
    </div>

    <div class="mb-4">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="users_tambah.php" class="btn btn-success me-2">
          <i class="fas fa-plus"></i> Tambah User
        </a>

        <button id="hapusSemua" class="btn btn-danger me-2">
          <i class="fas fa-trash-alt"></i> Hapus Semua
        </button>

      </div>
    </div>

    <div class="table-responsive">
      <table id="tabelUsers" class="table table-bordered table-striped" width="100%">
        <thead style="background-color: #cccbcbff;">
          <tr class="text-nowrap text-center align-middle">
            <th><input type="checkbox" id="checkAll"></th>
            <th>No</th>
            <th>Id Users</th>
            <th>Foto</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Alamat</th>
            <th>Jabatan</th>
            <th>NIP</th>
            <th>Role</th>
            <th>Verifikasi</th>
            <th>Tanggal Daftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $query = mysqli_query($conn, "SELECT * FROM users");
            while ($data = mysqli_fetch_assoc($query)) {
            echo "<tr>";
            echo "<td><input type='checkbox' class='checkItem' value='{$data['id_users']}'></td>";
            echo "<td>{$no}</td>";
            echo "<td>{$data['id_users']}</td>";

            // Foto
              $fotoFile = $data['foto'];
              $fotoPath = (!empty($fotoFile) && file_exists("../public/uploads/$fotoFile")) 
                  ? "../public/uploads/$fotoFile" 
                  : "../public/uploads/default.png";
              echo "<td><img src='$fotoPath' width='50' class='img-thumbnail'></td>";

            echo "<td>{$data['username']}</td>";
            echo "<td>{$data['nama']}</td>";
            echo "<td>{$data['jenis_kelamin']}</td>";
            echo "<td>{$data['email']}</td>";
            echo "<td>{$data['no_hp']}</td>";
            echo "<td>{$data['alamat']}</td>";
            echo "<td>{$data['jabatan']}</td>";
            echo "<td>{$data['nip']}</td>";
            echo "<td>{$data['role']}</td>";

            // Verifikasi dengan badge berwarna
            $verifikasiBadge = 'btn-secondary';
            if (strtolower($data['verifikasi']) === 'terverifikasi') {
                $verifikasiBadge = 'btn-success';
            } elseif (strtolower($data['verifikasi']) === 'belum terverifikasi') {
                $verifikasiBadge = 'btn-danger';
            }
            echo "<td><span class='btn btn-sm {$verifikasiBadge}' style='width: 120px; pointer-events: none;'>{$data['verifikasi']}</span></td>";

            echo "<td>" . date('Y-m-d', strtotime($data['created_at'])) . "</td>";

            echo "<td>
                    <div class='d-flex justify-content-center gap-2'>
                        <a href='users_view.php?id_users={$data['id_users']}' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i></a>
                        <a href='users_edit.php?id_users={$data['id_users']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                        <a href='users_hapus.php?id_users={$data['id_users']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin hapus?\")'><i class='fas fa-trash-alt'></i></a>
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

<!-- CSS -->
<style>
  .btn {
    margin-right: 8px;
  }
  .btn:last-child {
    margin-right: 0;
  }
</style>

<!-- Export Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Script DataTables dan Aksi -->
<script>
  $(document).ready(function () {
    var table = $('#tabelUsers').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 20, 25, 50, 100],
      dom: 'lBfrtip',
      buttons: [
        { extend: 'csv', className: 'd-none', title: 'Data Users' },
        { extend: 'excel', className: 'd-none', title: 'Data Users' },
        { extend: 'pdf', className: 'd-none', title: 'Data Users' },
        { extend: 'print', className: 'd-none', title: 'Data Users' }
      ]
    });

    $('#btn-csv').click(e => { e.preventDefault(); table.button(0).trigger(); });
    $('#btn-excel').click(e => { e.preventDefault(); table.button(1).trigger(); });
    $('#btn-pdf').click(e => { e.preventDefault(); table.button(2).trigger(); });
    $('#btn-print').click(e => { e.preventDefault(); table.button(3).trigger(); });

    $('#checkAll').click(function () {
      $('.checkItem').prop('checked', this.checked);
    });

    $('#hapusSemua').click(function () {
    var checked = $('.checkItem:checked');
    if (checked.length > 0) {
        if (confirm("Yakin ingin menghapus semua data penyimpanan yang dipilih?")) {
        var ids = [];
        checked.each(function () {
            ids.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: "hapus_semua.php",
            data: {
            id: ids,
            hapus_users: true
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