<?php
require 'funciones.php';
$link=conectar_a_bd();

if (isset($_GET['idMunicipio'])) {
	$idMunicipio=texto_seguro($_GET['idMunicipio'], $link);
} else {
	echo 'Error ::  Falta Id del Municipio';
	exit;
}

$query="SELECT id, nombre FROM localidades WHERE municipio_id = $idMunicipio ORDER BY nombre;";
if (!($localidades = mysqli_query($link, $query))) {
	//Error en la consulta		
	echo $texto=mysqli_error($link). ' ' . $query;
	$tipo='error';
	$mensaje=tipo_mensaje($tipo, $texto);				
} 

if (mysqli_num_rows($localidades)>0) {	
	/*
	$enter="";
	$xml_catalogo= "<catalogo>$enter";
	for ($i=0; $i<(mysqli_num_rows($localidades)); $i++) {
		$row = mysqli_fetch_assoc($localidades);
		$xml_catalogo.="<localidad>$enter";
		//echo $row['id']."--".$row['nombre']."<br>";
		$xml_catalogo.="<id>".$row['id']."</id>$enter";
		$xml_catalogo.="<nombre>".$row['nombre']."</nombre>$enter";
		$xml_catalogo.="</localidad>$enter";
	}
	$xml_catalogo.= "</catalogo>$enter";
	echo $xml_catalogo;	
	*/
	$combo="<select id=\"IdLocalidad\" name=\"IdLocalidad\" >";
	//$combo.="<option value=\"".mysqli_num_rows($localidades)."\">$query</option>";;
	$combo.="<option value=\"\">Seleccione una localidad</option>";;
	for ($i=0; $i<(mysqli_num_rows($localidades)); $i++) {
		$row = mysqli_fetch_assoc($localidades);
		$combo.="<option value=\"".$row['id']."\">".$row['nombre']."</option>";
	}
	echo $combo.="</select>";
}
?>