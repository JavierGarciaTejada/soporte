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
		$sql = "SELECT id, concat(ap,' ',am,' ',no) nombre FROM si_usr WHERE ux = 0 ORDER BY ap";
		$total['data'] = self::executeQuery($sql);
		$total['sql'] = $sql;
		return $total;
	}

	public static function RegistraReporte($data){

		try{

			$sql = "INSERT INTO bitacora 
			(usuario_captura, nombre, fecha_falla, fecha_soporte, asunto, impacto, comentarios, estado, fechaDeCaptura, `year`, activo) 
			VALUES (:uc, :no, :ff, :fs, :asu, :im, :co, :es, :fcap, :y, :ac)";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':uc', $data['captura']);
			Conexion::$prepare->bindParam(':no', $data['nombre']);

			Conexion::$prepare->bindParam(':ff', $data['fecha_falla']);
			Conexion::$prepare->bindParam(':fs', $data['fecha_soporte']);
			// Conexion::$prepare->bindParam(':dpe', "0000/00/00 00:00");

			Conexion::$prepare->bindParam(':asu', $data['asunto']);
			Conexion::$prepare->bindParam(':im', $data['impacto']);
			Conexion::$prepare->bindParam(':co', $data['comentarios']);
			Conexion::$prepare->bindParam(':es', $data['estado']);

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
			asunto = :asu,
			impacto = :im,
			comentarios = :co
			WHERE id = :id";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':no', $data['nombre']);
			Conexion::$prepare->bindParam(':ff', $data['fecha_falla']);
			Conexion::$prepare->bindParam(':fs', $data['fecha_soporte']);
			Conexion::$prepare->bindParam(':asu', $data['asunto']);
			Conexion::$prepare->bindParam(':im', $data['impacto']);
			Conexion::$prepare->bindParam(':co', $data['comentarios']);
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
			fechaDeCancelacion = :fc
			WHERE id = :id";
			Conexion::$connect = new Conexion();

			Conexion::$query = $sql;
			Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$prepare->bindParam(':estado', $data['estado']);
			Conexion::$prepare->bindParam(':fc', $data['fecha_cancelacion']);
			Conexion::$prepare->bindParam(':id', $data['id']);
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

	
}
