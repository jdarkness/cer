<?php
/*echo "<ul>				
					<li>Que ponga agrege un estado, municipio, localidad si no tiene nada </li>				
        </ul>";*/
require 'include/funciones.php';
//var_dump($_SESSION);
if (isset($_POST) and !empty($_POST)) {	
	//var_dump($_POST);	
}

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
	if (isset($_POST['IdEstado']) and !empty($_POST['IdEstado'])) {	
		$idEstado=texto_seguro($_POST['IdEstado'], $link);
	}

	$idMunicipio='';
	if (isset($_POST['IdMunicipio']) and !empty($_POST['IdMunicipio'])) {	
		$idMunicipio=texto_seguro($_POST['IdMunicipio'], $link);
	}

	$idLocalidad='';
	if (isset($_POST['IdLocalidad']) and !empty($_POST['IdLocalidad'])) {	
		$idLocalidad=texto_seguro($_POST['IdLocalidad'], $link);
	}
	
	$txtMunicipio='';
	if (isset($_POST['txtMunicipio']) and !empty($_POST['txtMunicipio'])) {	
		$txtMunicipio=texto_seguro($_POST['txtMunicipio'], $link);
	}

	$txtLocalidad='';
	if (isset($_POST['txtLocalidad']) and !empty($_POST['txtLocalidad'])) {	
		$txtLocalidad=texto_seguro($_POST['txtLocalidad'], $link);
	}


	$nombreEstado='';
	$abrevEstado='';


	
	$numPagina=1;	
	$buscar='';
	$txtBuscar='';
	$localidades='';
	$txtEstado='';
	$abrevEstado='';
	$visibleEstado='none';
	$visibleMunicipio='none';
	$visibleLocalidad='none';
	$visibleAbrev='none';
	$visiblelblMunicipio='block';
	$visiblelblLocalidad='block';
	$visibleEstadoAgregar='inline';
	$visibleEstadoGuardar='none';
	$visibleMunicipioAgregar='inline';
	$visibleMunicipioGuardar='none';
	$visibleLocalidadAgregar='inline';
	$visibleLocalidadGuardar='none';
	$mostrarMunicipios='no';
	$mostrarLocalidades='no';


	if (isset($_POST['accion'])) {
		$accion=texto_seguro($_POST['accion'], $link);
	} else {
		$accion='nuevo';
	}
	if (isset($_POST['valor'])) {
		$valor=texto_seguro($_POST['valor'], $link);
	} else {
		$valor=''; //tenia cero pero se ejecutaba el primer case del switch
	}
	switch($accion) {
		case 'nuevo':
			//echo "estamos aqui en nuevo";

		//echo "valor='$valor'<br>";
		switch ($valor) {
				case "estado":
						//echo "Agregaremos un Estado";
						$claveEstado='';
						if (isset($_POST['txtEstado'])) {
							$nombreEstado=texto_seguro($_POST['txtEstado'], $link);	
						} else {
							$nombreEstado='';
						}
						
						if (isset($_POST['abrevEstado'])) {
							$abrevEstado=substr(texto_seguro($_POST['abrevEstado'], $link),0, 10);	
						} else {
							$abrevEstado='';
						}
						
						//$abrevEstado=texto_seguro($_POST['abrevEstado'], $link);
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
										$nombreEstado='';
										$idEstado='';
										$accion='';
									}					
			  break; // case "estado":
			  
			  case "municipio":
			  	//echo "Agregaremos un Municipio";
			  	// Verificamos que tenemos seleccionado un estado para agregar el municipio
			  	if (!empty($idEstado)) {
			  		// Continuamos con el proceso de alta de municipio		  		
			  		// Validamos que tenemos algo que agregar
			  		if (!empty($txtMunicipio)){
			  			//Agregamos el municipio
			  			/*
				  		Vamos a agregar
				  		estado_id, clave, nombre
				  		estado_id  es el que este seleccionado
				  		clave es el consecutivo de ese municipio
				  		nombre es el nombre que le daremos al municipio
				  		*/
				  		//Buscamos el numero consecutivo para la clave del municipio
				  		$query="SELECT clave FROM municipios WHERE estado_id = $idEstado ORDER BY clave DESC";
				  		if ($resultado = mysqli_query($link, $query)) {
								$row = mysqli_fetch_assoc($resultado);
								//var_dump($row);
								//echo "Hola ".$row['clave']."-";
								if (empty($row['clave'])) {
									$row['clave']=1;
								} else { //if (empty($row['clave']) {
									$row['clave']=$row['clave']+1;
								} // else { //if (empty($row['clave']) {

								// Construimos la query para insercion del municipio
								$clave=sprintf("%'.03d", $row['clave']);
								$query="INSERT INTO municipios(estado_id, clave, nombre) VALUES ($idEstado,'$clave', '$txtMunicipio')";
								if (!$resultado = mysqli_query($link, $query)) {
									$texto=mysqli_error($link). ' ' . $query;
									$mensaje=tipo_mensaje('error', $texto);
								}
							} else {  //if ($resultado = mysqli_query($link, $query)) {
								// Error en la consulta
								$texto=mysqli_error($link). ' ' . $query;
								$mensaje=tipo_mensaje('error', $texto);
							} // else {  //if ($resultado = mysqli_query($link, $query)) {
			  		} else {  //if (!empty($txtMunicipio)){
			  			$mensaje=tipo_mensaje('error', 'Escriba un nombre para el Municipio');
			  		}
			  	} else {  //if (!empty($idEstado)) {
			  		$mensaje=tipo_mensaje('error', 'Seleccione un Estado');
			  	}  // else {  //if (!empty($idEstado)) {
			  		$idEstado='';
					  $accion='nuevo';
					  $txtMunicipio='';
			  	break; // case "municipio":

			  case "localidad":
			  	//echo "Agregaremos una Localidad";
			    if (!empty($idEstado)) {
			    	$idMunicipio = texto_seguro($_POST['IdMunicipio'], $link);
			    	if (!empty($idMunicipio)) {
			    		$txtLocalidad=texto_seguro($_POST['txtLocalidad'], $link);
			    		if (!empty($txtLocalidad)) {
			    			//Agregamos la localidad
			    			$query="INSERT INTO localidades(municipio_id, nombre) VALUES ($idMunicipio, '$txtLocalidad')";
			    			if (!$resultado = mysqli_query($link, $query)) {
									$texto=mysqli_error($link). ' ' . $query;
									$mensaje=tipo_mensaje('error', $texto);
								}
								$txtLocalidad='';
								$idEstado='';
			    		} else { //if (!empty($txtLocalidad)) {
			    			$accion='nuevo';
			    			$valor='';
			    			$mensaje=tipo_mensaje('error', 'Escriba el nombre de la Localidad');
			    		}
			    	} else { //if (!empty($idMunicipio)) {
			    		$accion='nuevo';
			    		$valor='';
			    		$mensaje=tipo_mensaje('error', 'Seleccione un Municipio');
			    	}
			    } else { //if (!empty($idEstado)) {
			    	$mensaje=tipo_mensaje('error', 'Seleccione un Estado');
			    	$accion='nuevo';
			    	$valor='';
			    }
			  	break; // case "localidad": de agregar

			}

			  

		break; // Case Nuevo
		
		case 'editar':
			// Buscamos el elemento a editar
			//echo "estamos aqui en editar";

			switch ($valor) {
				case 'estado':
					//echo "Editamos un Estado";
					$idEstado = texto_seguro($_POST['IdEstado'], $link);
					$query="SELECT nombre, abrev FROM estados WHERE id=$idEstado;";
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
					//echo "Editamos un Municipio";
					$idMunicipio = texto_seguro($_POST['IdMunicipio'], $link);
					$query="SELECT nombre FROM municipios WHERE id=$idMunicipio;";
					if ($resultado = mysqli_query($link, $query)) {
						$row = mysqli_fetch_assoc($resultado);
						$txtMunicipio=$row['nombre'];
						$visibleMunicipio='inline';
						$visiblelblLocalidad='none';
						$visibleMunicipioAgregar='none';
						$visibleMunicipioGuardar='inline';
						$mostrarMunicipios='si';
						$accion='actualizar';						
						} else {
							//Error en la consulta		
							$texto=mysqli_error($link). ' ' . $query;
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);				
						}
					break;
				case 'localidad':
					//echo "Editamos un Localidad";
					$idLocalidad = texto_seguro($_POST['IdLocalidad'], $link);
					$query="SELECT nombre FROM localidades WHERE id=$idLocalidad;";
					if ($resultado = mysqli_query($link, $query)) {
						$row = mysqli_fetch_assoc($resultado);
						$txtLocalidad=$row['nombre'];
						$visibleLocalidad='inline';
						//$visiblelblLocalidad='none';
						$visibleLocalidadAgregar='none';
						$visibleLocalidadGuardar='inline';
						$mostrarMunicipios='si';
						$mostrarLocalidades='si';
						$accion='actualizar';						
						} else {
							//Error en la consulta		
							$texto=mysqli_error($link). ' ' . $query;
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);				
						}
					break;
				}			
			break;
		case 'actualizar':
			// Actualizamos los datos del registros
		  switch ($valor) {
				case 'estado':
				//echo "<br>Actualizamos datos<br>";				
				if (isset($_POST['txtEstado']) && !empty($_POST['txtEstado']) && isset($_POST['abrevEstado']) && !empty($_POST['abrevEstado']) && $valor=='estado') {
					//echo "Actualizamos los datos de un Estado";
					$idEstado = texto_seguro($_POST['IdEstado'], $link);
					$nombreEstado=texto_seguro($_POST['txtEstado'], $link);
					$abrevEstado=substr(texto_seguro($_POST['abrevEstado'], $link),0, 10);
					//$abrevEstado=texto_seguro($_POST['abrevEstado'], $link);
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
				case 'municipio':
					//echo "Actualizamos un Municipio";
					if (isset($_POST['txtMunicipio']) && !empty($_POST['txtMunicipio']) && $valor=='municipio') {
					//echo "Actualizamos los datos de un Municipio";
					$idMunicipio = texto_seguro($_POST['IdMunicipio'], $link);
					$nombreMunicipio=texto_seguro($_POST['txtMunicipio'], $link);					
					$query="UPDATE municipios SET nombre='$nombreMunicipio' WHERE id='$idMunicipio';";
					if (!$resultado = mysqli_query($link, $query)) {
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}
					$visibleMunicipio='none';					
					$visiblelblMunicipio='block';
					$visiblelblLocalidad='block';
					$visibleMunicipioAgregar='inline';
					$visibleMunicipioGuardar='none';

					$mostrarMunicipios='no';
					$mostrarLocalidades='no';

					$accion='';
					$idEstado = texto_seguro($_POST['IdEstado'], $link);
					$idEstado='';

					} else { 
						$texto='Faltan datos obligatorios';
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}
					break;
				case 'localidad':
					//echo "Actualizamos una Localidad";
					if (isset($_POST['txtLocalidad']) && !empty($_POST['txtLocalidad']) && $valor=='localidad') {
					//echo "Actualizamos los datos de una Localidad";
					$idLocalidad = texto_seguro($_POST['IdLocalidad'], $link);
					$nombreLocalidad=texto_seguro($_POST['txtLocalidad'], $link);					
					$query="UPDATE localidades SET nombre='$nombreLocalidad' WHERE id='$idLocalidad';";
					if (!$resultado = mysqli_query($link, $query)) {
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}

					$mostrarMunicipios='no';
					$mostrarLocalidades='no';

					$visibleEstadoAgregar='inline';
					$visibleEstadoGuardar='none';
					$visibleMunicipio='none';					
					$visiblelblMunicipio='block';
					$visiblelblLocalidad='block';
					$visibleMunicipioAgregar='inline';
					$visibleMunicipioGuardar='none';

					$accion='';
					$idEstado = texto_seguro($_POST['IdEstado'], $link);
					$idMunicipio = texto_seguro($_POST['IdMunicipio'], $link);					
					$idEstado = '';
					$idMunicipio = '';
					} else { 
						$texto='Faltan datos obligatorios';
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}
					break;				
			}		
			break;


		case 'eliminar':
			// eliminamos un registro
		  switch ($valor) {
		  	case 'estado':
		  		//echo "borramos un estado y todos sus municipios y a su vez todas las localidades de todos sus municipios";
		  		//Primero buscamos los municipios de ese estado
		  		$query="SELECT id FROM municipios WHERE estado_id='$idEstado';";
		  		if (!$municipios = mysqli_query($link, $query)) {
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					} else { //Query de busqueda de los municipios del estado
						//var_dump($resultado);
						if (mysqli_num_rows($municipios)>0) {
							// hay municipios que pertenecen al estado
							for ($i=0; $i<(mysqli_num_rows($municipios)); $i++) {
								$municipio = mysqli_fetch_assoc($municipios);
								// buscamos las localidades de cada municipio
								$query="SELECT id FROM localidades WHERE municipio_id='".$municipio['id']."'; ";
								if (!$localidades = mysqli_query($link, $query)) {
									$texto=mysqli_error($link). ' ' . $query;
									$tipo='error';
									$mensaje=tipo_mensaje($tipo, $texto);
								} else {  //if (!$localidades = mysqli_query($link, $query)) {
									if (mysqli_num_rows($localidades)>0) {
										// Borramos las localidades de cada municipio
										for ($j=0; $j<(mysqli_num_rows($localidades)); $j++) {
											$localidad = mysqli_fetch_assoc($localidades);
											$query="DELETE FROM localidades WHERE id='".$localidad['id']."'; ";
											if (!$resultado = mysqli_query($link, $query)) {
												$texto=mysqli_error($link). ' ' . $query;
												$tipo='error';
												$mensaje=tipo_mensaje($tipo, $texto);
											}
										}
									}
									//Borramos el municipio
									$query="DELETE FROM municipios WHERE id='".$municipio['id']."'; ";
									if (!$resultado = mysqli_query($link, $query)) {
											$texto=mysqli_error($link). ' ' . $query;
											$tipo='error';
											$mensaje=tipo_mensaje($tipo, $texto);
									}
								}
							} //for ($i=0; $i<(mysqli_num_rows($municipios)); $i++) {
						} 
						// Borramos el estado no tiene municipios
						$query="DELETE FROM estados WHERE id='$idEstado';";
						if (!$resultado = mysqli_query($link, $query)) {
								$texto=mysqli_error($link). ' ' . $query;
								$tipo='error';
								$mensaje=tipo_mensaje($tipo, $texto);
						}						
					}
					$accion='';
					$idEstado ='';
					$idMunicipio ='';
					$municipios='';
					$localidades='';
		  	break;

		  	case 'municipio':
			  	//echo "borramos un municipio y todas sus localidades";
			  	//echo "borramos todas sus localidades";
			  	$idMunicipio = texto_seguro($_POST['IdMunicipio'], $link);		  	
			  	$query="DELETE FROM localidades WHERE municipio_id='$idMunicipio';";
					if (!$resultado = mysqli_query($link, $query)) {
							$texto=mysqli_error($link). ' ' . $query;
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);
					}
					$query="DELETE FROM municipios WHERE id='$idMunicipio';";
					if (!$resultado = mysqli_query($link, $query)) {
							$texto=mysqli_error($link). ' ' . $query;
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);
					}
					$idEstado='';
					$idMunicipio='';
					$idLocalidad='';

		  	break;

		  	case 'localidad':
		  		//echo "borramos una localidad";
		  		$idLocalidad = texto_seguro($_POST['IdLocalidad'], $link);
					$query="DELETE FROM localidades WHERE id='$idLocalidad';";
					if (!$resultado = mysqli_query($link, $query)) {
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}					
					$visibleEstadoAgregar='inline';
					$visibleEstadoGuardar='none';
					$visibleMunicipio='none';					
					$visiblelblMunicipio='block';
					$visiblelblLocalidad='block';
					$visibleMunicipioAgregar='inline';
					$visibleMunicipioGuardar='none';
					$accion='';
					$idEstado='';
					$idMunicipio='';					
		  	break;

		  }

			/*
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
			}*/
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

		// Buscamos los municipios, en caso de existir una edicion
		//echo "::$idMunicipio::";
		//var_dump($idMunicipio);
		//var_dump($accion);
		//var_dump($valor);
		if ($mostrarMunicipios=='si') {
			$query="SELECT id, nombre FROM municipios WHERE estado_id = $idEstado ORDER BY nombre ASC;";
			if (!($municipios = mysqli_query($link, $query))) {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
			}
		}

		if ($mostrarLocalidades=='si') {
			$query="SELECT id, nombre FROM localidades WHERE municipio_id = $idMunicipio ORDER BY nombre ASC;";
			if (!($localidades = mysqli_query($link, $query))) {
				//Error en la consulta		
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);				
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
			 <option value="--">Seleccione un Estado</option>
			<?php
			}
			?>
			</select>			
			<input name="txtEstado" id="txtEstado" type="text" value="<?php echo $txtEstado; ?>" placeholder="Nombre del Estado" style="display:<?php echo $visibleEstado; ?>">
			<input name="abrevEstado" id="abrevEstado" type="text" value="<?php echo $abrevEstado; ?>" placeholder="Abreviatura del Estado" maxlength="10" style="display:<?php echo $visibleAbrev; ?>">
			
			<span name="botonesEstadoAgregar" id="botonesEstadoAgregar" style="display:<?php echo $visibleEstadoAgregar; ?>">
			<a name="btnAgregarEstado" id="btnAgregarEstado" href="#" onclick="visualizar_elemento('lblMunicipio','none','lblLocalidad','none','botonesEstadoAgregar','none', 'IdEstado','none', 'botonesEstadoGuardar','inline','txtEstado','inline','abrevEstado','inline');" >				
				<img class="eliminar" src="img/add.svg" alt="agregar" >
		  </a>
		  <a name="btnEditarEstado" id="btnEditarEstado" href="#" onclick="if (document.getElementById('IdEstado').selectedIndex==0) {alert('Seleccione un Estado')} else {enviar_accion('editar','estado')};">
				<img class="eliminar" src="img/edit.svg" alt="agregar" >
		  </a>		  
		  <a name="btnEliminarEstado" id="btnEliminarEstado" href="#" onclick="if (confirm('Se borraran todos los muncipios y localidades del Estado') == true) { enviar_accion('eliminar','estado')};">
				<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
			</a>
			</span> 

			<span name="botonesEstadoGuardar" id="botonesEstadoGuardar" style="display:<?php echo $visibleEstadoGuardar; ?>">
		  <a name="btnOkEstado" id="btnOkEstado" href="#" onclick="enviar_accion('<?php echo $accion ?>','estado');" >
				<img class="eliminar" src="img/ok.svg" alt="bien" >
		  </a>
		  <a name="btnCancelarEstado" id="btnCancelarEstado" href="#" onclick="document.getElementById('accion').value='nuevo';document.getElementById('txtEstado').value='';document.getElementById('abrevEstado').value='';visualizar_elemento('lblMunicipio','block','lblLocalidad','block','botonesEstadoAgregar','inline', 'IdEstado','inline', 'botonesEstadoGuardar','none','txtEstado','none','abrevEstado','none');" >
				<img class="eliminar" src="img/cancel.svg" alt="bien" >
		  </a>
		  </span>

		</span></label>			
	<label for="IdMunicipio" name="lblMunicipio" id="lblMunicipio" style="display:<?php echo $visiblelblMunicipio; ?>"><span class="etiqueta">Municipio <span class="requerido">*</span> :</span>
		<span class="campo">
			<select id="IdMunicipio" name="IdMunicipio" onchange="getData('municipio',this.value);" style="display:<?php echo $visibleMunicipioAgregar; ?>">
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
			 <option value="">Seleccione un Municipio</option>
			<?php
			}
			?>
			</select>
			<input name="txtMunicipio" id="txtMunicipio" type="text" value="<?php echo $txtMunicipio; ?>" placeholder="Nombre del Municipio" style="display:<?php echo $visibleMunicipio; ?>">
			
			<span name="botonesMunicipioAgregar" id="botonesMunicipioAgregar" style="display:<?php echo $visibleMunicipioAgregar; ?>">
			<a name="btnAgregarMunicipio" id="btnAgregarMunicipio" href="#" onclick="visualizar_elemento('lblLocalidad','none','botonesMunicipioAgregar','none', 'botonesMunicipioGuardar','inline', 'txtMunicipio','inline', 'IdMunicipio', 'none');" >
				<img class="eliminar" src="img/add.svg" alt="agregar" >
		  </a>
		  <a name="btnEditarMunicipio" id="btnEditarMunicipio" href="#" onclick="if (document.getElementById('IdMunicipio').selectedIndex==0) {alert('Seleccione un Municipio')} else {enviar_accion('editar','municipio')};">
				<img class="eliminar" src="img/edit.svg" alt="agregar" >
		  </a>
		  <a name="btnEliminarMunicipio" id="btnEliminarMunicipio" href="#" onclick="if (confirm('Se borraran todas las localidades del municipio') == true) { enviar_accion('eliminar','municipio')};">
				<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
			</a>
		</span>

		<span name="botonesMuncipioGuardar" id="botonesMunicipioGuardar" style="display:<?php echo $visibleMunicipioGuardar; ?>">
		  <a name="btnOkMunicipio" id="btnOkMunicipio" href="#" onclick="enviar_accion('<?php echo $accion ?>', 'municipio');" >
				<img class="eliminar" src="img/ok.svg" alt="bien" >
		  </a>
		  <a name="btnCancelarMunicipio" id="btnCancelarMunicipio" href="#" onclick="document.getElementById('accion').value='nuevo';document.getElementById('txtMunicipio').value='';visualizar_elemento('IdMunicipio','inline','lblMunicipio','block','lblLocalidad','block','botonesMunicipioAgregar','inline', 'botonesMunicipioGuardar','none','txtMunicipio','none');" >
				<img class="eliminar" src="img/cancel.svg" alt="bien" >
		  </a>
		 </span>
		</span>
	</label>
	<label for="IdLocalidad" name="lblLocalidad" id="lblLocalidad" style="display:<?php echo $visiblelblLocalidad; ?>"><span class="etiqueta">Localidad <span class="requerido">*</span> :</span>	
		<span class="campo">
			<select id="IdLocalidad" name="IdLocalidad" style="display:<?php echo $visibleLocalidadAgregar; ?>">

				<?php				
					if (mysqli_num_rows($localidades)>0) {
					?>
				<option value="">Seleccione una Localidad</option>
				<?php					
						for ($i=0; $i<(mysqli_num_rows($localidades)); $i++) {
							$row = mysqli_fetch_assoc($localidades);						
						?>
				<option <?php if ($idLocalidad==$row['id']){?> selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
				<?php
						}
				} else { ?>
				<option value="">Seleccione una localidad</option>							
				<?php
				}
				?>				
			</select>
			<input name="txtLocalidad" id="txtLocalidad" type="text" value="<?php echo $txtLocalidad; ?>" placeholder="Nombre de la Localidad" style="display:<?php echo $visibleLocalidad; ?>">

			<span name="botonesLocalidadAgregar" id="botonesLocalidadAgregar" style="display:<?php echo $visibleLocalidadAgregar; ?>">
				<a name="btnAgregarLocalidad" id="btnAgregarLocalidad" href="#" onclick="visualizar_elemento('botonesLocalidadAgregar','none', 'botonesLocalidadGuardar','inline', 'txtLocalidad','inline', 'IdLocalidad', 'none');" >
					<img class="eliminar" src="img/add.svg" alt="agregar" >
			  </a>
			  <a name="btnEditarLocalidad" id="btnEditarLocalidad" href="#" onclick="if (document.getElementById('IdLocalidad').selectedIndex==0) {alert('Seleccione una Localidad')} else {enviar_accion('editar','localidad')};">
					<img class="eliminar" src="img/edit.svg" alt="agregar" >
			  </a>
			  <a name="btnEliminarLocalidad" id="btnEliminarLocalidad" href="#" onclick="enviar_accion('eliminar','localidad');">
					<img class="eliminar" src="img/garbage.svg" alt="eliminar" >
				</a>
			</span>
			<span name="botonesLocalidadGuardar" id="botonesLocalidadGuardar" style="display:<?php echo $visibleLocalidadGuardar; ?>">
			  <a name="btnOkLocalidad" id="btnOkLocalidad" href="#" onclick="enviar_accion('<?php echo $accion ?>', 'localidad');" >
					<img class="eliminar" src="img/ok.svg" alt="bien" >
			  </a>
			  <a name="btnCancelarLocalidad" id="btnCancelarLocalidad" href="#" onclick="document.getElementById('accion').value='nuevo';document.getElementById('txtLocalidad').value='';visualizar_elemento('IdLocalidad','inline','lblLocalidad','block','botonesLocalidadAgregar','inline', 'botonesLocalidadGuardar','none','txtLocalidad','none');" >
					<img class="eliminar" src="img/cancel.svg" alt="bien" >
			  </a>
			 </span>
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