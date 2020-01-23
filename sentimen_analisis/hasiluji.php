<?php 
//cek session
error_reporting(0);
session_start();
if ($_SESSION['id'] =='' || $_SESSION['nama']=='' || $_SESSION['email']=='') {
  session_destroy();
  header('location:../index.php');
}
?>

<?php 
  //Mengambil file yang dibutuhkan
  include '../config/koneksi.php'; //koneksi database
  include '../kelas/naive_bayes.php'; //funsi naive bayes
  include '../kelas/praproses.php'; //fungsi preprocessing
  include '../kelas/tfidf.php'; //fungsi tfidf
  include '../vendor/autoload.php'; //sastrawi
  include '../vendor/excelreader/excel_reader2.php'; //excel reader
  //pembuatan stemmer dan stopword removal
  $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory(); //deklarasi sastrawi stemmer
  $stemmer  = $stemmerFactory->createStemmer(); //deklarasi stemmer
  $StopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory(); //deklarasi sastrawi stopword remover
  $StopWordRemover  = $StopWordRemoverFactory->createStopWordRemover(); //deklarasi stopword remover
  $praproses=new praproses();
  $mnb=new naive_bayes();
  $tfidf=new tfidf();
  //cek file uji yg upload
  if (isset($_POST['ujiXLS'])) {
    //mempersiapkan file xls dan array untuk menyimpan data dari xls
    $tmpfname = basename($_FILES['uji']['name']);
    move_uploaded_file($_FILES['uji']['tmp_name'], $tmpfname);
    $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
    $excelObj = $excelReader->load($tmpfname);
    $worksheet = $excelObj->getSheet(0);
    $lastRow = $worksheet->getHighestRow();

    $komentarXLS=array();
    $komentarBersih=array();
    $sentimenXLS=array();

    for ($row = 2; $row <= $lastRow; $row++) { 
      $komentar1 = addslashes($worksheet->getCell('B'.$row)->getValue());
      $sentimen = $worksheet->getCell('C'.$row)->getValue();

      //praproses komentar
      $komentar=$praproses->addSlashes($komentar1);
      $komentar=$praproses->lower($komentar);
      $komentar=$praproses->removeURL($komentar);
      $komentar=$praproses->replaceStrip($komentar);
      $komentar=$praproses->removeUsername($komentar);
      $komentar=$praproses->removeNumber($komentar);
      $komentar=$stemmer->stem($komentar);
      $komentar=$praproses->removeLetter($komentar);
      $komentar=$StopWordRemover->remove($komentar);
      if ($komentar!=='' && $sentimen!=='' && ($sentimen=='1' || $sentimen=='0' || $sentimen=='Positif' || $sentimen=='Negatif')) {
        $komentarXLS[]=$komentar1;
        $komentarBersih[]=$komentar;

        if ($sentimen=='1' || $sentimen=='Positif') {
          $sentimenXLS[]='Positif';
        }elseif ($sentimen=='0' || $sentimen=='Negatif') {
          $sentimenXLS[]='Negatif';
        }
      }
    }
  }else{
    $err=1;
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
  <title>HASIL PENGUJIAN | Sentiment Analyzer</title>
</head>

<body class="bg-gradasi"> 
  
  <!-- NAVBAR -->
  <?php include 'navbar.php'; ?>
  <!-- NAVBAR -->
  <!-- CONTENT -->
  <div class="container-fluid mt-4 mb-2">
    <!-- BUTTON -->
    <button class="backtop bg-success" id="backtop" onclick="topFunction()" title="Go to top">&#8679; Kembali Ke Atas</button>
    <button class="btn btn-info" onclick="hsDetailPerhitungan();" id="btnhitung">Tampilkan Detail Perhitungan</button>
    <button class="btn btn-info" onclick="hsDetailHasil();" id="btnhasil">Tampilkan Detail Hasil</button>
  </div>
  <!-- BUTTON -->
  <div class="container-fluid">
    <div class="card"> 
      <div class="card-header text-white text-center bg-info">
      HASIL PENGUJIAN
      </div>
      <div class="card-body">
        <?php if (isset($err) && $err==1) { ?>
          <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
            <strong>HARAP UNGGAH DATA UJI DAHULU</strong>
          </div>
        <?php }elseif(count($komentarXLS)==0){ ?>
          <div class="alert alert-danger alert-dismissible fade show mb-0 text-center " style="padding-top: 15px;">
            <strong>ISI DATA UJI ANDA KOSONG ATAU TIDAK DAPAT DIPROSES</strong>
          </div>
        <?php }elseif(count($komentarXLS)>0){ ?>
          <?php
            $BOWpositif=array();
            $BOWnegatif=array();
            $idf=array();  
            $BOWpositif=$mnb->BagOfWords(1, $BOWpositif, $koneksi);
            $BOWnegatif=$mnb->BagOfWords(0, $BOWnegatif, $koneksi);
            $idf=$tfidf->idf($idf, $koneksi);
            $total_positif=$mnb->jumlahData(1, $koneksi);
            $total_negatif=$mnb->jumlahData(0, $koneksi);
            $prior_positif=$mnb->prior($total_positif,$total_negatif);
            $prior_negatif=$mnb->prior($total_negatif, $total_positif);
            $total_idf=$mnb->totalBobot($idf);
            // var_dump($BOWpositif); echo "<br><br>";
            // var_dump($BOWnegatif); echo "<br><br>";
            // var_dump($idf); echo "<br><br>";
            // var_dump($total_positif); echo "<br><br>";
            // var_dump($total_negatif); echo "<br><br>";
            // var_dump($prior_positif); echo "<br><br>";
            // var_dump($prior_negatif); echo "<br><br>";
            // var_dump($total_idf); echo "<br><br>";
          ?>
          
          <!-- CARD UNTUK MENAMPILKAN DIAGRAM PIE -->
          <div class="card mb-5"> <!-- START CARD-->
            <div class="card-header text-center bg-info text-white">
              HASIL ANALISIS SENTIMEN 
            </div> <!-- END CARD HEADER -->
            <div class="card-body">

              <div id="chartContainer" style="height: 370px; width: 100%;"><!-- START DIV GRAPH -->
              </div><!-- END DIV GRAPH -->
            </div><!-- END CARD BODY -->
          </div><!-- END CARD-->
          <!-- END CARD UNTUK MENAMPILKAN DIAGRAM PIE -->

          <!-- CARD UNTUK MENAMPILKAN DIAGRAM PERHITUNGAN -->
          <div class="card mb-5" style="display: none;"  id="detailPerhitungan"> <!-- START CARD-->
            <div class="card-header text-center bg-info text-white" >
              DETAIL PERHITUNGAN
            </div> <!-- END CARD HEADER -->
            <div class="card-body">

              <!-- START ROW -->
              <div class="row">
                <div class="col-md-3"></div>

                <!-- START KATA UNIK -->
                <div class="col-md-6 mb-5">
                  <div class="card"> 
                    <div class="card-header bg-success text-center text-white">
                      KATA UNIK DAN BOBOTNYA
                    </div>
                    <div class="card-body">
                      <table class="table table-striped table-bordered data">
                      <caption>Kata Unik dan Bobotnya</caption>
                      <thead>
                        <tr>
                          <th scope="col">NO</th>
                          <th scope="col">Kata</th>
                          <th scope="col">Bobot</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $no=0;
                          foreach ($idf as $key => $value) { 
                        ?>
                          <tr>
                            <th scope="row"><?= $no+1; ?></th>
                            <td><?= $key; ?></td>
                            <td><?= $value; ?></td>
                          </tr>
                        <?php $no++;
                          }  
                        ?>
                      </tbody>
                    </table>
                    </div>
                  </div> 
                </div>
                <!-- END KATA UNIK -->

                <div class="col-md-3"></div>

                <!-- START KATA POSITIF -->
                <div class="col-md-6">
                  <div class="card"> 
                    <div class="card-header bg-success text-center text-white">
                      KATA POSITIF DAN BOBOTNYA
                    </div>
                    <div class="card-body">
                      <table class="table table-striped table-bordered data">
                      <caption>Kata Positif dan Bobotnya</caption>
                      <thead>
                        <tr>
                          <th scope="col">No</th>
                          <th scope="col">Kata</th>
                          <th scope="col">Kemunculan</th>
                          <th scope="col">Bobot TF-IDF</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $no=0;
                          foreach ($BOWpositif as $key => $value) { 
                        ?>
                            <tr>
                              <th scope="row"><?= $no+1; ?></th>
                              <td><?= $key; ?></td>
                              <td><?= $value; ?></td>
                              <td><?php $BOWpositif[$key]=$idf[$key]*$value; echo $BOWpositif[$key]; ?></td>
                            </tr>
                        <?php $no++;
                          }  
                        ?>
                      </tbody>
                    </table>
                    </div>
                  </div>
                </div>
                 <!-- CARD KATA POSITIF -->

                 <!-- START KATA NEGATIF-->
                <div class="col-md-6 mb-5">
                  <div class="card"> 
                    <div class="card-header bg-success text-center text-white">
                      KATA NEGATIF DAN BOBOTNYA
                    </div>
                    <div class="card-body">
                      <table class="table table-striped table-bordered data">
                      <caption>Kata Negatif dan Bobotnya</caption>
                      <thead>
                        <tr>
                          <th scope="col">No</th>
                          <th scope="col">Kata</th>
                          <th scope="col">Kemunculan</th>
                          <th scope="col">Bobot TF-IDF</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $no=0;
                          foreach ($BOWnegatif as $key => $value) { 
                        ?>
                            <tr>
                              <th scope="row"><?= $no+1; ?></th>
                              <td><?= $key; ?></td>
                              <td><?= $value; ?></td>
                              <td><?php $BOWnegatif[$key]=$idf[$key]*$value; echo $BOWnegatif[$key]; ?></td>
                            </tr>
                        <?php $no++;
                        }  
                        ?>
                      </tbody>
                    </table>
                    </div>
                  </div> 
                </div>
                <!-- END KATA NEGATIF -->

                <!-- MENGHITUNG TOTAL BOBOT KATA POSITIF DAN NEGATIF -->
                <?php 
                  $total_bobot_kata_negatif=$mnb->totalBobot($BOWnegatif);
                  $total_bobot_kata_positif=$mnb->totalBobot($BOWpositif);
                ?>
                <!-- END MENGHITUNG TOTAL BOBOT KATA POSITIF DAN NEGATIF -->
                
                <!-- START CARD KATA PERHITUNGAN-->
                <div class="col-md-12">
                <div class="card"><!-- START CARD KATA PERHITUNGAN--> 
                  <div class="card-header bg-success text-center text-white">
                    PERHITUNGAN
                  </div>
                  <div class="card-body">
                    <table class="table table-striped table-bordered data">
                    <caption>Detail Perhitungan</caption>
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Komentar</th>
                        <th scope="col">Positif</th>
                        <th scope="col">Negatif</th>
                        <th scope="col">Hasil</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $sentimen_akhir=array();
                        $no=0;
                        foreach ($komentarBersih as $key) { 
                      ?>
                      <tr>
                        <td scope="row"><?= $no+1; ?></td>
                        <td><?= $komentarBersih[$no]; ?></td>
                        <td>
                          <?php 
                            $conditional=array();
                            //memecah komentar yg sudah bersih ke kata per kata
                            $komen=$komentarBersih[$no];
                            $komen=explode(" ", $komen);
                            //menghitung probabilitas kondisional kata per kata
                            $conditional=$mnb->conditionalProb($komen, $conditional, $BOWpositif, $total_bobot_kata_positif, $total_idf, 'Positif');
                            //menghitung total kondisional
                            $likelihood=$mnb-> totalConditional($conditional);
                            //menghiutng posterior probabilitas
                            $posterior_pos=$mnb->posteriorProb($likelihood,$prior_positif);
                            
                            //Tampilan Perhitungan
                            echo "<br>P(".$key."| Positif) = ";
                            foreach ($conditional as $k => $value) {
                              echo $value." * ";
                            }
                            echo $prior_positif." = <strong>".$posterior_pos;echo "</strong><br>";
                          ?>
                        </td>
                        <td>
                          <?php  
                            //menyiapkan wadah perhitungan conditional
                            $conditional=array();
                            //memecah komentar yg sudah bersih ke kata per kata
                            //menghitung probabilitas kondisional kata per kata
                            $conditional=$mnb->conditionalProb($komen, $conditional, $BOWnegatif, $total_bobot_kata_negatif, $total_idf, 'Negatif');
                            //menghitung total kondisional
                            $likelihood=$mnb-> totalConditional($conditional);
                            //menghiutng posterior probabilitas
                            $posterior_neg=$mnb->posteriorProb($likelihood,$prior_negatif);
                            
                            //Tampilan Perhitungan
                            echo "<br>P(".$key."| Negatif) = ";
                            foreach ($conditional as $k => $value) {
                              echo $value." * ";
                            }
                            echo $prior_negatif." = <strong>".$posterior_neg;echo "</strong><br>";
                          ?>
                        </td>
                        <?php  
                          $hasil=$mnb->sentimenAkhir($posterior_pos, $posterior_neg);
                          $sentimen_akhir[]=$hasil;
                        ?>
                        <td><?= $hasil; ?></td>
                      </tr>
                      <?php $no++;
                        }  
                      ?>
                    </tbody>
                  </table>
                  </div>
                </div> 
              </div>
              <!-- END PERHITUNGAN -->
              </div>
              <!-- END ROW -->

            </div><!-- END CARD BODY -->
          </div><!-- END CARD-->
          <!-- END CARD UNTUK MENAMPILKAN DIAGRAM PERHITUNGAN -->
          
          <!-- CARD UNTUK MENAMPILKAN DIAGRAM PERHITUNGAN -->
          <div class="card mb-5" style="display: none;"  id="detailHasil"> <!-- START CARD-->
            <div class="card-header text-center bg-info text-white" >
              DETAIL HASIL
            </div> <!-- END CARD HEADER -->
            <div class="card-body">
              
              <button class="btn btn-info mb-3" onclick="download();" id="btndownload" data-toggle="tooltip" data-placement="top" title="Tampilkan semua data tabel terlebih dahulu untuk mengunduh semua">&#8681; Unduh Hasil</button>

              <button class="btn btn-info mb-3" id="exportxls" data-toggle="tooltip" data-placement="top" title="Tampilkan semua data tabel terlebih dahulu untuk mengunduh semua">&#8681; Export ke .xls</button>

              <!-- MENAMPILKAN DETAIL HASIL -->
              <div class="table-responsive">
              <table class="table table-striped table-bordered data" id="tabel">
                <caption>Detail</caption>
                <thead>
                  <tr >
                    <th scope="col">No</th>
                    <th scope="col">Komentar</th>
                    <th scope="col">Sentimen Awal</th>
                    <th scope="col">Hasil</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $panjang=count($komentarXLS);
                    $benar=0;
                    $salah=0;
                    $no=0;
                    for ($i=0; $i < $panjang ; $i++) { ?>
                      <?php if ($sentimenXLS[$i]!==$sentimen_akhir[$i]) {?>
                      <tr class="bg-danger">
                      <?php $salah++;
                      }elseif ($sentimenXLS[$i]==$sentimen_akhir[$i]) { ?>
                      <tr>
                      <?php $benar++; 
                      } ?>
                      <th scope="row"><?= $no+1; ?></th>
                      <td><?= $komentarXLS[$i]; ?></td>
                      <td><?= $sentimenXLS[$i]; ?></td>           
                      <td><?= $sentimen_akhir[$no]; ?></td>
                      </tr>
                      <?php $no++;
                    }  
                  ?>
                </tbody>
              </table>
              </div>
              <!-- END MENAMPILKAN DETAIL HASIL -->
            </div><!-- END CARD BODY -->
          </div><!-- END CARD-->
          <!-- END CARD UNTUK MENAMPILKAN DIAGRAM PERHITUNGAN -->
    

        <?php } ?>
      </div>
    </div>
  </div><!-- END CONTAINER CONTENT -->
  <!-- CONTENT -->
  
<?php 
$dataPoints = array(array("label"=> "Benar", "y"=> $benar),array("label"=> "Salah", "y"=> $salah));
$akurasi=($benar/($benar+$salah))*100;
$txt="Akurasi Pengujian sebesar ".$akurasi."%";
$txt2="Hasil Pengujian";
unlink($_FILES['uji']['name']);
?>
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

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!--   <script src="vendor/bootstrap/js/jquery-3.2.1.slim.min.js"></script> -->
  <script src="../vendor/bootstrap/js/jquery-3.2.1.js"></script>
  <script src="../vendor/bootstrap/js/popper.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../vendor/bootstrap/datatables/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="../vendor/bootstrap/canvasjs/canvasjs.min.js"></script>
  <script type="text/javascript" src="../vendor/bootstrap/jspdf/jspdf.js"></script>
  <script type="text/javascript" src="../vendor/bootstrap/jspdf/autotable.js"></script>
  <script type="text/javascript" src="../vendor/bootstrap/table2excel/dist/jquery.table2excel.min.js"></script>
  
  <!-- JavaScript -->
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
    function hsDetailHasil(){
     var x = document.getElementById("detailHasil");
     var txt=document.getElementById("btnhasil");
     if (x.style.display==="none") {
      x.style.display = "block";
      txt.innerText="Sembunyikan Detail Hasil";
     }else{
      x.style.display = "none";
      txt.innerText="Tampilkan Detail Hasil";
     } 
    }

    function hsDetailPerhitungan(){
     var x = document.getElementById("detailPerhitungan");
     var txt=document.getElementById("btnhitung");
     if (x.style.display==="none") {
      x.style.display = "block";
      txt.innerText="Sembunyikan Detail Perhitungan";
     }else{
      x.style.display = "none";
      txt.innerText="Tampilkan Detail Perhitungan";
     } 
    }
  </script>

  <script>
  window.onload = function () {
  var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    exportEnabled: true,
    title:{
      text: "<?= $txt2; ?>"
    },
    subtitles: [{
      text:"<?= $txt; ?>"
    }],
    data: [{
      type: "pie",
      showInLegend: "true",
      legendText: "{label}",
      indexLabelFontSize: 16,
      indexLabel: "{label} - #percent%",
      yValueFormatString: "#,##0 Komentar",
      dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
    }]
  });
  chart.render();   
  }
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

  <script type="text/javascript">
  function download() {
    var doc = new jsPDF('p', 'pt', 'a4');
    var elem = document.getElementById('tabel');
    var data = doc.autoTableHtmlToJson(elem);
    
    var opts = { 
      columnStyles: {
      0: {columnWidth: 5},
      1: {columnWidth: 250},
      // etc
      } 
    }; 
    doc.autoTable(data.columns, data.rows, opts);

    
    doc.save("HASIL_PENGUJIAN.pdf");
  }
  </script>
    <script type="text/javascript">
    $(document).ready(function() {
      $('#exportxls').on('click', function(e){
          $("#tabel").table2excel({
              exclude: ".noExport",
              name: "HASI_PENGUJIAN",
              filename: "HASIL_PENGUJIAN.xls",
          });
      });
    });
  </script>
  <script type="text/javascript">
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  </script>

</body>
</html>