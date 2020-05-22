<?php 

defined("PROJECTPATH") or die("Access error");
require_once Config::$configuration->get('modelsFolder') . 'InventarioDAO.php';

class Inventario
{
	const PATH_EVALUACIONES = "inventario/";

	
	public function __construct()
	{
		$this->view = new View();
		Config::$configuration->set('ruta_login', '/index.php/login/');
		//Session::$_name_session = "ADMINAFA";
		Session::validaSession();
		$autorization = array('Administrador', 'Supervisor', 'Autorizador', 'Solicitante', 'Consultante');
		if(! in_array(Session::getSession("role"), $autorization) ) 
		{
			$arr = array('title' => 'Sin Privilegios');
			$this->view->show("no_privileges.html", $arr);
			die();
		}
	}
	
	public function index()
	{
		//$evaluaciones = EvaluacionesDAO::TotalEvaluacionesPorPerfil();
		$arr = array(
			'title' => 'Inventario Refacciones',
			'css' => array(
				'librerias/DataTables/DataTables-1.10.18/css/dataTables.bootstrap.min.css',
				'librerias/DataTables/Buttons-1.5.6/css/buttons.bootstrap.min.css',
				// 'librerias/Bootstrap/bootstrapvalidator-master/dist/css/bootstrapValidator.min.css',
				// 'librerias/jquery-ui-1.12.1/jquery-ui.css',
				// 'librerias/bootstrap-datetimepicker/jquery.datetimepicker.css',
				'librerias/tabulator/dist/css/tabulator.min.css',
				//'librerias/tabulator/dist/css/bootstrap/tabulator_bootstrap4.min.css',
				'librerias/tabulator/dist/css/bulma/tabulator_bulma.min.css',
				//'librerias/tabulator/dist/css/materialize/tabulator_materialize.min.css',
				//'librerias/tabulator/dist/css/semantic-ui/tabulator_semantic-ui.min.css',
				'css/inventario.css'
			),
			'js' => array(
				// 'librerias/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js',
				// 'librerias/DataTables/FixedHeader-3.1.4/js/dataTables.fixedHeader.js',
				// 'librerias/DataTables/DataTables-1.10.18/js/dataTables.bootstrap.min.js',
				// 'librerias/DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js',
				// 'librerias/DataTables/Buttons-1.5.6/js/jszip.min.js',
				// 'librerias/DataTables/Buttons-1.5.6/js/pdfmake.min.js',
				// 'librerias/DataTables/Buttons-1.5.6/js/vfs_fonts.js',

				// 'librerias/DataTables/Buttons-1.5.6/js/buttons.html5.min.js',
				// 'librerias/DataTables/Buttons-1.5.6/js/buttons.print.min.js',

				// 'librerias/Bootstrap/bootstrapvalidator-master/dist/js/bootstrapValidator.min.js',
				// 'librerias/jquery-ui-1.12.1/jquery-ui.js',
				// 'librerias/moment/moment.js',
				// 'librerias/bootstrap-datetimepicker/jquery.datetimepicker.full.js',
				'librerias/tabulator/dist/js/tabulator.min.js',
				'librerias/tabulator/dist/js/modules/xlsx.full.min.js',
				'js/inventario.js'
			)
		);
		$this->view->show("inventario.phtml", $arr);
	}

	public function getInventario(){
		$inventario = InventarioDAO::Inventario();
		Funciones::imprimeJson($inventario);
	}

	public function registro(){
		$data = Funciones::getDataPost();
		$out = InventarioDAO::Registro($data);
		Funciones::imprimeJson($out);
	}

	public function cambio(){
		$data = Funciones::getDataPost();
		$out = InventarioDAO::Cambio($data);
		Funciones::imprimeJson($out);
	}

}
