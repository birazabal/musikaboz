var URL = "http://192.168.0.158/"

function kontua_frogatu(){
      izena = $('#izena').val();
      pass = $('#pass').val();             
      $.ajax({    
        type: "POST",
        url: URL + "musikaboz/login.php",             
        dataType: "html",                
     	data: { izena: izena, pass: pass },
      })
     .done (function( msg ) {
        alert("zerbitzariaren erantzuna jasota bezeroan" + msg);
	if (msg == "ondo"){
		//alert(msg);
		location.href= URL + "musikaboz/musikaboz bezero/index.php";		
	}
	//$('#bistaraketaEremua').html(msg);
    });
}
