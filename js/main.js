function enviar_accion(accion, valor) {
	//alert('Alerta de envio ' + accion + ':'+valor);
	//console.log('mensaje de envio');
	//valor = document.getElementById('valor').value;
	//accion = document.getElementById('accion').value;	
	if (accion == 'eliminar') {
		var r = confirm("Esta acci\u00F3n es permanente\nDesea continuar?");
		if (r == true) {
			document.getElementById('valor').value=valor;
			document.getElementById('accion').value=accion;
			document.getElementById("formulario").submit();
		} 
	} else {
		document.getElementById('valor').value=valor;
		document.getElementById('accion').value=accion;
		document.getElementById("formulario").submit();
	}
	//document.getElementById("myForm").submit();
}

function restaMontoLiquido(num1, num2) {
	//alert(num1.indexOf(',')+ '::' + num2.indexOf(','));	
	// Quitamos las comas para que pueda sumar si las tiene
	if (num1.indexOf(',')>=0) {
		num1=num1.replace(/\,/g,'');		
	}
	if (num2.indexOf(',')>=0) {
		num2=num2.replace(/\,/g,'');		
	}	
	//alert(num1+ "--" + num2);
	var resultado = num1-num2;
	// lo ajustamos a solo dos decimales
	resultado=resultado.toFixed(2);
	//alert(resultado);
	return resultado;
}

function siguienteInput(e, siguiente) {
	//alert(e.keyCode);
	var e=(typeof event!='undefined')?window.event:e;// IE : Moz 
	if(e.keyCode==13){
		//alert('Enter');
		if (siguiente.length>0) {
			document.getElementById(siguiente).focus();
			//break;
		}		
	}	
}

function limpiar_form(form) {
	//alert(form.length);
	for(i=0; i<form.length; i++) {
		//alert(form[i].name+':'+form[i].type);
		if (form[i].type=='text' || form[i].type=='password') {
			form[i].value='';
		}
	}
	return false;
}

function separarMiles(campo, separador=",") {
	//alert('separarMiles');
	//alert(isNaN(campo));
	//alert (typeof campo);
	/*
	if (typeof  campo === 'number') {		
		var monto = campo;
	} else {
		return '';
	}*/	
	
	if (isNaN(campo)) {
		return '';
	} else {
		var monto = campo;
	}	
	//alert(campo.value);
	//alert(monto.indexOf('.'));
	// Separamos la parte entera y decimal si la hay
	if (monto.indexOf('.')>0) {
		var splitStr = monto.split('.');		
		var entero=splitStr[0];
		var decimal=splitStr[1];
	} else {
		entero=monto;
		decimal='';
	}
	
	
	// verificamos si es un numero negativo
	var negativo=false;
	if(entero[0]=="-") {		
		// le quitamos el signo al entero
		entero = entero.substring(1);
		negativo=true;
	}
	
	//alert("entero: " +entero +" " + "decimal: " +decimal);
	var posSeparador=1;
	var nuevoNumero='';	
	for (i=entero.length; i>0; i--) {		
		var pos=i-1;
		//alert ("i="+i+" num="+entero[pos]+" pos="+pos);
		//alert (entero[pos]+" "+i);
		if (posSeparador>=3) {
			//alert ('insertat coma');
			posSeparador=1;
			if (i==1) {
				nuevoNumero = nuevoNumero.concat(entero[pos]);
				
			} else {
				nuevoNumero = nuevoNumero.concat(entero[pos], separador);
			}
		} else {
			posSeparador++;
			nuevoNumero = nuevoNumero.concat(entero[pos]);
		}
	}
	//alert (nuevoNumero);
	//invertimos el numero porque queda al revés	
	entero='';
	for (i=nuevoNumero.length; i>0; i--) {
		var pos=i-1;
		entero = entero.concat(nuevoNumero[pos]);
	}
	//alert (entero);
	// Agregamos la parte decimal y el signo negativo de ser necesario
	if (decimal.length>0) {
		entero = entero.concat(".",decimal);
	}
	
	if (negativo==true){
		entero="-"+entero;
	}	
	//alert ('fin:'+entero);
	//campo.value = entero;	
	return entero;
	
	
}

function numberFormat(numero){
	// Variable que contendra el resultado final
	var resultado = "";
	
	// Si el numero empieza por el valor "-" (numero negativo)
	if(numero[0]=="-") {
		// Cogemos el numero eliminando los posibles puntos que tenga, y sin
		// el signo negativo
		nuevoNumero=numero.replace(/\./g,'').substring(1);
    }else{
		// Cogemos el numero eliminando los posibles puntos que tenga
		nuevoNumero=numero.replace(/\./g,'');
    }

	// Si tiene decimales, se los quitamos al numero
	if(numero.indexOf(",")>=0)
		nuevoNumero=nuevoNumero.substring(0,nuevoNumero.indexOf(","));
		// Ponemos un punto cada 3 caracteres
		for (var j, i = nuevoNumero.length - 1, j = 0; i >= 0; i--, j++)
			resultado = nuevoNumero.charAt(i) + ((j > 0) && (j % 3 == 0)? ".": "") + resultado;
		
		// Si tiene decimales, se lo añadimos al numero una vez forateado con 
		// los separadores de miles
		if(numero.indexOf(",")>=0)
			resultado+=numero.substring(numero.indexOf(","));
		
		if(numero[0]=="-") {
			// Devolvemos el valor añadiendo al inicio el signo negativo
            return "-"+resultado;
        } else {
            return resultado;
        }
}

function enviarForm(evento) {	
	//alert (evento+ " " + formulario);
	if (evento.which == 13 || event.evento == 13) {
        enviar_accion('buscar',document.getElementById('buscar').value);
        return false;
    }
    return true;
}

function getData(catalogo, id) {
	//alert("catalogo: "+catalogo+" id: " + id);
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {  // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	switch (catalogo) {
		case "estado":
			// Con el id del estado obtenemos los municipios
			//alert("estado: "+catalogo+" id: " + id);
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("IdMunicipio").innerHTML = this.responseText;
					document.getElementById("IdMunicipio").disabled=false;
				}
			};
			xmlhttp.open("GET", "include/getmunicipios.php?idEstado="+id, true);			
			xmlhttp.send();
			
			break;
		case "municipio":
			// Obtenemos las localidades con el id del municipio		
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("IdLocalidad").innerHTML = this.responseText;
					document.getElementById("IdLocalidad").disabled=false;
				}
			};
			xmlhttp.open("GET", "include/getlocalidad.php?idMunicipio="+id, true);			
			xmlhttp.send();
	}
}

function myFunction(xml, i) {
    var xmlDoc = xml.responseXML; 
    x = xmlDoc.getElementsByTagName("CD");
    document.getElementById("showCD").innerHTML =
    "Artist: " +
    x[i].getElementsByTagName("ARTIST")[0].childNodes[0].nodeValue +
    "<br>Title: " +
    x[i].getElementsByTagName("TITLE")[0].childNodes[0].nodeValue +
    "<br>Year: " + 
    x[i].getElementsByTagName("YEAR")[0].childNodes[0].nodeValue;
}

function myFunction() {
  var x = document.getElementById("mySelect");
  var option = document.createElement("option");
  option.text = "Kiwi";
  x.add(option, x[2]);
}

function myFunction(xml) {
  var i;
  var xmlDoc = xml.responseXML;
  var table="<tr><th>Artist</th><th>Title</th></tr>";
  var x = xmlDoc.getElementsByTagName("CD");
  for (i = 0; i <x.length; i++) { 
    table += "<tr><td>" +
    x[i].getElementsByTagName("ARTIST")[0].childNodes[0].nodeValue +
    "</td><td>" +
    x[i].getElementsByTagName("TITLE")[0].childNodes[0].nodeValue +
    "</td></tr>";
  }
  document.getElementById("demo").innerHTML = table;
}

function mostrar_agregar(btn1, btn2, btn3, btn4, btn5, btn6) {
	//'btnAgregar', 'IdEstado', 'btnOk','btnCancelar','txtEstado'
	//alert(btn1+" "+btn2+" "+btn3+" "+btn4+" "+btn5);
	document.getElementById(btn1).style.display="none";
	document.getElementById(btn2).style.display="none";
	document.getElementById(btn3).style.display="none";
	document.getElementById(btn4).style.display="inline";
	document.getElementById(btn5).style.display="inline";	
	document.getElementById(btn6).style.display="inline";	
}

function ocultar_agregar(btn1, btn2, btn3, btn4, btn5, btn6) {
	//'btnAgregar', 'IdEstado', 'btnOk','btnCancelar','txtEstado'
	//alert("estado: "+btn1+" id: " + btn2);
	document.getElementById(btn1).style.display="inline";
	document.getElementById(btn2).style.display="inline";
	document.getElementById(btn3).style.display="inline";
	document.getElementById(btn4).style.display="none";	
	document.getElementById(btn5).style.display="none";	
	document.getElementById(btn6).style.display="none";	
}

function visualizar_elemento() {
	//alert("Elementos: "+ arguments.length);
	// visualizar_elemento('lblMunicipio','none','lblLocalidad','none','btnAgregarEstado','none', 'IdEstado','none', 'btnEliminarEstado','none', 'btnOkEstado','inline','btnCancelarEstado','inline','txtEstado','inline')
	for (let i = 0; i < arguments.length; i=i+2) {
		//alert("Elemento: "+ arguments[i] + " Display: " + arguments[i+1]);
		document.getElementById(arguments[i]).style.display=arguments[i+1];
	}
}