<?php

	$file = "Soorten.txt";
	
	# MySql
	include "db.php";

	# Laad bestand in
	$input = join ( "", file ( $file  ) );
	$soorten = explode ( "\r\n", $input );
	
	
	$numberOf = 0;
	$succeeded = 0;
	
	# Controleer alles, en voer gegevens in
	foreach ( $soorten as $data )
	{
		$numberOf++;
		$data = explode ( "\t", $data );
		
		$soort = addslashes ( $data[0] );
		
		if ( $sql->query ( "INSERT INTO `soort` ( `soort_id` , `soort` ) VALUES ( '', '" . $soort . "' );" ) )
		{
			$succeeded++;
		}
	}
	
	echo $succeeded . "/" . $numberOf . " soorten ingevoerd.";
	
?>