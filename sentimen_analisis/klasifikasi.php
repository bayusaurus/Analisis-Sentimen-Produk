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
  </style>
  <link rel="icon" href="../favicon.ico" type="image/x-icon">

  <!-- TITLE -->
  <title>PENGUJIAN DAN PELABELAN | Sentiment Analyzer</title>
</head>

<body class="bg-gradasi">
  <!-- LOADER -->
  <?php include 'loader.php'; ?>
  <!-- LOADER -->
 
  <!-- NAVBAR -->
  <?php include 'navbar.php'; ?>
  <!-- NAVBAR -->

  <!-- CONTENT -->
  <div class="container-fluid mt-5">
  <!-- ALERT -->
  <!-- END ALERT -->

  <!-- INI BAGIAN DAFTAR DATA LATIH -->
    <div class="card mb-5">
      <div class="card-header text-center bg-info text-white">
        PENGUJIAN DAN PELABELAN
      </div>
      <div class="card-body">

        <?php //===============================CEK APAKAH ADA DATA LATIH==========================
        include '../config/koneksi.php';
        $query=mysqli_query($koneksi, "SELECT * FROM komentar_latih where sentimen=1");
        $p=mysqli_num_rows($query);
        $query=mysqli_query($koneksi, "SELECT * FROM komentar_latih where sentimen=0");
        $n=mysqli_num_rows($query);
        if ($p>0 && $n>0) { //============================KALAU ADA TAMPILKAN TABEL=========== ?> 
        
        <div class="row">
          
          <div class="col-md-6">
            <a href="../uploads/template_data_pengujian.xls" download="Template Data Pengujian">
              <button class="btn btn-success mb-3">&#8681; Download Template Pengujian .xls</button>
            </a>
            <div class="card">
              <div class="card-header text-white text-center bg-info">
                PENGUJIAN
              </div>
              <div class="card-body">
                <form action="hasiluji.php" method="post" enctype="multipart/form-data" id="myForm" class="mx-3 my-3">
                <div class="form-group">  
                  <label for="uji" class="">Masukkan File Pengujian Anda : </label>
                  <input class="form-control-file" name="uji" type="file" placeholder="File" id="filexls" onchange="fileValidation()" required="">
                </div>
                <button type="submit" class="btn btn-info mb-2" name="ujiXLS">Proses Pengujian</button>
              </form>
              </div>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <a href="../uploads/template_data_pelabelan.xls" download="Template Data Pelabelan">
              <button class="btn btn-success mb-3">&#8681; Download Template Pelabelan .xls</button>
            </a>
            <div class="card">
              <div class="card-header text-center bg-info text-white">
                PELABELAN
              </div>
              <div class="card-body">
                <form action="hasilpelabelan.php" method="post" enctype="multipart/form-data" id="myForm" class="mx-3 my-3">
                <div class="form-group">  
                  <label for="uji" class="">Masukkan File Pelabelan Anda : </label>
                  <input class="form-control-file" name="uji" type="file" placeholder="File" id="filexls2" onchange="fileValidation2()" required="">
                </div>
                <button type="submit" class="btn btn-info mb-2" name="ujiXLS">Proses Pelabelan</button>
              </form>
              </div>
            </div>
          </div>

        </div>

        <?php }else{ //===================== KONDISI BELUM PUNYA DATA LATIH MUNCUL SINI
        if ($n==0 && $p==0) {
          $alert='positif dan negatif';
        }elseif ($n==0) {
          $alert='negatif';
        }elseif ($p==0) {
          $alert='positif';
        }
        ?> 
      <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
        <strong>Tidak bisa melakukan pengujian atau pelabelan karena data latih anda tidak mempunyai komentar dengan sentimen <?= $alert; ?>, mohon tambahkan terlebih dahulu.</strong>
      </div>
      <?php //================== PENUTUP ELSE DAN PERULANGNA
        }
      //=====================PENUTUP ESLE DAN PERULANGAN?>

      </div>
    </div>
    <!-- INI BAGIAN END DAFTAR DATA LATIH -->
  </div>
  <!-- CONTENT -->

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container-fluid bg-dark text-center" id="foot">
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
          <form action="../controller/controller_user.php" method="post" onsubmit="return checkPassword(this);">
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

  <!-- MODAL PENGUJIAN  -->
  <div class="modal" tabindex="-1" role="dialog" id="modalUjiXls">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
          <h5 class="col-12 modal-title text-center text-white">
            PENGUJIAN
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
      <div class="modal-body" id="modal-uji">
        <p>Masukkan file data uji anda, Pastikan format file data latih anda sesuai dengan <a href="../uploads/template_data_latih.xls" download="Template Data Latih"><kbd>Template</kbd></a> yang disediakan.</p>
        <form action="hasil_pengujian2.php" method="post" enctype="multipart/form-data" id="myForm">
        <label for="nama">Nama Data Latih</label>
        <input class="form-control" name="nama_dl" type="text" id="nama_dl" readonly="">
        <label for="nama">File Data Uji</label>
        <input class="form-control-file" name="uji" type="file" placeholder="File" id="filexls" onchange="fileValidation()" pattern="[A-Za-z0-9 ]{3,50}" title="Hanya menerima input berupa huruf dan angka sepanjang 3-50 karakter" required="">
        <input type="hidden" name="id_dl" id="id_dl">
      </div>
      <div class="modal-footer">
        <button type="submit" name="ujiXls" class="btn btn-primary" id="btnEditDataLatih">Uji</button>
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

    function fileValidation(){
      var fileInput = document.getElementById('filexls');
      var filePath = fileInput.value;
      var allowedExtensions = /(\.xls)$/i;
      if(!allowedExtensions.exec(filePath)){
        alert('Tolong upload file yang berjenis .xls dan sesuai dengan template yang disediakan.');
        fileInput.value = '';
        return false;
      }
    }
    function fileValidation2(){
      var fileInput = document.getElementById('filexls2');
      var filePath = fileInput.value;
      var allowedExtensions = /(\.xls)$/i;
      if(!allowedExtensions.exec(filePath)){
        alert('Tolong upload file yang berjenis .xls dan sesuai dengan template yang disediakan.');
        fileInput.value = '';
        return false;
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

  <script type="text/javascript">
    $(document).on("click","#ujiXls",function(){
      var idj = $(this).data('id');
      var nama = $(this).data('nama');
      $("#modal-uji #id_dl").val(idj);
      $("#modal-uji #nama_dl").val(nama);
    })
  </script>

</body>
</html>