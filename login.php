<?php
include 'zerbitzari_funtzioak.php';
//LOGIN PETRAL BAT
//Jquery AJAX bidez datozen balioak jaso, izena eta pass 
//datu balioak hobeto kontrolatu
if ((isset($_POST["izena"])) && (isset($_POST["pass"]))){
   if ($_POST["izena"] != "" && $_POST["pass"] != ""){	
	//$mezua = "ADI: Izena eta pass jasota </br>";	
	$izena = $_POST["izena"];
	$pass = $_POST["pass"];
	//funtzioa erabili
	$emaitza = izena_eman($izena,$pass);
	if ($emaitza == "wtf"){
		$mezua = "ERABILTZAILE IZENA edo PASAHITZA gaizki daude";		
	}else{
		//DENA ONDO BERAZ-> sesioa hasi + ondo mezua bidali berbidalketarako js-n
		//JS-n berbidalketa ez da segurua, kontrolatu 
		session_start();
		$_SESSION["izena"] = $izena;
		$mezua = "ondo";
		//header("Location:http://192.168.0.158/musikaboz/musikaboz bezero/");
		
	} 
   	
   }else{
	$mezua = "ADI: Ezin dira gelaxkak hutsik utzi!";
   }	
	//$mezua = "kaixo" . $izena . " " .$pass . ">>" . "<a target='_blank' href='http://192.168.0.158/musikaboz/musikaboz bezero/'>link</a>";
}else{
	$mezua = "ADI: Datuak ONDO bete mesedez !!";
}


echo $mezua;
//oraingoz inprimatu badabilela frogatzeko




?>
