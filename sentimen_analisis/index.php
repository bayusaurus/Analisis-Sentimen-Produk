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
  <style type="text/css">
  .bg-gradasi{background: rgb(224,192,207); background: linear-gradient(55deg, rgba(224,192,207,1) 2%, rgba(198,241,217,1) 85%);}
  </style>
  <link rel="icon" href="../favicon.ico" type="image/x-icon">

  <!-- TITLE -->
  <title>HOME | Sentiment Analyzer</title>
</head>

<body class="bg-gradasi">
  <!-- INI LOADER -->
  <div  id="loader" style="background: rgba( 255, 255, 255, 0.9 );display: none; height: 100%; position: fixed; width: 100%; z-index: 9999;">
    <div style="left: 40%; top: 15%; position: absolute;"  >
      <div class="card" style="width: 15rem;">
        <div class="card-header text-center text-white bg-success">LOADING</div>
        <center><img class="mt-4 mb-2" src="../uploads/loader-large.gif" width="100" height="100" alt="Processing.........."></center>
        <div class="card-body">
          <p class="card-text text-center">Data anda sedang diproses.....</p>
          <p class="card-text text-center">Proses mungkin memakan waktu cukup lama tergantung pada banyaknya data komentar pada file excel anda, harap tunggu hingga proses selesai.</p>
        </div>
      </div>
    </div>
  </div>
  <!-- LOADER -->
 
  <!-- NAVBAR -->
  <?php include 'navbar.php'; ?>
  <!-- NAVBAR -->

  <!-- CONTENT -->
  <div class="container mt-5">
    <!-- ALERT -->
  <?php if (isset($_GET['m'])) {
    if ($_GET['m']=='GPS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Selamat! Ganti Password Sukses!</strong>
    </div>
  <?php
    }elseif ($_GET['m']=='LS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Selamat! Login Sukses!</strong>
    </div>
  <?php  
    }elseif ($_GET['m']=='GPG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Ganti Password Gagal!</strong> Pastikan password lama anda benar.
    </div>
  <?php
    }elseif ($_GET['m']=='LG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Login Gagal!</strong> Email atau password anda salah.
    </div>
    <?php
    }elseif ($_GET['m']=='LPG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Ganti Password Gagal!</strong> Silakan Coba Lagi.
    </div>
    <?php
    }
  } ?>
  <!-- END ALERT -->

    <!-- INI BAGIAN SELAMAT DATANG -->
    <div class="jumbotron">
      <h1 class="display-4 text-center">Hello, <?= $_SESSION['nama']; ?>!</h1>
      <p class="lead text-center">Selamat datang di Sentiment Analyzer.</p>
      <hr class="my-4">
      <p class="text-center">Untuk penjelasan cara melakukan Analisis Sentimen klik tombol Cara Menggunakan <a class="btn btn-info btn-sm" href="#" role="button" onclick="hideshow()">Cara Menggunakan</a></p>
    </div>

    <!-- INI BAGIAN CARA MENGGUNAKAN -->
    <div class="jumbotron mt-5" id="cara" style="display: none;">
      <h1 class="display-4 text-center">Cara Menggunakan</h1>
      <hr class="my-4">
      <p>1. Tambahkan Data Latih</p>
      <p>Download Template data latih yang telah disediakan, kemudian isikan data latih sesuai dengan data komentar yang anda ingin tambahkan. Pastikan mengisi data komentar dengan aturan yang telah dijelaskan dalam file template. Kemudian tambahkan data latih dalam opsi data latih dan upload file data latih yang telah anda isi tadi dan tunggu hingga proses penambahan data latih selesai. Anda juga bisa mengedit isi data latih anda pada halaman detail data latih. Anda juga bisa mengunduh template data latih disini -->  <a href="../uploads/template_data_latih.xls" download="Template Data Latih"><kbd>Template</kbd></a></p>
      <p>2. Lakukan Pengujian</p>
       <p>Download Template data pengujian yang telah disediakan, kemudian isikan data pengujian sesuai dengan data komentar yang anda ingin tambahkan. Pastikan mengisi data komentar dengan aturan yang telah dijelaskan dalam file template. Kemudian lakukan pengujian pada opsi pengujian dengan data pengujian yang telah anda buat. Setelah itu hasil pengujian akan muncul, hasil pengujian serta perhitungannya juga akan ditampilkan. Anda juga bisa mengunduh template data pengujian disini -->  <a href="../uploads/template_data_uji.xls" download="Template Data Uji"><kbd>Template</kbd></a></p>
      <p>3. Lakukan Pelabelan</p>
      <p>Download Template data pelabelan yang telah disediakan, kemudian isikan data pelabelan sesuai dengan data komentar yang anda ingin tambahkan. Pastikan mengisi data komentar dengan aturan yang telah dijelaskan dalam file template. Kemudian lakukan pelabelan pada opsi pelabelan dengan data pelabelan yang telah anda buat. Setelah itu hasil pelabelan akan muncul, hasil pelanelan serta perhitungannya juga akan ditampilkan. Anda juga bisa mengunduh template data pelabelan disini -->  <a href="../uploads/template_data_pelabelan.xls" download="Template Data Pelabelan"><kbd>Template</kbd></a></p>
    </div>
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
  <!-- MODAL -->

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!--   <script src="vendor/bootstrap/js/jquery-3.2.1.slim.min.js"></script> -->
  <script src="../vendor/bootstrap/js/jquery-3.2.1.js"></script>
  <script src="../vendor/bootstrap/js/popper.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
  
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

    function hideshow(){
     var x = document.getElementById("cara");
     var y =  document.getElementById("foot");
     if (x.style.display==="none") {
      y.classList.add("mt-5");
      x.style.display = "block";
     }else{
      y.classList.remove("mt-5");
      x.style.display = "none";
     } 
    }
  </script>

</body>
</html>