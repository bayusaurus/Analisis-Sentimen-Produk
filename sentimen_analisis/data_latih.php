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
  .bg-gradasi{
    background: rgb(224,192,207); 
    background: linear-gradient(55deg, rgba(224,192,207,1) 2%, rgba(198,241,217,1) 85%);
  }

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
  <title>Data Latih | Sentiment Analyzer</title>
</head>

<body class="bg-gradasi">
  <button class="backtop bg-success" id="backtop" onclick="topFunction()" title="Go to top">&#8679; Kembali Ke Atas</button>
  <!-- LOADER -->
  <?php include 'loader.php'; ?>
  <!-- LOADER -->
 
  <!-- NAVBAR -->
  <?php include 'navbar.php'; ?>
  <!-- NAVBAR -->

  <!-- CONTENT -->
  <div class="container-fluid mt-2">
  <!-- ALERT -->
  <?php if (isset($_GET['m'])) {
    if ($_GET['m']=='TKG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-2 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Komentar Gagal Ditambahkan, Pastikan komentar anda berisi kalimat dan kata.</strong>
    </div>
    <?php
    }elseif ($_GET['m']=='TKS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-2 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>komentar berhasil ditambahkan!</strong>
    </div>
    <?php
    }elseif ($_GET['m']=='HKS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-2 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>HAPUS KOMENTAR SUKSES</strong>
    </div>
    <?php
    }elseif ($_GET['m']=='HSKS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-2 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>SEMUA KOMENTAR BERHASIL DIHAPUS</strong>
    </div>
    <?php
    }elseif ($_GET['m']=='EKS') { ?>
    <div class="alert alert-success alert-dismissible fade show mb-2 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Komentar Sukses Diedit!</strong>
    </div>
    <?php
    }elseif ($_GET['m']=='EKG') { ?>
    <div class="alert alert-danger alert-dismissible fade show mb-2 text-center " style="padding-top: 15px;">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Komentar Gagal Diedit, Pastikan komentar anda berisi kalimat dan kata.</strong>
    </div>
    <?php
    }
  } ?>
  <!-- END ALERT -->
  <!-- INFO DATA LATIH -->
  <?php 
  include '../config/koneksi.php';
  $query=mysqli_query($koneksi, "SELECT id_komentar FROM komentar_latih WHERE sentimen=1;");
  $pos=mysqli_num_rows($query);
  $query=mysqli_query($koneksi, "SELECT id_komentar FROM komentar_latih WHERE sentimen=0;");
  $neg=mysqli_num_rows($query);
  $query=mysqli_query($koneksi, "SELECT * FROM komentar_latih;"); 
  $tot=mysqli_num_rows($query);
  ?>
  <div class="row">
    <div class="col-md-4">
        <div class="card-body bg-info">
            <h3 class="card-title text-center text-white">TOTAL DATA : <?= $tot; ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-body bg-success">
            <h3 class="card-title text-center text-white">TOTAL POSITIF : <?= $pos; ?></h3>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card-body bg-warning">
            <h3 class="card-title text-center text-white">TOTAL NEGATIF : <?= $neg; ?></h3>
        </div>
    </div>
  </div>
  <!-- END INFO DATA LATIH -->
  
  
  <!-- INI BAGIAN DAFTAR DATA LATIH -->
    <div class="card mb-5">
      <div class="card-header text-center bg-info text-white">
        DATA LATIH
      </div>
      <div class="card-body">
        <center>
        <button type="button" class="btn btn-info mb-3" data-toggle="modal" data-target="#modalTambahKomentar">&#10010; Tambah Komentar</button> 
        <button type="button" class="btn btn-info mb-3" data-toggle="modal" data-target="#modalTambahXLS">&#10010; Tambah Komentar Dengan File .xls</button>
        <a href="../uploads/template_data_latih.xls" download="Template Data Latih">
          <button class="btn btn-success mb-3">&#8681; Download Template .xls</button>
        </a>
        <?php if (mysqli_num_rows($query)>0) { //============================KALAU ADA TAMPILKAN TABEL=========== ?>
        <button class="btn btn-success mb-3" id="exportxls" data-toggle="tooltip" data-placement="top" title="Tampilkan semua komentar terlebih dahulu untuk mengexport semua komentar">&#8681; Export ke .xls</button>
        <button type="button" class="btn btn-danger mb-3" data-toggle="modal" data-target="#modalHapusSemua">&#10007;Hapus Semua Komentar</button>
        <?php } ?>
        </center>
        

        <?php if (mysqli_num_rows($query)>0) { //============================KALAU ADA TAMPILKAN TABEL=========== ?> 

        <table class="table table-striped table-bordered data" id="tabel">
        <caption>Daftar Data Latih Anda</caption>
        <thead>
          <tr>
            <th scope="col" class="text-center">No</th>
            <th scope="col" class="text-center">Komentar</th>
            <th scope="col" class="text-center">Sentimen</th>
            <th scope="col" class="text-center noExport">Ubah</th>
            <th scope="col" class="text-center noExport">Hapus</th>
          </tr>
        </thead>
        <tbody>

          <?php //=======================PERULANGAN MENAMPILKAN DAFTAR DATA LATIH komentar
          $no=1;
          while ($data = mysqli_fetch_array($query)) { //BAGIAN PERULANGANNNN==============?>
            <?php  
              if ($data['sentimen']==1) {
                $sentimen='Positif';
              }elseif ($data['sentimen']==0) {
                $sentimen='Negatif';
              }
            ?>
          <tr>
            <th scope="row" class="text-center"><?= $no; ?></th>
            <td><?= $data['komentar']; ?></td>
            <td class="text-center"><?= $sentimen; ?></td>
            <td class="text-center noExport">
              <a id="edit_komentar" data-toggle="modal" data-target="#modalEditKomentar" data-id="<?= $data['id_komentar']; ?>" data-komentar="<?= $data['komentar']; ?>" data-sentimen="<?= $data['sentimen']; ?>">
                <button class="btn btn-warning">&#9851;</button>
              </a>
            </td>
            <td class="text-center noExport">
              <a id="hapus_komentar" data-toggle="modal" data-target="#modalHapusKomentar" data-id="<?= $data['id_komentar']; ?>">
                <button class="btn btn-danger">&#10007;</button>
              </a>
            </td>
          </tr>
          <?php //================ BAGIAN PERULANGAN==========
            $no++;
          }
         //==============================END PERULANGAN DATA LATIHY ?>
        </tbody>
      </table>

       <?php }else{ //===================== KONDISI BELUM PUNYA DATA LATIH MUNCUL SINI?> 
      <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
        <strong>Anda belum mempunyai komentar sebagai data latih, tambahkan data latih terlebih dahulu.</strong>
      </div>
      <?php //================== PENUTUP ELSE DAN PERULANGNA
        }
      //=====================PENUTUP ESLE DAN PERULANGAN?>

      </div> <!-- end card body -->
    </div> <!-- end card -->
    <!-- INI BAGIAN END DAFTAR DATA LATIH -->
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

  <!-- MODAL TAMBAH -->
  <div class="modal" tabindex="-1" role="dialog" id="modalTambahKomentar">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="col-12 modal-title text-center text-white">
          TAMBAH KOMENTAR
          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
          </button>
        </h5>
      </div>
      <div class="modal-body">
        <form action="../proses/proses_data_latih.php" method="post" id="myForm1">
        <label for="nama">Komentar</label>
        <textarea class="form-control" name="komentar" rows="5" maxlength="280" required></textarea>
        <br>
        <label for="exampleFormControlSelect2">Sentimen</label>
        <select class="form-control" name="sentimen" required="">
          <option value="1">Positif</option>
          <option value="0">Negatif</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambahKomentar" class="btn btn-primary" id="btnTambahDataLatih">Tambah</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
  </div>

  <div class="modal" tabindex="-1" role="dialog" id="modalTambahXLS">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="col-12 modal-title text-center text-white">
          TAMBAH KOMENTAR DENGAN XLS
          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
          </button>
        </h5>
      </div>
      <div class="modal-body">
        <form action="../proses/proses_data_latih.php" method="post" enctype="multipart/form-data" id="myForm" class="mx-3 my-3">
        <div class="form-group">  
          <label for="nama" class="">Masukkan File Anda : </label>
          <input class="form-control-file" name="datalatih" type="file" placeholder="File" id="filexls" onchange="fileValidation()" pattern="[A-Za-z0-9 ]{3,50}" title="Hanya menerima input berupa huruf dan angka sepanjang 3-50 karakter" required="">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambahXLS" class="btn btn-primary" id="btnTambahDataLatih">Tambah</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
  </div>

  <!-- MODAL EDIT -->
  <div class="modal" tabindex="-1" role="dialog" id="modalEditKomentar">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="col-12 modal-title text-center text-white">
          EDIT KOMENTAR
          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
          </button>
        </h5>
      </div>
      <div class="modal-body" id="modal-edit">
        <form action="../proses/proses_data_latih.php" method="post" enctype="multipart/form-data" id="myForm3">
        <label for="nama">Komentar</label>
        <textarea class="form-control" name="komentar" id="komentar" rows="5" maxlength="280" required></textarea>
        <br>
        <label for="exampleFormControlSelect2">Sentimen</label>
        <select class="form-control" name="sentimen" required="" id="sentimen">
          <option value="1">Positif</option>
          <option value="0">Negatif</option>
        </select>
        <input type="hidden" name="id" id="id_komentar">
      </div>
      <div class="modal-footer">
        <button type="submit" name="editKomentar" class="btn btn-primary" id="btnEditDataLatih">Ubah</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
  </div>

  <!-- MODAL HAPUS -->
  <div class="modal" tabindex="-1" role="dialog" id="modalHapusKomentar">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="col-12 modal-title text-center text-white">
          HAPUS KOMENTAR
          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
          </button>
        </h5>
      </div>
      <div class="modal-body" id="modal-hapus">
        <form action="../proses/proses_data_latih.php" method="post" id="myForm4">
          <p>Apakah anda yakin ingin menghapus Data Komentar ini?</p>
        <input type="hidden" name="id" id="id_komentar">
      </div>
      <div class="modal-footer">
        <button type="submit" name="hapusKomentar" class="btn btn-danger" id="btnHapusDL">Hapus</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
  </div>

  <!-- MODAL LOGOUT -->
  <div class="modal" tabindex="-1" role="dialog" id="modalHapusSemua">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="col-12 modal-title text-center text-white">
            HAPUS SEMUA KOMENTAR
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </h5>
        </div>
        <div class="modal-body">
          <p>Sebelum menghapus semua komentar kami sarankan anda untuk melakukan EXPORT DATA LATIH ke XLS melalui menu yang telah disediakan. Apakah anda yakin ingin menghapus semua komentar?</p>
        </div>
        <div class="modal-footer">
          <form action="../proses/proses_data_latih.php" method="post">
            <button type="submit" name="hapusSemua" class="btn btn-danger">Hapus Semua</button>
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
  <script type="text/javascript" src="../vendor/bootstrap/table2excel/dist/jquery.table2excel.min.js"></script>
  
  <!-- JavaScript -->
  <script type="text/javascript">
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
    $(document).on("click","#hapus_komentar",function(){
      var id = $(this).data('id');
      $("#modal-hapus #id_komentar").val(id);
    })
  </script>

  <script type="text/javascript">
    $(document).on("click","#edit_komentar",function(){
      var id = $(this).data('id');
      var komentar = $(this).data('komentar');
      var sentimen = $(this).data('sentimen');
      $("#modal-edit #id_komentar").val(id);
      $("#modal-edit #komentar").val(komentar);
      $("#modal-edit #sentimen").val(sentimen);
    })
  </script>

  <script type="text/javascript">
    $(function(){
      $('#myForm').submit(function() {
      $('#loader').show(); 
      return true;
    });
    });</script>
  <script type="text/javascript">
    $(function(){
      $('#myForm1').submit(function() {
      $('#loader').show(); 
      return true;
    });
    });</script>
  <script type="text/javascript">
    $(function(){
      $('#myForm2').submit(function() {
      $('#loader').show(); 
      return true;
    });
    });</script>
  <script type="text/javascript">
    $(function(){
      $('#myForm3').submit(function() {
      $('#loader').show(); 
      return true;
    });
    });</script>
  <script type="text/javascript">
    $(function(){
      $('#myForm4').submit(function() {
      $('#loader').show(); 
      return true;
    });
    });</script>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#exportxls').on('click', function(e){
          $("#tabel").table2excel({
              exclude: ".noExport",
              name: "DATA_LATIH",
              filename: "DATA_LATIH.xls",
          });
      });
    });
  </script>
  <script type="text/javascript">
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
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