<?
	include "class.mysql.php";

	$sql = new mysql;

	$sql->connect ( getenv('MYSQL_HOSTNAME'), getenv('MYSQL_USERNAME'), getenv('MYSQL_PASSWORD')); // localhost is hoogstwaarschijnlijk al goed
	$sql->select_db ( getenv('MYSQL_DATABASE') );
	$sql->query("SET NAMES utf8");
