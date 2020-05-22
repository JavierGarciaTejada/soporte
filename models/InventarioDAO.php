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

	public static function executeOperation($sql){
		try 
		{
			Conexion::$connect = new Conexion();
			Conexion::$query = $sql;
			Conexion::$result = Conexion::$connect->prepare(Conexion::$query);
			Conexion::$result->execute();
			return Conexion::$result->rowCount();
		}
		catch(Exception $e)
		{
			die("Error al ejecutar operacion $sql. Error! : ". $e->getMessage());
		}
	}

	public static function setParametersQuery($data){
		if (empty($data))
			return false;

		$itemsSet = array();
		foreach ($data as $key => $value) {
			if( $key !== 'id' ){
				$set = $key." = '".$value."'";
				array_push($itemsSet, $set);
			}
		}
		return join(", ", $itemsSet);
	}


	public static function InventarioRedDivArea($red, $f = null){
		$sql = "SELECT * FROM $red ORDER BY DIVISION, MODELO";
		$inv = self::executeQuery($sql);
		$data = array();
		foreach ($inv as $key => $value) {
			$data['division'][$value['DIVISION']][$value['MODELO']][] = $value;
			$data['area'][$value['DIVISION']][$value['MODELO']][$value['AREA']][] = $value;
		}
		return $data;
	}

	public static function Inventario(){
		$sql = "SELECT * FROM refacciones_telmex ORDER BY id DESC";
		$inv = self::executeQuery($sql);

		$final = array("last_page"=>1, "data"=>$inv);
		return $inv;
	}

	public static function Registro($data){
		$set = self::setParametersQuery($data);
		$sql = "INSERT INTO refacciones_telmex SET $set";
		$registro = self::executeOperation($sql);
		return array( 'message' => true, 'affected' => $registro, 'values' => $data );
	}

	public static function Cambio($data){
		$set = self::setParametersQuery($data);
		$sql = "UPDATE refacciones_telmex SET $set WHERE id = ".$data['id'];
		$cambio = self::executeOperation($sql);
		return array( 'message' => true, 'affected' => $cambio, 'values' => $data );
	}

	
}
