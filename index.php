<?php
	require_once 'functions.php'; 
	$functions = new Functions();
	$priorities = $functions->getTicketPriorities();
	$problems = $functions->GetTicketProblems();
?>
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
			<h2 class="page-header">Ticket de servicio t&eacute;cnico</h2>

	   	  	<div class="alert alert-danger" id="validation-errors-ticket" style="display: none"></div>
	   	  	<div class="alert alert-success" id="success-ticket" style="display: none"></div>

		   	<form id="form-add-ticket" name="form-add-ticket" action="server.php?action=addticket" method="post">
				<div class="row">
			        <div class="form-group col-md-2">
			            <label for="placa">Placa</label>
			            <input type="text" name="placa" id="placa" class="form-control input-sm" placeholder="Ingrese #placa required">
			        </div>
			   	</div>

				<div id="placa-info" class="panel panel-default" style="display: none">
					<div class="panel-heading">
						<h3 class="panel-title">Detalle equipo</h3>
					</div>
					<div class="panel-body">
						<div class="col-md-5">
				            <label>Cliente:</label>
				            <span id="cliente_nombre"></span>
						</div>
						<div class="col-md-2">
				            <label>Serial:</label>
				            <span id="serial_maquina"></span>
						</div>
						<div class="col-md-2">
				            <label>Marca:</label>
				            <span id="marca_maquina"></span>
						</div>
						<div class="col-md-3">
				            <label>Modelo:</label>
				            <span id="modelo_maquina"></span>
						</div>
					</div>
				</div>

			   	<div class="row">
			        <div class="form-group col-md-3">
			            <label for="solicita">Persona que solicita</label>
			            <input type="text" name="solicita" id="solicita" class="form-control input-sm" placeholder="Ingrese nombre persona que solicita">
			        </div>
			       
			        <div class="form-group col-md-3">
			            <label for="telefono">Tel&eacute;fono</label>
			            <input type="text" name="telefono" id="telefono" class="form-control input-sm" placeholder="Ingrese #tel&eacute;fono">
			        </div>

			        <div class="form-group col-md-3">
						<label for="problema">Tipo de Problema:</label>
						<select class="form-control input-sm" id="problema" name="problema">
							<option value="">Seleccione</option>
							<?php foreach ($problems as $item) { ?>
								<option value="<?php echo $item->key; ?>"><?php echo $item->value; ?></option>
							<? } ?>	
						</select>
					</div>
			   	</div>

			   	<div class="row">
			        <div class="form-group col-md-6">
			            <label for="descripcion">Detalles de la solicitud:</label>
	 	 				<textarea class="form-control" rows="4" id="descripcion" name="descripcion"></textarea>		        
					</div>

					<div class="form-group col-md-3">
						<label for="prioridad">Prioridad:</label>
						<select class="form-control input-sm" id="prioridad" name="prioridad">
							<option value="">Seleccione</option>
							<?php foreach ($priorities as $item) { ?>
								<option value="<?php echo $item->key; ?>"><?php echo $item->value; ?></option>
							<? } ?>
						</select>
					</div>
			   	</div>
			   	
			   	<div class="row">
			        <div class="form-group col-md-6">
			        	<button type="submit" class="btn btn-primary btn-sm">Enviar</button>
					</div>
			   	</div>
			</form>

		</div>
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="bootstrap/js/app.min.js"></script>
	</body>
</html>
