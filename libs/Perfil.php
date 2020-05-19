<?php

class Perfil
{

	public static function FiltroHomeRole(){

		$lr = Session::getSession('lr');
		$role = Session::getSession("role");

		$filtros = array();
		//ADMINISTRADOR NO TIENE FILTROS, VE TODO
		// if( $role === "Administrador" ) return $filtros;

		array_push($filtros, " rl = '$lr' ");
		$filtro = implode(' AND ', $filtros);

		return $filtro;
	}

	public static function FiltroUsuarioGerencia(){

		$filtros = array();
		$ixCli 	= Session::getSession("siglas");
		$role = Session::getSession("role");
		$id = Session::getSession("id");
		$ni = Session::getSession("ni");

		if( $role === "Administrador" ) return $filtros;

		$gcl = " ad_sig.cl = '$ixCli' ";
		if( (int)$id == 43 || (int)$id == 72 ) $gcl = " ad_sig.cl IN ('$ixCli', 'Ler') ";
		

		$mail = explode("@", $ni);
		if( $mail[1] == "nokia.com" ){
			//$gcl = "ad_sig.cl <> ''";
			$proveedor = "proveedor = 'NOKIA'";
		}

		array_push($filtros, $gcl);
		array_push($filtros, $proveedor);

		return implode(" AND ", $filtros);

	}

	public static function FiltroLugarEquipo(){

		$filtros = array();
		$ixCli 	= Session::getSession("siglas");
		$role = Session::getSession("role");

		if( $role === "Administrador" ) return $filtros;

		$f = ( $ixCli == 'Leg' || $ixCli == 'Let') ? " siglas IN ('Leg', 'Let') " : " siglas = '$ixCli' ";	
		array_push($filtros, $f);
		
		return implode(" AND ", $filtros);

	}

	
	public static function FiltroListadoEvaluaciones($anio = ""){

		$ix 	= Session::getSession("ix");
		$role	= Session::getSession("role");
		$puesto	= Session::getSession("puesto");
		$siglas	= Session::getSession("siglas");
		$ixCli 	= Session::getSession("gcl");

		$filtros = array();
		$anterior = date('Y', strtotime('-1 year'));
		$actual = date('Y');
		array_push($filtros, " YEAR(a.fs) IN ($anterior, $actual) OR YEAR(a.fl) > $anterior ");


		//ADMINISTRADOR NO TIENE FILTROS, VE TODO
		if( $role === "Administrador" ) return implode(' AND ', $filtros);

		//SI ES CLIENTE
		if( ! empty($ixCli) ){
			array_push($filtros, " a.ac IN (SELECT ix FROM ad_gcl WHERE gcl = '$ixCli') ");
		}else{
			//SUBDIRECTOR LABORATORIO NO TIENE FILTROS, VE TODO
			if( $puesto === "Subdirector" )
				return implode(' AND ', $filtros);
			else{
				if( $ix == "919056264925290" ) //BRENDA LLEVA DOS GERENCIAS
					array_push($filtros, " a.al IN (SELECT ix FROM ad_alb WHERE cl IN ('$siglas', 'Leg') ) ");
				else
					array_push($filtros, " a.al = (SELECT ix FROM ad_alb WHERE cl = '$siglas') ");
			} 
		}

		$filtro = implode(' AND ', $filtros);
		return $filtro;
	}


	// public static function FiltroUsuarioGerencia(){

	// 	$cl = Session::getSession("cl");
	// 	$role = Session::getSession("role");
	// 	$filtros = array();
	// 	if( $role === "Administrador" ) return $filtros;

	// 	array_push($filtros, " a.cl = '$cl' "); //SOLO DE LA GERENCIA
	// 	// array_push($filtros, " lr IN ('919056264924931', '919056264924932') "); //SOLO SUBGERENTES E INGENIEROS

	// 	$filtro = implode(' AND ', $filtros);
	// 	return $filtro;
	// }

	
	
}