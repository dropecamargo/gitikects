<!-- Instance db -->
<? require('Postgresql.php'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="../../favicon.ico">

		<title>Ticket de Servicio</title>

		<!-- Bootstrap core CSS -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="bootstrap/css/starter-template.css" rel="stylesheet">
	</head>

	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">
						<img src="img/granimagen.png" width="200" height="42" border="0" name="Granimagen" alt="GranImagen Logo" align="middle">
					</a>
				</div>
			</div>
		</nav>

		<div class="container">
			<!-- Instance db -->
			<?
				$db = new Postgresql();
				$connect = $db->connect();
			?>
			<h2 class="page-header">Ticket de servicio técnico</h2>
			<div class="row">
		        <div class="form-group col-md-2">
		            <label for="placa">Placa</label>
		            <input type="text" name="placa" id="placa" class="form-control input-sm" placeholder="Ingrese #placa">
		        </div>
		   	</div>

		   	<div class="row">
		        <div class="form-group col-md-3">
		            <label for="solicita">Persona que solicita</label>
		            <input type="text" name="solicita" id="solicita" class="form-control input-sm" placeholder="Ingrese nombre persona que solicita">
		        </div>
		       
		        <div class="form-group col-md-3">
		            <label for="telefono">Teléfono</label>
		            <input type="text" name="telefono" id="telefono" class="form-control input-sm" placeholder="Ingrese #teléfono">
		        </div>

		        <div class="form-group col-md-3">
					<label for="problema">Tipo de Problema:</label>
					<select class="form-control input-sm" id="problema" name="problema">
						<option value="">Seleccione</option>
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
					</select>
				</div>
		   	</div>

		   	<div class="row">
		        <div class="form-group col-md-6">
		            <label for="descripcion">Detalles de la Solicitud:</label>
 	 				<textarea class="form-control" rows="4" id="descripcion" name="descripcion"></textarea>		        
				</div>

				<div class="form-group col-md-3">
					<label for="prioridad">Prioridad:</label>
					<select class="form-control input-sm" id="prioridad" name="prioridad">
						<option value="">Seleccione</option>
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
					</select>
				</div>
		   	</div>
		   	

		</div>
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="bootstrap/js/app.min.js"></script>
	</body>
</html>
