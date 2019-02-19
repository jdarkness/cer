<?php
require 'funciones.php';
$link=conectar_a_bd();

if (isset($_GET['idEstado'])) {
	$idEstado=texto_seguro($_GET['idEstado'], $link);
} else {
	echo 'Error ::  Falta Id del Estado';
	exit;
}

$query="SELECT id, nombre FROM municipios WHERE estado_id = $idEstado ORDER BY nombre";
if (!($municipios = mysqli_query($link, $query))) {
	//Error en la consulta		
	echo $texto=mysqli_error($link). ' ' . $query;
	$tipo='error';
	$mensaje=tipo_mensaje($tipo, $texto);				
} 

if (mysqli_num_rows($municipios)>0) {
	/*echo "<br>Lista de Municipios:<br>";
	for ($i=0; $i<(mysqli_num_rows($municipios)); $i++) {
		$row = mysqli_fetch_assoc($municipios);
		echo $row['id']."--".$row['nombre']."<br>";
	}*/
	/*
	$enter="";
	$xml_catalogo= "<catalogo>$enter";
	for ($i=0; $i<(mysqli_num_rows($municipios)); $i++) {
		$row = mysqli_fetch_assoc($municipios);
		$xml_catalogo.="<localidad>$enter";
		//echo $row['id']."--".$row['nombre']."<br>";
		$xml_catalogo.="<id>".$row['id']."</id>$enter";
		$xml_catalogo.="<nombre>".$row['nombre']."</nombre>$enter";
		$xml_catalogo.="</localidad>$enter";
	}
	$xml_catalogo.= "</catalogo>$enter";
	echo $xml_catalogo;	
	*/
	/* <select id="IdMunicipio" name="IdMunicipio" onchange="getData('municipio',this.value);" disabled>
				<option value="">Selecciona un municipio</option>			
			</select>*/
	//echo mysqli_num_rows($municipios);
	$combo="<select id=\"IdMunicipio\" name=\"IdMunicipio\" onchange=\"getData('municipio',this.value);\" >";	
	$combo.="<option value=\"\">Seleccione un Municipio</option>";;
	for ($i=0; $i<(mysqli_num_rows($municipios)); $i++) {
		$row = mysqli_fetch_assoc($municipios);
		$combo.="<option value=\"".$row['id']."\">".$row['nombre']."</option>";
	}
	echo $combo.="</select>";
}
?>