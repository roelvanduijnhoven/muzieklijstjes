<?
	include "class.mysql.php";

	$sql = new mysql;

	$sql->connect ( "mysql", "user", "password" ); // localhost is hoogstwaarschijnlijk al goed
	$sql->select_db ( "dev" );					// deze is hoogstwaarschijnlijk 'register'
	$sql->query("SET NAMES utf8");
