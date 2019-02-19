<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$registrosAMostrar=20;

function texto_seguro($texto, $link) {	
	if (!empty($texto) || $texto != NULL) {		
		$texto=stripslashes($texto);
		$texto=mysqli_real_escape_string($link, $texto);
		$texto=htmlspecialchars($texto);		
		return $texto;
	}	
}

function conectar_a_bd($basededatos="CER") {	
	// base de datos local
	$usuario='usuario';
	$contrasenia='password';	
	$host='localhost';
	$link = mysqli_connect($host, $usuario, $contrasenia, $basededatos);
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}	
	/*
	$db_selected = mysqli_select_db($basededatos, $link);
	if (!$db_selected) {
		die ('Can\'t use '.$basededatos.' : ' . mysqli_error($link);
	}
	*/
	return $link;
}

function tipo_mensaje($tipo_error='error', $texto='NULL') {	
	//$mensaje='';
	$tipo_error=strtolower($tipo_error);
	switch ($tipo_error) {
		case 'error':
			$mensaje['texto']='<strong>Error: </strong>'.$texto;
			$mensaje['clase']='alert-danger';
			return $mensaje;
			break;
		case 'info':
			$mensaje['texto']='<strong>Info: </strong>'.$texto;
			$mensaje['clase']='alert-info';
			return $mensaje;			
			break;
		case 'exito':
			$mensaje['texto']='<strong>OK: </strong>'.$texto;
			$mensaje['clase']='alert-success';
			return $mensaje;			
			break;
		case 'advertencia':
			$mensaje['texto']='<strong>Mensaje: </strong>'.$texto;
			$mensaje['clase']='alert-warning';
			return $mensaje;			
			break;
	}
}

function crear_salt() {	
	$salt=mt_rand(0,99999999);
	return $salt;
}

function encriptar_password($password, $salt) {
	//echo $password."::".$salt;
	$txtencriptado=utf8_encode($salt.$password);	
	for ($i=0; $i<1000; $i++) {
		//$txtencriptado=md5($txtencriptado);
		$txtencriptado=sha1($txtencriptado); 
	}
	return base64_encode($txtencriptado);
}

function isDate($fecha) {
	//fechas validas, separador -,/,.
	/* año-mes-dia
	   dia-mes-año	
		2018-01-22
		2018/01/22
		22-01-2018
		22/01/2018
		2018-1-2
		2018/1/2
		
	*/
	// si es vacio es una fecha invalida
	if (!$fecha) {
        return false;
    }
	
	// Si tiene mas de 10 caracteres o menos de 8 es una fecha invalida
	if (strlen($fecha)>10 || strlen($fecha)<8) {
		return false;
	}
	
	// Tiene la longitud correcta, ahora vemos si tiene '-' o '/' como separador
	$separadores=['-','/','.'];	
	foreach ($separadores as $separador) {
		//echo $separador .'<br>';
		$pos=strpos($fecha, $separador);
		if ($pos !== false) {
			// verificamos que sea una fecha correcta
			$fechavalida=explode($separador,$fecha);
			if (count($fechavalida)<3){
				return false;
			}
			if ($fechavalida[0]>1000) {
				//bool checkdate ( int $month , int $day , int $year )
				// Tiene formato año-mes-dia
				return checkdate($fechavalida[1], $fechavalida[2], $fechavalida[0]);		
			} else {
				// Tiene formato dia-mes-año
				return checkdate($fechavalida[1], $fechavalida[0], $fechavalida[2]);
			}
		}
	}
	/*
	try {
        new \DateTime($value);
        return true;
    } catch (\Exception $e) {
        return false;
    }*/	
}

function toSQLDate($fecha) {
	//MySQL comes with the following data types for storing a date or a date/time value in the database:
	//DATE - format YYYY-MM-DD.
	if (isDate($fecha)) {
		$separadores=['-','/','.'];	
		foreach ($separadores as $separador) {
			//echo $separador .'<br>';
			$pos=strpos($fecha, $separador);
			if ($pos !== false) {
				// verificamos que sea una fecha correcta
				$fechavalida=explode($separador,$fecha);				
				if ($fechavalida[0]>1000) {					
					// Tiene formato año-mes-dia
					return $fechavalida[0].'-'.$fechavalida[1].'-'.$fechavalida[2];
				} else {
					// Tiene formato dia-mes-año
					return $fechavalida[2].'-'.$fechavalida[1].'-'.$fechavalida[0];
				}
			}
		}
	} else {
		return false;
	}
}

function toHTMLDate($fecha, $separador='/') {
	$fecha=explode("-", $fecha);
	return $fecha[2].$separador.$fecha[1].$separador.$fecha[0];
}

function quitarSeparadorMiles($monto, $separador=",") {
	// por default es una coma el separador pero puede recibir cualquier caracter incluso un array de separadores
	// Produce: Hll Wrld f PHP
	// $vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
	// $onlyconsonants = str_replace($vowels, "", "Hello World of PHP");
	return str_replace($separador, "", $monto);
}
?>
