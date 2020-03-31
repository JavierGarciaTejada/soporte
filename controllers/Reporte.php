<?php 

defined("PROJECTPATH") or die("Access error");
require_once Config::$configuration->get('modelsFolder') . 'ReporteDAO.php';

class Reporte
{
	const PATH_EVALUACIONES = "reporte/";

	
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
			'title' => 'Bitácora de Reportes',
			'css' => array(
				'librerias/DataTables/DataTables-1.10.18/css/dataTables.bootstrap.min.css',
				'librerias/DataTables/Buttons-1.5.6/css/buttons.bootstrap.min.css',
				'librerias/Bootstrap/bootstrapvalidator-master/dist/css/bootstrapValidator.min.css',
				'librerias/jquery-ui-1.12.1/jquery-ui.css',
				'librerias/bootstrap-datetimepicker/jquery.datetimepicker.css'
			),
			'js' => array(
				'librerias/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js',
				'librerias/DataTables/FixedHeader-3.1.4/js/dataTables.fixedHeader.js',
				'librerias/DataTables/DataTables-1.10.18/js/dataTables.bootstrap.min.js',
				'librerias/DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js',
				'librerias/DataTables/Buttons-1.5.6/js/jszip.min.js',
				'librerias/DataTables/Buttons-1.5.6/js/pdfmake.min.js',
				'librerias/DataTables/Buttons-1.5.6/js/vfs_fonts.js',

				'librerias/DataTables/Buttons-1.5.6/js/buttons.html5.min.js',
				'librerias/DataTables/Buttons-1.5.6/js/buttons.print.min.js',

				'librerias/Bootstrap/bootstrapvalidator-master/dist/js/bootstrapValidator.min.js',
				'librerias/jquery-ui-1.12.1/jquery-ui.js',
				'librerias/moment/moment.js',
				'librerias/bootstrap-datetimepicker/jquery.datetimepicker.full.js',
				'js/validaciones/reporte-validation.js',
				'js/reporte.js'
			)
		);
		$this->view->show("reporte.phtml", $arr);
	}

	public function getReportes(){
		$filtro = Perfil::FiltroBitacoraSoporte();
		$reportes = ReporteDAO::BitacoraReportes($filtro);
		Funciones::imprimeJson($reportes);
	}

	public function getIngenieros(){
		$ingenieros = ReporteDAO::IngenierosSoporte();
		Funciones::imprimeJson($ingenieros);
	}

	public function getClientes(){
		$clientes = ReporteDAO::Clientes();
		Funciones::imprimeJson($clientes);
	}

	public function getEntidades(){
		$entidad = ReporteDAO::Entidades();
		Funciones::imprimeJson($entidad);
	}

	public function getEquipos(){
		$equipos = ReporteDAO::Equipos();
		Funciones::imprimeJson($equipos);
	}

	public function getLugares(){
		$lugares = ReporteDAO::Lugares();
		Funciones::imprimeJson($lugares);
	}

	public function getProveedores(){
		$lugares = ReporteDAO::Proveedores();
		Funciones::imprimeJson($lugares);
	}

	public function registraReporte(){
		$data = Funciones::getDataPost();
		$data['estado'] = "En Proceso";
		$data['captura'] = Session::getSession("id");
		$data['activo'] = "1";
 		$insert = ReporteDAO::RegistraReporte($data);
		Funciones::imprimeJson($insert);
	}

	public function modificarReporte(){
		$data = Funciones::getDataPost();
 		$update = ReporteDAO::ModificaReporte($data);
		Funciones::imprimeJson($update);
	}

	public function finalizarReporte(){
		$data = Funciones::getDataPost();
		$data['estado'] = "Liquidado";
		$data['solucion'] = $data['solucion_fin'];
		$data['fecha_cancelacion'] = '0000-00-00 00:00:00';
 		$update = ReporteDAO::ModificaEstado($data);
		Funciones::imprimeJson($update);
	}

	public function cancelarReporte(){
		$data = Funciones::getDataPost();
		$data['id_rep_fin'] = $data['id'];
		$data['estado'] = "Cancelado";
		$data['fecha_cancelacion'] = date('Y-m-d H:i:s');
 		$update = ReporteDAO::ModificaEstado($data);
		Funciones::imprimeJson($update);
	}

	public function cargaAnexosReporte(){

		$result = array();
		$result['estatus'] = false;
		$result['nombreGenerado'] = null;
		$result['mensaje'] = "El archivo no pudo ser almacenado";

		//comprobamos que sea una petición ajax
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
		{

			$file = $_FILES['archivo']['name'];
			$exp = explode(".", $file);

			//genera un nombre de archivo para evitar caracteres especiales
			$nombreReporte = "reporte_". date('Ymd_his') .".". end($exp);
			$result['nombreGenerado'] = $nombreReporte;
			$result['nombre'] = $file;
			$ruta = Config::$configuration->get('anexos_path') ."reportes/" . $nombreReporte;

			//comprobamos si el archivo ha subido
			if ($file && move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta))
			{
				sleep(1);
				$result['estatus'] = true;
				$result['extension'] = end($exp);
				$result['mensaje'] = "Archivo subido correctamente";
			}

		}else{
			throw new Exception("Error Processing Request", 1);
		}

		Funciones::imprimeJson($result);

	}


	public function registaAnexo(){

		$data = Funciones::getDataPost();
		$data['fx'] = date('Y-m-d h:i:s');
		$insert = ReporteDAO::RegistrarAnexo($data);
		Funciones::imprimeJson($insert);

	}

	public function getAnexos(){

		$data = Funciones::getDataGet();
		$anexos = ReporteDAO::Anexos($data);
		Funciones::imprimeJson($anexos);

	}


	public function escalarReporte(){
		$data = Funciones::getDataPost();
 		$update = ReporteDAO::EscalarReporte($data);
		Funciones::imprimeJson($update);
	}


}
