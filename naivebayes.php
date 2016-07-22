<html>
<head>
	<style>
	.error {color: #FF0000;}
	table{
		border:1 solid #4CAF50;
		width: 100%;
		empty-cells: hide;
	}
	th,td{
		text-align: center;
		padding: 8px;
	}
	tr:nth-child(even){
		background-color: #f2f2f2;
	}
	th{
		background-color: #4CAF50;
		color: white;
	}
	.header{
		background-color: #4CAD59;
		color:white;
		width: 100%;
		height: 100px;
		text-align: center;
		padding: 10px;
	}
	.form{
		border-radius: 5px;
		background-color: #f2f2f2;
		padding: 20px;
	}
	input[type=submit]:hover{
		background-color: #45a049;
	}
	input[type=submit]{
		width: 20%;
		background-color: #4CAF50;
		color:white;
		padding: 14px 20px;
		margin:0 auto;
		border:none;
		border-radius: 4px;
		cursor: pointer;
		display: block;

	}
	input[type=text],input[type=file]{
		width: 100%;
		padding: 12px 20px;
		margin:8px 0;
		display:inline-block;
		border: 1px solid #CCC;
		box-sizing: border-box;
	}
	</style>
	<script type="text/javascript" src="js/style.css"></script>
</head>
<body>
<div class="header" >
	<h1>SISTEM PENDUKUNG KEPUTUSAN PENERIMAAN SISWA BARU</h1>
</div>
	<div >
	<h2 align="center">Data Training SPK Naive Bayes</h2>
	<table>
		<tr>
			<th>Nomor</th><th>IQ</th><th>UAN</th><th>Nilai Tes</th><th>Hasil</th>
		</tr>

	<?php
	$file_training="data/data_training.csv";
		if(($handle=fopen($file_training,"r+"))!==false){
			$i=0;
			$data_training= array();
			while(($lineArray=fgetcsv($handle))!==false){	
				echo "<tr>";
				for($j=0;$j<count($lineArray);$j++){
					$data_training[$i][$j]=$lineArray[$j];
					echo "<td align='center'>".$data_training[$i][$j]."</td>";
				}
				$i++;
				echo "</tr>";
			}
		fclose($handle);
		}
	?>
	</table>
	</div>
	<?php
	    $rows=jumlahbaris();
		$ya=$tidak=0;
		for($row=0;$row<$rows;$row++){
			if($data_training[$row][4]=='ya'){
				$ya++;
			}
		}
		
		for($row=0;$row<$rows;$row++){
			if($data_training[$row][4]=='tidak'){
				$tidak++;
			}
		}

		$prior = array('ya' => array('value' =>'ya','jumlah'=>$ya ),
					 'tidak'=>array('value'=>'tidak','jumlah'=>$tidak));
		
	   
		function jumlahbaris(){
		
			$rows=count(file('data/data_training.csv'));
			return $rows;
		}
		
		function trainingAngka($data,$prior){
		
		$id=0;		
		$iq=1;
		$uan=2;
		$tes=3;
		$rows=jumlahbaris();
			
		$IQ=hitungVarianData($data,$iq,$prior['ya']['value'],$prior['ya']['jumlah']);
		$UAN=hitungVarianData($data,$uan,$prior['ya']['value'],$prior['ya']['jumlah']);
		$TEST=hitungVarianData($data,$tes,$prior['ya']['value'],$prior['ya']['jumlah']);
		$IQt=hitungVarianData($data,$iq,$prior['tidak']['value'],$prior['tidak']['jumlah']);
		$UANt=hitungVarianData($data,$uan,$prior['tidak']['value'],$prior['tidak']['jumlah']);
		$TESTt=hitungVarianData($data,$tes,$prior['tidak']['value'],$prior['tidak']['jumlah']);
		
		$hasilTraining = array(
			'diterima' => array(
				'iq' =>array(	'mean' =>$IQ['mean'] ,
								'varian'=>$IQ['varian'],
								'varianExp2'=>$IQ['varianExp2'])
				,'uan'=>array(	'mean' =>$UAN['mean'],
								'varian'=>$UAN['varian'],
								'varianExp2'=>$UAN['varianExp2'])
				,'tes'=>array(	'mean' =>$TEST['mean'] ,
								'varian'=>$TEST['varian'],
								'varianExp2'=>$TEST['varianExp2']) ),
			'ditolak'=>array('iq' =>array(	'mean' =>$IQt['mean'] ,
								'varian'=>$IQt['varian'],
								'varianExp2'=>$IQt['varianExp2'])
				,'uan'=>array(	'mean' =>$UANt['mean'] ,
								'varian'=>$UANt['varian'],
								'varianExp2'=>$UANt['varianExp2'])
				,'tes'=>array(	'mean' =>$TESTt['mean'] ,
								'varian'=>$TESTt['varian'],
								'varianExp2'=>$TESTt['varianExp2']) )
			);
		return $hasilTraining;
		
		}
 $hasilTraining=trainingAngka($data_training,$prior);
		function hitungVarianData($data,$var,$prior,$cprior){
			$total=$counter=$varianExp2=0;
			$simpan=array();
			$rows=jumlahbaris();
			for($row=0;$row<$rows;$row++){
				if($data[$row][4]==$prior){
					$total+=$data[$row][$var];
					$simpan[$counter]=$data[$row][$var];
					$counter++;
				}
			}
			$mean=$total/$counter;
			for($i=0;$i<$counter;$i++){
				$varianExp2+=($simpan[$i]-$mean)**2;;
			} 
			
			$varianExp2=$varianExp2/($cprior-1);
			$varian=sqrt($varianExp2);
			$hasil= array(
				'mean' =>$mean,
				'varian'=>$varian,
				'varianExp2'=>$varianExp2
			 );
			return $hasil;
		}	
		// define variables and set to empty values
		$nomorErr = $iqErr = $uanErr =$tesErr= "";
		$nomorpendaftar = $iqpendaftar = $uanpendaftar= $tespendaftar = "";

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
		  if (empty($_POST["nomor"])) {
		    $nomorpendaftarErr = "Name is required";
		  } else {
		    $nomorpendaftar = test_input($_POST["nomor"]);
		    // check if name only contains letters and whitespace
		   }
		 
		  if (empty($_POST["iq"])) {
		   $iqErr = "iq is required";
		  } else {
		    $iqpendaftar = test_input($_POST["iq"]);
		    
		  }

		  if (empty($_POST["uan"])) {
		    $uanErr = "uan pendaftar is required";
		  } else {
		    $uanpendaftar = test_input($_POST["uan"]);
		  }

		  if (empty($_POST["tes"])) {
		    $tesErr = "tes is required";
		  } else {
		    $tespendaftar = test_input($_POST["tes"]);
		  }
		}

		function test_input($data) {
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}
	?>
	<div class="form">
	<h2 align="center">TESTING NAIVE BAYES</h2>
	<h2 align="center">INPUT VALUE</h2>
	<p><span class="error">* required field.</span></p>
	<form  method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
	  Nomor:<span class="error">*<?php echo $nomorErr;?></span>
	  <input type="text" name="nomor" value="<?php echo $nomorpendaftar?>">
	  <br><br>
	  iq :<span class="error">*<?php echo $iqErr;?></span> 
	  <input type="text" name="iq" value="<?php echo $iqpendaftar;?>">
	  <br><br>
	  uan : <span class="error">*<?php echo $uanErr;?></span>
	  <input type="text" name="uan" value="<?php echo $uanpendaftar;?>">
	  <br><br>
	  test :<span class="error">*<?php echo $tesErr;?></span>
	  <input type="text" name="tes" value="<?php echo $tespendaftar;?>">
      <br><br>
	  <input type="submit" name="submit" value="Submit">  
	</form>
	
	<?php
		$input = array('nomor' =>$nomorpendaftar,
		'iq'=>$iqpendaftar,
		'uan'=>$uanpendaftar,
		'tes'=>$tespendaftar);
		tesData($hasilTraining,$input);
		
			function tesData($hasil,$input){
				$peluangditerima=$peluangditolak=0;
				$PIqDiterima=(1/sqrt(2*pi()*$hasil['diterima']['iq']['varian']))*exp(((($input['iq']-$hasil['diterima']['iq']['mean'])**2)*-1)/(2*$hasil['diterima']['iq']['varianExp2']));

				$PUanDiterima=(1/sqrt(2*pi()*$hasil['diterima']['uan']['varian']))*exp(((($input['uan']-$hasil['diterima']['uan']['mean'])**2)*-1)/(2*$hasil['diterima']['uan']['varianExp2']));

				$PTesDiterima=(1/sqrt(2*pi()*$hasil['diterima']['tes']['varian']))*exp(((($input['uan']-$hasil['diterima']['tes']['mean'])**2)*-1)/(2*$hasil['diterima']['tes']['varianExp2']));

				$PIqDitolak=(1/sqrt(2*pi()*$hasil['ditolak']['iq']['varian']))*exp(((($input['iq']-$hasil['ditolak']['iq']['mean'])**2)*-1)/(2*$hasil['ditolak']['iq']['varianExp2']));

				$PUanDitolak=(1/sqrt(2*pi()*$hasil['ditolak']['uan']['varian']))*exp(((($input['uan']-$hasil['ditolak']['uan']['mean'])**2)*-1)/(2*$hasil['ditolak']['uan']['varianExp2']));

				$PTesDitolak=(1/sqrt(2*pi()*$hasil['ditolak']['tes']['varian']))*exp(((($input['uan']-$hasil['ditolak']['tes']['mean'])**2)*-1)/(2*$hasil['ditolak']['tes']['varianExp2']));
				$peluangditerima=14*$PIqDiterima*$PUanDiterima*$PTesDiterima;
				$peluangditolak=6*$PIqDitolak*$PTesDitolak*$PUanDitolak;
				echo "peluang iq diterima : ".$PIqDiterima."<br>";
				echo "peluang uan diterima : ".$PUanDiterima."<br>";
				echo "peluang tes diterima: ".$PTesDiterima."<br>";
				echo "peluang iq ditolak : ".$PIqDitolak."<br>";
				echo "peluang uan diterima : ".$PUanDitolak."<br>";
				echo "peluang tes diterima: ".$PTesDitolak."<br>";
				echo "hasil probabilitas calon diterima adalah ".$peluangditerima."<br>";
				echo "hasil probabilitas calon ditolak adalah ".$peluangditolak."<br>";

				if($peluangditerima==0){
					echo "data belum diisi";
				}else{
				if($peluangditerima>$peluangditolak){
					echo "<h1 align='center' class='error'>DITERIMA</h1>";
					/*echo "peluang iq diterima : ".$PIqDiterima."<br>";
				echo "peluang uan diterima : ".$PUanDiterima."<br>";
				echo "peluang tes diterima: ".$PTesDiterima."<br>";
				echo "peluang iq ditolak : ".$PIqDitolak."<br>";
				echo "peluang uan diterima : ".$PUanDitolak."<br>";
				echo "peluang tes diterima: ".$PTesDitolak."<br>";
				echo "hasil probabilitas calon diterima adalah ".$peluangditerima."<br>";
				echo "hasil probabilitas calon ditolak adalah ".$peluangditolak."<br>";
*/
				}else{
					echo "<h1 align='center' class='error'>DITOLAK</h1>";
				/*echo "peluang iq diterima : ".$PIqDiterima."<br>";
				echo "peluang uan diterima : ".$PUanDiterima."<br>";
				echo "peluang tes diterima: ".$PTesDiterima."<br>";
				echo "peluang iq ditolak : ".$PIqDitolak."<br>";
				echo "peluang uan diterima : ".$PUanDitolak."<br>";
				echo "peluang tes diterima: ".$PTesDitolak."<br>";
				echo "hasil probabilitas calon diterima adalah ".$peluangditerima."<br>";
				echo "hasil probabilitas calon ditolak adalah ".$peluangditolak."<br>";
*/}}
					
				}
			?>
</div>
<!--<div class="inputCsv">
	<form>
<input type="file" name="fileupload" accept="image/*" />
<input type="submit" name="submit" value="Submit">
</form>
</div>-->
<?php
	    echo "<h1 align='center'>HASIL DATA TRAINING UNTUK KELAS YA DAN TIDAK</h1>";
		echo "<table>";
		echo "<tr><th rowspan='2'></th><th colspan='3'>DITERIMA</th><th colspan='3'>DITOLAK</th></tr>";
		echo "<tr><th>IQ</th><th>NILAI UAN</th><th>NILAI TES</th><th>IQ</th><th>NILAI UAN</th><th>NILAI TES</th></tr>";
		echo "<tr><th>RATA-RATA</th><td>".$hasilTraining['diterima']['iq']['mean']."</td><td>".$hasilTraining['diterima']['uan']['mean']."</td><td>".$hasilTraining['diterima']['tes']['mean']."</td><td>".$hasilTraining['ditolak']['iq']['mean']."</td><td>".$hasilTraining['ditolak']['uan']['mean']."</td><td>".$hasilTraining['ditolak']['tes']['mean']."</td></tr>";
		echo "<tr><th>VARIAN</th><td>".$hasilTraining['diterima']['iq']['varian']."</td><td>".$hasilTraining['diterima']['uan']['varian']."</td><td>".$hasilTraining['diterima']['tes']['varian']."</td><td>".$hasilTraining['ditolak']['iq']['varian']."</td><td>".$hasilTraining['ditolak']['uan']['varian']."</td><td>".$hasilTraining['ditolak']['tes']['varian']."</td></tr>";
		
	?> 

	</body>
	</html>