<?

	$template = "main/tags";
	include "../inc/inc.php";
	
	
	$min_size = 10;
	$max_size = 35;	
	

	/*
		Selecteer belangrijkste albums en bereken tekstgroote
		ahv belangrijkheid.
		-----------------------------------------------------
	*/
	
	$albums = $sql->query( "
		SELECT		id, album, lijsten
		FROM		album
		ORDER BY 	lijsten DESC
		LIMIT		0, 50" );
	
	$max_lijsten = 0;	// Minmum aantal lijsten
	$min_lijsten = -1;	// Maximum aantal lijsten
	$arrAlbum = array();	// Albums
	
	/*
		Bepaal eerst minimum en maximum aantal lijsten.
		Bewaar albums in een array zodat je ze nog kan 
		loopen naderhand
	*/
	while ( $album = $sql->fetch_assoc( $albums ) )
	{
		array_unshift( $album, $artiest['album'] );	
		$arrAlbum[] = $album;
		
		if ( $album['lijsten'] > $max_lijsten )
		{
			$max_lijsten = $album['lijsten'];
		}
		else if ( 	$album['lijsten'] < $min_lijsten
				||	$min_lijsten < 0 )
		{
			$min_lijsten = $album['lijsten'];
		}
	}

	/*
		Loop er nu nog eens doorheen en bepaal aan de hand
		van de ratio van belangrijkheid de tekstgroote
	*/

	$ratio = ( $max_size - $min_size ) / ( $max_lijsten - $min_lijsten );
	
	sort( $arrAlbum );	// Maak de volgorde binnen de array random
	foreach ( $arrAlbum as $album )
	{
		$album['size'] = $min_size + round( ( $album['lijsten'] - $min_lijsten ) * $ratio );
		
		$tpl->newblock( 'tag.album' );
		$tpl->assign( $album );
	}
	
	
	
	/*
		Selecteer belangrijkste artiesten en bereken tekstgroote
		ahv belangrijkheid.
		-----------------------------------------------------
	*/
	
	$artiesten = $sql->query( "
		SELECT	a.id as id, artiest, COUNT(*) as lijsten
		FROM		artiest AS a
				LEFT OUTER JOIN album AS ab ON a.id = ab.artiest_id
				LEFT OUTER JOIN lijsten AS l ON l.album_id = ab.id
		WHERE		artiest <> '#'
				AND	artiest <> '#s'
		GROUP BY	a.id
		ORDER BY 	lijsten DESC
		LIMIT		0, 50" );
	
	$max_lijsten = 0;	// Minmum aantal lijsten
	$min_lijsten = -1;	// Maximum aantal lijsten
	$arrArtiest = array();	// Albums
	
	/*
		Bepaal eerst minimum en maximum aantal lijsten.
		Bewaar albums in een array zodat je ze nog kan 
		loopen naderhand
	*/
	while ( $artiest = $sql->fetch_assoc( $artiesten ) )
	{
		array_unshift( $artiest, $artiest['artiest'] );
		$arrArtiest[] = $artiest;
		
		if ( $artiest['lijsten'] > $max_lijsten )
		{
			$max_lijsten = $artiest['lijsten'];
		}
		else if ( 	$artiest['lijsten'] < $min_lijsten
				||	$min_lijsten < 0 )
		{
			$min_lijsten = $artiest['lijsten'];
		}
	}

	/*
		Loop er nu nog eens doorheen en bepaal aan de hand
		van de ratio van belangrijkheid de tekstgroote
	*/

	$ratio = ( $max_size - $min_size ) / ( $max_lijsten - $min_lijsten );
	
	sort( $arrArtiest );
	foreach ( $arrArtiest as $artiest )
	{
		
		$artiest['size'] = $min_size + round( ( $artiest['lijsten'] - $min_lijsten ) * $ratio );
		
		$tpl->newblock( 'tag.artiest' );
		$tpl->assign( $artiest );
	}	
	
?>