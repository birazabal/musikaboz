<?php
include 'zerbitzari_funtzioak.php';

//informazioa POST moduan jaso :(
$nirealdagai = $_POST["informazioa"];
echo "kaixo zerbitzaritik, bidalitako aldagaia: " . $nirealdagai;
gorde_datubasean_hautapenak($nirealdagai);

?> 

