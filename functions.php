<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL|E_STRICT);

require_once 'postgresql.php'; 
require "PHPMailer_v2.0.0/class.phpmailer.php";

class Functions {

	var $db;

	function __construct() {
		$db = new Postgresql();
		
		if( !$db->connect() ) die("Error DB connect");

		$this->db = $db;
	}

	public function getTicketPriorities() 
	{
		$result = $this->db->getTable('ticketspriority_code, ticketspriority_name', 'business.ticketspriorities');
		
		$data = array();
		while ($arr = pg_fetch_array($result)) { 

			$object = new stdClass();
			$object->key = $arr['ticketspriority_code'];
			$object->value = $arr['ticketspriority_name'];

			$data[] = $object;
        }
        return $data;
   	}

   	public function getTicketProblems()
   	{
		$result = $this->db->getTable('ticketsproblem_code, ticketsproblem_name', 'business.ticketsproblems');
		
		$data = array();
		while ($arr = pg_fetch_array($result)) { 

			$object = new stdClass();
			$object->key = $arr['ticketsproblem_code'];
			$object->value = $arr['ticketsproblem_name'];

			$data[] = $object;
        }
        return $data;
	}

	public function getOrderProblemTypes()
   	{
		$result = $this->db->getTable('orderproblemtype_code, orderproblemtype_name', 'business.orderproblemtypes');
		
		$data = array();
		while ($arr = pg_fetch_array($result)) { 

			$object = new stdClass();
			$object->key = $arr['orderproblemtype_code'];
			$object->value = $arr['orderproblemtype_name'];

			$data[] = $object;
        }
	    return $data;
	}

	public function isValidTicket($arrayForm)
	{
		if(!isset($arrayForm['placa']) || !$arrayForm['placa'] || trim($arrayForm['placa']) == '') {
			return 'Por favor ingrese placa.';
		}

		if(!isset($arrayForm['email']) || !$arrayForm['email'] || trim($arrayForm['email']) == '') {
			return 'Por favor ingrese email.';
		}

		if(!isset($arrayForm['confirm_email']) || !$arrayForm['confirm_email'] || trim($arrayForm['confirm_email']) == '') {
			return 'Por favor ingrese confirmaci&oacute;n de email.';
		}
		
		if(trim($arrayForm['email']) != $arrayForm['confirm_email']) {
			return 'Las direcci&oacute;nes de email que introdujo no coinciden.';
		}

		if(!isset($arrayForm['solicita']) || !$arrayForm['solicita'] || trim($arrayForm['solicita']) == '') {
			return 'Por favor ingrese nombre de la persona que solicita.';
		}

		if(!isset($arrayForm['telefono']) || !$arrayForm['telefono'] || trim($arrayForm['telefono']) == '') {
			return 'Por favor ingrese tel&eacute;fono.';
		}

		if(!isset($arrayForm['problema']) || !$arrayForm['problema'] || trim($arrayForm['problema']) == '') {
			return 'Por favor seleccione tipo de problema.';
		}

		if(!isset($arrayForm['descripcion']) || !$arrayForm['descripcion'] || trim($arrayForm['descripcion']) == '') {
			return 'Por favor ingrese detalles';
		}

		if(!isset($arrayForm['prioridad']) || !$arrayForm['prioridad'] || trim($arrayForm['prioridad']) == '') {
			return 'Por favor seleccione prioridad.';
		}		
		return 'OK';
	}

	public function addTicket($arrayForm)
	{	
		// Prepare response
		$response = new stdClass();
		$response->success = false;

		$valid = $this->isValidTicket($arrayForm);
		if($valid != 'OK') {
			$response->msg = $valid;
			return $response;
		}
		
		// Recuperar projecto maquina
		$project = pg_query("SELECT MAX(p.project_id) AS project_number 
			FROM business.projects AS p
			INNER JOIN business.machines AS m ON p.project_machine = m.machine_plate
			INNER JOIN business.clients AS c ON p.project_client = c.client_costcenter
			WHERE project_machine = {$arrayForm['placa']}");

		$project = pg_fetch_array($project);
		if(!isset($project['project_number']) || !is_numeric($project['project_number'])) {
		    $response->msg = "Ocurrio un error recuperando informacion de la maquina {$arrayForm['placa']} - Consulte al administrador.";
		    return $response;
		}

		// Recuperar maquina
		$machine =  pg_query($this->getMachine($arrayForm['placa'], $project['project_number']));
		$machine = pg_fetch_array($machine);
		if(!isset($machine['machine_plate']) || !is_numeric($machine['machine_plate'])) {
		    $response->msg = "Ocurrio un error recuperando informacion de la maquina {$arrayForm['placa']} - Consulte al administrador.";
			return $response;
		}

		// Recuperar consecutivo ticket
		$consecutive =  pg_query("SELECT sucursal_ticket, sucursal_order FROM business.sucursales WHERE sucursal_codigo = 10");
		$consecutive = pg_fetch_array($consecutive);
		if(!isset($consecutive['sucursal_ticket']) || !is_numeric($consecutive['sucursal_ticket'])) {
		    $response->msg = "Ocurrio un error recuperando consecutivo ticket - Consulte al administrador.";
			return $response;
		}
		$consecutivo = $consecutive['sucursal_ticket'] + 1;

		// Recuperar consecutivo orden
		if(!isset($consecutive['sucursal_order']) || !is_numeric($consecutive['sucursal_order'])) {
		    $response->msg = "Ocurrio un error recuperando consecutivo orden - Consulte al administrador.";
			return $response;
		}
		$consecutivoOrden = $consecutive['sucursal_order'] + 1;

		// Begin transaction 
		pg_query("BEGIN") or die("Could not start transaction");

		// Insertar tickect
		pg_query("INSERT INTO business.tickets (ticket_number, ticket_client, ticket_priority, ticket_problemdescription, ticket_machine, ticket_datecreation, ticket_hourcreation, ticket_state, ticket_problem, ticket_source, ticket_usercreation, ticket_contact, ticket_contactphone, ticket_branch, ticket_clientname, ticket_order, ticket_email) VALUES ($consecutivo, '".$machine['client_costcenter']."', ".$arrayForm['prioridad'].", '".strtoupper($arrayForm['descripcion'])."', '".$machine['machine_plate']."', 'now()', '".date('H:i:s')."', 2, 7, 5, 999999999, '".strtoupper($arrayForm['solicita'])."', '".$arrayForm['telefono']."', 10, '".$machine['client_name']."', $consecutivoOrden, '".strtolower($arrayForm['email'])."')");

		// Insertar orden
		pg_query("INSERT INTO business.orders (order_number, order_branch, order_client, order_machine, order_date, order_hour, order_ubication, order_area, order_area2, order_address, order_contact, order_person_call, order_problem, order_problem_type, order_priority, order_close, order_user_creation, order_date_creation, order_hour_creation, order_cont, order_document, order_type, order_client_name, order_technician, order_technician_name, order_cancellation, order_finaly_cont, order_services, order_ubication_name, order_project, order_source, order_zone, order_ticket) VALUES ($consecutivoOrden, 10, '".$machine['client_costcenter']."', '".$machine['machine_plate']."', 'now()', '".date('H:i:s')."', '".$machine['area_municipality']."', '".$machine['area_name']."', '".$machine['area_name2']."', '".$machine['area_address']."', '".($machine['contact_first_name'].' '.$machine['contact_last_name'])."', '".strtoupper($arrayForm['solicita'])."', '".strtoupper($arrayForm['descripcion'])."', '".$arrayForm['problema']."', ".$arrayForm['prioridad'].", false, 999999999, 'now()', '".date('H:i:s')."', ".$machine['machine_cont'].", 'ORDER', 1, '".$machine['client_name']."', '".$machine['project_technician']."', '".($machine['technician_first_name'].' '.$machine['technician_last_name'])."', false, 0, 0, '".$machine['municipio_nombre']."', ".$machine['project_id'].", 7, '".$machine['project_zone']."', $consecutivo)"); 	


		// Actualizar consecutivos
		pg_query("UPDATE business.sucursales SET sucursal_ticket = $consecutivo, sucursal_order = $consecutivoOrden WHERE sucursal_codigo = 10");

		// Commit transaction 
	    pg_query("COMMIT") or die("Transaction commit failed\n"); 
	    // pg_query("ROLLBACK") or die("Transaction rollback failed");

		$mail = new phpmailer();
		$mail->PluginDir = "PHPMailer_v2.0.0/";
		$mail->SetLanguage( 'es', 'PHPMailer_v2.0.0/language/' );
		$mail->Mailer = "smtp";
		$mail->Host = "smtp.granimagen.com";          
		$mail->SMTPAuth = true;
		$mail->Username = "alvaroforero@granimagen.com";
		$mail->Password = "alvaroforero2016";
		$mail->From = "alvaroforero@granimagen.com";
		$mail->FromName = "Gran Imagen - Ticket de servicio";
		$mail->Timeout = 50;
		$mail->AddAddress(strtolower($arrayForm['email']));
		$mail->Subject = "Ticket #$consecutivo - ".date("Y-m-d H:i:s");
		$Msg="Se generado con exito el ticket #".$consecutivo.", por favor guarde este n&uacute;mero para el seguimiento del mismo";
		$mail->Body = stripslashes($Msg);
		$mail->AltBody = stripslashes($Msg);
		$exito = $mail->Send();

	    $response->success = true;
	    $response->msg = "Se generado con exito el ticket #$consecutivo, por favor guarde este n&uacute;mero para el seguimiento del mismo.";
		return $response;
	}

	public function getMachine($placa, $projecto) {
		return " SELECT p.project_client, p.project_area, p.project_user_creation, p.project_notes, p.project_date, p.project_state, p.project_id, p.project_technician, p.project_close, p.project_user_close, p.project_date_close, p.project_hour_close, p.project_cancellation, p.project_branch, p.project_zone, p.project_machine, p.project_date_creation, c.client_person, c.client_first_name, c.client_last_name, c.client_trade_name, m.machine_name, m.machine_plate, m.machine_cont, mt.machinetype_name, m.machine_serial, u.user_first_name, u.user_last_name, ma.mark_name, mo.model_name, te.technician_first_name, te.technician_last_name, de.departamento_nombre, pz.projectzone_name, mu.municipio_nombre, co.contact_id, co.contact_first_name, co.contact_last_name, co.contact_email, co.contact_phone, co.contact_movil, co.contact_fax, co.contact_title, a.area_name, a.area_address, a.area_name2, a.area_municipality, a.area_code, c.client_costcenter, c.client_nit, 
			(CASE WHEN c.client_person = 1 THEN c.client_trade_name ELSE c.client_first_name || ' ' || c.client_last_name END) AS client_name 
			FROM business.projects p
			LEFT JOIN business.clients c ON p.project_client = c.client_costcenter
			LEFT JOIN business.machines m ON p.project_machine = m.machine_plate
			LEFT JOIN business.machinetypes mt ON m.machine_name::text = mt.machinetype_code::text
			LEFT JOIN business.technician te ON p.project_technician = te.technician_id
			LEFT JOIN business.areas a ON p.project_area = a.area_code
			LEFT JOIN business.marks ma ON m.machine_mark::text = ma.mark_code::text
			LEFT JOIN business.models mo ON m.machine_model = mo.model_id
			LEFT JOIN business.municipios mu ON a.area_municipality = mu.municipio_codigo
			LEFT JOIN business.departamentos de ON mu.municipio_departamento = de.departamento_codigo
			LEFT JOIN business.contacts co ON a.area_code = co.contact_area
			LEFT JOIN business.projectzones pz ON p.project_zone = pz.projectzone_code
			LEFT JOIN business.users u ON p.project_user_creation = u.user_id 
			WHERE p.project_machine = '$placa' AND p.project_id = $projecto ";
	}

	public function getDetail($arrayForm)
	{
		// Prepare response
		$response = new stdClass();
		$response->success = false;	

		// Recuperar projecto maquina
		$project = pg_query("SELECT MAX(p.project_id) AS project_number 
			FROM business.projects AS p
			INNER JOIN business.machines AS m ON p.project_machine = m.machine_plate
			INNER JOIN business.clients AS c ON p.project_client = c.client_costcenter
			WHERE project_machine = {$arrayForm['placa']}");

		$project = pg_fetch_array($project);
		if(!isset($project['project_number']) || !is_numeric($project['project_number'])) {
		    $response->msg = "No es posible recuperar informacion de la maquina {$arrayForm['placa']}, por favor verifique n&uacute;mero de placa.";
		    return $response;
		}

		// Recuperar maquina
		$machine =  pg_query($this->getMachine($arrayForm['placa'], $project['project_number']));
		$machine = pg_fetch_array($machine);
		if(!isset($machine['machine_plate']) || !is_numeric($machine['machine_plate'])) {
		    $response->msg = "No es posible recuperar informacion de la maquina {$arrayForm['placa']}, por favor verifique n&uacute;mero de placa.";
			return $response;
		}

		$response->machine = $machine;
		$response->success = true;	
		return $response;
	}
}

?>