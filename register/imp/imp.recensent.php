<?
	$recensentenFile = __DIR__ . "/input/recensenten.csv";
	$genresFile = __DIR__ . "/genres.csv";
	$kenmerkenFile = __DIR__ . "/kenmerken.csv";

	# MySql
	include "db.php";


	# Haal de verschillende genres op, sla ze op in de db en houd ze in het geheugen om erin te kunnen zoeken
	$input = join ( "", file ( $genresFile  ) );
	$genres = explode ( "\r\n", $input );

	$genresArr = array ( );

	foreach ( $genres as $data )
	{
		$data = explode ( "\t", $data );
		if (!$data[0]) {
			continue;
		}

		$herkenning = $data[0];
		$genre = addslashes ( $data[1] );

		if ( $sql->query ( "INSERT INTO `genre` (`genre`) VALUES ('" . $genre . "')" ) )
		{
			$nGenres++;
			$genresArr[ $herkenning ] = mysql_insert_id ( );
		}

		$nGenresT++;
	}


	# Haal de verschillende kenmerken op, sla ze op in de db en houd ze in het geheugen om erin te kunnen zoeken
	$input = join ( "", file ( $kenmerkenFile  ) );
	$kenmerken = explode ( "\r\n", $input );

	$kenmerkArr = array ( );

	foreach ( $kenmerken as $data )
	{
		$data = explode ( "\t", $data );
		if (!$data[0]) {
			continue;
		}

		$herkenning = $data[0];
		$kenmerk = addslashes ( $data[1] );

		if ( $sql->query ( "INSERT INTO `kenmerk` (`kenmerk`) VALUES ('" . $kenmerk . "')" ) )
		{
			$nKenmerken++;
			$kenmerkArr[ $herkenning ] = mysql_insert_id ( );
		}

		$nKenmerkenT++;
	}


	# Laad bestand in
	$input = join ( "", file ( $recensentenFile  ) );
	$recensenten = explode ( PHP_EOL, $input );

	# Controleer alles, en voer gegevens in
	foreach ( $recensenten as $data )
	{
		$data = explode ( "\t", $data );

		$recensent = addslashes ( $data[2] );
		$sRecensent = addslashes ( $data[3] );
		$aRecensent = addslashes ( $data[1] );
		$url = addslashes ( $data[4] );
		$genres = addslashes ( $data[5] );
		$kenmerken = addslashes ( $data[6] );

		if ( $sql->query ( " INSERT INTO `recensent` (`recensent`, `sRecensent`, `aRecensent`, `url`) VALUES ('" . $recensent . "', '" . $sRecensent . "', '". $aRecensent ."', '" . $url . "')" ) )
			$i++;


		$recensent_id = mysql_insert_id ( );


		if ( !empty ( $genres ) )
		{
			$genres = explode ( " ", $genres );

			foreach ( $genres as $herkenning )
			{
				if ( isset ( $genresArr [ $herkenning ] ) )
					$sql->query ( "INSERT INTO `genre2recensent` ( `recensent_id` , `genre_id` ) VALUES ('" . $recensent_id . "', '" . $genresArr [ $herkenning ] . "')" );
			}
		}

		if ( !empty ( $kenmerken ) )
		{
			$kenmerken = explode ( " ", $kenmerken );

			foreach ( $kenmerken as $herkenning )
			{
				if ( isset ( $kenmerkArr [ $herkenning ] ) )
					$sql->query ( "INSERT INTO `kenmerk2recensent` ( `recensent_id` , `kenmerk_id` ) VALUES ('" . $recensent_id . "', '" . $kenmerkArr [ $herkenning ] . "')" );
			}
		}


		unset ( $genresIds, $kenmerkenIds );
	}

	echo $i . " / " . count ( $recensenten ) . " recensenten geimporteerd<br>";
	echo $nGenres . "/" . $nGenresT . " genres geimporteerd<br>";
	echo $nKenmerken . "/" . $nKenmerkenT . " genres geimporteerd<br>";
?>
