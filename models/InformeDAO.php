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

		array_push($f, "estado <> 'Cancelado'");

		if( $filtro == null )
			return ' WHERE '.join(" AND ", $f);

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
		$filtros = self::Filtro($f);
		$keys = array();

		$sql = "SELECT a.id, a.comentarios, a.estado, 
		CONCAT(c.cl,'-', a.id, '/', year) folio, c.cl gerencia, impacto, fecha_soporte, UPPER(evento) evento,
		round((TIMESTAMPDIFF(SECOND, fecha_soporte, NOW()) / 60), 2) transcurrido,
		if(impacto LIKE '%SA', 'SIN AFECTACION', 'CON AFECTACION') afectacion, TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60 tiempo
		FROM bitacora a 
		LEFT JOIN si_usr b ON id_ingeniero = b.id 
		LEFT JOIN ad_sig c ON b.cl = c.ix 
		$filtros 
		ORDER BY c.cl";

		$data = self::executeQuery($sql);
		$keys['total'] = [];

		foreach($data as $key => $value){

			$keys['total'][] = $value;
			$keys[$value['estado']]['A_TOTAL'][] = $value;
			$keys[$value['estado']]['B_'.$value['afectacion']][] = $value;

			if( ! empty($value['fecha_escalado']) && $value['fecha_escalado'] != "0000-00-00 00:00:00" )
				$keys[$value['estado']]['D_ESCALADO'][] = $value;

			$keys[$value['estado']]['C_'.$value['evento']][] = $value;
			$keys['Gerencias'][$value['gerencia']][] = $value;

		}

		//ksort($keys['Liquidado']);
		//ksort($keys['En Proceso']);

		$sqlPromedio = "SELECT if(evento <> 'Falla', UPPER(evento), if(impacto LIKE '%SA', 'FALLA SIN AFECTACION', 'FALLA CON AFECTACION')) evento, 
		ROUND(AVG(TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60),2) tiempo 
		FROM bitacora a 
		LEFT JOIN si_usr b ON id_ingeniero = b.id 
		LEFT JOIN ad_sig c ON b.cl = c.ix 
		$filtros 
		GROUP BY if(evento <> 'Falla', evento, if(impacto LIKE '%SA', 'Falla sin afectacion', 'Falla con afectacion'))";
		$dataPromedio = self::executeQuery($sqlPromedio);

		$keys['Promedio']['FALLA CON AFECTACION'] = 0;
		$keys['Promedio']['FALLA SIN AFECTACION'] = 0;
		$keys['Promedio']['ASESORIA / CONSULTA'] = 0;
		$keys['Promedio']['CTRL CAMBIOS / PROGRAMADO'] = 0;

		foreach($dataPromedio as $keyP => $valueP){
			if( $valueP['evento'] != "REFACCIÓN" )
				$keys['Promedio'][$valueP['evento']] = $valueP['tiempo'];
		}

		array_multisort($keys);

		$keys['conteo'] = self::InformeEventosConteo($f); 
		$keys['afectacion'] = self::InformeEventosImpactoEvento($f);

		$informe['data'] = $keys;
		$informe['sql'] = $sql;

		return $informe;

	}

	public static function InformeEventosConteo($f = null){
		$filtros = str_replace(' WHERE ', ' AND ', self::Filtro($f));

		$sql =  "SELECT a.id, c.cl siglas, turno, SUBSTR(fecha_soporte, 1, 10) dia, CONCAT( SUBSTR(turno, 1, 1), ' - ' ,nombre) nombre
		FROM bitacora a 
		LEFT JOIN si_usr b ON id_ingeniero = b.id 
		LEFT JOIN ad_sig c ON b.cl = c.ix 
		LEFT JOIN ad_tur d ON b.id = d.id_usr
		#MONTH(fecha_soporte) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND
		WHERE c.cl IN ('Lec','Leg','Lep','Ler','Let') $filtros
		GROUP BY a.id ORDER BY a.fecha_soporte";

		$data = self::executeQuery($sql);
		$keys = array();

		$s = array('Lec' => 0, 'Leg' => 0, 'Lep' => 0, 'Ler' => 0, 'Let' => 0);

		$t = array();
		$d = array();
		$i = array();

		foreach ($data as $key => $value) {
			$t[$value['siglas']] = 0;

			if( ! array_key_exists($value['dia'], $d) )
				$d[$value['dia']] = array('Lec' => [], 'Leg' => [], 'Lep' => [], 'Ler' => [], 'Let' => []);

			$d[$value['dia']][$value['siglas']][] = $value;
			$i[$value['dia']][$value['siglas']][$value['nombre']][] = $value['id'];
		}

		foreach ($d as $k => $v) {
			ksort($d[$k]);
		}
		ksort($s);

		foreach ($i as $ki => $vi) {
			foreach ($vi as $kvi => $vvi) {
				foreach ($vvi as $kvvi => $vvvi) {
					$i[$ki][$kvi][$kvvi] = count($vvvi);
				}
				arsort($i[$ki][$kvi]);
			}
		}

		$keys['head'] = $s;
		$keys['body'] = $d;
		$keys['ing'] = $i;
		$keys['sql'] = $sql;

		return $keys;

	}


	public static function InformeEventosDiaSiglas($filtros){

		$dia = $filtros['dia'];
		$siglas = $filtros['siglas'];

		$sql = "SELECT dia, cl, i.nombre, CONCAT( SUBSTR(turno, 1, 1), ' - ' ,i.nombre) nt, 
		if(total is null, 0, total) total, turno, sitio, tur, sit, if(CA IS NULL, 0, CA) CA, if(SA IS NULL, 0, SA) SA FROM 
		(SELECT UPPER(concat(ap,' ',am,' ',b.no)) nombre,a.turno, a.sitio, b.id, c.cl
		FROM ad_tur a 
		LEFT JOIN si_usr b ON a.id_usr = b.id
		LEFT JOIN ad_sig c ON b.cl = c.ix 
		WHERE b.pu <> 919056264924930 AND c.cl = '$siglas' GROUP BY a.id_usr) i 
		LEFT JOIN 
		(SELECT nombre, SUBSTR(fecha_soporte, 1, 10) dia, tur, sit, count(1) total
		FROM bitacora WHERE SUBSTR(fecha_soporte, 1, 10) = '$dia' GROUP BY nombre, dia) bi 
		ON i.nombre = bi.nombre 
		LEFT JOIN 
		( SELECT count(1) CA, id_ingeniero FROM bitacora WHERE impacto LIKE '%ca' AND SUBSTR(fecha_soporte, 1, 10) = '$dia' GROUP BY id_ingeniero ) imp_ca
		ON i.id = imp_ca.id_ingeniero
		LEFT JOIN 
		( SELECT count(1) SA, id_ingeniero FROM bitacora WHERE impacto LIKE '%sa' AND SUBSTR(fecha_soporte, 1, 10) = '$dia' GROUP BY id_ingeniero ) imp_sa
		ON i.id = imp_sa.id_ingeniero
		ORDER BY total DESC";

		$data = self::executeQuery($sql);

		$keys['sql'] = $sql;
		$keys['data'] = $data;

		return $keys;

	}

	public static function InformeEventosImpactoEvento($f = null){

		$filtros = self::Filtro($f);
		$global = array();

		$sqlTotal = "SELECT COUNT(1) c, if(estado = 'En Proceso', 'p_total', if(estado = 'Liquidado', 'l_total', estado)) id FROM 
		bitacora a
		INNER JOIN si_usr b ON a.id_ingeniero = b.id
		INNER JOIN ad_sig c ON b.cl = c.ix 
		$filtros GROUP BY estado";
		$total = self::executeQuery($sqlTotal);

		foreach ($total as $keyT => $valueT) 
			$global[ $valueT['id'] ] = $valueT['c'];

		$sqlImpacto = "SELECT COUNT(1) c, estado, if(impacto LIKE '%SA', 'sa', 'ca') afectacion, 
		CONCAT(if(estado = 'En Proceso', 'p_', if(estado = 'Liquidado', 'l_', 'c_')), if(impacto LIKE '%SA', 'sa', 'ca')) id
		FROM bitacora a
		INNER JOIN si_usr b ON a.id_ingeniero = b.id
		INNER JOIN ad_sig c ON b.cl = c.ix 
		$filtros GROUP BY estado, afectacion";
		$impacto = self::executeQuery($sqlImpacto);

		foreach ($impacto as $keyI => $valueI) 
			$global[ $valueI['id'] ] = $valueI['c'];

		$sqlEvento = "SELECT estado, if(impacto LIKE '%SA', 'SA', 'CA') afectacion, evento, COUNT(1) c,
		CONCAT(if(estado = 'En Proceso', 'p_', if(estado = 'Liquidado', 'l_', 'c_')),if(impacto LIKE '%SA', 'sa', 'ca'),'_', lower(substr(evento, 1, 2))) id
		FROM bitacora a
		INNER JOIN si_usr b ON a.id_ingeniero = b.id
		INNER JOIN ad_sig c ON b.cl = c.ix
		$filtros GROUP BY estado, afectacion, evento";
		$evento = self::executeQuery($sqlEvento);

		foreach ($evento as $keyE => $valueE) 
			$global[ $valueE['id'] ] = $valueE['c'];


		return $global;

	}


	public static function InformeProceso($f){

		$filtros = array();
		if( ! empty($f['estado']) )
			array_push($filtros, "estado = '".$f['estado']."'");

		if( ! empty($f['afectacion']) )
			array_push($filtros, "impacto LIKE '%".$f['afectacion']."'");

		if( ! empty($f['evento']) )
			array_push($filtros, "evento = '".$f['evento']."'");

		if( ! empty($f['fecha_soporte']) )
			array_push($filtros, "fecha_soporte >= '".$f['fecha_soporte']."'");

		if( ! empty($f['fecha_fin_falla']) )
			array_push($filtros,"fecha_soporte <= '".$f['fecha_fin_falla']."'");

		if( ! empty($f['gerencia']) )
			array_push($filtros,"c.cl = '".$f['gerencia']."'");

		$fil = ( count($filtros) > 0 ) ?  ' WHERE '.join(" AND ", $filtros) : "";

		$sql = "SELECT a.id, a.comentarios, a.estado, 
		CONCAT(c.cl,'-', a.id, '/', year) folio, c.cl gerencia, impacto, fecha_soporte, UPPER(evento) evento,
		round((TIMESTAMPDIFF(SECOND, fecha_soporte, NOW()) / 60), 2) transcurrido,
		if(impacto LIKE '%SA', 'SIN AFECTACION', 'CON AFECTACION') afectacion, TIMESTAMPDIFF(SECOND, fecha_soporte, fecha_fin_falla) / 60 tiempo
		FROM bitacora a 
		LEFT JOIN si_usr b ON id_ingeniero = b.id 
		LEFT JOIN ad_sig c ON b.cl = c.ix 
		$fil 
		ORDER BY c.cl";

		$data = self::executeQuery($sql);

		return $data;

	}
	
}
