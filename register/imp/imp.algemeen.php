<?
	error_reporting(E_ERROR);
	set_time_limit ( 0 );
	ini_set('memory_limit', '800M');


	$file = "algemeen.csv";


	# Tijdschriften
	$tijdschriften = array (
		"O" => 1,
		"H" => 2,
		"A" => 3,
		"M" => 4,
		"A3" => 5,
		"R" => 6,
		"A1" => 7,
		"A2" => 8
	);


	# MySql
	include "db.php";


	## Haal alle rubrieken op
	$qRubriek = "SELECT id, aRubriek FROM rubriek";
	$rRubriek = $sql->query ( $qRubriek );

	$rubriekArr = array ( );
	while ( $rubriek = $sql->fetch_assoc ( $rRubriek ) )
	{
		$rubriekArr[ strtolower ( $rubriek['aRubriek'] ) ] = $rubriek['id'];
	}

	echo "rubrieken geladen<br>";


	# Haal alle artiesten op
	$qArtiest = "SELECT id, artiest FROM artiest";
	$rArtiest = $sql->query ( $qArtiest );

	$artiestArr = array ( );
	while ( $artiest = $sql->fetch_assoc ( $rArtiest ) )
	{
		$artiestArr[ strtolower ( $artiest['artiest'] ) ] = $artiest['id'];
	}

	echo "artiesten geladen<br>";


	# Haal alle recensenten op
	$qRecensent = "SELECT id, aRecensent FROM recensent";
	$rRecensent = $sql->query ( $qRecensent );

	$recensentArr = array ( );
	while ( $recensent = $sql->fetch_assoc ( $rRecensent ) )
	{
		$recensentArr[ strtolower ( $recensent['aRecensent'] ) ] = $recensent['id'];
	}

	echo "recensenten geladen<br>";


	## Haal alle albums op
	$qAlbum = "SELECT id, album, artiest_id FROM album";
	$rAlbum = $sql->query ( $qAlbum );

	$albumArr = array ( );
	while ( $album = $sql->fetch_assoc ( $rAlbum ) )
	{
		$temp = strtolower ( $album['album'] ) . $album['artiest_id'];
		$albumArr[ $temp ] = $album['id'];
	}

	echo "albums geladen<br>";


	## Haal alle lijsten op
	$qLijst = "SELECT id, individueel, lijst FROM lijstenB";
	$rLijst = $sql->query ( $qLijst );

	$lijstArr = array ( );
	while ( $lijst = $sql->fetch_assoc ( $rLijst ) )
	{
		$lijstArr[ $lijst['lijst'] ] = array ( 'id' => $lijst['id'], 'individueel' => $lijst['individueel'] );
	}

	echo "beschrijvingen van lijsten geladen<br><br>";


	# Laad bestand in
	$input = join ( "", file ( $file ) );
	$recensies = explode ( PHP_EOL, $input );


	# Controleer alles, en voer gegevens in
	foreach ( $recensies as $data )
	{
		$data = explode ( "\t", $data );
		if (!$data[0]) {
			continue;
		}


		## Quickref
		$artiest = addslashes ( $data[0] );
		$album = addslashes ( $data[1] );
		$albumjaar = addslashes ( $data[2] );
		$titelnummer = addslashes ( $data[3] );

		$tijdschrift = addslashes ( $data[4] );
		$rubriek = addslashes ( $data[5] );
		$tijdschriftjaar = addslashes ( $data[6] );
		$nummer = addslashes ( $data[7] );

		$aRecensent = addslashes ( $data[8] );
		$waardering = addslashes ( $data[9] );
		$sArtiest = addslashes ( $data[10] );
		$lijst = addslashes ( $data[11] );
		$ak = addslashes ( $data[13] );
		$pos = addslashes ( $data[14] );
		$gPos = addslashes ( $data[15] );
		$materiaal = addslashes ( $data[17] );

		## Verwerken
		$tijdschrift_id = $tijdschriften[ $tijdschrift ];
		$pos = trim ( str_replace ( "x", "", $pos ) );
		$ak = trim ( str_replace ( "x", "", $ak ) );
		$rubriek_id = $rubriekArr[ strtolower ( $rubriek ) ];
		$titelnummer = str_replace ( "Onbekend", "", $titelnummer );


		# Kijk of het individueel lijstje is, zoja zet AK op 0 zodat naderhand real-time de echte AK berekend kan worden.
		if ( $lijstArr [ $lijst ]['individueel'] > 0 )
			$ak = 0;



		# Check voor album
		$temp = strtolower ( $album ) . $artiestArr[ strtolower ( $artiest ) ];
		if ( isset ( $albumArr [ $temp ] ) )
			$album_id = $albumArr [ $temp ];
		else
		{
			# Check voor artiest
			if ( isset ( $artiestArr[ strtolower ( $artiest ) ] ) )
				$artiest_id = $artiestArr[ strtolower ( $artiest ) ];
			else
			{
				if ( $sql->query ( "INSERT INTO `artiest` (`id`, `artiest`, `sArtiest`) VALUES ('', \"". $artiest ."\", \"". $sArtiest ."\")" ) )
					$stats['artiest']++;

				$artiest_id = mysql_insert_id ( );
				$artiestArr[ strtolower ( $artiest ) ] = $artiest_id;
			}

			if ( $sql->query ( "INSERT INTO `album` (`id`, `artiest_id`, `album`, `jaar` , `titelnummer`, `materiaal`) VALUES ('', '" . $artiest_id . "', \"" . $album . "\", '" . $albumjaar . "', '" . $titelnummer . "', '" . $materiaal . "' )" ) )
			{
				$stats['album']++;

				$AR_STATS[ $artiest_id ]['album']++;
			}

			$album_id = mysql_insert_id ( );

			$temp = strtolower ( $album ) . $artiest_id;
			$albumArr[ $temp ] = $album_id;
		}


		# Kijk of er een recentie aan vast hangt
		if ( !empty ( $aRecensent ) )
		{
			# Check voor recensent
			if ( isset ( $recensentArr[ strtolower ( $aRecensent ) ] ) )
				$recensent_id = $recensentArr[ strtolower ( $aRecensent ) ];
			else
			{
				echo $album . ": onbekende recensent, " . $aRecensent . "<br>";
				$recensent_id = 0;
			}


			# Voer recensie in
			if ( $sql->query ( "INSERT INTO `recensie` (`id`, `album_id`, `tijdschrift_id`, `recensent_id`, `jaar`, `maand`, `nummer`, `waardering`, `rubriek`) VALUES ('', '" . $album_id . "', '" . $tijdschrift_id . "', '" . $recensent_id . "', '" . $tijdschriftjaar . "', '" . $maand . "', '" . $nummer . "', '" . $waardering . "', '" . $rubriek_id . "')" ) )
			{
				$stats['recensie']++;

				$RE_STATS[ $recensent_id ]++;
				$AR_STATS[ $artiest_id ]['recensie']++;
				if ( $album_id != 0 ) $A_STATS[ $album_id ]['recensie']++;
			}
		}


		# Kijk of hij in een lijst genoemd is en geldig is
		if ( $lijstArr [ $lijst ]['id'] > 0 )
		{
			$lijst_id = $lijstArr [ $lijst ]['id'];

			if ( $sql->query ( " INSERT INTO `lijsten` (`id`, `album_id`, `lijst_id`, `ak`, `pos`) VALUES ('', '".$album_id."', '".$lijst_id."', '".$ak."', '".$pos."')" ) )
			{
				$stats['lijst']++;

				$AR_STATS[ $artiest_id ]['lijst']++;
				if ( $album_id != 0 ) $A_STATS[ $album_id ]['lijst']++;
			}
		}
	}


	echo "<br>";
	echo $stats['album'] . " albums ingevoerd<br>";
	echo $stats['artiest'] . " artiesten ingevoerd<br>";
	echo $stats['recensie'] . " recensies ingevoerd<br>";
	echo $stats['lijst'] . " lijsten ingevoerd<br>";

	echo "<br>";


	# Recensenten
	foreach ( $RE_STATS as $id => $aantal )
	{
		if ( $sql->query ( "UPDATE recensent SET nRecensie = " . $aantal . " WHERE id = " . $id ) )
			$done++;
	}

	echo $done . " album's gecaht voor recensent<br>";


	# Artiest
	$done = 0;
	foreach ( $AR_STATS as $id => $data )
	{
		if ( !$data['album'] ) $data['album'] = 0;
		if ( !$data['recensie'] ) $data['recensie'] = 0;
		if ( !$data['lijst'] ) $data['lijst'] = 0;

		if ( $sql->query ( "UPDATE artiest SET albums=albums+" . $data['album'] . ",recensies=recensies+" . $data['recensie'] . ",lijsten=lijsten+" . $data['lijst'] . " WHERE id=" . $id  ) )
			$done++;

		$t++;
	}
	echo $done . " / " . $t . " artiesten bijgewerkt<br>";


	# Album
	$done = 0;
	$t = 0;
	foreach ( $A_STATS as $id => $data )
	{
		if ( !$data['recensie'] ) $data['recensie'] = 0;
		if ( !$data['lijst'] ) $data['lijst'] = 0;

		if ( $sql->query ( "UPDATE album SET recensies=recensies+" . $data['recensie'] . ",lijsten=lijsten+" . $data['lijst'] . " WHERE id=" . $id  ) )
			$done++;

		$t++;
	}
	echo $done . " / " . $t . " albums bijgewerkt<br>";

	echo "<br>Gereed";
?>
