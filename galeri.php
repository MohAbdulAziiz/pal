<?php
include 'header.php';
include 'koneksi.php'; // Pastikan koneksi database

// Query galeri semua barang (untuk section galeri utama)
$queryGaleri = mysqli_query($conn, "SELECT * FROM barang");

// Query hasil pencarian (khusus untuk section pencarian)
$searchResults = [];
$keyword = '';
$queryGaleri = mysqli_query($conn, "SELECT * FROM barang WHERE foto IS NOT NULL AND foto != '' AND nama_barang IS NOT NULL AND nama_barang != '' AND keterangan IS NOT NULL AND keterangan != ''");
if (isset($_GET['search']) && $_GET['search'] !== '') {
  $keyword = mysqli_real_escape_string($conn, $_GET['search']);
  $searchQuery = mysqli_query($conn, "SELECT * FROM barang WHERE nama_barang LIKE '%$keyword%' OR keterangan LIKE '%$keyword%'");
  while ($data = mysqli_fetch_assoc($searchQuery)) {
    $searchResults[] = $data;
  }
}
?>
<br><br><br><br>

<!-- Section Pencarian -->
<section id="search-barang" class="section bg-light py-5">
  <div class="container" data-aos="fade-down" data-aos-delay="100">
<!-- Judul Pencarian -->
<div class="text-center mb-4">
  <h2 class="fw-bold">Hasil Pencarian Barang</h2>
  <hr style="width: 100px; border-top: 5px solid #012970; margin: 10px auto;">
  <p class="text-muted">Berikut adalah daftar barang yang sesuai dengan kata kunci pencarian Anda, lengkap dengan foto, nama, dan keterangannya.</p>
</div>

    <!-- Form Pencarian -->
    <form action="" method="GET" class="row justify-content-center mb-5">
      <div class="col-md-6 col-sm-10 d-flex">
        <input type="text" name="search" id="searchInput" class="form-control me-2" placeholder="Cari Barang...." value="<?= isset($_GET['search']) ? htmlspecialchars($keyword) : ''; ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
      </div>
    </form>

    <!-- Hasil Pencarian -->
    <?php if ($keyword !== ''): ?>
      <div class="row gy-4" id="searchResults">
        <?php if (count($searchResults) > 0): ?>
          <?php foreach ($searchResults as $data): ?>
            <div class="col-lg-3 col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="200">
              <div class="card shadow-sm border-0 h-100">
                <div class="image-container" style="width: 100%; height: 250px; overflow: hidden;">
                  <img src="../pal/public/uploads/<?= $data['foto']; ?>" alt="<?= $data['nama_barang']; ?>" 
                       class="img-fluid w-100 h-100" style="object-fit: cover;">
                </div>
                <div class="card-body text-center">
                  <h5 class="card-title fw-bold"><?= $data['nama_barang']; ?></h5>
                  <p class="card-text text-muted"><?= $data['keterangan']; ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12 text-center">
            <p class="text-muted">Barang tidak ditemukan.</p>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</section>
<!-- /Section Pencarian -->

<!-- Gallery Section -->
<section id="gallery" class="gallery section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <!-- Judul Galeri -->
    <div class="section text-center mb-4">
      <h2 class="fw-bold">Galeri Barang</h2>
      <hr style="width: 100px; border-top: 5px solid #012970; margin: 10px auto;">
      <p class="text-muted">Dokumentasi data barang lengkap dengan foto, nama, dan keterangannya.</p>
    </div>

    <!-- Galeri Grid -->
    <div class="row gy-4">
      <?php 
      $counter = 0;
      if (mysqli_num_rows($queryGaleri) > 0): 
        while ($data = mysqli_fetch_assoc($queryGaleri)) : 
          if ($counter >= 8) break; // Batasi hanya 8 card
          $counter++;
      ?>
          <div class="col-lg-3 col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="200">
            <div class="card shadow-sm border-0 h-100">
              <!-- Gambar -->
              <div class="image-container" style="width: 100%; height: 200px; overflow: hidden;">
                <img src="../pal/public/uploads/<?= $data['foto']; ?>" alt="<?= $data['nama_barang']; ?>" 
                     class="img-fluid w-100 h-100" style="object-fit: cover;">
              </div>
              <!-- Body -->
              <div class="card-body text-center">
                <h5 class="card-title fw-bold"><?= $data['nama_barang']; ?></h5>
                <p class="card-text text-muted"><?= $data['keterangan']; ?></p>
              </div>
            </div>
          </div>
      <?php 
        endwhile; 
      else: ?>
        <div class="col-12 text-center">
          <p class="text-muted">Tidak ada barang yang tersedia untuk ditampilkan.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>
<!-- /Gallery Section -->


 <!-- Script untuk menghapus hasil pencarian setelah 5 detik -->
<?php if ($keyword !== ''): ?>
<script>
  setTimeout(function () {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    window.history.replaceState({}, document.title, url.toString());

    const searchInput = document.getElementById('searchInput');
    const resultContainer = document.getElementById('searchResults');

    if (searchInput) searchInput.value = '';
    if (resultContainer) resultContainer.innerHTML = '';
  }, 5000);
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
