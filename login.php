<?php
include 'zerbitzari_funtzioak.php';
//LOGIN PETRAL BAT
//Jquery AJAX bidez datozen balioak jaso, izena eta pass 
//datu balioak hobeto kontrolatu


$ktr = new erabiltzaileKontrola();
$ktr->login_kontrola();
//login_kontrola();



?>
