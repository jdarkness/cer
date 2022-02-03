<?php
require 'include/funciones.php';
//var_dump($_SESSION);
var_dump($_POST);
if (empty($_SESSION["usuario"])) {
	// No hay sesion iniciado, mostramos la pagina de login
	//echo '<h2>Pagina de Login</h2>';
	header("Location: index.php");
	exit();
} else {
	// hay session iniciada, redirigimos a la pagina de paquetes
	//echo '<h2>Pagina de Lista de Paquetes</h2>';
	$link=conectar_a_bd();
	$idEstado='';
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
	$txtEstado='';
	$abrevEstado='';
	$visibleEstado='none';
	$visibleAbrev='none';
	$visiblelblMunicipio='block';
	$visiblelblLocalidad='block';
	$visibleEstadoAgregar='inline';
	$visibleEstadoGuardar='none';	

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
			//echo "estamos aqui en nuevo";
			if (isset($_POST['txtEstado']) && !empty($_POST['txtEstado']) && $valor=='estado') {
				//echo "Agregaremos un Estado";
				$claveEstado='';
				$nombreEstado=texto_seguro($_POST['txtEstado'], $link);
				$abrevEstado=texto_seguro($_POST['abrevEstado'], $link);
				$query="INSERT INTO estados(clave, nombre, abrev) 
										VALUES('$claveEstado', '$nombreEstado', '$abrevEstado');";
				//echo $query;
							if (!($resultado = mysqli_query($link, $query))) {
								//Error en la consulta						
								$texto=mysqli_error($link). ' ' . $query;
								$tipo='error';
								$mensaje=tipo_mensaje($tipo, $texto);				
							} else {
								$claveEstado=mysqli_insert_id($link);
								$query="UPDATE estados 
										    SET clave='$claveEstado'
										    WHERE id=$claveEstado;";
								if (!($resultado = mysqli_query($link, $query))) {
									//Error en la consulta						
									$texto=mysqli_error($link). ' ' . $query;
									$tipo='error';
									$mensaje=tipo_mensaje($tipo, $texto);
								}
								$mensaje=tipo_mensaje('info', 'Estado Agregado');
								$claveEstado='';
							 	$nombreEstado='';
								$abrevEstado='';
							}

			}
			if (isset($_POST['txtMunicipio']) && !empty($_POST['txtMunicipio']) && $valor=='municipio') {
				echo "Agregaremos un Municipio";
			}
			if (isset($_POST['txtLocalidad']) && !empty($_POST['txtLocalidad']) && $valor=='localidad') {
				echo "Agregaremos una localidad-";
				echo ($_POST['IdMunicipio']);
				//corroborar que este seleccionado algo o si no mandamos el error. si todo bien procedemos a agregar la localidad
			}
			break;
		
		case 'editar':
			// Buscamos el elemento a editar
			//echo "estamos aqui en editar";

			switch ($valor) {
				case 'estado':
					//echo "Editamos un Estado";
					$idEstado = texto_seguro($_POST['IdEstado'], $link);
					echo $query="SELECT nombre, abrev FROM estados WHERE id=$idEstado;";
					if ($resultado = mysqli_query($link, $query)) {
					$row = mysqli_fetch_assoc($resultado);
					$txtEstado=$row['nombre'];
					$abrevEstado=$row['abrev'];
					$visibleEstado='inline';
					$visibleAbrev='inline';
					$visiblelblMunicipio='none';
					$visiblelblLocalidad='none';
					$visibleEstadoAgregar='none';
					$visibleEstadoGuardar='inline';
					$accion='actualizar';
					//$msgBoton='Guardar Cambios';
					//$accion='actualizar';
					//echo "todo bien en la consulta";
					} else {
						//Error en la consulta		
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);				
					}
					break;
				case 'municipio':
					echo "Editamos un Municipio";
					break;
				case 'localidad':
					echo "Editamos un Localidad";
					break;
				}			
			break;
		case 'actualizar':
			// Actualizamos los datos del registros
		  switch ($valor) {
				case 'estado':
				echo "<br>Actualizamos datos<br>";				
				if (isset($_POST['txtEstado']) && !empty($_POST['txtEstado']) && isset($_POST['abrevEstado']) && !empty($_POST['abrevEstado']) && $valor=='estado') {
					echo "Actualizamos los datos de un Estado";
					$idEstado = texto_seguro($_POST['IdEstado'], $link);
					$nombreEstado=texto_seguro($_POST['txtEstado'], $link);
					$abrevEstado=texto_seguro($_POST['abrevEstado'], $link);
					$query="UPDATE estados SET nombre='$nombreEstado', abrev='$abrevEstado' WHERE id='$idEstado';";
					if (!$resultado = mysqli_query($link, $query)) {
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}
					$visibleEstado='none';
					$visibleAbrev='none';
					$visiblelblMunicipio='block';
					$visiblelblLocalidad='block';
					$visibleEstadoAgregar='inline';
					$visibleEstadoGuardar='none';
					$accion='';
					$idEstado='';
				} else { 
					$texto='Faltan datos obrigatorios';
					$tipo='error';
					$mensaje=tipo_mensaje($tipo, $texto);
				}
					break;				
			}




/*
			$numObra=texto_seguro($_POST['NoObra'], $link);
			$idEvento=texto_seguro($_POST['IdEvento'], $link);
			$numContrato=texto_seguro($_POST['NoContrato'], $link);
			//$nomMunicipio=texto_seguro($_POST['Municipio'], $link);
			$idMunicipio=texto_seguro($_POST['IdMunicipio'], $link);
			$idLocalidad=texto_seguro($_POST['IdLocalidad'], $link);
			//$nomLocalidad=texto_seguro($_POST['Localidad'], $link);
			$nomCentroTrabajo=texto_seguro($_POST['CentroTrabajo'], $link);
			$idContratista=texto_seguro($_POST['IdContratista'], $link);

////////////////////////////////////////////////
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

/////////////////////////////////////////////////
								
								//$query="UPDATE Obra SET NoObra='$numObra', IdEvento='$idEvento', IdMunicipio='$idMunicipio', IdLocalidad='$idLocalidad', NoContrato='$numContrato', CentroTrabajo='$nomCentroTrabajo', IdContratista='$idContratista' WHERE IdObra='$valor';";
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
							}
						}
					}		*/		
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
			$txtBuscar="WHERE localidades.nombre LIKE '%$buscar%'";
			$accion='nuevo';
			break;
	} // switch($accion) {
	
		// Determinamos el numero de registros que existen		
		$query="SELECT COUNT(id) AS NumRegistros FROM localidades $txtBuscar";
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
	$query="SELECT estados.nombre AS Estado, municipios.nombre AS Municipio, localidades.nombre AS Localidad, localidades.id AS IdLocalidad
							 FROM localidades
							 INNER JOIN municipios ON municipios.id = localidades.municipio_id
							 INNER JOIN estados ON estados.id = municipios.estado_id 
					     $txtBuscar 
							 LIMIT $registrosAMostrar OFFSET $numPagina;";
	
		// Buscamos si hay elementos para mostrar
		//$query='SELECT IdObra, NoObra, IdEvento, NoContrato, Municipio, Localidad, CentroTrabajo, IdContratista FROM Obra;';
		$pagActual=$numPagina;
		$numPagina--;
		$numPagina=$numPagina*$registrosAMostrar;		
		//$query="SELECT IdContratista, NombreLargo, NombreCorto FROM Contratista $txtBuscar LIMIT $registrosAMostrar OFFSET $numPagina ";		
		/*$query="SELECT IdObra, NoObra, municipios.nombre AS NomMunicipio, localidades.nombre AS NomLocalidad, NoContrato, CentroTrabajo, Contratista.NombreLargo AS NomContratista, Evento.NombreLargo AS NomEvento 
				FROM Obra 
				LEFT JOIN Contratista ON Obra.IdContratista = Contratista.IdContratista 
				LEFT JOIN Evento ON Obra.IdEvento = Evento.IdEvento 
				LEFT JOIN municipios ON Obra.IdMunicipio = municipios.id
				LEFT JOIN localidades ON Obra.IdLocalidad = localidades.id
				$txtBuscar 
				LIMIT $registrosAMostrar OFFSET $numPagina;";		*/
		if (!($resultado = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		}
		
		// Buscamos los estados
		$query='SELECT id, nombre FROM estados WHERE activo = 1 ORDER BY nombre ASC;';
		if (!($estados = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
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
	<label for="IdEstado" name="lblEstado" id="lblEstado"><span class="etiqueta alinear_arriba">Estado <span class="requerido">*</span> :</span>
		<span class="campo">			
			<select id="IdEstado" name="IdEstado" onchange="getData('estado',this.value);" style="display:<?php echo $visibleEstadoAgregar; ?>" >
		<?php
			if (mysqli_num_rows($estados)>0) {
				?>
				<option value="">Seleccione un Estado</option>
				<?php				
				for ($i=0; $i<(mysqli_num_rows($estados)); $i++) {
					$row = mysqli_fetch_assoc($estados);
				?>
				<option <?php if ($idEstado==$row['id']){?> selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
				<?php
				}
			} else { ?>
			 <option value="--">Agrege un Estado antes de continuar</option>
			<?php
			}
			?>
			</select>			
			<input name="txtEstado" id="txtEstado" type="text" value="<?php echo $txtEstado; ?>" placeholder="Nombre del Estado" style="display:<?php echo $visibleEstado; ?>">
			<input name="abrevEstado" id="abrevEstado" type="text" value="<?php echo $abrevEstado; ?>" placeholder="Abreviatura del Estado" style="display:<?php echo $visibleAbrev; ?>">
			
			<span name="botonesEstadoAgregar" id="botonesEstadoAgregar" style="display:<?php echo $visibleEstadoAgregar; ?>">
			<a name="btnAgregarEstado" id="btnAgregarEstado" href="#" onclick="visualizar_elemento('lblMunicipio','none','lblLocalidad','none','botonesEstadoAgregar','none', 'IdEstado','none', 'botonesEstadoGuardar','inline','txtEstado','inline','abrevEstado','inline');" >				
				<img class="eliminar" src="img/add.svg" alt="agregar" >
		  </a>
		  <a name="btnEditarEstado" id="btnEditarEstado" href="#" onclick="enviar_accion('editar','estado');">
				<img class="eliminar" src="img/edit.svg" alt="agregar" >
		  </a>		  
		  <a name="btnEliminarEstado" id="btnEliminarEstado" href="#" onclick="enviar_accion('eeeliminar','<?php echo $row['IdLocalidad'];?>');">
				<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
			</a>
			</span> 

			<span name="botonesEstadoGuardar" id="botonesEstadoGuardar" style="display:<?php echo $visibleEstadoGuardar; ?>">
		  <a name="btnOkEstado" id="btnOkEstado" href="#" onclick="enviar_accion('<?php echo $accion ?>','estado');" >
				<img class="eliminar" src="img/ok.svg" alt="bien" >
		  </a>
		  <a name="btnCancelarEstado" id="btnCancelarEstado" href="#" onclick="visualizar_elemento('lblMunicipio','block','lblLocalidad','block','botonesEstadoAgregar','inline', 'IdEstado','inline', 'botonesEstadoGuardar','none','txtEstado','none','abrevEstado','none');" >
				<img class="eliminar" src="img/cancel.svg" alt="bien" >
		  </a>
		  </span>

		</span></label>			
	<label for="IdMunicipio" name="lblMunicipio" id="lblMunicipio" style="display:<?php echo $visiblelblMunicipio; ?>"><span class="etiqueta">Municipio <span class="requerido">*</span> :</span>
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
			<input name="txtMunicipio" id="txtMunicipio" type="text" value="" style="display:none">			
			<a name="btnAgregarMunicipio" id="btnAgregarMunicipio" href="#" onclick="mostrar_agregar('btnAgregarMunicipio', 'IdMunicipio', 'btnEliminarMunicipio', 'btnOkMunicipio','btnCancelarMunicipio','txtMunicipio');" >
				<img class="eliminar" src="img/add.svg" alt="agregar" >
		  </a>
		  <a name="btnEliminarMunicipio" id="btnEliminarMunicipio" href="#" onclick="enviar_accion('eeeliminar','<?php echo $row['IdLocalidad'];?>');">
				<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
			</a> 
		  <a name="btnOkMunicipio" id="btnOkMunicipio" href="#" onclick="enviar_accion('nuevo', 'municipio');"  style="display:none">
				<img class="eliminar" src="img/ok.svg" alt="bien" >
		  </a>
		  <a name="btnCancelarMunicipio" id="btnCancelarMunicipio" href="#" onclick="ocultar_agregar('btnAgregarMunicipio', 'IdMunicipio', 'btnEliminarMunicipio', 'btnOkMunicipio','btnCancelarMunicipio','txtMunicipio');" style="display:none">
				<img class="eliminar" src="img/cancel.svg" alt="bien" >
		  </a>		
		</span>
	</label>
	<label for="IdLocalidad" name="lblLocalidad" id="lblLocalidad" style="display:<?php echo $visiblelblLocalidad; ?>"><span class="etiqueta">Localidad <span class="requerido">*</span> :</span>	
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
				/*
				var x = document.getElementById("mySelect").options.selectedIndex;
				var x = document.getElementById("mySelect");
				var i = x.selectedIndex;
				document.getElementById("demo").innerHTML = x.options[i].text; 
				<option value="Pi">Pineapple</option>
  			var x = document.getElementById("mySelect").selectedIndex;
  			document.getElementById("demo").innerHTML =  
  				document.getElementById("mySelect").options[x].value + " " + document.getElementById("mySelect").options[x].text;
					
					document.getElementById('IdMunicipio').options[document.getElementById('IdMunicipio').selectedIndex].value
				*/
				?>				
			</select>
			<input name="txtLocalidad" id="txtLocalidad" type="text" value="" style="display:none">			
			<a name="btnAgregarLocalidad" id="btnAgregarLocalidad" href="#" onclick="mostrar_agregar('btnAgregarLocalidad', 'IdLocalidad', 'btnEliminarLocalidad', 'btnOkLocalidad','btnCancelarLocalidad','txtLocalidad');" >
				<img class="eliminar" src="img/add.svg" alt="agregar" >
		  </a>
		  <a name="btnEliminarLocalidad" id="btnEliminarLocalidad" href="#" onclick="enviar_accion('eeeliminar','<?php echo $row['IdLocalidad'];?>');">
				<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
			</a> 
		  <a name="btnOkLocalidad" id="btnOkLocalidad" href="#" onclick="enviar_accion('nuevo', 'localidad');" style="display:none">
				<img class="eliminar" src="img/ok.svg" alt="bien" >
		  </a>
		  <a name="btnCancelarLocalidad" id="btnCancelarLocalidad" href="#" onclick="ocultar_agregar('btnAgregarLocalidad', 'IdLocalidad', 'btnEliminarLocalidad', 'btnOkLocalidad','btnCancelarLocalidad','txtLocalidad');" style="display:none">
				<img class="eliminar" src="img/cancel.svg" alt="bien" >
		  </a>
		</span>
	</label>	
	<label><span class="etiqueta"><span class="requerido">*</span><span class="indicaciones"> Datos requeridos</span></span></label>		
	<label><span class="etiqueta">&nbsp;</span><span class="campo">&nbsp;</span></label>	
	<label for="buscar">
		<span class="etiqueta">Buscar Localidad :</span>
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
		<th>Estado</th>
		<th>Municipio</th>
		<th>Localidad</th>								
		<th>Acci&oacute;n</th>
	  </tr>		
		<?php
		// Mostramos la tabla con los resultados $numPagina=$numPagina*$registrosAMostrar;
		$inicio=($pagActual*$registrosAMostrar)-$registrosAMostrar;
		$fin=mysqli_num_rows($resultado)+$inicio;		
		for ($i=$inicio; $i<$fin; $i++) {			
			$row = mysqli_fetch_assoc($resultado);			
			?>
	
	<tr >
	<td class="alinear_centro" ><?php echo $i+1; ?></td>
  <td ><?php echo $row['Estado'];?></td>    
	<td ><?php echo $row['Municipio'];?></td>
	<td ><?php echo $row['Localidad'];?></td>	
	<td>		
		<div class="acciones">
		<a href="#" onclick="enviar_accion('editar','<?php echo $row['IdLocalidad'];?>');">
			<img class="eliminar"src="img/edit.svg" alt="modificar" >
		</a> 
		<a href="#" onclick="enviar_accion('eliminar','<?php echo $row['IdLocalidad'];?>');">
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