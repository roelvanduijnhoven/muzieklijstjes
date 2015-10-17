<?
	error_reporting(E_ERROR);
	set_time_limit ( 0 );
	ini_set('memory_limit', '400M');

	$file = "lijstenI.csv";

	# MySql
	include "db.php";


	# Haal alle artiesten op
	$qArtiest = "SELECT id, artiest FROM artiest";
	$rArtiest = $sql->query ( $qArtiest );

	$artiestArr = array ( );
	while ( $artiest = $sql->fetch_assoc ( $rArtiest ) )
	{
		$artiestArr[ strtolower ( addslashes ( $artiest['artiest'] ) ) ] = $artiest['id'];
	}

	echo "artiesten geladen<br>";


	# Haal alle recensenten op
	$qRecensent = "SELECT id, aRecensent FROM recensent";
	$rRecensent = $sql->query ( $qRecensent );

	$recensentArr = array ( );
	while ( $recensent = $sql->fetch_assoc ( $rRecensent ) )
	{
		$recensentArr[ strtolower ( addslashes ( $recensent['aRecensent'] ) ) ] = $recensent['id'];
	}

	echo "recensenten geladen<br>";


	## Haal alle albums op
	$qAlbum = "SELECT id, album, artiest_id FROM album";
	$rAlbum = $sql->query ( $qAlbum );

	$albumArr = array ( );
	while ( $album = $sql->fetch_assoc ( $rAlbum ) )
	{
		$temp = strtolower ( addslashes ( $album['album'] ) ) . $album['artiest_id'];
		$albumArr[ $temp ] = $album['id'];
	}

	echo "albums geladen<br>";


	## Haal alle lijsten beschrijvingen op
	$qLijst = "SELECT id, lijst, individueel FROM lijstenB";
	$rLijst = $sql->query ( $qLijst );

	$lijstArr = array ( );
	while ( $lijst = $sql->fetch_assoc ( $rLijst ) )
	{
		$lijstArr[ strtolower ( $lijst['lijst'] ) ] = array ( 'id' => $lijst['id'], 'individueel' => $lijst['individueel'] );
	}

	echo "beschrijvingen van lijsten geladen<br>";



	## Haal alle lijsten zelf op
	$qLijsten = "SELECT id, album_id, lijst_id FROM lijsten";
	$rLijsten = $sql->query ( $qLijsten );

	$lijstenArr = array ( );
	while ( $lijsten = $sql->fetch_assoc ( $rLijsten ) )
	{
		$temp = strtolower ( $lijsten['album_id'] ) . "_" . strtolower ( $lijsten['lijst_id'] );
		$lijstenArr[ $temp ] = $lijsten['id'];
	}

	echo "lijstkoppelingen geladen<br><br>";


	# Update Array voor titels
	$UPDATE_titel = array ( );


	# Laad bestand in
	$input = join ( "", file ( $file  ) );
	$individueel = explode ( PHP_EOL, $input );


	$i = 1;
	# Controleer alles, en voer gegevens in
	foreach ( $individueel as $data )
	{
		$i++;

		$data = explode ( "\t", $data );
		if (!$data[0]) {
			continue;
		}

		$aRecensent = addslashes ( $data[0] );
		$lijst = addslashes ( $data[1] );
		$pos = addslashes ( $data[2] );
		$artiest = addslashes ( $data[3] );
		$album = addslashes ( $data[4] );

		if (!$pos) {
			$pos = 1;
		}

		# Recensent_id
		if ( isset ( $recensentArr [ strtolower ( $aRecensent ) ] ) )
			$recensent_id = $recensentArr [ strtolower ( $aRecensent ) ];
		else
			$log .= $i . ": Onbekende recenent (" . $aRecensent . ")<br>";


		# Lijst
		if ( isset ( $lijstArr [ strtolower ( $lijst ) ]['id'] ) )
			$lijst_id = $lijstArr [ strtolower ( $lijst ) ]['id'];
		else
			$log .= $i . ": Onbekende lijst (" . $lijst . ")<br>";


		# Artiest
		if ( isset ( $artiestArr [ strtolower ( $artiest ) ] ) )
			$artiest_id = $artiestArr [ strtolower ( $artiest ) ];
		else
			$log .= $i . ": Onbekende artiest (" . $artiest . ")<br>";


		if ( $artiest_id > 0 )
		{
			# Album
			$temp = strtolower ( $album ) . $artiest_id;
			if ( isset ( $albumArr [ $temp ] ) )
				$album_id = $albumArr [ $temp ];
			else
				$log .= $i . ": Onbekend album (" . $album . ")<br>";


			if ( $album_id  > 0 )
			{
				$temp = strtolower ( $album_id ) . "_" . strtolower ( $lijst_id );
				if ( isset ( $lijstenArr [ $temp ] ) )
					$lijstMatch_id = $lijstenArr [ $temp ];
				else
					$log .= $i . ": Deze gegevens zijn nog niet gelinkt aan een lijst (" . $album . ", " . $artiest . ", " . $lijst . ", " . $pos . ")<br>";
			}
		}


		if ( $lijst_id > 0 && $recensent_id > 0 && $artiest_id > 0 && $album_id > 0 && $lijstMatch_id > 0 )
		{
			if ( $sql->query ( "INSERT INTO `lijstenI` ( `lijsten_id` , `recensent_id` , `pos` ) VALUES ( " . $lijstMatch_id . ", " . $recensent_id . ", " . $pos . " )" ) )
				$succes++;

			$UPDATE_titel[ $album_id . "_" . $lijst_id ]['punten'] += $lijstArr[ strtolower ( $lijst ) ]['individueel'] - $pos + 1;
			$UPDATE_titel[ $album_id . "_" . $lijst_id ]['ak']++;
		}


		unset ( $lijst_id, $recensent_id, $artiest_id, $album_id, $lijstMatch_id );
	}

	echo $log;

	echo "<br><br>";


	# Update titels
	foreach ( $UPDATE_titel as $album_lijst_id => $punten )
	{
		$album_id = substr ( $album_lijst_id, 0, strpos ( $album_lijst_id, "_" ) );
		$lijst_id = substr ( $album_lijst_id, strpos ( $album_lijst_id, "_" ) + 1 );

		$sql->query ( "UPDATE lijsten SET ak = " . $punten['ak'] . ", punten = " . $punten['punten'] . " WHERE album_id = " . $album_id . " AND lijst_id = " . $lijst_id );

		$updated++;
	}


	echo "<br><br><br>";
	echo $succes . "/" . ( $i - 1 ) . " succesvol herkent!<br>";
	echo $updated . "/" . ( $i - 1 ) . " AK's geupdate"
?>
