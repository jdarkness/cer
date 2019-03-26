<?php
require 'include/funciones.php';
//var_dump($_SESSION);
if (empty($_SESSION["usuario"])) {
	// No hay sesion iniciado, mostramos la pagina de login
	//echo '<h2>Pagina de Login</h2>';
	header("Location: index.php");
	exit();
} else {
	$link=conectar_a_bd();
	$idPaquete=texto_seguro($_GET['idPaquete'], $link);	
	$query ="SELECT Estimacion.IdEstimacion, Evento.NombreCorto, Estimacion.NoContrato, NoEstimacion, MontoRetenciones, MontoLiquido, MontoEjercido, Observaciones, Obra.NoContrato AS NumContrato, Obra.CentroTrabajo, Contratista.NombreLargo, municipios.nombre AS Municipio
			 FROM Estimacion 
			 LEFT JOIN Obra ON Estimacion.IdObra = Obra.IdObra 
			 LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista 
			 LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento
			 LEFT JOIN municipios ON Obra.IdMunicipio = municipios.id
			 WHERE Estimacion.IdPaquete = '$idPaquete'";
	if (!($resultado = mysqli_query($link, $query))) {
		//Error en la consulta		
		echo $texto=mysqli_error($link). ' ' . $query;
		$tipo='error';
		$mensaje=tipo_mensaje($tipo, $texto);
	}	
?>
<?php require 'include/doctype.php'; ?>
<title>Reporte de Recepci&oacute;n de Estimaciones - C.E.R.</title>
<?php require 'include/head.php'; ?>

<body>
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
  <div id="impresion_reporte">
  <table>
	<tr>
		<td colspan="2">Entrego:</td>
		<td colspan="9">Marco</td>
	</tr>
	<tr>
		<td colspan="2">Fecha Entrega:</td>
		<td colspan="9">11-01-2019</td>		
	</tr>
	<tr>
		<td colspan="2">Fecha Devoluci&oacute;:</td>
		<td colspan="9">11-01-2019</td>		
	</tr>
	<tr>
		<th>No.</th>
		<th>Evento</th>
		<th>Contrato</th>
		<th>Contratista</th>
		<th>Nom. Obra</th>
		<th>Municipio</th>		
		<th>No. Estimaci&oacute;n</th>
		<th>Retenciones</th>
		<th>L&iacute;quido</th>
		<th>Ejercido</th>
		<th>Observaciones</th>		
	  </tr>
	  <?php
		// Mostramos la tabla con los resultados
		for ($i=0; $i<(mysqli_num_rows($resultado)); $i++) {
			$row = mysqli_fetch_assoc($resultado);
			?>
	
		<tr>
		<td class="alinear_centro" ><?php echo $i+1; ?></td>
		<td ><?php echo $row['NombreCorto'];?></td>
		<td ><?php echo $row['NoContrato'];?></td>
		<td ><?php echo $row['NombreLargo'];?></td>
		<td ><?php echo $row['CentroTrabajo'];?></td>    
		<td class="alinear_centro"><?php echo $row['Municipio'];?></td>
		<td class="alinear_centro"><?php echo $row['NoEstimacion'];?></td>
		<td class="alinear_derecha"><?php echo number_format($row['MontoRetenciones'], 2, '.', ',');?></td>
		<td ><?php echo number_format($row['MontoLiquido'], 2, '.', ',');?></td>
		<td ><?php echo number_format($row['MontoEjercido'], 2, '.', ',');?></td>
		<td ><?php echo $row['Observaciones'];?></td>	
	  </tr>
		<?php
		}
		?>
	</table>
		<?php
		mysqli_free_result($resultado);	 
  ?>
  </table>
  </div>  
</body>
</html>
<script type="text/javascript">
	window.print();
	//window.close();
</script>    

<?php
}
?>