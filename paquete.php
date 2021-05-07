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
	$numPaquete='';
	$fechaRecepcion=date("d/m/Y");
	$quienEntrego='';
	$fechaTermino='';	
	$txtBuscar='';
	$numPagina=1;
	$msgBoton='Agregar Paquete';
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
			if (isset($_POST['FechaRecepcion'])) {
				//$mensaje=tipo_mensaje('info', 'Segunda vez');
				
				// Obtenemos los datos que necesitamos para agregar el evento
				$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
				$quienEntrego=texto_seguro($_POST['QuienEntrego'], $link);
				$fechaTermino=texto_seguro($_POST['FechaTermino'], $link);
				//echo $nombreLargo. ' ' . $nombreCorto . ' '.$Descripcion;
				
				// Corroboramos que tenemos los datos minimos para guardar en la tabla
				if (empty($fechaRecepcion) || empty($quienEntrego)) {
					$mensaje=tipo_mensaje('error', 'Falta campo obligatorio. ');
				} else {
					// Tenemos los datos minimos, procedemos a guardar en la tabla.
					//$mensaje=tipo_mensaje('info', 'Todo bien, Guardamos en la tabla. ');
					
					// Validamos que el valor sea una fecha					
					if (!isDate($fechaRecepcion)){
						$mensaje=tipo_mensaje('error', 'Fecha Incorrecta.');
						$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
					} else {
						
						// Si hay fecha de termino la validamos sino procedemos a guardar
						if (!empty($fechaTermino)){
							// Validamos la fecha de termino antes de guardarla
							if (!isDate($fechaTermino)) {							
								$mensaje=tipo_mensaje('error', 'Fecha Incorrecta.');
								$fechaTermino=texto_seguro($_POST['fechaTermino'], $link);
							} else {
								//DATE - format YYYY-MM-DD.
								$fechaRecepcion=toSQLDate($fechaRecepcion);
								$fechaTermino=toSQLDate($fechaTermino);
								$query="INSERT INTO Paquete(FechaRecepcion, FechaTermino, QuienEntrego) VALUES('$fechaRecepcion', '$fechaTermino', '$quienEntrego');";
								if (!($resultado = mysqli_query($link, $query))) {
									// Error en la consulta									
									$texto=mysqli_error($link). ' ' . $query;
									$tipo='error';
									$mensaje=tipo_mensaje($tipo, $texto);				
								} else {
									$mensaje=tipo_mensaje('info', 'Evento Agregado');
									$fechaRecepcion=date("d/m/Y");
									$quienEntrego='';
									$fechaTermino='';
								}
							}
						} else {
							//DATE - format YYYY-MM-DD.
							$fechaRecepcion=toSQLDate($fechaRecepcion);
							$query="INSERT INTO Paquete(FechaRecepcion, QuienEntrego) VALUES('$fechaRecepcion', '$quienEntrego');";
							if (!($resultado = mysqli_query($link, $query))) {
								// Error en la consulta									
								$texto=mysqli_error($link). ' ' . $query;
								$tipo='error';
								$mensaje=tipo_mensaje($tipo, $texto);				
							} else {
								$mensaje=tipo_mensaje('info', 'Evento Agregado');
								$fechaRecepcion=date("d/m/Y");
								$quienEntrego='';
								$fechaTermino='';
							}
						}
					}
				}
			}
			break;
		
		case 'editar':
			// Buscamos el elemento a editar
			//$valor=texto_seguro($_POST['valor'], $link);
			$query="SELECT IdPaquete, FechaRecepcion, FechaTermino, QuienEntrego FROM Paquete WHERE IdPaquete='$valor';";
			if ($resultado = mysqli_query($link, $query)) {
				$row = mysqli_fetch_assoc($resultado);
				$numPaquete=$row['IdPaquete'];
				// Format view 12/11/2018   ---  //DATE - format YYYY-MM-DD.				
				$fechaRecepcion=toHTMLDate($row['FechaRecepcion']);
				$fechaTermino='';
				if (!empty($row['FechaTermino'])) {					
					$fechaTermino=toHTMLDate($row['FechaTermino']);
				}
				$quienEntrego=$row['QuienEntrego'];
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
			$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
			$quienEntrego=texto_seguro($_POST['QuienEntrego'], $link);
			$fechaTermino=texto_seguro($_POST['FechaTermino'], $link);
			
			// Corroboramos que tenemos los datos minimos para guardar en la tabla
			if (empty($fechaRecepcion) || empty($quienEntrego)) {
				$mensaje=tipo_mensaje('error', 'Falta campo obligatorio. ');
			} else {
				// Tenemos los datos minimos, procedemos a guardar en la tabla.				
				// Validamos que el valor sea una fecha				
				if (!isDate($fechaRecepcion)){
					$mensaje=tipo_mensaje('error', 'Fecha Incorrecta.');
					$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
					$numPaquete=texto_seguro($_POST['NumPaquete'], $link);
					$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
					$quienEntrego=texto_seguro($_POST['QuienEntrego'], $link);
					$fechaTermino=texto_seguro($_POST['FechaTermino'], $link);
					$msgBoton='Guardar Cambios';								
				} else {
					// Validamos la fecha de termino antes de guardarla
					if (!empty($fechaTermino)) {
						if (!isDate($fechaTermino)) {
							$mensaje=tipo_mensaje('error', 'Fecha Incorrecta.');
							$fechaTermino=texto_seguro($_POST['FechaTermino'], $link);
							$numPaquete=texto_seguro($_POST['NumPaquete'], $link);
							$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
							$quienEntrego=texto_seguro($_POST['QuienEntrego'], $link);
							$fechaTermino=texto_seguro($_POST['FechaTermino'], $link);
							$msgBoton='Guardar Cambios';								
						} else {
							//DATE - format YYYY-MM-DD.
							$fechaRecepcion=toSQLDate($fechaRecepcion);
							$fechaTermino=toSQLDate($fechaTermino);
							$query="UPDATE Paquete SET FechaRecepcion='$fechaRecepcion', FechaTermino='$fechaTermino', QuienEntrego='$quienEntrego' WHERE IdPaquete='$valor';";
							if (!($resultado = mysqli_query($link, $query))) {
								// Error en la consulta									
								$texto=mysqli_error($link). ' ' . $query;
								$tipo='error';
								$mensaje=tipo_mensaje($tipo, $texto);
								$numPaquete=texto_seguro($_POST['NumPaquete'], $link);
								$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
								$quienEntrego=texto_seguro($_POST['QuienEntrego'], $link);
								$fechaTermino=texto_seguro($_POST['FechaTermino'], $link);
								$msgBoton='Guardar Cambios';								
							} else {
								$mensaje=tipo_mensaje('info', 'Paquete Actualizado');
								$fechaRecepcion=date("d/m/Y");
								$quienEntrego='';
								$fechaTermino='';
								$accion='nuevo';
								$valor=0;
							}
						}
					} else {
						//DATE - format YYYY-MM-DD.
						$fechaRecepcion=toSQLDate($fechaRecepcion);
						$query="UPDATE Paquete SET FechaRecepcion='$fechaRecepcion', FechaTermino=NULL, QuienEntrego='$quienEntrego' WHERE IdPaquete='$valor';";
						if (!($resultado = mysqli_query($link, $query))) {
							// Error en la consulta									
							$texto=mysqli_error($link). ' ' . $query;
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);
							$numPaquete=texto_seguro($_POST['NumPaquete'], $link);
							$fechaRecepcion=texto_seguro($_POST['FechaRecepcion'], $link);
							$quienEntrego=texto_seguro($_POST['QuienEntrego'], $link);
							$fechaTermino=texto_seguro($_POST['FechaTermino'], $link);								
						} else {
							$mensaje=tipo_mensaje('info', 'Paquete Actualizado');
							$fechaRecepcion=date("d/m/Y");
							$quienEntrego='';
							$fechaTermino='';
							$accion='nuevo';
							$valor=0;
						}
					}
				}
			}
			break;
		case 'eliminar':
			// eliminamos un registro
			$query="DELETE FROM Paquete WHERE IdPaquete='$valor';";
			if ($resultado = mysqli_query($link, $query)) {				
				$msgBoton='Agregar Evento';
				$accion='nuevo';								
				$valor='';
				$fechaRecepcion=date("d/m/Y");
				$quienEntrego='';
				$fechaTermino='';
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
			break;
	} // switch($accion) {
	
		
		// Determinamos el numero de registros que existen
		//$query="SELECT COUNT(IdContratista) as NumRegistros FROM Contratista $txtBuscar";
		$query="SELECT COUNT(IdPaquete) AS NumRegistros FROM Paquete $txtBuscar ORDER BY FechaRecepcion, IdPaquete DESC;";
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
		//$query="SELECT IdContratista, NombreLargo, NombreCorto FROM Contratista $txtBuscar LIMIT $registrosAMostrar OFFSET $numPagina ";
		$query="SELECT IdPaquete, FechaRecepcion, FechaTermino, QuienEntrego FROM Paquete $txtBuscar ORDER BY FechaRecepcion DESC, IdPaquete DESC LIMIT $registrosAMostrar OFFSET $numPagina;";
		if (!($resultado = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		} 
	
?>
<?php require 'include/doctype.php'; ?>
<title>Paquetes - C.E.R.</title>
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
	<?php
		if ($accion=='actualizar') { ?> 	
	<label for="NumPaquete"><span class="etiqueta">Paquete N&uacute;mero :</span><span class="campo"><input type="text" id="NumPaquete" name="NumPaquete" value="<?php echo $numPaquete; ?>" /></span></label>
		<?php } ?>
	<label for="FechaRecepcion"><span class="etiqueta">Fecha de Recepcion <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="FechaRecepcion" name="FechaRecepcion" value="<?php echo $fechaRecepcion; ?>" required /></span></label>
	<label for="QuienEntrego"><span class="etiqueta">Quien Entrega <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="QuienEntrego" name="QuienEntrego" value="<?php echo $quienEntrego; ?>" required /></span></label>
	<label for="FechaTermino"><span class="etiqueta">Fecha Termino :</span><span class="campo"><input type="text" id="FechaTermino" name="FechaTermino" value="<?php echo $fechaTermino; ?>" /></span></label>
	<label><span class="etiqueta"><span class="requerido">*</span><span class="indicaciones"> Datos requeridos</span></span></label>	
	<div class="label1">
		<span class="etiqueta">&nbsp;</span>
		<span class="campo botones">
			<input class="small button" type="submit" value="<?php echo $msgBoton; ?>" />
			<?php if ($msgBoton=='Agregar Paquete') { ?> 
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
		<th class="alinear_centro">#</th>
		<th class="alinear_centro">No. Paquete</th>
		<th>Fecha Recepcion</th>
		<th>Quien Entrega</th>
		<th>Fecha Termino</th>
		<th>Acci&oacute;n</th>
	  </tr>		
		<?php
		// Mostramos la tabla con los resultados
		$inicio=($pagActual*$registrosAMostrar)-$registrosAMostrar;
		$fin=mysqli_num_rows($resultado)+$inicio;
		for ($i=0; $i<(mysqli_num_rows($resultado)); $i++) {
		//for ($i=$inicio; $i<$fin; $i++) {
			$row = mysqli_fetch_assoc($resultado);			
			?>
	
	<tr >
	<td class="alinear_centro" ><?php echo $i+1; ?></td>
	<td class="alinear_centro" ><?php echo $row['IdPaquete']; ?></td>
    <td ><?php echo toHTMLDate($row['FechaRecepcion'],'-');?></td>
    <td ><?php echo $row['QuienEntrego'];?></td>
    <td ><?php echo empty($row['FechaTermino']) ? '': toHTMLDate($row['FechaTermino'],'-'); ?></td>
	<td>		
		<div class="acciones">
		<a href="#" onclick="enviar_accion('editar','<?php echo $row['IdPaquete'];?>');">
			<img class="eliminar"src="img/edit.svg" alt="editar" >
		</a>
		<a href="#" onclick="enviar_accion('eliminar','<?php echo $row['IdPaquete'];?>');">
			<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
		</a>
		<a href="estimacion.php?idPaquete=<?php echo $row['IdPaquete']; ?>" >
			<img class="eliminar" src="img/ojo.svg" alt="ver" >
		</a> 		
		<a href="reporte_paquete.php?idPaquete=<?php echo $row['IdPaquete']; ?>" target="_blank">
			<img class="eliminar" src="img/impresora.svg" alt="imprimir" >
		</a>		
		</div>
	</td>
  </tr>
	
			<?php
		}
		?>
		<tr>
			<td colspan="6" class="alinear_centro"">
				<a href="#" onclick="enviar_accion('primero','<?php echo $primero; ?>');" ><img class="eliminar"src="img/previous.svg" alt="primero" ></a>&nbsp;&nbsp;
				<a href="#" onclick="enviar_accion('anterior','<?php echo $anterior; ?>');" ><img class="eliminar"src="img/backward.svg" alt="anterior" ></a>&nbsp;&nbsp;<?php echo $pagActual.'/'.$ultimo; ?>&nbsp;&nbsp;
				<a href="#" onclick="enviar_accion('siguiente','<?php echo $siguiente; ?>');" ><img class="eliminar"src="img/play-next-button.svg" alt="siguiente" ></a>&nbsp;&nbsp;
				<a href="#" onclick="enviar_accion('ultimo','<?php echo $ultimo; ?>');" ><img class="eliminar"src="img/fast-forward-arrows.svg" alt="ultimo" ></a>
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