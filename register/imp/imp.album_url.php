<?
	$file = "url_album.txt";

	# MySql
	include "db.php";

	# Laad bestand in
	$input = join ( "", file ( $file  ) );
	$urls = explode ( "\r\n", $input );

	## Laad albums in
	$qAlbum = "SELECT a.id as id, album, artiest FROM album as a LEFT JOIN artiest AS ar ON a.artiest_id = ar.id";
	$rAlbum = $sql->query ( $qAlbum );

	$tempAlbum = array ( );
	while ( $album = $sql->fetch_assoc ( $rAlbum ) )
	{
		$temp = strtolower ( $album['artiest'] . $album['album'] );
		$tempAlbum [ $temp ] = $album['id'];
	}


	# Controleer alles, en voer gegevens in
	foreach ( $urls as $data )
	{
		$data = explode ( "\t", $data );
		if (!$data[0]) {
			continue;
		}

		$artiest = stripslashes ( $data[0] );
		$album = stripslashes ( $data[1] );
		$url = stripslashes ( $data[2] );

		$search = strtolower ( $artiest . $album );
		$id = $tempAlbum [ $search ];

		if ( $id > 0 )
		{
			$sql->query ( "UPDATE album SET `url` = '" . $url . "' WHERE id = " . $id );

			if ( mysql_affected_rows ( ) == 1 )
				$j++;
		}
		else
			$output .= $artiest . $album . ", album komt niet voor in collectie<br>";

		unset ( $id );
	}


	## Statestieken
	echo $j . "/" . count ( $urls ) . " geupdate<br><br>";
	echo $output;
?>
