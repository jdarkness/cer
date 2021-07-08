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
	if (isset($_GET['idPaquete'])) {
		$idPaquete=texto_seguro($_GET['idPaquete'], $link);
	} else {
		$idPaquete='';	
	}	
	$numObra='';
	$numContrato='';
	$numEstimacion='';
	$montoEjercido='';
	$montoRetenciones='';
	$montoLiquido='';
	$observaciones='';
	$numOficio='';
	$fechaOficio='';
	$buscar='';
	$txtBuscar="WHERE 1 = 0 ";
	$msgBoton='Agregar Estimaci&oacute;n';
	
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
			if (isset($_POST['NoContrato']) && isset($_POST['IdPaquete'])) {
				//$mensaje=tipo_mensaje('info', 'Segunda vez');

				// Obtenemos los datos que necesitamos para agregar la estimacion				
				$idPaquete=texto_seguro($_POST['IdPaquete'], $link);
				$numObra=texto_seguro($_POST['NoObra'], $link);
				$numContrato=texto_seguro($_POST['NoContrato'], $link);
				$numEstimacion=texto_seguro($_POST['NumEstimacion'], $link);
				$montoEjercido=quitarSeparadorMiles(texto_seguro($_POST['MontoEjercido'], $link));
				$montoRetenciones=quitarSeparadorMiles(texto_seguro($_POST['MontoRetenciones'], $link));
				$montoLiquido=quitarSeparadorMiles(texto_seguro($_POST['MontoLiquido'], $link));
				$observaciones=texto_seguro($_POST['Observaciones'], $link);
				$numOficio=texto_seguro($_POST['NoOficio'], $link);
				$fechaOficio=texto_seguro($_POST['FechaOficio'], $link);
				
				// Corroboramos que tenemos los datos minimos para guardar en la tabla
				if (empty($numObra) || empty($numContrato) || empty($idPaquete) || empty($numEstimacion) || empty($montoEjercido) || empty($montoRetenciones) || empty($montoLiquido) ) {
					$mensaje=tipo_mensaje('error', 'Falta campo obligatorio. ');
				} else {
					// Verificamos que la fecha es valida si la hay
					if (!isDate($fechaOficio) && !empty($fechaOficio)) {
						// fecha invalida
						$mensaje=tipo_mensaje('error', 'Fecha Incorrecta.');
					} else {
						// Fecha correcta o no hay						
						// Corroboramos que no este dado de alta el registro en la tabla
						//echo $query="SELECT IdEstimacion FROM Estimacion WHERE NoContrato = '$numContrato' AND IdPaquete = $idPaquete AND NoEstimacion = '$numEstimacion';";
						$query="SELECT IdEstimacion FROM Estimacion INNER JOIN Obra ON Estimacion.IdObra = Obra.IdObra WHERE Estimacion.NoContrato = '$numContrato' AND Estimacion.IdPaquete = $idPaquete AND Estimacion.NoEstimacion = '$numEstimacion' AND Obra.NoObra = '$numObra';";
						if (!($resultado = mysqli_query($link, $query))) {
							//Error en la consulta						
							$texto=mysqli_error($link). ' ' . $query;
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);
						} else {
							if (mysqli_num_rows($resultado)>0) {
								$mensaje=tipo_mensaje('advertencia', 'Ya existe la estimaci&oacute;n en el paquete');
								$numContrato='';
								$numObra='';
								$numEstimacion='';
								$montoEjercido='';
								$montoRetenciones='';
								$montoLiquido='';
								$observaciones='';
								$numOficio='';
								$fechaOficio='';
							} else {
								// El registro no existe asi que la agregamos
								// Tenemos los datos minimos, procedemos a guardar en la tabla.
								//$mensaje=tipo_mensaje('info', 'Todo bien, Guardamos en la tabla. ');
								if (!empty($fechaOficio)){
									$fechaOficio=toSQLDate($fechaOficio);
								} else {
									$fechaOficio="0000-00-00";
								}
			
								// Buscamos su numero de obra
								$idObra='';
								//$noContrato=substr($numContrato, 0, 7);
								//$query="SELECT IdObra FROM Obra WHERE NoContrato LIKE '$noContrato%'";
								//$query="SELECT IdObra FROM Obra WHERE NoContrato LIKE '$numContrato%'";
								$query="SELECT IdObra FROM Obra WHERE NoContrato = '$numContrato' AND NoObra = '$numObra';";								
								if (!($resultado = mysqli_query($link, $query))) {
									//Error en la consulta						
									$texto=mysqli_error($link). ' ' . $query;
									$tipo='error';
									$mensaje=tipo_mensaje($tipo, $texto);				
								} else {
									if (mysqli_num_rows($resultado)>0) {
										$row = mysqli_fetch_assoc($resultado);
										$idObra=$row['IdObra'];
										$query="INSERT INTO Estimacion(IdPaquete, IdObra, NoContrato, NoEstimacion, NoOficio, FechaOficio, MontoEjercido, MontoRetenciones, MontoLiquido, Observaciones) 
											VALUES('$idPaquete', '$idObra', '$numContrato', '$numEstimacion', '$numOficio', '$fechaOficio', '$montoEjercido', '$montoRetenciones', '$montoLiquido','$observaciones');";
										if (!($resultado = mysqli_query($link, $query))) {
											//Error en la consulta						
											$texto=mysqli_error($link). ' ' . $query;
											$tipo='error';
											$mensaje=tipo_mensaje($tipo, $texto);				
										} else {
											$mensaje=tipo_mensaje('info', 'Estimaci&oacute;n Agregada');
											//$idPaquete='';
											$numContrato='';
											$numObra='';
											$numEstimacion='';
											$montoEjercido='';
											$montoRetenciones='';
											$montoLiquido='';
											$observaciones='';
											$numOficio='';
											$fechaOficio='';
										}
									} else {
										// No existe la obra, no esta dada de alta
										$mensaje=tipo_mensaje('advertencia', 'No existe el n&uacute;mero de Obra o Contrato');
										if ($fechaOficio=="0000-00-00") { $fechaOficio=''; } // Eliminamos los 0000-00-00 que le pusimos en caso de estar vacio										 
									}
								}	
							}
						}
					}
				}			
			}
			break;
		
		case 'editar':
			// Buscamos el elemento a editar
			//$valor=texto_seguro($_POST['valor'], $link);
		  $query="SELECT Estimacion.IdEstimacion, Estimacion.NoContrato, Obra.NoObra, Estimacion.IdPaquete, Estimacion.NoEstimacion, Estimacion.NoOficio, Estimacion.FechaOficio, Estimacion.MontoEjercido, Estimacion.MontoRetenciones, Estimacion.MontoLiquido, Estimacion.Observaciones  FROM Estimacion LEFT JOIN Obra ON Obra.IdObra = Estimacion.IdObra	WHERE Estimacion.IdEstimacion=$valor;";
			//echo $query="SELECT IdEstimacion, NoContrato, IdPaquete, NoEstimacion, NoOficio, FechaOficio, MontoEjercido, MontoRetenciones, MontoLiquido, Observaciones FROM Estimacion WHERE IdEstimacion=$valor;";
			if ($resultado = mysqli_query($link, $query)) {
				$row = mysqli_fetch_assoc($resultado);
				$idPaquete=$row['IdPaquete'];
				$numContrato=$row['NoContrato'];
				$numObra=$row['NoObra'];
				$numEstimacion=$row['NoEstimacion'];
				$montoEjercido=number_format($row['MontoEjercido'], 2, '.', ',');
				$montoRetenciones=number_format($row['MontoRetenciones'], 2, '.', ',');
				$montoLiquido=number_format($row['MontoLiquido'], 2, '.', ',');
				$observaciones=$row['Observaciones'];
				$numOficio=$row['NoOficio'];
				if (isDate($row['FechaOficio'])){
					$fechaOficio=toHTMLDate($row['FechaOficio'],'-');
				} else {
					$fechaOficio='';
				}				
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
			// Obtenemos los datos que necesitamos para actualizar, no se puede modificar el paquete al que pertenece				
			$idPaquete=texto_seguro($_POST['IdPaquete'], $link);
			$numContrato=texto_seguro($_POST['NoContrato'], $link);
			$numObra=texto_seguro($_POST['NoObra'], $link);
			$numEstimacion=texto_seguro($_POST['NumEstimacion'], $link);
			$montoEjercido=quitarSeparadorMiles(texto_seguro($_POST['MontoEjercido'], $link));			
			$montoRetenciones=quitarSeparadorMiles(texto_seguro($_POST['MontoRetenciones'], $link));
			$montoLiquido=quitarSeparadorMiles(texto_seguro($_POST['MontoLiquido'], $link));
			$observaciones=texto_seguro($_POST['Observaciones'], $link);
			$numOficio=texto_seguro($_POST['NoOficio'], $link);
			$fechaOficio=texto_seguro($_POST['FechaOficio'], $link);
			
			// Corroboramos que tenemos los datos minimos para guardar en la tabla
			if (empty($numContrato) || empty($idPaquete) || empty($numEstimacion) || empty($montoEjercido) || empty($montoRetenciones) || empty($montoLiquido) ) {
				$mensaje=tipo_mensaje('error', 'Falta campo obligatorio. ');
			} else {
				// Verificamos que la fecha es valida si la hay
				if (!isDate($fechaOficio) && !empty($fechaOficio)) {
					// fecha invalida
					$mensaje=tipo_mensaje('error', 'Fecha Incorrecta.');
				} else {
					// Fecha correcta o no hay
					if (!empty($fechaOficio)){
						$fechaOficio=toSQLDate($fechaOficio);
					} else {
						$fechaOficio="0000-00-00";
					}
					// Buscamos su numero de obra
					$idObra='';
					$noContrato=substr($numContrato, 0, 7);
					//echo $query="SELECT IdObra FROM Obra WHERE NoContrato LIKE '$noContrato%'";
					$query="SELECT IdObra FROM Obra WHERE NoObra LIKE '$numObra'";
					if (!($resultado = mysqli_query($link, $query))) {
						//Error en la consulta						
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);				
					} else {
						if (mysqli_num_rows($resultado)>0) {
							$row = mysqli_fetch_assoc($resultado);
							$idObra=$row['IdObra'];
							// Todo bien actualizamos los datos
							$query="UPDATE Estimacion SET IdPaquete='$idPaquete', IdObra='$idObra', NoContrato='$numContrato', NoEstimacion='$numEstimacion', MontoEjercido='$montoEjercido', MontoRetenciones='$montoRetenciones', MontoLiquido='$montoLiquido', NoOficio='$numOficio', FechaOficio='$fechaOficio', Observaciones='$observaciones' WHERE IdEstimacion='$valor';";
							if ($resultado = mysqli_query($link, $query)) {				
								$msgBoton='Agregar Estimacion';
								$accion='nuevo';
								$numContrato='';
								$numObra='';
								$numEstimacion='';
								$montoEjercido='';
								$montoRetenciones='';
								$montoLiquido='';
								$observaciones='';
								$numOficio='';
								$fechaOficio='';
								$mensaje=tipo_mensaje('info', 'Registro Actualizado');
							} else {
								//Error en la consulta		
								$texto=mysqli_error($link). ' ' . $query;
								$tipo='error';
								$mensaje=tipo_mensaje($tipo, $texto);				
							}
						} else {							
							$mensaje=tipo_mensaje("error", "No existe el Numero de Obra");
						}
					}					
				}
			}			
			break;
		case 'eliminar':
			// eliminamos un registro
			$query="DELETE FROM Estimacion WHERE IdEstimacion='$valor';";
			if ($resultado = mysqli_query($link, $query)) {				
				$msgBoton='Agregar Estimaci&oacute;n';
				$accion='nuevo';
				$idPaquete=texto_seguro($_POST['IdPaquete'], $link);
				$numContrato='';
				$numObra='';
				$numEstimacion='';
				$montoEjercido='';
				$montoRetenciones='';
				$montoLiquido='';
				$observaciones='';
				$numOficio='';
				$fechaOficio='';
			} else {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
			break;
		case 'buscar':
			$buscar=texto_seguro($_POST['valor'], $link);
			$txtBuscar="WHERE Estimacion.NoContrato LIKE '%$buscar%'";
			$accion='nuevo';
			break;
	} // switch($accion) {
	
	
		
		// Buscamos si hay elementos para mostrar que sean del paquete que acaba de agregar
		if (!empty($idPaquete)) {
			//$query="SELECT Estimacion.IdEstimacion, Evento.NombreCorto, Estimacion.NoContrato, NoEstimacion, MontoRetenciones, MontoLiquido, MontoEjercido, Observaciones, Obra.NoContrato AS NumContrato, Obra.Municipio, Obra.CentroTrabajo, Contratista.NombreLargo FROM Estimacion LEFT JOIN Obra ON Estimacion.IdObra = Obra.IdObra LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento WHERE Estimacion.IdPaquete = '$idPaquete'";
			$query="SELECT Estimacion.IdEstimacion, Evento.NombreCorto, Estimacion.NoContrato, NoEstimacion, MontoRetenciones, MontoLiquido, MontoEjercido, Observaciones, Obra.NoContrato AS NumContrato, Obra.NoObra, municipios.nombre AS NomMunicipio, Obra.CentroTrabajo, Contratista.NombreLargo 
				    FROM Estimacion 
					LEFT JOIN Obra ON Estimacion.IdObra = Obra.IdObra 
					LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista 
					LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento 
					LEFT JOIN municipios ON Obra.IdMunicipio = municipios.id 
					WHERE Estimacion.IdPaquete = '$idPaquete'";			
			if (!($resultado = mysqli_query($link, $query))) {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
		} else {
			//$query='SELECT IdEstimacion FROM Estimacion WHERE 1 = 0;';
			$query="SELECT Estimacion.IdEstimacion, Evento.NombreCorto, Estimacion.NoContrato, NoEstimacion, MontoRetenciones, MontoLiquido, MontoEjercido, Observaciones, Obra.NoContrato AS NumContrato, Obra.NoObra, municipios.nombre AS NomMunicipio, Obra.CentroTrabajo, Contratista.NombreLargo 
			        FROM Estimacion 
					LEFT JOIN Obra ON Estimacion.IdObra = Obra.IdObra 
					LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista 
					LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento 
					LEFT JOIN municipios ON Obra.IdMunicipio = municipios.id 
					$txtBuscar";
			/*$query="SELECT Estimacion.IdEstimacion, Evento.NombreCorto, Estimacion.NoContrato, NoEstimacion, MontoRetenciones, MontoLiquido, MontoEjercido, Observaciones, Obra.NoContrato AS NumContrato, Obra.Municipio, Obra.CentroTrabajo, Contratista.NombreLargo 
				    FROM Estimacion 
					LEFT JOIN Obra ON Estimacion.IdObra = Obra.IdObra 
					LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista 
					LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento 
					$txtBuscar";			*/
			if (!($resultado = mysqli_query($link, $query))) {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			} else {
				if (mysqli_num_rows($resultado)<=0 && $txtBuscar!='WHERE 1 = 0 ') {
				$tipo='advertencia';
				$mensaje=tipo_mensaje($tipo, 'No hay registros');			
				}
			}
		}
		// Buscamos los paquetes
		$query="SELECT IdPaquete, FechaRecepcion, QuienEntrego FROM Paquete WHERE FechaTermino IS NULL OR IdPaquete = '$idPaquete' ORDER BY IdPaquete;";
		if (!($paquetes = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		}		
	
?>
<?php require 'include/doctype.php'; ?>
<title>Estimaciones - C.E.R.</title>
<?php require 'include/head.php'; ?>

<body>
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
  <div id="contenedor">  
  <?php require 'include/encabezado.php'; ?>
  <?php require 'include/menu.php'; ?>  
  <form name="formulario" id="formulario" method="post" action="<?php echo basename(__FILE__); ?>" >	
	<input name="accion" id="accion" type="hidden" value="<?php echo $accion; ?>">
	<input name="valor" id="valor" type="hidden" value="<?php echo $valor; ?>">
	<label for="IdPaquete"><span class="etiqueta">Num. Paquete <span class="requerido">*</span> :</span>
		<span class="campo"><!-- required -->
			<select id="IdPaquete" name="IdPaquete" required>
		<?php
			if (mysqli_num_rows($paquetes)>0) {
				?>
				<option value="">Seleccione un paquete</option>
				<?php				
				for ($i=0; $i<(mysqli_num_rows($paquetes)); $i++) {
					$row = mysqli_fetch_assoc($paquetes);					
				?>
				<option <?php if ($idPaquete==$row['IdPaquete']){?> selected <?php } ?> value="<?php echo $row['IdPaquete']; ?>"><?php echo $row['IdPaquete'].' - '.$row['QuienEntrego'].' ('.$row['FechaRecepcion'].')'; ?></option>
				<?php
				}
			} else { ?>
			 <option value="--">Agrege un paquete antes de continuar</option>
			<?php
			}
			?>
			</select>
		</span></label>
	<label for="NoObra"><span class="etiqueta">Num. Obra <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="NoObra" name="NoObra" value="<?php echo $numObra; ?>" onkeypress="siguienteInput(event,'NumContrato')" required /></span></label>	
	<label for="NoContrato"><span class="etiqueta">Num. Contrato <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="NoContrato" name="NoContrato" value="<?php echo $numContrato; ?>" onkeypress="siguienteInput(event,'NumEstimacion')" required /></span></label>
	<label for="NumEstimacion"><span class="etiqueta">Num. Estimaci&oacute;n <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="NumEstimacion" name="NumEstimacion" value="<?php echo $numEstimacion; ?>" onkeypress="siguienteInput(event,'MontoEjercido')" required /></span></label>
	<label for="MontoEjercido"><span class="etiqueta">Monto Ejercido <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="MontoEjercido" name="MontoEjercido" value="<?php echo $montoEjercido; ?>" onfocusout="if (this.value.indexOf(',')==-1){this.value=separarMiles(this.value);}" required /></span></label>
	<label for="MontoRetenciones"><span class="etiqueta">Monto Retenciones <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="MontoRetenciones" name="MontoRetenciones" value="<?php echo $montoRetenciones; ?>" onfocusout="if (this.value.indexOf(',')==-1){this.value=separarMiles(this.value);}" required /></span></label>
	<label for="MontoLiquido"><span class="etiqueta">Monto L&iacute;quido <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="MontoLiquido" name="MontoLiquido" value="<?php echo $montoLiquido; ?>" onfocus="this.value=separarMiles(restaMontoLiquido(document.getElementById('MontoEjercido').value,document.getElementById('MontoRetenciones').value));" onkeypress="siguienteInput(event,'NoOficio')" required /></span></label>
	<label for="NoOficio"><span class="etiqueta">Num. Oficio :</span><span class="campo"><input type="text" id="NoOficio" name="NoOficio" value="<?php echo $numOficio; ?>" onkeypress="siguienteInput(event,'FechaOficio')" /></span></label>
	<label for="FechaOficio"><span class="etiqueta">Fecha Oficio :</span><span class="campo"><input type="text" id="FechaOficio" name="FechaOficio" value="<?php echo $fechaOficio; ?>" onkeypress="siguienteInput(event,'Observaciones')" /></span></label>
	<label for="Observaciones"><span class="etiqueta alinear_arriba">Observaciones :</span><span class="campo"><textarea id="Observaciones" name="Observaciones" ><?php echo $observaciones; ?></textarea></span></label>		
	<label><span class="etiqueta"><span class="requerido">*</span><span class="indicaciones"> Datos requeridos</span></span></label>	
	<div class="label1">
		<span class="etiqueta">&nbsp;</span>
		<span class="campo botones">
			<input class="small button" type="submit" value="<?php echo $msgBoton; ?>" />
			<?php if ($msgBoton=='Agregar Obra') { ?> 
			<input class="small button" type="reset" value="Limpiar Formulario" />
			<?php } else {?>
			<input class="small button" type="button" onclick="location.href='<?php echo basename(__FILE__); ?>'"; value="Nuevo Registro" />
			<?php } ?>
		</span>
	</div>
	<label><span class="etiqueta">&nbsp;</span><span class="campo">&nbsp;</span></label>	
	<label for="buscar">
		<span class="etiqueta">Buscar Num. Obra :</span>
		<span class="campo">
			<input type="text" id="buscar" name="buscar" value="<?php echo $buscar; ?>" onkeypress="return enviarForm(event);"/>
			<a href="#" onclick="enviar_accion('buscar',document.getElementById('buscar').value);">
				<img class="eliminar" src="img/magnifier.svg" alt="buscar" >
			</a>
		</span>
	</label>
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
		<th>No.</th>
		<th>Evento</th>
		<th>Contrato</th>
		<th>Contratista</th>
		<th>Nomb. Obra</th>
		<th>Municipio</th>		
		<th>No. Estimaci&oacute;n</th>
		<th>Retenciones</th>
		<th>L&iacute;quido</th>
		<th>Ejercido</th>
		<th>Observaciones</th>
		<th>Acci&oacute;n</th>
	  </tr>		
		<?php
		// Mostramos la tabla con los resultados
		for ($i=0; $i<(mysqli_num_rows($resultado)); $i++) {
			$row = mysqli_fetch_assoc($resultado);
			?>
	
	<tr >
	<td class="alinear_centro" ><?php echo $i+1; ?></td>
	<td ><?php echo $row['NombreCorto'];?></td>
	<td class="alinear_centro" ><?php echo $row['NoContrato']."<BR />(".$row['NoObra'].")";?></td>
    <td ><?php echo $row['NombreLargo'];?></td>
    <td ><?php echo $row['CentroTrabajo'];?></td>    
	<td class="alinear_centro" ><?php echo $row['NomMunicipio'];?></td>
	<td class="alinear_derecha" ><?php echo $row['NoEstimacion'];?></td>
	<td class="alinear_derecha" ><?php echo number_format($row['MontoRetenciones'], 2, '.', ',');?></td>
	<td class="alinear_derecha" ><?php echo number_format($row['MontoLiquido'], 2, '.', ',');?></td>
	<td class="alinear_derecha" ><?php echo number_format($row['MontoEjercido'], 2, '.', ',');?></td>
	<td ><?php echo $row['Observaciones'];?></td>
	<td>		
		<div class="acciones">
		<a href="#" onclick="enviar_accion('editar','<?php echo $row['IdEstimacion'];?>');">
			<img class="eliminar"src="img/edit.svg" alt="modificar" >
		</a> 
		<a href="#" onclick="enviar_accion('eliminar','<?php echo $row['IdEstimacion'];?>');">
			<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
		</a> 
		</div>
	</td>
  </tr>
		<?php
		}
		?>
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

<?php
/*

var separador = document.getElementById('separadorMiles');

separador.addEventListener('input', (e) => {
    var entrada = e.target.value.split(','),
      parteEntera = entrada[0].replace(/\./g, ''),
      parteDecimal = entrada[1],
      salida = parteEntera.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
      
    e.target.value = salida + (parteDecimal !== undefined ? ',' + parteDecimal : '');
}, false);

<input type="text" name="inversion" placeholder="$10.000.000" class="form-control" id="separadorMiles" required><br>


var monto = document.querySelector('input');
var options = {
	style:'currency',
  currency:'ARS',
  minimumFractionDigits:0
};
monto.addEventListener('input',function(){
  // Si el último caracter es una coma, deberemos agregarla
  var separador = (this.value.substr(this.value.length - 1,1)===',')?',':''; 
	var monto1 = this.value
  .replace(/[^\d,]/g,"") //remover $ punto y otros caracteres no validos
  .replace(",","."); // JavaScript usa punto como separador decimal para los números,entonces reemplazamos la coma por un punto
  this.value = Intl.NumberFormat('es-AR',options).format(monto1)
  + separador; // Si el último caracter es una coma, agregarlo.
});

<input type="text" placeholder="$ 10.000">


https://stackoverrun.com/es/q/566790
// 2056776401.50 = 2,056,776,401.50 
function humanizeNumber(n) { 
    n = n.toString() 
    while (true) { 
    var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, '$1,$2$3') 
    if (n == n2) break 
    n = n2 
    } 
    return n 
} 


function format(n, sep, decimals) { 
    sep = sep || "."; // Default to period as decimal separator 
    decimals = decimals || 2; // Default to 2 decimals 

    return n.toLocaleString().split(sep)[0] 
     + sep 
     + n.toFixed(decimals).split(sep)[1]; 
} 

format(4567354.677623); // 4,567,354.68 

También podría sondear separador decimal de la configuración regional con:

var sep = (0).toFixed(1)[1]; 


*/

?>
