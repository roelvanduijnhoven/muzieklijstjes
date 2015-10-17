<?
	$file = "rubrieken.txt";
	
	# MySql
	include "db.php";
	
	# laad tijdschriften in
	$qTijdschrift = "SELECT id, tijdschrift FROM tijdschrift";
	$rTijdschrift = mysql_query ( $qTijdschrift );
	
	$tijdschriftArr = array ( );
	if ( mysql_num_rows ( $rTijdschrift ) > 0 )
	{
		while ( $tijdschrift = mysql_fetch_assoc ( $rTijdschrift ) )
		{
			$tijdschriftArr[ strtolower ( $tijdschrift['tijdschrift'] ) ] = $tijdschrift['id'];
		}
	}

	# Laad bestand in
	$input = join ( "", file ( $file  ) );
	$rubrieken = explode ( "\r\n", $input );
	
	# Controleer alles, en voer gegevens in
	foreach ( $rubrieken as $data )
	{
		$data = explode ( "\t", $data );
		
		$aRubriek = addslashes ( $data[0] );
		$rubriek = addslashes ( $data[1] );
		$tijdschrift = addslashes ( $data[2] );
		
		if ( isset ( $tijdschriftArr [ strtolower ( $tijdschrift ) ] ) )
			$tijdschrift = $tijdschriftArr [ strtolower ( $tijdschrift ) ];
		else
			$tijdschrift = 0;
				
		if ( $sql->query ( "INSERT INTO rubriek SET tijdschrift_id = " . $tijdschrift . ", aRubriek = '" . $aRubriek . "', rubriek = '" . $rubriek . "'" ) )
			$i++;
	}
	
	echo $i . " / " . count ( $rubrieken ) . " rubrieken beschrijvingen geimporteerd"
?>