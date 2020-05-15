<?php 
header("Content-Type: text/html;charset=utf-8");

class InventarioDAO
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


	public static function InventarioRedDivArea($red, $f = null){

		$sql = "SELECT * FROM $red ORDER BY DIVISION, MODELO";
		$inv = self::executeQuery($sql);

		$data = array();
		foreach ($inv as $key => $value) {
			$data['division'][$value['DIVISION']][$value['MODELO']][] = $value;
			$data['area'][$value['DIVISION']][$value['MODELO']][$value['AREA']][] = $value;
			// $data['modelo'][$value['MODELO']][] = $value;
			// $data['area'][$value['AREA']][] = $value;
		}
		return $data;

	}


	
}
