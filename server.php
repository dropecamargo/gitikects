<?php
	require_once 'functions.php'; 
    require_once 'JSON.php';

	$action = $_GET['action'];
	
	// Prepare response
	$json = new Services_JSON();

	// Instance funcions
	$functions = new Functions();

	switch ($action) {
		case 'addticket':
			$response = $functions->addTicket($_POST);
			print_r( $json->encode($response) );
			return;

		case 'getdetail':
			$response = $functions->getDetail($_GET);
			print_r( $json->encode($response) );
			return;

		break;
	}
?>