<?php 
/**
 * 
 */
class tfidf
{
	
	//UNTUK MENGAMBIL DATA DAN NILAI IDF NYA DARI DATABASE
	function idf($array, $koneksi){
		$query=mysqli_query($koneksi, "SELECT kata_bobot, bobot FROM bobot_idf;");
	  	$arraykata=array();
	  	$arraybobot=array();
	  	while ($data=mysqli_fetch_array($query)) {
	    	$arraykata[]=$data['kata_bobot'];
	    	$arraybobot[]=$data['bobot'];
	  	}
	  	$array=array_combine($arraykata, $arraybobot);
	  	return $array;
	}

	function hitungIDF($komentarBersih, $vocab, $D){
		$idf=array();
		foreach ($vocab as $kata) {
	    	$f=0;
	    	foreach ($komentarBersih as $komen) {
	        	$komen=explode(" ", $komen);
	        	if (in_array($kata, $komen)) {
	            	$f++;
	        	}
	    	}
	    	$idf[]=round(log10($D/$f), 5);
		}
		return $idf;
	}
}
?>