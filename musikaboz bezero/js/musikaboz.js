/*ADI! ZERBITZARIAREN HELBIDEA ADIERAZI BEHAR DA
*/
//192.168.0.155:455 https://localhost/
var URL = "http://192.168.157/";//"http://192.168.0.158/";

//Hautatu daitezkeen abestien zerrenda eskuratu
function eskatu_hautapen_zerrenda(){             
      $.ajax({    
        type: "GET",
        url: URL + "musikaboz/eman_hautapen_zerrenda.php",             
        dataType: "html",                
        success: function(response){                    
            $("#bistaraketaEremua").html(response); 

        }

        });
	bozkaketa_denbora_eskuratu();
}

//Hautatutako elementuen zerrenda eskuratu
function eskatu_hautatuen_zerrenda(){
                   
      $.ajax({    
        type: "GET",
        url: URL + "musikaboz/eman_hautatuen_zerrenda.php",             
        dataType: "html",             
        success: function(response){                    
            $("#bistaraketaEremua").html(response); 
	    //alert("komunikazioa ok");
        }

        });
}

//Playerra bistaratzeko funtzioa
function playerra_bistaratu(){
      $.ajax({    
        type: "GET",
        url: URL + "musikaboz/ikusi_erreprodukzio_zerrenda.php",             
        dataType: "html",             
        success: function(response){                    
            $("#bistaraketaEremua").html(response); 
	    //alert("komunikazioa ok");
        }

        });
}

//klik eginda dauden elementuak batzeko funtzioa. 
function aukeratu_zerrenda(){
	aukeratuak = [];
	i = 0;       
	 $("input:checked").each(function() {
           aukeratuak[i] = ($(this).val());
	   i = i + 1;
	   //alert($(this).val());
           //console.log($(this).val());        
	});
        return aukeratuak;
}

//zerrenda aukeratzeko funtzio zaharra 
function aukeratu_zerrenda_zaharra(){
	aukeratuak = "";       
	 $("input:checked").each(function() {
           aukeratuak = aukeratuak + ($(this).val());
	   aukeratuak = aukeratuak + ",";
	   //alert($(this).val());
           //console.log($(this).val());        
	});
        return aukeratuak;

}
//Hautatutako zerrenda bidaltzeko funtzioa:
function bidali_hautatutakoak(){ 

     bidaltzeko = aukeratu_zerrenda();
     //alert(bidaltzeko);
     //bidaltzeko = "nire string"      
     $.ajax({
  	method: "POST",
  	url: URL + "musikaboz/bidali_hautatuak.php",
        data: { informazioa: bidaltzeko },
     })
     .done (function( msg ) {
        alert( "Zerbitzariaren erantzuna: " + msg );
    });
}

//BOZKAKETAren bozkaketa tartearen inguruko datuak eskuratu

var errepikakorra;
function bozkaketa_denbora_eskuratu(){
	//erab = $('#erabinfo').val();
	//this.kont = 60;
	$.ajax({
	  method: "GET",
	  url: URL + "musikaboz/bozkaketa_datuak_jaso.php",
	 //data: { erabiltzailea: erab},
	})
	.done (function(msg){
		var oraingo_ordua =  $.now(); // jsn Date.now(); diff ??
		var jasotako_muga = msg;
		localStorage.setItem("jasotako_muga",jasotako_muga);
		//alert(msg + " " + oraingo_ordua);		
		//var ezberdintasuna = parseInt(jasotako muga) - parseInt(oraingo_ordua);
		ezberdintasuna = msg - oraingo_ordua;
		ezberdintasuna = Math.floor(ezberdintasuna / 1000);
		//abiatuta = localStorage.getItem("erlojua_abiatuta");
		if (localStorage.getItem("erlojua_abiatuta") === null || localStorage.getItem("erlojua_abiatuta") == "False"){
			localStorage.setItem("erlojua_abiatuta", "True");			
			localStorage.setItem("segunduak",ezberdintasuna);
			alert(ezberdintasuna);
			//bozkaketaren hasiera ordua jaso, honen arabera ordularia jartzeko
			errepikakorra = setInterval(erlojua, 1000); //1000 will  run it every 1 second
		}
		
	});
}
function erlojua(){
	//muga = localStorage.getItem("jasotako_muga");
	segunduak = localStorage.getItem("segunduak");	
	//alert(segunduak);
	if (segunduak > 0){
		segunduak -= 1;
		localStorage.setItem("segunduak",segunduak);
		minutuak = Math.floor(segunduak / 60);
		bensegunduak = segunduak % 60;
		$("#denbora").html(minutuak + " " + bensegunduak);	
	}else{
		clearInterval(errepikakorra);
		localStorage.setItem("erlojua_abiatuta", "False")
	}	
}

