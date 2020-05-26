<?php 
header("Content-Type: text/html;charset=utf-8");
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

class ServerSideDAO
{

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


	public static function get($data, $filtro, $table, $index_column, $columns){

		// Paging
		$sLimit = "";
		// $data['iDisplayStart'] = 10;
		// $data['iDisplayLength'] = 10;
		if ( isset( $data['iDisplayStart'] ) && $data['iDisplayLength'] != '-1' )
			$sLimit = "LIMIT ".intval( $data['iDisplayStart'] ).", ".intval( $data['iDisplayLength'] );
		
		// Ordering
		$sOrder = "ORDER BY id DESC";
		if ( isset( $data['iSortCol_0'] ) ) {
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $data['iSortingCols'] ) ; $i++ ) {
				if ( $data[ 'bSortable_'.intval($data['iSortCol_'.$i]) ] == "true" ) {
					$sortDir = (strcasecmp($data['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
					$sOrder .= "`".$columns[ intval( $data['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" ) {
				$sOrder = "";
			}
		}

		$sWhere = "";
		if ( isset($data['sSearch']) && $data['sSearch'] != "" ) {
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($columns) ; $i++ ) {
				if ( isset($data['bSearchable_'.$i]) && $data['bSearchable_'.$i] == "true" ) {
					$sWhere .= "`".$columns[$i]."` LIKE :search OR ";
				}
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}

		if( empty($sWhere) && !empty($filtro) )
			$sWhere = "WHERE $filtro";
		else if ( !empty($sWhere) && !empty($filtro) )
			$sWhere .= " AND $filtro";
		
		// Individual column filtering
		// for ( $i=0 ; $i<count($columns) ; $i++ ) {
		// 	if ( isset($data['bSearchable_'.$i]) && $data['bSearchable_'.$i] == "true" && $data['sSearch_'.$i] != '' ) {
		// 		if ( $sWhere == "" ) {
		// 			$sWhere = "WHERE ";
		// 		}
		// 		else {
		// 			$sWhere .= " AND ";
		// 		}
		// 		$sWhere .= "`".$columns[$i]."` LIKE :search".$i." ";
		// 	}
		// }


		$sQuery = "SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $columns))."` FROM `".$table."` ".$sWhere." ".$sOrder." ".$sLimit;

		Conexion::$connect = new Conexion();
		Conexion::$query = $sQuery;
		Conexion::$prepare = Conexion::$connect->prepare(Conexion::$query);
		$statement = Conexion::$connect->prepare($sQuery);
		
		// Bind parameters
		if ( isset($data['sSearch']) && $data['sSearch'] != "" ) {
			$s = '%'.$data['sSearch'].'%';
			Conexion::$prepare->bindParam(':search',$s, PDO::PARAM_STR);
		}
		for ( $i=0 ; $i<count($columns) ; $i++ ) {
			if ( isset($data['bSearchable_'.$i]) && $data['bSearchable_'.$i] == "true" && $data['sSearch_'.$i] != '' ) {
				$s_ = '%'.$data['sSearch_'.$i].'%';
				Conexion::$prepare->bindParam(':search'.$i, $s_, PDO::PARAM_STR);
			}
		}

		Conexion::$prepare->execute();
		$rResult = Conexion::$prepare->fetchAll();
		
		$iFilteredTotal = current(Conexion::$connect->query('SELECT FOUND_ROWS()')->fetch());
		
		// Get total number of rows in table
		$sQuery = "SELECT COUNT(`".$index_column."`) FROM `".$table."`";
		$iTotal = current(Conexion::$connect->query($sQuery)->fetch());
		
		// Output
		$sEcho = ( isset($data['sEcho']) ) ? intval($data['sEcho']) : 0;
		$output = array(
			"sEcho" => $sEcho,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => $rResult //array()
		);
		
		// Return array of values
		// foreach($rResult as $aRow) {
		// 	$row = array();			
		// 	for ( $i = 0; $i < count($columns); $i++ ) {
		// 		if ( $columns[$i] == "version" ) {
		// 			// Special output formatting for 'version' column
		// 			$row[] = ($aRow[ $columns[$i] ]=="0") ? '-' : $aRow[ $columns[$i] ];
		// 		}
		// 		else if ( $columns[$i] != ' ' ) {
		// 			$row[] = $aRow[ $columns[$i] ];
		// 		}
		// 	}
		// 	$output['aaData'][] = $aRow;
		// }
		
		//echo json_encode( $output );
		return $output;

	}

	
}
