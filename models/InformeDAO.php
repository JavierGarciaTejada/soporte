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

		// array_push($f, "estado = 'Liquidado'");

		if( ! empty($filtro['fecha_soporte']) )
			array_push($f, "fecha_soporte >= '".$filtro['fecha_soporte']."'");

		if( ! empty($filtro['fecha_fin_falla']) )
			array_push($f,"fecha_soporte <= '".$filtro['fecha_fin_falla']."'");

		if( ! empty($filtro['gerencia']) )
			array_push($f,"c.cl = '".$filtro['gerencia']."'");
		
		$fil = ( count($f) > 0 ) ?  ' WHERE '.join(" AND ", $f) : "";

		return $fil;
	}

	public static function InformeEventos($f = null){
		$filtros = ( $f === null ) ? "" : self::Filtro($f);
		$keys = array();

		$sql = "SELECT a.*, CONCAT(c.cl,'-', a.id, '/', year) folio, c.cl gerencia, UPPER(evento) evento, 
		if(impacto LIKE '%SA', 'SIN AFECTACION', 'CON AFECTACION') afectacion, TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60 tiempo
		FROM bitacora a 
		LEFT JOIN si_usr b ON id_ingeniero = b.id 
		LEFT JOIN ad_sig c ON b.cl = c.ix 
		$filtros 
		ORDER BY a.estado DESC";

		$data = self::executeQuery($sql);
		$keys['total'] = [];

		foreach($data as $key => $value){

			$keys['total'][] = $value['id'];
			$keys[$value['estado']]['A_TOTAL'][] = $value['id'];
			$keys[$value['estado']]['B_'.$value['afectacion']][] = $value['id'];

			if( ! empty($value['fecha_escalado']) && $value['fecha_escalado'] != "0000-00-00 00:00:00" )
				$keys[$value['estado']]['D_ESCALADO'][] = $value['id'];

			$keys[$value['estado']]['C_'.$value['evento']][] = $value['id'];
			$keys['Gerencias'][$value['gerencia']][] = $value['id'];

		}

		ksort($keys['Liquidado']);
		ksort($keys['En Proceso']);

		$sqlPromedio = "SELECT if(evento <> 'Falla', UPPER(evento), if(impacto LIKE '%SA', 'FALLA SIN AFECTACION', 'FALLA CON AFECTACION')) evento, 
		ROUND(AVG(TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60),2) tiempo 
		FROM bitacora a 
		LEFT JOIN si_usr b ON id_ingeniero = b.id 
		LEFT JOIN ad_sig c ON b.cl = c.ix 
		$filtros 
		GROUP BY if(evento <> 'Falla', evento, if(impacto LIKE '%SA', 'Falla sin afectacion', 'Falla con afectacion'))";
		$dataPromedio = self::executeQuery($sqlPromedio);

		foreach($dataPromedio as $keyP => $valueP){
			if( $valueP['evento'] != "REFACCIÓN" )
				$keys['Promedio'][$valueP['evento']] = $valueP['tiempo'];
		}

		array_multisort($keys);
		$informe['data'] = $keys;
		$informe['sql'] = $sql;

		return $informe;

	}

	
}
