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
	$nombre='';
	$idUsuario='';
	$Usuario='';
	$aPaterno='';
	$aMaterno='';		
	
	if (isset($_POST['accion'])) {
		$accion=texto_seguro($_POST['accion'], $link);
	} else {
		$accion='actualizar';
	}
	if (isset($_POST['valor'])) {
		$valor=texto_seguro($_POST['valor'], $link);
	} else {
		$valor=0;
	}	
	
	switch($accion) {		
		case 'actualizar':
			
			if (isset($_POST['Nombre']) && isset($_POST['aPaterno']) && isset($_POST['aMaterno'])) {
				// Actualizamos los datos del registro		
				// Obtenemos los datos que necesitamos para actualizar
				$nombre=texto_seguro($_POST['Nombre'], $link);
				$aPaterno=texto_seguro($_POST['aPaterno'], $link);
				$aMaterno=texto_seguro($_POST['aMaterno'], $link);
				
				// Todo bien actualizamos los datos
				$query="UPDATE Usuario SET Nombre='$nombre', APaterno='$aPaterno', AMaterno='$aMaterno' WHERE IdUsuario='$valor';";
				if ($resultado = mysqli_query($link, $query)) {									
					$accion='actualizar';				
					$mensaje=tipo_mensaje('info', 'Registro Actualizado');
				} else {
					//Error en la consulta		
					$texto=mysqli_error($link). ' ' . $query;
					$tipo='error';
					$mensaje=tipo_mensaje($tipo, $texto);				
				}
			}
			break;		
		case 'update_pass':
			//echo 'entramos al case de cambiar contraseña';
			// Verificamos que tenemos los datos necesarios
			if (isset($_POST['passActual']) && isset($_POST['passNuevo']) && isset($_POST['passNuevo2'])) {
				//echo 'echo tenemos todos los datos necesarios para actualizar';
				//obtenemos los datos
				$passActual=texto_seguro($_POST['passActual'], $link);
				$nuevoPass=texto_seguro($_POST['passNuevo'], $link);
				$nuevoPass2=texto_seguro($_POST['passNuevo2'], $link);
				
				if (empty($passActual) || empty($nuevoPass) || empty($nuevoPass2)) {
					$texto='Faltan campos obligarotios';
					$tipo='error';
					$mensaje=tipo_mensaje($tipo, $texto);
				} else {
					//$password1 = encriptar_password($Password, $row['Salt']);
					//$salt = crear_salt();
					//$password = encriptar_password($password1, $salt);
				
					// Corroboramos que la contraseña actual es la correcta
					$query="SELECT Contrasenia, Salt FROM Usuario WHERE IdUsuario='$valor';";
					if ($resultado = mysqli_query($link, $query)) {
						// nos regreso algun registro
						if (mysqli_num_rows($resultado)>0) {
							$row = mysqli_fetch_assoc($resultado);
							$passBD=$row['Contrasenia'];
							$passActual=encriptar_password($passActual, $row['Salt']);
							if ($passActual==$passBD) {
								// Verificamos que las dos contraseñas nuevas sean iguales
								if ($nuevoPass == $nuevoPass2 ) {
									// Todo bien, creamos el nuevo password
									$salt = crear_salt();
									$nuevoPass = encriptar_password($nuevoPass, $salt);
									$query="UPDATE Usuario SET Contrasenia = '$nuevoPass', Salt = '$salt' WHERE IdUsuario='$valor';";
									if ($resultado = mysqli_query($link, $query)) {
										$accion='actualizar';				
										$mensaje=tipo_mensaje('info', 'Contraseña Actualizada');
									} else {
										//Error en la consulta		
										$texto=mysqli_error($link). ' ' . $query;
										$tipo='error';
										$mensaje=tipo_mensaje($tipo, $texto);
									}
								} else {
									$texto='La nueva contrasenia no coincide';
									$tipo='error';
									$mensaje=tipo_mensaje($tipo, $texto);
								}
							} else {
								$texto='Contrasenia Actual Incorrecta';
								$tipo='error';
								$mensaje=tipo_mensaje($tipo, $texto);
							}
						} else {
							//Error en la consulta		
							$texto='No existen registros';
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);
						}					
					} else {
						//Error en la consulta		
						$texto=mysqli_error($link). ' ' . $query;
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);				
					}
					
				}
			}
			break;
	} // switch($accion) {
	
	
			
		// Buscamos los datos del usuario
		//print_r($_SESSION);
		$query="SELECT IdUsuario, Usuario, Nombre, APaterno, AMaterno FROM Usuario WHERE Usuario ='".$_SESSION['usuario']."';";
		if (!($resultado = mysqli_query($link, $query))) {
			//Error en la consulta		
			$texto=mysqli_error($link). ' ' . $query;
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);				
		} else {
			if (mysqli_num_rows($resultado)>0) {
				// Tenemos resultados
				$row = mysqli_fetch_assoc($resultado);
				$valor=$row['IdUsuario'];
				$Usuario=$row['Usuario'];
				$nombre=$row['Nombre'];
				$aPaterno=$row['APaterno'];
				$aMaterno=$row['AMaterno'];
			}
		}
?>
<?php require 'include/doctype.php'; ?>
<title>Usuario - C.E.R.</title>
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
	<label for="Usuario"><span class="etiqueta">Usuario :</span><span class="campo"><input type="text" id="Usuario" name="Usuario" value="<?php echo $Usuario; ?>" readonly disabled ></span></label>
	<label for="Nombre"><span class="etiqueta">Nombre(s) :</span><span class="campo"><input type="text" id="Nombre" name="Nombre" value="<?php echo $nombre; ?>"  /></span></label>
	<label for="aPaterno"><span class="etiqueta">A. Paterno :</span><span class="campo"><input type="text" id="aPaterno" name="aPaterno" value="<?php echo $aPaterno; ?>"  /></span></label>
	<label for="aMaterno"><span class="etiqueta">A. Materno :</span><span class="campo"><input type="text" id="aMaterno" name="aMaterno" value="<?php echo $aMaterno; ?>"  /></span></label>		
	<label><span class="etiqueta">&nbsp;</span><span class="campo">&nbsp;</span></label>
	<div class="label1">
		<span class="etiqueta">&nbsp;</span>
		<span class="campo botones">
			<input class="small button" type="submit" value="Actualizar Datos" />			
		</span>
	</div>
	<label><hr></label>
	<label for="passActual"><span class="etiqueta">Contrase&ntilde;a Actual <span class="requerido">*</span> :</span><span class="campo"><input type="password" id="passActual" name="passActual" /></span></label>
	<label for="passNuevo"><span class="etiqueta">Nueva Contrase&ntilde;a <span class="requerido">*</span> :</span><span class="campo"><input type="password" id="passNuevo" name="passNuevo" /></span></label>
	<label for="passNuevo2"><span class="etiqueta">Repetir Contrase&ntilde;a <span class="requerido">*</span> :</span><span class="campo"><input type="password" id="passNuevo2" name="passNuevo2" /></span></label>
	<label><span class="etiqueta"><span class="requerido">*</span><span class="indicaciones"> Datos requeridos</span></span></label>	
	<label><span class="etiqueta">&nbsp;</span><span class="campo">&nbsp;</span></label>
	<div class="label1">
		<span class="etiqueta">&nbsp;</span>
		<span class="campo botones">
			<input class="small button" type="button" onclick="enviar_accion('update_pass','<?php echo $valor; ?>');" value="Actualizar contraseña" />			
		</span>
	</div>
	<?php
		if (!empty($mensaje['texto'])){ 	?>
		<span class="mensaje alert <?php echo $mensaje['clase']; ?>"><?php echo $mensaje['texto']; ?></span>
	<?php
		}
	?>
  </form>
  </div>
  <script src="js/main.js"></script>  
  
</body>
</html>

<?php
}
?>