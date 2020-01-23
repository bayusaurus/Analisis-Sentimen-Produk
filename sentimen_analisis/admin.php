<?php 
session_start();
if ($_SESSION['id'] =='' || $_SESSION['nama']=='' || $_SESSION['email']=='') {
  session_destroy();
  header('location:../index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../vendor/bootstrap/css/stylex.css">
  <link rel="stylesheet" href="../vendor/bootstrap/datatables/css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../vendor/bootstrap/datatables/css/jquery.dataTables.css">
  <style type="text/css">
  .bg-gradasi{background: rgb(224,192,207); background: linear-gradient(55deg, rgba(224,192,207,1) 2%, rgba(198,241,217,1) 85%);}
  .backtop {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 30px;
    z-index: 99;
    font-size: 18px;
    border: none;
    outline: none;
    color: white;
    cursor: pointer;
    padding: 15px;
    border-radius: 4px;
  }

  .backtop:hover {
    background-color: #555;
  }
  </style>
  <link rel="icon" href="../favicon.ico" type="image/x-icon">


  <!-- TITLE -->
  <title>ADMIN | Sentiment Analyzer</title>
</head>

<body class="bg-gradasi">
  <button class="backtop bg-success" id="backtop" onclick="topFunction()" title="Go to top">&#8679; Kembali Ke Atas</button>
 
  <!-- NAVBAR -->
  <?php include 'navbar.php'; ?>
  <!-- NAVBAR -->

  <!-- CONTENT -->
  <div class="container-fluid mt-5">
  <!-- ALERT -->
  <?php if (isset($_GET['m'])) {
    if ($_GET['m']=='GPS') { ?>
      <div class="alert alert-success alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Ganti Password Sukses</strong>
      </div>
  <?php  }elseif ($_GET['m']=='GPG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Ganti Password Gagal!</strong> Pastikan password lama anda benar.
    </div>
  <?php  }elseif ($_GET['m']=='GNS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Ganti Nama Sukses</strong>
    </div>
  <?php  }elseif ($_GET['m']=='TAG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Tambah Admin Gagal Karena Email Telah Terdaftar</strong>
    </div>
  <?php  }elseif ($_GET['m']=='TAS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Tambah Admin Sukses. Password admin baru = admin. Admin bisa mengubah password saat sudah masuk ke dalam sistem.</strong>
    </div>
  <?php  }elseif ($_GET['m']=='NG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Nonaktikan Akun Gagal karena anda satu-satunya admin yang aktif.</strong>
    </div>
  <?php  }
  } ?>
  <!-- END ALERT -->

  <!-- INI BAGIAN DAFTAR DATA LATIH -->
    <div class="card mb-5">

      <div class="card-header text-center bg-info text-white">
      ADMIN
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-header text-center bg-info text-white">Info Anda</div>
              <div class="card-body">
                <button class="btn btn-info mb-3" data-toggle="modal" data-target="#modalGantiPassword">Ubah Password</button>
                <button class="btn btn-danger mb-3" data-toggle="modal" data-target="#modalNonaktif">Nonaktifkan Akun</button>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <td>Nama</td>
                      <td><?= $_SESSION['nama']; ?></td>
                      <td><button class="btn-sm btn btn-info" data-toggle="modal" data-target="#modalGantiNama">Ubah</button></td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Email</td>
                      <td><?= $_SESSION['email']; ?></td>
                      <td><button class="btn-sm btn btn-info" data-toggle="modal" data-target="#modalGantiEmail">Ubah</button></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card">
              <div class="card-header text-center bg-info text-white">Daftar Admin</div>
              <div class="card-body">
                <button class="mb-3 btn btn-info" data-toggle="modal" data-target="#modalTambahAdmin">Tambah Admin</button>
                <div class="table-responsive">
                  <table class="table table-striped table-bordered data">
                    <caption>Daftar Admin</caption>
                    <thead>
                      <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Email</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        include '../config/koneksi.php';
                        $query=mysqli_query($koneksi, "SELECT * FROM admin");
                        $no=1;
                        while ($data=mysqli_fetch_array($query)) { ?>
                          <tr>
                            <td class="text-center"><?= $no; ?></td>
                            <td class="text-center"><?= $data['nama']; ?></td>
                            <td class="text-center"><?= $data['email']; ?></td>
                          </tr>
                      <?php $no++; }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      
  </div>
  <!-- CONTENT -->

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container-fluid bg-dark text-center mt-5" id="foot">
      <span class="text-white">By Bayu Damar Jati. 2019.</span>
    </div>
  </footer>
  <!-- FOOTER -->

  <!-- MODAL -->
  <!-- MODAL LOGOUT -->
  <div class="modal" tabindex="-1" role="dialog" id="modalLogout">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="col-12 modal-title text-center text-white">
            LOG OUT
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <p>Apakah anda yakin ingin keluar?</p>
        </div>
        <div class="modal-footer">
          <form action="../proses/controller_user.php" method="post">
            <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL GANTI PASSSWORD-->
  <div class="modal" tabindex="-1" role="dialog" id="modalGantiPassword">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="col-12 modal-title text-center text-white">
            GANTI PASSWORD
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <!-- <p>Silakan Login terlebih dahulu.</p> -->
          <form action="../proses/controller_user.php" method="post" onsubmit="return checkPassword(this);">
            <input type="hidden" name="email" value="<?= $_SESSION['email']; ?>">
            <label for="passwordLama">Password Lama :</label>
            <input class="form-control" type="password" name="passwordLama" placeholder="Password Lama" id="password" required="">
            <label for="passwordBaru">Password Baru : </label>
            <input class="form-control" type="password" name="passwordBaru" placeholder="Password Baru" id="passwordBaru" required="">
            <label for="passwordBaru2">Konfirmasi Password Baru :</label>
            <input class="form-control" type="password" name="passwordBaru2" placeholder="Konfirmasi Password Baru" id="passwordBaru2" required="">
          </div>
          <div class="modal-footer">
            <button type="submit" name="gantiPassword" class="btn btn-primary">Ganti Password</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL GANTI PASSSWORD-->
  <div class="modal" tabindex="-1" role="dialog" id="modalGantiNama">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="col-12 modal-title text-center text-white">
            GANTI NAMA
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <!-- <p>Silakan Login terlebih dahulu.</p> -->
          <form action="../proses/controller_user.php" method="post">
            <input type="hidden" name="email" value="<?= $_SESSION['email']; ?>">
            <label for="passwordLama">Masukkan Nama Baru :</label>
            <input class="form-control" type="text" name="nama" placeholder="Nama Baru" pattern="[a-zA-Z ]{3-20}" required="">
          </div>
          <div class="modal-footer">
            <button type="submit" name="gantiNama" class="btn btn-primary">Ganti Nama</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL GANTI PASSSWORD-->
  <div class="modal" tabindex="-1" role="dialog" id="modalGantiEmail">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="col-12 modal-title text-center text-white">
            GANTI EMAIL
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <!-- <p>Silakan Login terlebih dahulu.</p> -->
          <form action="../proses/controller_user.php" method="post">
            <input type="hidden" name="email" value="<?= $_SESSION['email']; ?>">
            <label for="passwordLama">Masukkan Email Baru :</label>
            <input class="form-control" type="email" name="emailBaru" placeholder="Email Baru" pattern="{3-50}" required="">
          </div>
          <div class="modal-footer">
            <button type="submit" name="gantiEmail" class="btn btn-primary">Ganti Email</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL GANTI PASSSWORD-->
  <div class="modal" tabindex="-1" role="dialog" id="modalTambahAdmin">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="col-12 modal-title text-center text-white">
            TAMBAH ADMIN
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <!-- <p>Silakan Login terlebih dahulu.</p> -->
          <form action="../proses/controller_user.php" method="post">
            <label for="passwordLama">Masukkan Nama :</label>
            <input class="form-control" type="text" name="nama" placeholder="Nama" pattern="[a-zA-Z ]{3-20}" required="">
            <label for="passwordLama">Masukkan Email :</label>
            <input class="form-control" type="email" name="email" placeholder="Email" pattern="{3-50}" required="">
          </div>
          <div class="modal-footer">
            <button type="submit" name="tambahAdmin" class="btn btn-primary">Tambah Admin</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL LOGOUT -->
  <div class="modal" tabindex="-1" role="dialog" id="modalNonaktif">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="col-12 modal-title text-center text-white">
            NONAKTIFKAN AKUN
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <p>Apakah anda yakin ingin menonaktikan akun anda?</p>
        </div>
        <div class="modal-footer">
          <form action="../proses/controller_user.php" method="post">
            <input type="hidden" name="id" value="<?= $_SESSION['id']; ?>">
            <button type="submit" name="nonaktif" class="btn btn-danger">Nonaktifkan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!--   <script src="vendor/bootstrap/js/jquery-3.2.1.slim.min.js"></script> -->
  <script src="../vendor/bootstrap/js/jquery-3.2.1.js"></script>
  <script src="../vendor/bootstrap/js/popper.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../vendor/bootstrap/datatables/js/jquery.dataTables.js"></script>
  
  <!-- JavaScript -->
  <script type="text/javascript">
    function checkPassword(theForm) {
      if (theForm.passwordBaru.value != theForm.passwordBaru2.value)
      {
        alert('Konfirmasi password Baru salah! Ulangi password dengan nilai yang sama.');
        return false;
      } else {
        return true;
      }
    }
  </script>

  <script type="text/javascript">
  $(document).ready(function(){
    $('.data').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        "language":{
          "decimal":        "",
          "emptyTable":     "Tidak ada data pada tabel ini",
          "info":           "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "infoEmpty":      "Menampilkan 0 hingga 0 dari 0 data",
          "infoFiltered":   "( Disaring dari _MAX_ total data)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "Tampilkan _MENU_ data",
          "loadingRecords": "Memuat...",
          "processing":     "Memproses...",
          "search":         "Cari:",
          "zeroRecords":    "Tidak ada data yang ditemukan",
          "paginate": {
              "first":      "Pertama",
              "last":       "Terakhir",
              "next":       "Selanjutnya",
              "previous":   "Sebelumnya"
          },
          "aria": {
              "sortAscending":  ": activate to sort column ascending",
              "sortDescending": ": activate to sort column descending"
          }
      }
      });
  });
  </script>

  <script>
  // When the user scrolls down 20px from the top of the document, show the button
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      document.getElementById("backtop").style.display = "block";
    } else {
      document.getElementById("backtop").style.display = "none";
    }
  }

  // When the user clicks on the button, scroll to the top of the document
  function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }
  </script>

</body>
</html>