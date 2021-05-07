<?php
require 'include/funciones.php';
//var_dump($_SESSION);
if (empty($_SESSION["usuario"])) {
	// No hay sesion iniciado, mostramos la pagina de login
	//echo '<h2>Pagina de Login</h2>';
	header("Location: index.php");
	exit();
} else {
	// hay session iniciada, redirigimos a la pagina de paquetes
	//echo '<h2>Pagina de Lista de Paquetes</h2>';
?>
<?php require 'include/doctype.php'; ?>
<title>Acerca de - C.E.R.</title>
<?php require 'include/head.php'; ?>

<body>
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
  <div id="contenedor">  
  <?php require 'include/encabezado.php'; ?>
  <?php require 'include/menu.php'; ?>
  <div id="acercade">
	<p>
		<span>Sistema para el <b>C</b>ontrol de <b>E</b>stimaciones <b>R</b>ecibidas (C.E.R.<sup>&trade;</sup>)</span>
		Todos los derechos reservados 2018-2021<sup>&reg;</sup>.
		<strong>Autor: Jose Antonio</strong>
	</p>
  </div>	
  </div>
  <script src="js/main.js"></script>  
  
</body>
</html>

<?php
}
?>