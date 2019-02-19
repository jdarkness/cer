<?php
$actual=basename($_SERVER['PHP_SELF']);
?>
<div id="menu">
	<ul>
	  <li><a <?php if ($actual=='paquete.php') {?> class="activo" <?php } ?> href="paquete.php">Paquete</a></li>
	  <li><a <?php if ($actual=='estimacion.php') {?> class="activo" <?php } ?> href="estimacion.php">Estimaci&oacute;n</a></li>
	  <li><a <?php if ($actual=='obra.php') {?> class="activo" <?php } ?>href="obra.php">Obra</a></li>	  
	  <li><a <?php if ($actual=='contratista.php') {?> class="activo" <?php } ?>href="contratista.php">Contratista</a></li>
	  <li><a <?php if ($actual=='evento.php') {?> class="activo" <?php } ?>href="evento.php">Evento</a></li>
	  <li><a <?php if ($actual=='usuario.php') {?> class="activo" <?php } ?>href="usuario.php">Usuario</a></li>
	  <li><a <?php if ($actual=='signup.php') {?> class="activo" <?php } ?>href="signup.php">Nuevo Usuario</a></li>
	  <li><a <?php if ($actual=='acercade.php') {?> class="activo" <?php } ?>href="acercade.php">Acerca de</a></li>
	  <li><a href="logout.php">Salir</a></li>
	  <li><a <?php if ($actual=='pendientes.php') {?> class="activo" <?php } ?>href="pendientes.php">Pendientes</a></li>
	  
	</ul>
</div>
<?php 
/*
echo basename(__FILE__). '<br />'; 
echo PHP_URL_PATH. '<br />';
echo realpath(__FILE__). '<br />';
echo '$_SERVER[PHP_SELF]: ' . $_SERVER['PHP_SELF'] . '<br />';
echo 'Dirname($_SERVER[PHP_SELF]: ' . dirname($_SERVER['PHP_SELF']) . '<br>';
var_dump($_SERVER);*/

//echo basename($_SERVER['PHP_SELF']);
?>