<!DOCTYPE html>
<html lang="es-ES">
<head>
	<meta charset="UTF-8">
	<title>Obtener comunas</title>
	<script src="//code.jquery.com/jquery-latest.js"></script>
    <script src="miscript.js"></script>
</head>
<body>
	
<?php 
include("conect.php");
?>
<form id="datos">
	Comuna Origen
	<select id="origen" name="origen">
	<?php
	 $sql = 'SELECT * FROM comunas ORDER BY nombre';
	    foreach ($con->query($sql) as $row) {
		?>
		<option value="<?php echo $row['codigo'] ?>"><?php echo $row['nombre'] ?></option>
		<?php }; ?>
	</select>
	<br>
	Comuna destino
		<select id="destino" name="destino">
	<?php
	 $sql = 'SELECT * FROM comunas ORDER BY nombre';
	    foreach ($con->query($sql) as $row) {
		?>
		<option value="<?php echo $row['codigo'] ?>"><?php echo $row['nombre'] ?></option>
		<?php }; ?>
	</select>
<br>
	kilos
	<input type="text" name="kilos" id="kilos" value="">
	<br>
	dimensiones
	<input type="text" name="dimensiones" id="dimensiones" value="">

	<button type="button" id="obtenervalores">Enviar</button>


	<div id="response-container"></div>
</form>
</body>
</html>