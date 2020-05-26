<?php 

defined("PROJECTPATH") or die("Access error");
require_once Config::$configuration->get('modelsFolder') . 'ReporteDAO.php';
require_once Config::$configuration->get('modelsFolder') . 'ServerSideDAO.php';
require_once PROJECTPATH . '/libraries/spout-3.1.0/src/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;

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
		$filtro = Perfil::FiltroUsuarioGerenciaView();
		$data = Funciones::getDataGet();
		//$reportes = ReporteDAO::BitacoraReportes($filtro);
		$reportes = ServerSideDAO::get($data, $filtro, 'bitacora_view', 'id', array('id','folio','cl','tiempo','usuario_captura','nombre','id_ingeniero','fecha_falla','fecha_soporte','fecha_fin_falla','asunto','impacto','comentarios','estado','fechaDeCaptura','fechaDeCancelacion','solucion','year','archivo_adjunto','archivo_nombre','activo','nombre_reporta','entidad','evento','fecha_reporte_falla','lugar','equipo','reporte_escalado','ingeniero_escalado','proveedor_escalado','asistencia_proveedor','solucion_escalado','fecha_escalado','cobo','proveedor','subevento','causa_falla','imputable','area','fecha_fin_escalado','tur','sit','equipo_clli','reporte_refaccion','cantidad_refaccion','codigos_refaccion','origen_refaccion'));
		Funciones::imprimeJson($reportes);
	}

	public function getIngenieros(){
		$filtro = Perfil::FiltroUsuarioGerencia();
		$ingenieros = ReporteDAO::IngenierosSoporte($filtro);
		Funciones::imprimeJson($ingenieros);
	}

	public function getClientes(){
		$filtro = Perfil::FiltroUsuarioGerencia();
		$clientes = ReporteDAO::Clientes($filtro);
		Funciones::imprimeJson($clientes);
	}

	public function getEntidades(){
		$entidad = ReporteDAO::Entidades();
		Funciones::imprimeJson($entidad);
	}

	public function getEquipos(){
		$filtro = Perfil::FiltroLugarEquipo();
		$equipos = ReporteDAO::Equipos($filtro);
		Funciones::imprimeJson($equipos);
	}

	public function getLugares(){
		$filtro = Perfil::FiltroLugarEquipo();
		$lugares = ReporteDAO::Lugares($filtro);
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


	public function getExcelSpout(){
		
		$writer = WriterEntityFactory::createXLSXWriter();

		$filePath = PROJECTPATH ."/anexos/reportes/soportes_".Session::getSession("id").".xlsx";
		$fileExport = "../../anexos/reportes/soportes_".Session::getSession("id").".xlsx";
		$writer->openToFile($filePath);

		$styleHead = (new StyleBuilder())
           ->setFontBold()
           ->setFontSize(12)
           ->setFontColor(Color::rgb(0,0,122))
           #->setShouldWrapText()
           ->setCellAlignment(CellAlignment::CENTER)
           #->setBackgroundColor(Color::YELLOW)
           ->build();

		$headers = array('No Reporte','Ingeniero','Evento','Estatus','Impacto','Equipo','Proveedor','Nombre / CLLI','Falla','Lugar','Cobo','Tipo Evento','Causa','Imputable','Area','Inicio de Falla','Aviso a Soporte','Inicio de Soporte','Fin de Soporte','Min Atención','Rep Refacción','Cant Refacciones','Códigos','Fecha Escalación','Proveedor Escalado','Fecha Fin Escalado');
		$writer->addRow( WriterEntityFactory::createRowFromArray($headers, $styleHead) );


		$styleBody = (new StyleBuilder())
           ->setFontName('Arial')
           ->setFontSize(9)
           ->build();

		$filtro = Perfil::FiltroUsuarioGerencia();
		$reportes = ReporteDAO::BitacoraReportesExcel($filtro);
		foreach ($reportes as $key => $value) {
			$writer->addRow( WriterEntityFactory::createRowFromArray($value, $styleBody) );
		}

		$writer->close();
		$out = array("path" => $fileExport, 'status' => 'OK');

		Funciones::imprimeJson($out);

	}


}
