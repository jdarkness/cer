<?php
require 'include/funciones.php';
//var_dump($_SESSION);
// session_start(); // esta el archivo de funciones.php
//$_SESSION['usuario']='jfigueroa';
//unset($_SESSION['usuario']);
if (empty($_SESSION["usuario"])) {
	// No hay sesion iniciado, mostramos la pagina de login
	//echo '<h2>Pagina de Login</h2>';
	$Usuario='';
	if (isset($_POST['Usuario'])) {		
		//require 'include/funciones.php';
		//echo 'Tiene algo la variable';
		$link=conectar_a_bd();
		//echo 'Segunda vez';
		
		// Obtenemos los datos que necesitamos para poder ingresar
		$Usuario=strtolower(texto_seguro($_POST['Usuario'], $link));		
		$Password=texto_seguro($_POST['Password'], $link);
		
		// Verificamos que esten los datos necesarios
		if (empty($Usuario) OR empty($Password)) {
			// Faltan campos obligatorios
			$texto='Falta campo obligatorio.';
			$tipo='error';
			$mensaje=tipo_mensaje($tipo, $texto);
		} else {
			// Todos los campos necesarios estan presentes
			
			// Buscamos los datos en la base de datos
			$query="SELECT Contrasenia, Salt FROM Usuario WHERE Usuario ='$Usuario'";
			if ($resultado = mysqli_query($link, $query)) {
				
				// Verificamos que nos regrese un resultado
				if (mysqli_num_rows($resultado)<=0) {					
					// no encontro al usuario
					$texto='Usuario y/o Contrase&ntilde;a incorrecta.';
					$tipo='error';
					$mensaje=tipo_mensaje($tipo, $texto);
				} else {
					//echo 'Encontro un resultado';
					$row = mysqli_fetch_assoc($resultado);
					$password1 = encriptar_password($Password, $row['Salt']);
					$password2 = $row['Contrasenia'];
					if ($password1 == $password2) {
						// los password coinciden iniciamos sesion y redireccionamos a la pagina de paquetes.
						$_SESSION['usuario']=$Usuario;
						header("Location: paquete.php");
						exit();
					} else {
						// contraseña incorrecta
						$texto='Usuario y/o Contrase&ntilde;a incorrecta.';
						$tipo='error';
						$mensaje=tipo_mensaje($tipo, $texto);
					}
				
				}
				
				
			} else {
				// error en la consulta
				$texto=mysqli_error($link). ' ' . $query;
				$tipo='error';
				$mensaje=tipo_mensaje($tipo, $texto);
			}
		}
	} else {
		//echo 'Primera vez';
	}
	
	?>
<!doctype html>
<html class="no-js" lang="">
<title>C.E.R.</title>
<?php require 'include/head.php'; ?>
<body>
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
  <div id="login">  
  <h2>Control de Estimaciones Recibidas</h2>
  <form method="post" name="formulario" id="formulario" action="<?php echo basename(__FILE__); ?>">
	<fieldset>
		<legend>Datos del Usuario</legend>
		<label for="Usuario"><span class="etiqueta">Usuario <span class="requerido">*</span> :</span><span class="campo"><input class="minuscula" type="text" id="Usuario" name="Usuario" value="<?php echo $Usuario; ?>"  /><!-- required --></span></label>
		<label for="Password"><span class="etiqueta">Contrase&ntilde;a <span class="requerido">*</span> :</span><span class="campo"><input type="password" id="Password" name="Password"  /><!-- required --></span></label>		
		<label><span class="etiqueta"><span class="requerido">*</span><span class="indicaciones"> Datos requeridos</span></span></label>	
		<label><span class="etiqueta"></span><span class="campo derecha"><input class="green button" type="submit" value="Ingresar" /></span></label>		
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
<?php
} else {
	// hay session iniciada, redirigimos a la pagina de paquetes
	//echo '<h2>Pagina de Lista de Paquetes</h2>';
	header("Location: paquete.php");
	exit();
}
?>