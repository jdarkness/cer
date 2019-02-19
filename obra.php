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
	$idContratista='';
	$idEvento='';
	$idMunicipio='';
	$idLocalidad='';
	$numObra='';
	$numContrato='';
	$nomMunicipio='';
	$nomLocalidad='';
	$nomCentroTrabajo='';
	$msgBoton='Agregar Obra';
	$numPagina=1;	
	$buscar='';
	$txtBuscar='';
	$localidades='';
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
			if (isset($_POST['NoObra']) && isset($_POST['IdEvento'])) {
				//$mensaje=tipo_mensaje('info', 'Segunda vez');
				
				// Obtenemos los datos que necesitamos para agregar la obra
				$numObra=texto_seguro($_POST['NoObra'], $link);
				$idEvento=texto_seguro($_POST['IdEvento'], $link);
				$idMunicipio=texto_seguro($_POST['IdMunicipio'], $link);
				$idLocalidad=texto_seguro($_POST['IdLocalidad'], $link);
				$numContrato=texto_seguro($_POST['NoContrato'], $link);
				//$nomMunicipio=texto_seguro($_POST['Municipio'], $link);
				//$nomLocalidad=texto_seguro($_POST['Localidad'], $link);
				$nomCentroTrabajo=texto_seguro($_POST['CentroTrabajo'], $link);
				$idContratista=texto_seguro($_POST['IdContratista'], $link);
				
				// Corroboramos que tenemos los datos minimos para guardar en la tabla
				if (empty($numObra) || empty($idEvento)) {
					$mensaje=tipo_mensaje('error', 'Falta campo obligatorio. ');
				} else {
					// Corroboramos que no este dado de alta ese numero de obra en ese evento
					$query="SELECT IdObra FROM Obra WHERE NoObra = '$numObra' AND IdEvento = $idEvento;";
					if (!($resultado = mysqli_query($link, $query))) {
						//Error en la consulta						
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					} else {
						if (mysqli_num_rows($resultado)>0) {
							$mensaje=tipo_mensaje('advertencia', 'Ya existe la obra');
						} else {
							// La obra no existe asi que la agregamos
							// Tenemos los datos minimos, procedemos a guardar en la tabla.
							//$mensaje=tipo_mensaje('info', 'Todo bien, Guardamos en la tabla. ');
							/*$query="INSERT INTO Obra(NoObra, IdEvento, IdMunicipio, IdLocalidad, NoContrato, Municipio, Localidad, CentroTrabajo, IdContratista) 
										VALUES('$numObra', $idEvento, $idMunicipio, $idLocalidad, '$numContrato', '$nomMunicipio', '$nomLocalidad', '$nomCentroTrabajo', '$idContratista');";*/
							$query="INSERT INTO Obra(NoObra, IdEvento, IdMunicipio, IdLocalidad, NoContrato, CentroTrabajo, IdContratista) 
										VALUES('$numObra', $idEvento, $idMunicipio, $idLocalidad, '$numContrato', '$nomCentroTrabajo', '$idContratista');";
							if (!($resultado = mysqli_query($link, $query))) {
								//Error en la consulta						
								$texto=mysqli_error($link). ' ' . $query;
								$tipo='error';
								$mensaje=tipo_mensaje($tipo, $texto);				
							} else {
								$mensaje=tipo_mensaje('info', 'Obra Agregada');
								$numObra='';
								$idEvento='';
								$idMunicipio='';
								$idLocalidad='';
								$numContrato='';
								//$nomMunicipio='';
								//$nomLocalidad='';
								$nomCentroTrabajo='';
								$idContratista='';
							}				
							
						}
					}
				}			
			}
			break;
		
		case 'editar':
			// Buscamos el elemento a editar
			//$valor=texto_seguro($_POST['valor'], $link);
			//$query="SELECT IdObra, NoObra, IdEvento, NoContrato, IdMunicipio, Municipio, IdLocalidad, Localidad, CentroTrabajo, IdContratista FROM Obra WHERE IdObra=$valor;";
			$query="SELECT IdObra, NoObra, IdEvento, NoContrato, IdMunicipio, IdLocalidad, CentroTrabajo, IdContratista FROM Obra WHERE IdObra=$valor;";
			if ($resultado = mysqli_query($link, $query)) {
				$row = mysqli_fetch_assoc($resultado);
				$numObra=$row['NoObra'];
				$idEvento=$row['IdEvento'];
				$idMunicipio=$row['IdMunicipio'];
				$idLocalidad=$row['IdLocalidad'];
				$numContrato=$row['NoContrato'];
				//$nomMunicipio=$row['Municipio'];
				//$nomLocalidad=$row['Localidad'];
				$nomCentroTrabajo=$row['CentroTrabajo'];
				$idContratista=$row['IdContratista'];			
				$msgBoton='Guardar Cambios';
				$accion='actualizar';
			} else {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
			
			// Buscamos los nombres de las localidades correspondientes a ese municipio
			$query="SELECT id, nombre FROM localidades WHERE municipio_id ='$idMunicipio' ORDER BY nombre;";
			if ($localidades = mysqli_query($link, $query)) {			
			} else {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
			
			break;
		case 'actualizar':
			// Actualizamos los datos del registros	
			$idEvento=texto_seguro($_POST['IdEvento'], $link);
			$numContrato=texto_seguro($_POST['NoContrato'], $link);
			//$nomMunicipio=texto_seguro($_POST['Municipio'], $link);
			$idMunicipio=texto_seguro($_POST['IdMunicipio'], $link);
			$idLocalidad=texto_seguro($_POST['IdLocalidad'], $link);
			//$nomLocalidad=texto_seguro($_POST['Localidad'], $link);
			$nomCentroTrabajo=texto_seguro($_POST['CentroTrabajo'], $link);
			$idContratista=texto_seguro($_POST['IdContratista'], $link);
			//$query="UPDATE Obra SET IdEvento='$idEvento', IdMunicipio='$idMunicipio', IdLocalidad='$idLocalidad', NoContrato='$numContrato', Municipio='$nomMunicipio', Localidad='$nomLocalidad', CentroTrabajo='$nomCentroTrabajo', IdContratista='$idContratista' WHERE IdObra='$valor';";
			$query="UPDATE Obra SET IdEvento='$idEvento', IdMunicipio='$idMunicipio', IdLocalidad='$idLocalidad', NoContrato='$numContrato', CentroTrabajo='$nomCentroTrabajo', IdContratista='$idContratista' WHERE IdObra='$valor';";
			if ($resultado = mysqli_query($link, $query)) {				
				$msgBoton='Agregar Obra';
				$accion='nuevo';
				$numObra='';
				$idEvento='';
				$idMunicipio='';
				$idLocalidad='';
				$numContrato='';
				//$nomMunicipio='';
				//$nomLocalidad='';
				$nomCentroTrabajo='';
				$idContratista='';
				$valor='';
				$mensaje=tipo_mensaje('info', 'Registro Actualizado');
			} else {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
			break;
		case 'eliminar':
			// eliminamos un registro
			$query="DELETE FROM Obra WHERE IdObra='$valor';";
			if ($resultado = mysqli_query($link, $query)) {				
				$msgBoton='Agregar Obra';
				$accion='nuevo';
				$numObra='';
				$idEvento='';
				$idMunicipio='';
				$idLocalidad='';
				$numContrato='';
				//$nomMunicipio='';
				//$nomLocalidad='';
				$nomCentroTrabajo='';
				$idContratista='';
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
		case 'buscar':
			$buscar=texto_seguro($_POST['valor'], $link);
			$txtBuscar="WHERE NoObra LIKE '%$buscar%'";
			$accion='nuevo';
			break;
	} // switch($accion) {
	
		// Determinamos el numero de registros que existen
		//$query="SELECT COUNT(IdContratista) as NumRegistros FROM Contratista $txtBuscar";
		$query="SELECT COUNT(IdObra) AS NumRegistros FROM Obra $txtBuscar";
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
	//$query="SELECT IdObra, NoObra, NoContrato, Municipio, Localidad, CentroTrabajo, Contratista.NombreLargo AS NomContratista, Evento.NombreLargo AS NomEvento 
	$query="SELECT IdObra, NoObra, NoContrato, CentroTrabajo, Contratista.NombreLargo AS NomContratista, Evento.NombreLargo AS NomEvento 
				FROM Obra 
				LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista 
				LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento 
				$txtBuscar 
				LIMIT $registrosAMostrar OFFSET $numPagina;";
	
		// Buscamos si hay elementos para mostrar
		//$query='SELECT IdObra, NoObra, IdEvento, NoContrato, Municipio, Localidad, CentroTrabajo, IdContratista FROM Obra;';
		$pagActual=$numPagina;
		$numPagina--;
		$numPagina=$numPagina*$registrosAMostrar;		
		//$query="SELECT IdContratista, NombreLargo, NombreCorto FROM Contratista $txtBuscar LIMIT $registrosAMostrar OFFSET $numPagina ";		
		$query="SELECT IdObra, NoObra, municipios.nombre AS NomMunicipio, localidades.nombre AS NomLocalidad, NoContrato, CentroTrabajo, Contratista.NombreLargo AS NomContratista, Evento.NombreLargo AS NomEvento 
				FROM Obra 
				LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista 
				LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento 
				LEFT JOIN municipios ON Obra.IdMunicipio = municipios.id
				LEFT JOIN localidades ON Obra.IdLocalidad = localidades.id
				$txtBuscar 
				LIMIT $registrosAMostrar OFFSET $numPagina;";		
		if (!($resultado = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		}
		
		// Buscamos los eventos
		$query='SELECT IdEvento, NombreLargo, NombreCorto FROM Evento;';
		if (!($eventos = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		}
		
		// Buscamos los municipios // 7 es para chiapas
		$query="SELECT id, nombre FROM municipios WHERE estado_id = 7 ORDER BY nombre";
		if (!($municipios = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		} 
		
		// Buscamos a los contratistas
		$query='SELECT IdContratista, NombreLargo FROM Contratista ORDER BY NombreLargo ASC;';
		if (!($contratistas = mysqli_query($link, $query))) {
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
<title>Obras - C.E.R.</title>
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
	<label for="IdEvento"><span class="etiqueta">Evento <span class="requerido">*</span> :</span>
		<span class="campo">
			<select id="IdEvento" name="IdEvento" required >
		<?php
			if (mysqli_num_rows($eventos)>0) {
				?>
				<option value="">Seleccione un evento</option>
				<?php				
				for ($i=0; $i<(mysqli_num_rows($eventos)); $i++) {
					$row = mysqli_fetch_assoc($eventos);
					if (empty($row['NombreCorto'])) {
						$nombreEvento=$row['NombreLargo'];
					} else {
						$nombreEvento=$row['NombreCorto'];
					}
				?>
				<option <?php if ($idEvento==$row['IdEvento']){?> selected <?php } ?> value="<?php echo $row['IdEvento']; ?>"><?php echo $nombreEvento; ?></option>
				<?php
				}
			} else { ?>
			 <option value="--">Agrege un evento antes de continuar</option>
			<?php
			}
			?>
			</select>
		</span></label>	
	<label for="NoObra"><span class="etiqueta">Num. Obra <span class="requerido">*</span> :</span><span class="campo"><input type="text" id="NoObra" name="NoObra" value="<?php echo $numObra; ?>" required <?php if ($accion=='actualizar') { ?> readonly <?php } ?>/></span></label>
	<label for="NoContrato"><span class="etiqueta">Num. Contrato :</span><span class="campo"><input type="text" id="NoContrato" name="NoContrato" value="<?php echo $numContrato; ?>"  /></span></label>
	<label for="IdMunicipio"><span class="etiqueta">Municipio <span class="requerido">*</span> :</span>
		<span class="campo">
			<select id="IdMunicipio" name="IdMunicipio" onchange="getData('municipio',this.value);" >
				<?php
			if (mysqli_num_rows($municipios)>0) {
				?>
				<option value="">Seleccione un municipio</option>
				<?php				
				for ($i=0; $i<(mysqli_num_rows($municipios)); $i++) {
					$row = mysqli_fetch_assoc($municipios);
				?>
				<option <?php if ($idMunicipio==$row['id']){?> selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
				<?php
				}
			} else { ?>
			 <option value="">Agrege un municipio antes de continuar</option>
			<?php
			}
			?>
			</select>
		</span>
	</label>
	<!-- <label for="Municipio"><span class="etiqueta">Municipio :</span><span class="campo"><input type="text" id="Municipio" name="Municipio" value="<?php echo $nomMunicipio; ?>"  /></span></label> -->
	<!-- <label for="Localidad"><span class="etiqueta">Localidad :</span><span class="campo"><input type="text" id="Localidad" name="Localidad" value="<?php echo $nomLocalidad; ?>"  /></span></label> -->
	<label for="IdLocalidad"><span class="etiqueta">Localidad <span class="requerido">*</span> :</span>	
		<span class="campo">
			<select id="IdLocalidad" name="IdLocalidad" required >
				<option value="">Selecciona una localidad</option>
				<?php
				
					if (mysqli_num_rows($localidades)>0) {
						for ($i=0; $i<(mysqli_num_rows($localidades)); $i++) {
							$row = mysqli_fetch_assoc($localidades);
						
						?>
				<option <?php if ($idLocalidad==$row['id']){?> selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
				<?php
						}
				}
				?>
			</select>
		</span>
	</label>
	<label for="CentroTrabajo"><span class="etiqueta">Centro de Trabajo :</span><span class="campo"><input type="text" id="CentroTrabajo" name="CentroTrabajo" value="<?php echo $nomCentroTrabajo; ?>"  /></span></label>
	<label for="IdContratista"><span class="etiqueta">Contratista :</span>
		<span class="campo">
			<select id="IdContratista" name="IdContratista">
		<?php
			if (mysqli_num_rows($contratistas)>0) {
				?>
				<option value="">Seleccione un Contratista</option>
				<?php				
				for ($i=0; $i<(mysqli_num_rows($contratistas)); $i++) {
					$row = mysqli_fetch_assoc($contratistas);
				?>
				<option <?php if ($idContratista==$row['IdContratista']){?> selected <?php } ?>value="<?php echo $row['IdContratista']; ?>"><?php echo $row['NombreLargo']; ?></option>
				<?php
				}
			} else { ?>
			 <option value="--">Agrege un Contratista</option>
			<?php
			}
			?>
			</select>
		</span></label>	
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
			<input type="text" id="buscar" name="buscar" value="<?php echo $buscar; ?>" onkeypress="return enviarForm(event)" />
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
		<th>No. Obra</th>
		<th>No. Contrato</th>
		<th>Municipio</th>		
		<th>Localidad</th>
		<th>Nomb. Obra</th>		
		<th>Contratista</th>
		<th>Acci&oacute;n</th>
	  </tr>		
		<?php
		// Mostramos la tabla con los resultados $numPagina=$numPagina*$registrosAMostrar;
		$inicio=($pagActual*$registrosAMostrar)-$registrosAMostrar;
		$fin=mysqli_num_rows($resultado)+$inicio;
		//$inicio--;
		//echo 'inicio='.$inicio.' Fin='.$fin;
		//for ($i=0; $i<(mysqli_num_rows($resultado)); $i++) {
		for ($i=$inicio; $i<$fin; $i++) {
			$row = mysqli_fetch_assoc($resultado);
			?>
	
	<tr class="editar_registro" >
	<td class="alinear_centro" onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $i+1; ?></td>
	<td onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $row['NomEvento'];?></td>
    <td onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $row['NoObra'];?></td>
    <td onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $row['NoContrato'];?></td>    
	<td onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $row['NomMunicipio'];?></td>
	<td onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $row['NomLocalidad'];?></td>
	<td onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $row['CentroTrabajo'];?></td>
	<td onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');"><?php echo $row['NomContratista'];?></td>
	<td>		
		<div class="acciones">
		<a href="#" onclick="enviar_accion('editar','<?php echo $row['IdObra'];?>');">
			<img class="eliminar"src="img/edit.svg" alt="modificar" >
		</a> 
		<a href="#" onclick="enviar_accion('eliminar','<?php echo $row['IdObra'];?>');">
			<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
		</a> 
		</div>
	</td>
  </tr>
		<?php
		}
		?>
		<tr>
			<td colspan="9" class="alinear_centro">
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
  <script src="js/main.js?id=<?php echo rand(); ?>"></script>
</body>
</html>
<?php
}
?>