/*ADI! ZERBITZARIAREN HELBIDEA ADIERAZI BEHAR DA
*/
var URL = "https://localhost/"

//
function eskatu_hautapen_zerrenda(){             
      $.ajax({    
        type: "GET",
        url: URL + "musikaboz/eman_hautapen_zerrenda.php",             
        dataType: "html",                
        success: function(response){                    
            $("#bistaraketaEremua").html(response); 

        }

        });
}

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
function bidali_hautatutakoak(){ 

     bidaltzeko = aukeratu_zerrenda();
     alert(bidaltzeko);
     //bidaltzeko = "nire string"      
     $.ajax({
  	method: "POST",
  	url: URL + "musikaboz/bidali_hautatuak.php",
        data: { informazioa: bidaltzeko },
     })
     .done (function( msg ) {
        alert( "Zure bozka ondo gorde da: " + msg );
    });
}
