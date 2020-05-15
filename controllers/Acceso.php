<?php 

defined("PROJECTPATH") or die("Access error");
require_once Config::$configuration->get('modelsFolder') . 'InventarioDAO.php';

class Acceso
{
	const PATH_EVALUACIONES = "acceso/";

	
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
			'title' => 'Red de Acceso',
			'css' => array(
				'librerias/DataTables/DataTables-1.10.18/css/dataTables.bootstrap.min.css',
				'librerias/DataTables/Buttons-1.5.6/css/buttons.bootstrap.min.css',
				// 'librerias/Bootstrap/bootstrapvalidator-master/dist/css/bootstrapValidator.min.css',
				// 'librerias/jquery-ui-1.12.1/jquery-ui.css',
				// 'librerias/bootstrap-datetimepicker/jquery.datetimepicker.css',
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
				'js/inventario_acceso.js'
			)
		);
		$this->view->show("inventario_acceso.phtml", $arr);
	}

	public function getInventario(){
		//$filtro = Perfil::FiltroUsuarioGerencia();
		$inventario = InventarioDAO::InventarioRedDivArea('inventario_acceso');
		Funciones::imprimeJson($inventario);
	}


}
