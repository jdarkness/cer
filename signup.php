<?php
	$nombre='';
	$aPaterno='';
	$aMaterno='';
	$Usuario='';	
	if (isset($_POST['Usuario'])) {
		require 'include/funciones.php';
		//echo 'Tiene algo la variable';
		$link=conectar_a_bd();
		
		//Obtenemos los datos 
		$nombre=texto_seguro($_POST['Nombre'], $link);
		$aPaterno=texto_seguro($_POST['APaterno'], $link);
		$aMaterno=texto_seguro($_POST['AMaterno'], $link);
		$Usuario=strtolower(texto_seguro($_POST['Usuario'], $link));		
		$password1=texto_seguro($_POST['Password1'], $link);
		$password2=texto_seguro($_POST['Password2'], $link);
		
		//echo $nombre.' '. $aPaterno . ' ' . $aMaterno . ' ' . $Usuario . ' ' . $password1 . ' ' . $password2;
		
		// Verificamos que esten los datos necesarios
		if (empty($Usuario) OR empty($password1) OR empty($password2)) {
			// Faltan campos obligatorios
			$texto='Falta campo obligatorio.';
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);
		} else {
			// Todos los campos necesarios estan presentes
			
			// Revisamos que el usuario no exista
			$query="SELECT Usuario FROM Usuario WHERE Usuario = '". $Usuario ."'";
			
			if ($resultado = mysqli_query($link, $query)) {
				//printf("La selección devolvió %d filas.\n", mysqli_num_rows($resultado));				
				/* liberar el conjunto de resultados */
				if (mysqli_num_rows($resultado)>0) {
					$texto='El usuario ya existe';
					$tipo='error';
					$mensaje=tipo_mensaje($tipo, $texto);
					mysqli_free_result($resultado);
				} else {
					// El usuario no existe, asi que verificamos que las contraseñas sean iguales
					if ($password1 == $password2) {
						// Contraseñas iguales , creamos el hash para guardar en la base de datos
						$salt = crear_salt();
						$password = encriptar_password($password1, $salt);
						$query = "INSERT INTO Usuario(Usuario, Nombre, APaterno, AMaterno, Contrasenia, Salt) VALUES(";
						$query .="'".$Usuario ."','". $nombre ."','". $aPaterno ."','". $aMaterno ."','". $password ."','". $salt ."')";
						//echo $query;
						if ($resultado = mysqli_query($link, $query)) {
							$texto = 'Usuario creado con exito';
							$tipo='exito';
							$mensaje=tipo_mensaje($tipo, $texto);
							$nombre='';
							$aPaterno='';
							$aMaterno='';
							$Usuario='';
							$password1='';
							$password2='';
						} else {
							// error en la insercion
							$texto=mysqli_error($link). ' ' . $query;
							$tipo='error';
							$mensaje=tipo_mensaje($tipo, $texto);
						}						
						
					} else {
						// las contraseñas son diferentes, enviamos el mensaje de error
						$texto='Las contraseñas no coinciden.';
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}
				}				
				mysqli_close($link);
			} else {
				// error en la consulta
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);
			}
			
		}
		
		
	}


?>
<?php require 'include/doctype.php'; ?>
<title>Nuevo Usuario - C.E.R.</title>
<?php require 'include/head.php'; ?>

<body>
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->
  
  <div id="login"> 
  <h2>Control de Estimaciones Recibidas</h2>
  <form method="post" name="formulario" id="formulario" action="<?php echo basename(__FILE__); ?>">
  <fieldset>
	<legend>Alta nuevo usuario</legend>
	<label for="Usuario"><span class="etiqueta">Usuario<span class="requerido">*</span> :</span><span class="campo"><input class="minuscula" type="text" id="Usuario" name="Usuario" value="<?php echo $Usuario; ?>" required /></span></label>
	<label for="Password1"><span class="etiqueta">Contrase&ntilde;a<span class="requerido">*</span> :</span><span class="campo"><input type="password" id="Password1" name="Password1" required /></span></label>
	<label for="Password2"><span class="etiqueta">Repetir Contrase&ntilde;a<span class="requerido">*</span> :</span><span class="campo"><input type="password" id="Password2" name="Password2" required /></span></label>	
	<label for="Nombre"><span class="etiqueta">Nombre(s) :</span><span class="campo"><input type="text" id="Nombre" name="Nombre" value="<?php echo $nombre; ?>" /></span></label>
	<label for="APaterno"><span class="etiqueta">Apellido Paterno :</span><span class="campo"><input type="text" id="APaterno" name="APaterno" value="<?php echo $aPaterno; ?>" /></span></label>
	<label for="AMaterno"><span class="etiqueta">Apellido Materno :</span><span class="campo"><input type="text" id="AMaterno" name="AMaterno" value="<?php echo $aMaterno; ?>" /></span></label>

	<label><span class="etiqueta"><span class="requerido">*</span><span class="indicaciones"> Datos requeridos</span></span></label>	
	<label><span class="etiqueta"></span>
		<span class="campo derecha">
			<!-- <input class="button" type="button" onclick="limpiar_form(this.form); return false;" value="LimpiarR" />
			<button class="button" type="button" onclick="limpiar_form(this.form); return false;" value="LimpiarR" >LimpiarRR</button> -->
			<input class="green button" type="reset" value="Limpiar" />
			<input class="green button" type="submit" value="Crear Usuario" />
			
		</span>
	</label>
	<?php
		if (!empty($mensaje['texto'])){ 	?>
		<label><span class="mensaje alert <?php echo $mensaje['clase']; ?>"><?php echo $mensaje['texto']; ?></span></label>
	<?php
		}
	?>
	</fieldset>
  </form>
  </div>
  <script src="js/main.js"></script>
</body>
</html>
