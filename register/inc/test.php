<?
	include "class.mysql.php";
	
	$sql = new mysql;
	
	$sql->connect ( );
	$sql->select_db ( "register" );
	
	$rResult = $sql->query ( "SELECT * FROM genre" );
	if ( $sql->num_rows ( $rResult ) > 0 )
	{
		while ( $genre = $sql->fetch_assoc ( $rResult ) )
		{
			echo $genre['genre'] . "<br>\n";
		}
	}
	
	echo "<br>SQL time: " . $sql->getSqlTime ( );
?>