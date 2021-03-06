<?php 
header("Content-Type: text/html;charset=utf-8");

class ReporteDAO
{

	private static function _connectDBInstance()
	{
		Conexion::$connect = Conexion::connectionDinamic("localhost", "3306", "evaluaciones", "root", "jonas", "utf8");
		Conexion::$connect->query("SET NAMES 'utf8'");
	}

	public static function executeQuery($sql, $fetch = ""){

		try 
		{
			Conexion::$connect = new Conexion();
			Conexion::$query = $sql;
			Conexion::$result = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$result->execute();
			if( empty($fetch) )
				return Conexion::$result->fetchAll(PDO::FETCH_ASSOC);
			else 
				return Conexion::$result->fetchAll(PDO::FETCH_NUM);
		}
		catch(Exception $e)
		{
			die("Error al ejecutar consulta $sql. Error! : ". $e->getMessage());
		}

	}

	public static function BitacoraReportes($filtro = ""){
		$where = empty($filtro) ? "" : "WHERE $filtro";
		$sql = "SELECT a.*, ROUND(TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60,2) tiempo, CONCAT(ad_sig.cl,'-', a.id, '/', year) folio FROM bitacora a LEFT JOIN si_usr b ON id_ingeniero = b.id LEFT JOIN ad_sig on b.cl = ad_sig.ix $where ORDER BY a.id desc limit 1000";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function BitacoraReportesExcel($filtro = ""){
		$where = empty($filtro) ? "" : "WHERE $filtro";
		#replace(solucion_escalado,'\\n',' ') solucion_escalado
		$sql = "SELECT  CONCAT(ad_sig.cl,'-', a.id, '/', year) folio, nombre,evento,estado,impacto,equipo,proveedor,
		equipo_clli,replace(comentarios,'\\n',' ') comentarios,lugar,cobo,subevento,causa_falla,imputable,area,fecha_falla,fecha_reporte_falla,fecha_soporte,fecha_fin_falla,ROUND(TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60,2) tiempo,
		reporte_refaccion,cantidad_refaccion,codigos_refaccion,fecha_escalado,reporte_escalado,fecha_fin_escalado 
		FROM bitacora a LEFT JOIN si_usr b ON 
		id_ingeniero = b.id LEFT JOIN ad_sig on b.cl = ad_sig.ix $where ORDER BY a.id desc";
		$total = self::executeQuery($sql, 'FETCH_NUM');
		return $total;
	}

	public static function IngenierosSoporte($filtro = ""){
		$where = empty($filtro) ? "" : "AND $filtro";
		$sql = "SELECT a.id, UPPER(concat(ap,' ',am,' ',a.no)) label, ad_sig.cl FROM si_usr a LEFT JOIN ad_sig 
		ON a.cl = ad_sig.ix WHERE a.ux = 0 $where ORDER BY ap";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Clientes($filtro = ""){
		$sql = "SELECT id, nom_completo label FROM clientes WHERE ux = 0 GROUP BY nom_completo ORDER BY nom_completo";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Entidades($filtro = ""){
		$where = empty($filtro) ? "" : "WHERE $filtro";
		$sql = "SELECT id, no label FROM entidades WHERE ux = 0 ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Equipos($filtro = ""){
		$where = ""; //empty($filtro) ? "" : "AND $filtro";
		$sql = "SELECT id, no label FROM equipos WHERE ux = 0 $where ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Lugares($filtro = ""){
		$where = empty($filtro) ? "" : "AND $filtro";
		$sql = "SELECT id, no label FROM lugares WHERE ux = 0 ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Proveedores($filtro = ""){
		$where = empty($filtro) ? "" : "WHERE $filtro";
		$sql = "SELECT id, no label FROM proveedores WHERE ux = 0 GROUP BY no ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Turno($id){
		$sql = "SELECT turno, sitio FROM ad_tur WHERE id_usr = $id";
		$t = self::executeQuery($sql);
		return $t[0];
	}

	public static function RegistraReporte($data){

		try{

			$sql = "INSERT INTO bitacora 
			(usuario_captura, nombre,id_ingeniero, fecha_falla, fecha_soporte, impacto, comentarios, estado, fechaDeCaptura, `year`, activo, nombre_reporta,entidad,proveedor,evento,fecha_reporte_falla,lugar,equipo,reporte_escalado,fecha_escalado,fecha_fin_escalado,asistencia_proveedor,solucion_escalado,cobo,subevento,causa_falla,imputable,area,tur,sit,equipo_clli,reporte_refaccion,cantidad_refaccion,codigos_refaccion,origen_refaccion) 
			VALUES (:uc, :no, :ii, :ff, :fs, :im, :co, :es, :fcap, :y, :ac, :nombre_reporta,:entidad,:proveedor,:evento,:fecha_reporte_falla,:lugar,:equipo,:reporte_escalado,:fecha_escalado,:fecha_fin_escalado,:asistencia_proveedor,:solucion_escalado,:cobo,:subevento,:causa_falla,:imputable,:area,:tur,:sit,:equipo_clli,:reporte_refaccion,:cantidad_refaccion,:codigos_refaccion,:origen_refaccion)";
			Conexion::$connect = new Conexion();

			$t = self::Turno($data['id_ingeniero']);

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':uc', $data['captura']);
			Conexion::$prepare->bindParam(':no', $data['nombre']);
			Conexion::$prepare->bindParam(':ii', $data['id_ingeniero']);

			Conexion::$prepare->bindParam(':ff', $data['fecha_falla']);
			Conexion::$prepare->bindParam(':fs', $data['fecha_soporte']);
			// Conexion::$prepare->bindParam(':dpe', "0000/00/00 00:00");

			Conexion::$prepare->bindParam(':im', $data['impacto']);
			Conexion::$prepare->bindParam(':co', $data['comentarios']);
			Conexion::$prepare->bindParam(':es', $data['estado']);

			Conexion::$prepare->bindParam(':nombre_reporta', $data['nombre_reporta']);
			Conexion::$prepare->bindParam(':entidad', $data['entidad']);
			Conexion::$prepare->bindParam(':proveedor', $data['proveedor']);
			Conexion::$prepare->bindParam(':evento', $data['evento']);
			Conexion::$prepare->bindParam(':fecha_reporte_falla', $data['fecha_reporte_falla']);
			Conexion::$prepare->bindParam(':lugar', $data['lugar']);
			Conexion::$prepare->bindParam(':equipo', $data['equipo']);
			Conexion::$prepare->bindParam(':equipo_clli', $data['equipo_clli']);

			Conexion::$prepare->bindParam(':reporte_escalado', $data['reporte_escalado']);
			Conexion::$prepare->bindParam(':fecha_escalado', $data['fecha_escalado']);
			Conexion::$prepare->bindParam(':fecha_fin_escalado', $data['fecha_fin_escalado']);
			Conexion::$prepare->bindParam(':asistencia_proveedor', $data['asistencia_proveedor']);
			Conexion::$prepare->bindParam(':solucion_escalado', $data['solucion_escalado']);
			Conexion::$prepare->bindParam(':cobo', $data['cobo']);
			Conexion::$prepare->bindParam(':subevento', $data['subevento']);
			Conexion::$prepare->bindParam(':causa_falla', $data['causa_falla']);
			Conexion::$prepare->bindParam(':imputable', $data['imputable']);
			Conexion::$prepare->bindParam(':area', $data['area']);

			Conexion::$prepare->bindParam(':reporte_refaccion', $data['reporte_refaccion']);
			Conexion::$prepare->bindParam(':cantidad_refaccion', $data['cantidad_refaccion']);

			$cod = trim(preg_replace('/\s+/', '', $data['codigos_refaccion']));
			Conexion::$prepare->bindParam(':codigos_refaccion', $cod);
			Conexion::$prepare->bindParam(':origen_refaccion', $data['origen_refaccion']);

			$now = date('Y-m-d H:i:s');
			$year = date('Y');
			Conexion::$prepare->bindParam(':fcap', $now);
			Conexion::$prepare->bindParam(':y', $year);
			Conexion::$prepare->bindParam(':ac', $data['activo']);

			Conexion::$prepare->bindParam(':tur', $t['turno']);
			Conexion::$prepare->bindParam(':sit', $t['sitio']);


			$result = Conexion::$prepare->execute();
			return $result;
		}catch( Exception $e ){
			die("Error al registrar reporte. Error! : ". $e->getMessage());
		}

	}


	public static function ModificaReporte($data){

		try{

			$sql = "UPDATE bitacora SET 
			nombre = :no,
			id_ingeniero = :ii,
			fecha_falla = :ff,
			fecha_soporte = :fs,
			impacto = :im,
			comentarios = :co,
			nombre_reporta = :nombre_reporta,
			entidad = :entidad,
			proveedor = :proveedor,
			evento = :evento,
			fecha_reporte_falla = :fecha_reporte_falla,
			lugar = :lugar,
			
			reporte_escalado = :reporte_escalado,
			fecha_escalado = :fecha_escalado,
			fecha_fin_escalado = :fecha_fin_escalado,
			asistencia_proveedor = :asistencia_proveedor,
			solucion_escalado = :solucion_escalado,
			cobo = :cobo,
			subevento = :subevento,
			causa_falla = :causa_falla,
			imputable = :imputable,
			area = :area,

			reporte_refaccion = :reporte_refaccion,
			cantidad_refaccion = :cantidad_refaccion,
			codigos_refaccion = :codigos_refaccion,
			origen_refaccion = :origen_refaccion,

			solucion = :solucion,
			equipo = :equipo,
			equipo_clli = :equipo_clli
			WHERE id = :id";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':no', $data['nombre']);
			Conexion::$prepare->bindParam(':ii', $data['id_ingeniero']);
			Conexion::$prepare->bindParam(':ff', $data['fecha_falla']);
			Conexion::$prepare->bindParam(':fs', $data['fecha_soporte']);
			Conexion::$prepare->bindParam(':im', $data['impacto']);
			Conexion::$prepare->bindParam(':co', $data['comentarios']);

			Conexion::$prepare->bindParam(':nombre_reporta', $data['nombre_reporta']);
			Conexion::$prepare->bindParam(':entidad', $data['entidad']);
			Conexion::$prepare->bindParam(':proveedor', $data['proveedor']);
			Conexion::$prepare->bindParam(':evento', $data['evento']);
			Conexion::$prepare->bindParam(':fecha_reporte_falla', $data['fecha_reporte_falla']);
			Conexion::$prepare->bindParam(':lugar', $data['lugar']);


			Conexion::$prepare->bindParam(':reporte_escalado', $data['reporte_escalado']);
			Conexion::$prepare->bindParam(':fecha_escalado', $data['fecha_escalado']);
			Conexion::$prepare->bindParam(':fecha_fin_escalado', $data['fecha_fin_escalado']);
			Conexion::$prepare->bindParam(':asistencia_proveedor', $data['asistencia_proveedor']);
			Conexion::$prepare->bindParam(':solucion_escalado', $data['solucion_escalado']);
			Conexion::$prepare->bindParam(':cobo', $data['cobo']);
			Conexion::$prepare->bindParam(':subevento', $data['subevento']);
			Conexion::$prepare->bindParam(':causa_falla', $data['causa_falla']);
			Conexion::$prepare->bindParam(':imputable', $data['imputable']);
			Conexion::$prepare->bindParam(':area', $data['area']);

			Conexion::$prepare->bindParam(':reporte_refaccion', $data['reporte_refaccion']);
			Conexion::$prepare->bindParam(':cantidad_refaccion', $data['cantidad_refaccion']);
			Conexion::$prepare->bindParam(':codigos_refaccion', $data['codigos_refaccion']);
			Conexion::$prepare->bindParam(':origen_refaccion', $data['origen_refaccion']);

			// Conexion::$prepare->bindParam(':fecha_fin_falla', $data['fecha_fin_falla']);
			Conexion::$prepare->bindParam(':solucion', $data['solucion']);
			Conexion::$prepare->bindParam(':equipo', $data['equipo']);
			Conexion::$prepare->bindParam(':equipo_clli', $data['equipo_clli']);

			Conexion::$prepare->bindParam(':id', $data['id']);
			$result = Conexion::$prepare->execute();

			return $result;
		}catch( Exception $e ){
			die("Error al registrar reporte. Error! : ". $e->getMessage());
		}

	}


	public static function ModificaEstado($data){

		try{

			$sql = "UPDATE bitacora SET 
			estado = :estado,
			fecha_fin_falla = :ff,
			solucion = :so,
			fechaDeCancelacion = :fc
			WHERE id = :id";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':estado', $data['estado']);
			Conexion::$prepare->bindParam(':ff', $data['fecha_fin_falla_upd']);
			Conexion::$prepare->bindParam(':so', $data['solucion']);
			Conexion::$prepare->bindParam(':fc', $data['fecha_cancelacion']);
			Conexion::$prepare->bindParam(':id', $data['id_rep_fin']);
			$result = Conexion::$prepare->execute();

			return $result;
		}catch( Exception $e ){
			die("Error al registrar reporte. Error! : ". $e->getMessage());
		}

	}


	public static function RegistrarAnexo($data){

		try{

			$sql = "INSERT INTO si_doc (fx, rx, no, no_generado, tp) VALUES (:fx, :rx, :no, :no_generado, :tp) ";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':fx', $data['fx']);
			Conexion::$prepare->bindParam(':rx', $data['rx']);
			Conexion::$prepare->bindParam(':no', $data['no']);
			Conexion::$prepare->bindParam(':no_generado', $data['no_generado']);
			Conexion::$prepare->bindParam(':tp', $data['tp']);

			$result = Conexion::$prepare->execute();
			return $result;

		}catch( Exception $e ){
			die("Error al registrar anexo. Error! : ". $e->getMessage());
		}

	}

	public static function Anexos($data){

		try{

			$sql = "SELECT * FROM si_doc WHERE rx = :id";
			Conexion::$connect = new Conexion();
			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':id', $data['id']);

			Conexion::$prepare->execute();
			return Conexion::$prepare->fetchAll(PDO::FETCH_ASSOC);

		}catch( Exception $e ){
			die("Error al obtener anexos de evaluación. Error! : ". $e->getMessage());
		}

	}

	public static function EscalarReporte($data){

		try{

			$sql = "UPDATE bitacora SET 
			reporte_escalado = :reporte_escalado,
			ingeniero_escalado = :ingeniero_escalado,
			proveedor_escalado = :proveedor_escalado,
			solucion_escalado = :solucion_escalado,
			fecha_escalado = :fecha_escalado
			WHERE id = :id_rep_escalado";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':reporte_escalado', $data['reporte_escalado']);
			Conexion::$prepare->bindParam(':ingeniero_escalado', $data['ingeniero_escalado']);
			Conexion::$prepare->bindParam(':proveedor_escalado', $data['proveedor_escalado']);
			Conexion::$prepare->bindParam(':solucion_escalado', $data['solucion_escalado']);
			Conexion::$prepare->bindParam(':fecha_escalado', $data['fecha_escalado']);
			Conexion::$prepare->bindParam(':id_rep_escalado', $data['id_rep_escalado']);
			$result = Conexion::$prepare->execute();

			return $result;
		}catch( Exception $e ){
			die("Error al registrar reporte. Error! : ". $e->getMessage());
		}

	}

	
}
