<?
	$file = __DIR__ . "/input/lijstenB.csv";

	# MySql
	include "db.php";

	# Laad bestand in
	$input = join ( "", file ( $file  ) );
	$lijsten = explode ( PHP_EOL, $input );

	# Laad alle soorten in
	$arrSoort = array ( );
	$rSoort = $sql->query ( "SELECT soort_id, soort FROM soort ORDER BY soort ASC" );
	if ( $sql->num_rows ( $rSoort ) > 0 )
	{
		while ( $soort = $sql->fetch_assoc ( $rSoort ) )
		{
			$arrSoort [ $soort['soort'] ] = $soort['soort_id'];
		}
	}

	# Controleer alles, en voer gegevens in
	foreach ( $lijsten as $data )
	{
		$data = explode ( "\t", $data );
		if (!$data[0]) {
			continue;
		}

		$lijst = addslashes ( $data[0] );
		$jaar = addslashes ( $data[2] );
		$bron = addslashes ( $data[3] );
		$omschrijving = addslashes ( $data[4] );
		$url = addslashes ( $data[5] );
		$type = addslashes ( $data[6] );
		$cannon = addslashes ( $data[7] );
		$individueel = addslashes ( $data[8] );
		$soort = addslashes ( $data[9] );

		if ( isset ( $arrSoort [ $soort ] ) )
			$soort_id = $arrSoort[ $soort ];
		else
			$soort_id = 0;

		if ( empty ( $individueel ) ) $individueel = 0;

		if ( $sql->query ( "INSERT INTO `lijstenB` (`lijst`, `jaar`, `bron`, `omschrijving`, `url`, `type`, `canon`, `individueel`, `soort_id`) VALUES ('".$lijst."', '".$jaar."', '".$bron."', '".$omschrijving."', '".$url."', '".$type."', '" . $cannon . "', " . $individueel . ", " . $soort_id . ")" ) )
			$i++;
	}

	echo $i . " / " . count ( $lijsten ) . " omschrijvingen van lijsten geimporteer.";
?>
