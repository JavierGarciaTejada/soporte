<?php 
header("Content-Type: text/html;charset=utf-8");

class InformeDAO
{

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

	public static function Filtro($filtro){
		$f = array();
		if( ! empty($filtro['fecha_soporte']) )
			array_push($f, "fecha_soporte >= '".$filtro['fecha_soporte']."'");

		if( ! empty($filtro['fecha_soporte']) )
			array_push($f,"fecha_soporte >= '".$filtro['fecha_soporte']."'");

		if( ! empty($filtro['gerencia']) )
			array_push($f,"c.cl = '".$filtro['gerencia']."'");
		
		$fil = ( count($f) > 0 ) ?  ' WHERE '.join(" AND ", $f) : "";

		return $fil;
	}

	public static function InformeEventos(){
		$sql = "SELECT a.*, CONCAT('LEPR-', a.id, '/', year) folio, c.cl gerencia, if(impacto = 'MIMPSA', 'SIN AFECTACION', 'CON AFECTACION') afectacion, TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60 tiempo FROM bitacora a inner join si_usr b on usuario_captura = b.id inner join ad_sig c on b.cl = c.ix ORDER BY a.id desc";
		$data = self::executeQuery($sql);
		$detalle['promedio'] = 0;
		foreach ($data as $key => $value) {
			$detalle['promedio'] += (int)$value['tiempo'];
			$detalle['total'][] = $value;
			$keys['Impacto'][$value['afectacion']][] = $value;
			$keys['Evento'][$value['evento']][] = $value;
			$keys['Gerencia'][$value['gerencia']][] = $value;
			if( ! empty($value['fecha_escalado']) ) 
				$keys['Escalado'][$value['nombre']][] = $value;
		}
		$total['data'] = $keys;
		$total['detalle'] = $detalle;
		$total['sql'] = $sql;
		return $total;
	}

	public static function InformeEventosFiltrado($filtro){

		$fil = self::Filtro($filtro);

		$sql = "SELECT a.*, CONCAT(c.cl, a.id, '/', year) folio, c.cl gerencia, if(impacto = 'MIMPSA', 'SIN AFECTACION', 'CON AFECTACION') afectacion, TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60 tiempo FROM bitacora a inner join si_usr b on usuario_captura = b.id inner join ad_sig c on b.cl = c.ix $fil ORDER BY a.id desc";
		$data = self::executeQuery($sql);
		$detalle['promedio'] = 0;
		$detalle['total'] = [];
		foreach ($data as $key => $value) {
			$detalle['promedio'] += (int)$value['tiempo'];
			$detalle['total'][] = $value;
			$keys['Impacto'][$value['afectacion']][] = $value;
			$keys['Evento'][$value['evento']][] = $value;
			$keys['Gerencia'][$value['gerencia']][] = $value;
			if( ! empty($value['fecha_escalado']) ) 
				$keys['Escalado'][$value['nombre']][] = $value;
		}
		$total['data'] = $keys;
		$total['detalle'] = $detalle;
		$total['sql'] = $sql;
		return $total;
	}

	public static function InformePromedio(){
		$sql = "SELECT evento, ROUND(AVG(TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 3600),2) tiempo FROM bitacora GROUP BY evento";
		$data = self::executeQuery($sql);
		return $data;
	}

	public static function InformePromedioFiltrado($filtro){
		$fil = self::Filtro($filtro);
		$sql = "SELECT evento, ROUND(AVG(TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 3600),2) tiempo, c.cl gerencia FROM bitacora a inner join si_usr b on usuario_captura = b.id inner join ad_sig c on b.cl = c.ix $fil GROUP BY evento";
		$data = self::executeQuery($sql);
		return $data;
	}

	
}
