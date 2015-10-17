<?
	include "class.mysql.php";
	
	$sql = new mysql;

	$sql->connect ( "localhost", "root", "" ); // localhost is hoogstwaarschijnlijk al goed
	$sql->select_db ( "register" );					// deze is hoogstwaarschijnlijk 'register'
?>