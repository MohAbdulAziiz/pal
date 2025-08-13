<?php
include '../koneksi.php';

require_once '../vendor/autoload.php'; // pastikan path ini benar
use Dompdf\Dompdf;

$format = $_GET['format'] ?? 'print';

// Query data
$query = mysqli_query($conn, "SELECT * FROM users");

// Daftar kolom
$columns = [
  'NO', 'ID', 'Username', 'Email', 'No HP', 'Nama', 'Jenis Kelamin',
  'Alamat', 'Jabatan', 'NIP', 'Role', 'Foto', 'Verifikasi', 'Created At'
];

// Export ke CSV
if ($format == 'csv') {
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=users.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, $columns);

    $no = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        fputcsv($output, [
            $no++,
            $row['id_users'],
            $row['username'],
            $row['email'],
            $row['no_hp'],
            $row['nama'],
            $row['jenis_kelamin'],
            $row['alamat'],
            $row['jabatan'],
            $row['nip'],
            $row['role'],
            $row['foto'],
            $row['verifikasi'],
            $row['created_at']
        ]);
    }

    fclose($output);
    exit;
}

// Export ke Excel
elseif ($format == 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=users.xls");
}

// Export ke PDF
elseif ($format == 'pdf') {
    ob_start();
    ?>

    <style>
      table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
        font-size: 10pt;
        font-family: "Times New Roman", Times, serif;
      }
      th, td {
        border: 1px solid #000;
        padding: 4px;
        text-align: left;
        vertical-align: top;
      }
      th {
        background-color: #f2f2f2;
      }
      img {
        max-width: 60px;
        height: auto;
      }
      h2 {
        text-align: center;
        margin-bottom: 20px;
        font-family: "Times New Roman", Times, serif;
      }
    </style>
    <h2>Data Users</h2>
    <table>
      <thead>
        <tr>
          <?php foreach ($columns as $col): ?>
            <th><?= htmlspecialchars($col) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        mysqli_data_seek($query, 0);
        while ($row = mysqli_fetch_assoc($query)) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['id_users']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['no_hp']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['jenis_kelamin']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['jabatan']}</td>
                    <td>{$row['nip']}</td>
                    <td>{$row['role']}</td>
                    <td><img src='../public/uploads/{$row['foto']}'></td>
                    <td>{$row['verifikasi']}</td>
                    <td>{$row['created_at']}</td>
                  </tr>";
            $no++;
        }
        ?>
      </tbody>
    </table>

    <?php
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // Landscape A4
    $dompdf->setPaper('A4', 'landscape');

    $dompdf->render();
    $dompdf->stream("users.pdf", ["Attachment" => true]); // Attachment true = download
    exit;
}
?>

<!-- HTML hanya untuk tampilan print -->
<?php if ($format == 'print'): ?>
<!DOCTYPE html>
<html>
<head>
  <title>Print Users</title>
  <style>
    @page { size: A4 landscape; margin: 20mm; }
    body { font-family: "Times New Roman"; font-size: 10pt; margin: 0; padding: 0; }
    h2 { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; word-wrap: break-word; }
    th, td { border: 1px solid #000; padding: 4px; text-align: left; vertical-align: top; font-size: 10pt; }
    th { background-color: #f2f2f2; }
    img { max-width: 60px; height: auto; }
    @media print {
      @page { size: A4 landscape; margin: 20mm; }
      html, body {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
    }
  </style>
</head>
<body>
  <h2>Data Users</h2>
  <table>
    <thead>
      <tr>
        <?php foreach ($columns as $col): ?>
          <th><?= htmlspecialchars($col) ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      mysqli_data_seek($query, 0);
      while ($row = mysqli_fetch_assoc($query)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['id_users']}</td>
                  <td>{$row['username']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['no_hp']}</td>
                  <td>{$row['nama']}</td>
                  <td>{$row['jenis_kelamin']}</td>
                  <td>{$row['alamat']}</td>
                  <td>{$row['jabatan']}</td>
                  <td>{$row['nip']}</td>
                  <td>{$row['role']}</td>
                  <td><img src='../public/uploads/{$row['foto']}'></td>
                  <td>{$row['verifikasi']}</td>
                  <td>{$row['created_at']}</td>
                </tr>";
          $no++;
      }
      ?>
    </tbody>
  </table>

  <script>
    window.onload = function() {
      alert("Untuk mencetak langsung, pastikan printer aktif dan opsi 'Save as PDF' tidak dipilih.");
      window.print();
    };
  </script>
</body>
</html>
<?php endif; ?>
