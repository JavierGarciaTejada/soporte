<?php 
header("Content-Type: text/html;charset=utf-8");

class ReporteDAO
{

	private static function _connectDBInstance()
	{
		Conexion::$connect = Conexion::connectionDinamic("localhost", "3306", "evaluaciones", "root", "jonas", "utf8");
		Conexion::$connect->query("SET NAMES 'utf8'");
	}

	public static function executeQuery($sql){

		try 
		{
			Conexion::$connect = new Conexion();
			Conexion::$query = $sql;
			Conexion::$result = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$result->execute();
			return Conexion::$result->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(Exception $e)
		{
			die("Error al ejecutar consulta $sql. Error! : ". $e->getMessage());
		}

	}

	public static function BitacoraReportes(){
		$sql = "SELECT *, CONCAT('LEPR-', id, '/', year) folio FROM bitacora ORDER BY id desc";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function IngenierosSoporte(){
		$sql = "SELECT id, UPPER(concat(ap,' ',am,' ',no)) label FROM si_usr WHERE ux = 0 ORDER BY ap";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Clientes(){
		$sql = "SELECT id, nom_completo label FROM clientes WHERE ux = 0 ORDER BY nom_completo";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Entidades(){
		$sql = "SELECT id, no label FROM entidades WHERE ux = 0 ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Equipos(){
		$sql = "SELECT id, no label FROM equipos WHERE ux = 0 ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Lugares(){
		$sql = "SELECT id, no label FROM lugares WHERE ux = 0 ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function Proveedores(){
		$sql = "SELECT id, no FROM proveedores WHERE ux = 0 ORDER BY no";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function RegistraReporte($data){

		try{

			$sql = "INSERT INTO bitacora 
			(usuario_captura, nombre, fecha_falla, fecha_soporte, impacto, comentarios, estado, fechaDeCaptura, `year`, activo, nombre_reporta,entidad,evento,fecha_reporte_falla,lugar,equipo) 
			VALUES (:uc, :no, :ff, :fs, :im, :co, :es, :fcap, :y, :ac, :nombre_reporta,:entidad,:evento,:fecha_reporte_falla,:lugar,:equipo)";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':uc', $data['captura']);
			Conexion::$prepare->bindParam(':no', $data['nombre']);

			Conexion::$prepare->bindParam(':ff', $data['fecha_falla']);
			Conexion::$prepare->bindParam(':fs', $data['fecha_soporte']);
			// Conexion::$prepare->bindParam(':dpe', "0000/00/00 00:00");

			Conexion::$prepare->bindParam(':im', $data['impacto']);
			Conexion::$prepare->bindParam(':co', $data['comentarios']);
			Conexion::$prepare->bindParam(':es', $data['estado']);

			Conexion::$prepare->bindParam(':nombre_reporta', $data['nombre_reporta']);
			Conexion::$prepare->bindParam(':entidad', $data['entidad']);
			Conexion::$prepare->bindParam(':evento', $data['evento']);
			Conexion::$prepare->bindParam(':fecha_reporte_falla', $data['fecha_reporte_falla']);
			Conexion::$prepare->bindParam(':lugar', $data['lugar']);
			Conexion::$prepare->bindParam(':equipo', $data['equipo']);

			$now = date('Y-m-d H:i:s');
			$year = date('Y');
			Conexion::$prepare->bindParam(':fcap', $now);
			// Conexion::$prepare->bindParam(':fcan', "0000/00/00 00:00");
			Conexion::$prepare->bindParam(':y', $year);
			Conexion::$prepare->bindParam(':ac', $data['activo']);
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
			fecha_falla = :ff,
			fecha_soporte = :fs,
			impacto = :im,
			comentarios = :co,
			nombre_reporta = :nombre_reporta,
			entidad = :entidad,
			evento = :evento,
			fecha_reporte_falla = :fecha_reporte_falla,
			lugar = :lugar,
			fecha_fin_falla = :fecha_fin_falla,
			solucion = :solucion,
			equipo = :equipo
			WHERE id = :id";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':no', $data['nombre']);
			Conexion::$prepare->bindParam(':ff', $data['fecha_falla']);
			Conexion::$prepare->bindParam(':fs', $data['fecha_soporte']);
			Conexion::$prepare->bindParam(':im', $data['impacto']);
			Conexion::$prepare->bindParam(':co', $data['comentarios']);

			Conexion::$prepare->bindParam(':nombre_reporta', $data['nombre_reporta']);
			Conexion::$prepare->bindParam(':entidad', $data['entidad']);
			Conexion::$prepare->bindParam(':evento', $data['evento']);
			Conexion::$prepare->bindParam(':fecha_reporte_falla', $data['fecha_reporte_falla']);
			Conexion::$prepare->bindParam(':lugar', $data['lugar']);
			Conexion::$prepare->bindParam(':fecha_fin_falla', $data['fecha_fin_falla']);
			Conexion::$prepare->bindParam(':solucion', $data['solucion']);
			Conexion::$prepare->bindParam(':equipo', $data['equipo']);

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
