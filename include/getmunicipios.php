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
	$combo="<select id=\"IdMunicipio\" name=\"IdMunicipio\" onchange=\"getData('municipio',this.value);\" >";	
	$combo.="<option value=\"\">Seleccione un Municipio</option>";
	for ($i=0; $i<(mysqli_num_rows($municipios)); $i++) {
		$row = mysqli_fetch_assoc($municipios);
		$combo.="<option value=\"".$row['id']."\">".$row['nombre']."</option>";
	}
	echo $combo.="</select>";
} else {
	$combo="<select id=\"IdMunicipio\" name=\"IdMunicipio\" onchange=\"getData('municipio',this.value);\" >";	
	$combo.="<option value=\"\">Agregue un Municipio</option>";
	echo $combo.="</select>";
}

?>