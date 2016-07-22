<html>
<!--MAU DITAMBAHI INPUT YANG TYPE NYA .CSV SEHINGGA LANGSUNG UNTUK SEMUA DATA APAKAH DITERIMA ATAU TIDAK SEHINGGA BISA LANGSUNG DI SORT DAN DI CUT SESUAI KAPASITAS SEKOLAH-->
<head>
	<title>DSS BAYES</title>
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
	#hide,#show{
		width: 20%;
		background-color: #4CAF50;
		color:white;
		padding: 14px 20px;
		margin:20px auto;
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
	<script type="text/javascript" src="jquery-1.4.3.min.js"></script>
	 <script type="text/javascript" language="javascript">
	$(document).ready(function() {

     $("#show").click(function () {
        $(".dataTraining").show();
     });

     $("#hide").click(function () {
        $(".dataTraining").hide();
     });
     
   });
</script>
</head>
<body>
<div class="header" >
	<h1>SISTEM PENDUKUNG KEPUTUSAN PENERIMAAN SISWA BARU</h1>
</div>
	<div >
	<h2 align="center">Data Training SPK Naive Bayes</h2>
	 <input id="show" type="button" value="Show" /> 
   
	<div class="dataTraining">
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
	<input id="hide" type="button" value="Hide" />  
	</div>
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
		
	   $hasilTraining=trainingAngka($data_training,$prior);
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

	    echo "<h1 align='center'>HASIL DATA TRAINING UNTUK KELAS YA DAN TIDAK</h1>";
		echo "<table>";
		echo "<tr><th rowspan='2'></th><th colspan='3'>DITERIMA</th><th colspan='3'>DITOLAK</th></tr>";
		echo "<tr><th>IQ</th><th>NILAI UAN</th><th>NILAI TES</th><th>IQ</th><th>NILAI UAN</th><th>NILAI TES</th></tr>";
		echo "<tr><th>RATA-RATA</th><td>".$hasilTraining['diterima']['iq']['mean']."</td><td>".$hasilTraining['diterima']['uan']['mean']."</td><td>".$hasilTraining['diterima']['tes']['mean']."</td><td>".$hasilTraining['ditolak']['iq']['mean']."</td><td>".$hasilTraining['ditolak']['uan']['mean']."</td><td>".$hasilTraining['ditolak']['tes']['mean']."</td></tr>";
		echo "<tr><th>VARIAN</th><td>".$hasilTraining['diterima']['iq']['varian']."</td><td>".$hasilTraining['diterima']['uan']['varian']."</td><td>".$hasilTraining['diterima']['tes']['varian']."</td><td>".$hasilTraining['ditolak']['iq']['varian']."</td><td>".$hasilTraining['ditolak']['uan']['varian']."</td><td>".$hasilTraining['ditolak']['tes']['varian']."</td></tr>";
		
	?> 
	 <H1> MENGUJI DATA </H1>
	 <form>
	 	<input type="file" name="datates">
	 	<input type="submit" name="submit" value="Submit" />
	 </form>

	</body>
	</html>