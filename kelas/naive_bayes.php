<?php  

/**
 * 
 */
class naive_bayes
{	
	//UNTUK MEMBUAT BAG OF WORD YANG BERISI KATA DAN JUMLAH KEMUNCULAN
	//CONTOH : bagus 4,  jelek 67 dll
	function BagOfWords($sentimen, $array, $koneksi){
		$query=mysqli_query($koneksi, "SELECT kata.kata as kata FROM kata, komentar_latih WHERE kata.id_komentar=komentar_latih.id_komentar AND komentar_latih.sentimen=$sentimen;");
	  	while ($data=mysqli_fetch_array($query)) {
	    	$array[]=$data['kata'];
	  	}
	  	// $array=array_filter($array);
	  	$array=array_count_values($array);
	  	return $array;
	}


	//UNTUK MENGHITUNG JUMLAH KOMENTAR DATA LATIH
	function jumlahData($sentimen, $koneksi){
		$query=mysqli_query($koneksi,"SELECT sentimen FROM komentar_latih where sentimen=$sentimen;");
		return mysqli_num_rows($query);
	}

	//UNTUK MENGHITUNG NILAI PRIOR PROBABILITY
	function prior($nilai1, $nilai2){
		return round($nilai1/($nilai1+$nilai2),5);
	}

	//UNTUK MENGHITUNG NILAI CONDITIONAL PROBABILITY DAN MENCETAKNYA
	function conditionalProb($komen, $array, $BOW, $sumtfidf, $sumidf, $sentimen){
		foreach ($komen as $kata){
	        if (isset($BOW[$kata])) {
	        	$array[]=round(($BOW[$kata]+1)/($sumtfidf+$sumidf), 6);
	            echo "P(".$kata." | ".$sentimen.") = ".$BOW[$kata]." + 1 / ".$sumtfidf." + ".$sumidf." = <strong>". round(($BOW[$kata]+1)/($sumtfidf+$sumidf), 6)."</strong>"; 
	            echo "<br>";
	        }else{
	        	$array[]=round(1/($sumtfidf+$sumidf), 6);
	            echo "P(".$kata." | ".$sentimen.") = 0 + 1 / ".$sumtfidf." + ".$sumidf." = <strong>". round(1/($sumtfidf+$sumidf), 6)."</strong>"; 
	            echo "<br>";
	        }
	    }
	    return $array;     
	}

	//NUNTUK MENGHITUNG NILAI CONDITIONAL PROBABILITY TANPA MENCETAKNYA DALAM LAYAR
	function conditionalProb1($komen, $array, $BOW, $sumtfidf, $sumidf, $sentimen){
		foreach ($komen as $kata){
	        if (isset($BOW[$kata])) {
	        	$array[]=round(($BOW[$kata]+1)/($sumtfidf+$sumidf), 6);
	        }else{
	        	$array[]=round(1/($sumtfidf+$sumidf), 6);
	        }
	    }
	    return $array;     
	}

	//UNTUK MENGHITUNG NILAI POSTERIOR PROBABILITY
	function posteriorProb($likelihood, $prior){
		return $likelihood*$prior;
	}

	//UNTUK MENGHITUNG TOTAL NILAI CONDITIONAL PROBABILITY
	function totalConditional($array){
		$likelihood=0;
		foreach ($array as $prob) {
	        if ($likelihood==0) {
	        	$likelihood=$prob;
	        }else{
	        	$likelihood=$likelihood*$prob;
	        }
	    }
	    return $likelihood;
	}

	//UNTUK MENCARI NILAI AKHIR SENTIMEN, POSITIF ATAU NEGATIF
	function sentimenAkhir($posterior_pos, $posterior_neg){
		if ($posterior_pos>$posterior_neg) {
	        $hasil='Positif';
	    }else{
	        $hasil='Negatif';
	    }
	    return $hasil;
	}

	//UNTUK MENGHITUNG TOTAL BOBOT KATA
	function totalBobot($array){
		$total=0;
		foreach ($array as $key => $value) {
			$total=$total+$value;
		}
		return round($total, 3);
	}
}

?>