<?php
require 'include/funciones.php';
//var_dump($_SESSION);
//var_dump($_POST);
if (empty($_SESSION["usuario"])) {
	// No hay sesion iniciado, mostramos la pagina de login
	//echo '<h2>Pagina de Login</h2>';
	header("Location: index.php");
	exit();
} else {
	// hay session iniciada, redirigimos a la pagina de paquetes
	//echo '<h2>Pagina de Lista de Paquetes</h2>';
	$link=conectar_a_bd();
	$nombreLargo='';
	$nombreCorto='';
	$Descripcion='';
	$numPagina=1;
	$txtBuscar='';
	$msgBoton='Agregar Evento';
	if (isset($_POST['accion'])) {
		$accion=texto_seguro($_POST['accion'], $link);
	} else {
		$accion='nuevo';
	}
	if (isset($_POST['valor'])) {
		$valor=texto_seguro($_POST['valor'], $link);
	} else {
		$valor=0;
	}
	switch($accion) {
		case 'nuevo':
			if (isset($_POST['NombreLargo'])) {
				//$mensaje=tipo_mensaje('info', 'Segunda vez');
				
				// Obtenemos los datos que necesitamos para agregar el evento
				$nombreLargo=texto_seguro($_POST['NombreLargo'], $link);
				$nombreCorto=texto_seguro($_POST['NombreCorto'], $link);
				$Descripcion=texto_seguro($_POST['Descripcion'], $link);
				//echo $nombreLargo. ' ' . $nombreCorto . ' '.$Descripcion;
				
				// Corroboramos que tenemos los datos minimos para guardar en la tabla
				if (empty($nombreLargo)) {
					$mensaje=tipo_mensaje('error', 'Falta campo obligatorio. ');
				} else {
					// Tenemos los datos minimos, procedemos a guardar en la tabla.
					//$mensaje=tipo_mensaje('info', 'Todo bien, Guardamos en la tabla. ');
					$query="INSERT INTO Evento(NombreLargo, NombreCorto, Descripcion) VALUES('$nombreLargo', '$nombreCorto', '$Descripcion');";
					if (!($resultado = mysqli_query($link, $query))) {
						//Error en la consulta
						// error en la consulta
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);				
					} else {
						$mensaje=tipo_mensaje('info', 'Evento Agregado');
						$nombreLargo='';
						$nombreCorto='';
						$Descripcion='';
					}				
				}			
			}
			break;
		
		case 'editar':
			// Buscamos el elemento a editar
			//$valor=texto_seguro($_POST['valor'], $link);
			$query="SELECT IdEvento, NombreLargo, NombreCorto, Descripcion FROM Evento WHERE IdEvento='$valor';";
			if ($resultado = mysqli_query($link, $query)) {
				$row = mysqli_fetch_assoc($resultado);
				$nombreLargo=$row['NombreLargo'];
				$nombreCorto=$row['NombreCorto'];
				$Descripcion=$row['Descripcion'];
				$msgBoton='Guardar Cambios';
				$accion='actualizar';
			} else {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
			break;
		case 'actualizar':
			// Actualizamos los datos del registros
			$nombreLargo=texto_seguro($_POST['NombreLargo'], $link);
			$nombreCorto=texto_seguro($_POST['NombreCorto'], $link);
			$Descripcion=texto_seguro($_POST['Descripcion'], $link);
			$query="UPDATE Evento SET NombreLargo='$nombreLargo', NombreCorto='$nombreCorto', Descripcion='$Descripcion' WHERE IdEvento='$valor';";
			if ($resultado = mysqli_query($link, $query)) {				
				$msgBoton='Agregar Evento';
				$accion='nuevo';
				$nombreLargo='';
				$nombreCorto='';
				$Descripcion='';
				$valor='';
			} else {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
			break;
		case 'eliminar':
			// eliminamos un registro
			$query="DELETE FROM Evento WHERE IdEvento='$valor';";
			if ($resultado = mysqli_query($link, $query)) {				
				$msgBoton='Agregar Evento';
				$accion='nuevo';
				$nombreLargo='';
				$nombreCorto='';
				$Descripcion='';
				$valor='';
			} else {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
			break;
		case 'primero':		
		case 'anterior':
		case 'siguiente':
		case 'ultimo':
			$numPagina=$valor;
			$accion='nuevo';
			break;
	} // switch($accion) {
	
		// Determinamos el numero de registros que existen		
		$query="SELECT COUNT(IdEvento) AS NumRegistros FROM Evento $txtBuscar;";
		if (!($resultado = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		} else {
			$siguiente=1;
			$anterior=1;
			$primero=1;
			$ultimo=1;
			if (mysqli_num_rows($resultado)>0) {
				$row = mysqli_fetch_assoc($resultado);
				$numRegistros=$row['NumRegistros'];				
				if ($numRegistros>0) {
					$paginas=ceil($numRegistros/$registrosAMostrar);
					$actual=$numPagina;
					
					// siguiente
					$siguiente=$actual+1;
					if ($siguiente>$paginas){
						$siguiente=$actual;
					}
					
					// anterior
					$anterior=$actual-1;
					if ($anterior<1){
						$anterior=1;
					}
					
					// ultimo
					$ultimo=$paginas;
				}
			}
		}
	
		// Buscamos si hay elementos para mostrar
		$pagActual=$numPagina;
		$numPagina--;
		$numPagina=$numPagina*$registrosAMostrar;		
		$query="SELECT IdEvento, NombreLargo, NombreCorto, Descripcion FROM Evento $txtBuscar LIMIT $registrosAMostrar OFFSET $numPagina;";
		if (!($resultado = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		} else {
			if (mysqli_num_rows($resultado)<=0) {
				$tipo='advertencia';
				$mensaje=tipo_mensaje($tipo, 'No hay registros');
			}
		}
?>
<?php require 'include/doctype.php'; ?>
<title>Eventos - C.E.R.</title>
<?php require 'include/head.php'; ?>

<body>
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
  <div id="contenedor">  
  <?php require 'include/encabezado.php'; ?>
  <?php require 'include/menu.php'; ?>  
  <form name="formulario" id="formulario" method="post" action="<?php echo basename(__FILE__); ?>">
	<input name="accion" id="accion" type="hidden" value="<?php echo $accion; ?>">
	<input name="valor" id="valor" type="hidden" value="<?php echo $valor; ?>">
	<label for="NombreLargo"><span class="etiqueta alinear_arriba">Nombre Completo <span class="requerido">*</span> :</span><span class="campo"><textarea id="NombreLargo" name="NombreLargo" required ><?php echo $nombreLargo; ?></textarea></span></label>
	<label for="NombreCorto"><span class="etiqueta">Nombre Corto :</span><span class="campo"><input type="text" id="NombreCorto" name="NombreCorto" value="<?php echo $nombreCorto; ?>" /></span></label>
	<label for="Descripcion"><span class="etiqueta alinear_arriba">Descripci&oacute;n :</span><span class="campo"><textarea id="Descripcion" name="Descripcion" ><?php echo $Descripcion; ?></textarea></span></label>	
	<label><span class="etiqueta"><span class="requerido">*</span><span class="indicaciones"> Datos requeridos</span></span></label>	
	<div class="label1">
		<span class="etiqueta">&nbsp;</span>
		<span class="campo botones">
			<input class="small button" type="submit" value="<?php echo $msgBoton; ?>" />
			<?php if ($msgBoton=='Agregar Evento') { ?> 
			<input class="small button" type="reset" value="Limpiar Formulario" />
			<?php } else {?>
			<input class="small button" type="button" onclick="location.href='<?php echo basename(__FILE__); ?>'"; value="Nuevo Registro" />
			<?php } ?>
		</span>
	</div>
	<?php
		if (!empty($mensaje['texto'])){ 	?>
		<span class="mensaje alert <?php echo $mensaje['clase']; ?>"><?php echo $mensaje['texto']; ?></span>
	<?php
		}
	?>
  </form>
  <?php  
	if (mysqli_num_rows($resultado)>0) { ?>
   <table id="registros">
	  <tr>
		<th class="alinear_centro">No.</th>
		<th>Nombre</th>
		<th>N. Corto</th>
		<th>Descripci&oacute;n</th>
		<th>Acci&oacute;n</th>
	  </tr>		
		<?php
		// Mostramos la tabla con los resultados
		$inicio=($pagActual*$registrosAMostrar)-$registrosAMostrar;
		$fin=mysqli_num_rows($resultado)+$inicio;
		//for ($i=0; $i<(mysqli_num_rows($resultado)); $i++) {
		for ($i=$inicio; $i<$fin; $i++) {
		//for ($i=0; $i<(mysqli_num_rows($resultado)); $i++) {
			$row = mysqli_fetch_assoc($resultado);
			?>
	
	<tr >
	<td class="alinear_centro" ><?php echo $i+1; ?></td>
    <td ><?php echo $row['NombreLargo'];?></td>
    <td ><?php echo $row['NombreCorto'];?></td>
    <td ><?php echo $row['Descripcion'];?></td>
	<td>		
		<div class="acciones">
		<a href="#" onclick="enviar_accion('editar','<?php echo $row['IdEvento'];?>');">
			<img class="eliminar"src="img/edit.svg" alt="modificar" >
		</a> 
	
	<?php /*echo $row['IdEvento'];*/?>
	
		<a href="#" onclick="enviar_accion('eliminar','<?php echo $row['IdEvento'];?>');">
			<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
		</a> 
		</div>
	</td>
  </tr>
	
			<?php
		}
		?>
		<tr>
			<td colspan="5" class="alinear_centro"">
				<a href="#" onclick="enviar_accion('primero','<?php echo $primero; ?>');" ><img class="eliminar"src="img/previous.svg" alt="primero" ></a>&nbsp;&nbsp;
				<a href="#" onclick="enviar_accion('anterior','<?php echo $anterior; ?>');" ><img class="eliminar"src="img/backward.svg" alt="primero" ></a>&nbsp;&nbsp;<?php echo $pagActual.'/'.$ultimo; ?>&nbsp;&nbsp;
				<a href="#" onclick="enviar_accion('siguiente','<?php echo $siguiente; ?>');" ><img class="eliminar"src="img/play-next-button.svg" alt="primero" ></a>&nbsp;&nbsp;
				<a href="#" onclick="enviar_accion('ultimo','<?php echo $ultimo; ?>');" ><img class="eliminar"src="img/fast-forward-arrows.svg" alt="primero" ></a>
			</td>
		</tr>
		</table>
		<?php
		mysqli_free_result($resultado);
		
	} 
  ?>
  </div>
  <script src="js/main.js"></script>  
  
</body>
</html>

<?php
}
?>