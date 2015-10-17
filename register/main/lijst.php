	<?
	$template = "main/lijst";
	include "../inc/inc.php";
	
	if ( isset ( $_GET['lijst'] ) && is_numeric ( $_GET['lijst'] ) )
	{
		$qLijst = "SELECT id, lijst, omschrijving, bron, jaar, url, type FROM lijstenB WHERE id = " . $_GET['lijst'];
		$rLijst = $sql->query ( $qLijst );
		
		if ( $sql->num_rows ( $rLijst ) > 0 )
		{
			## Informatie
			$lijst = $sql->fetch_assoc ( $rLijst );
			
			
			## Positie
			if ( $lijst['type'] == "pos" )
			{
				$tpl->newblock ( "l.pos" );
				$tpl->assign ( array ( 
					"omschrijving" => $lijst['omschrijving'],
					"bron" => $lijst['bron'],
					"jaar" => $lijst['jaar'],
					"url_muzieklijstjes" => empty ( $lijst['url'] ) ? NULL : "http://www.muzieklijstjes.nl/" . $lijst['url'],
					"lijst_id" => $lijst['id']
				) );
				
				## Haal nummers op
				$qAlbum = "
				SELECT
					l.pos as pos,
					
					a.id as album_id,
					a.album as album,
					a.recensies as recensies,
					
					ar.id as artiest_id,
					ar.artiest as artiest,
					a.jaar
				FROM
					( lijsten as l,
					album as a )
					LEFT JOIN artiest as ar ON ar.id = a.artiest_id
				WHERE
					l.lijst_id = " . $lijst['id'] . " AND
					a.id = l.album_id
				ORDER BY ";
				
				
				## Order
				$order = array (
					"album" => "album, ar.sArtiest",
					"artiest" => "ar.sArtiest, album",
					"jaar" => "jaar, ar.sArtiest, album",
					"pos" => "(pos+0) ASC, album, ar.sArtiest",
					"recensie" => "recensies DESC, ar.sArtiest, album"
				);
			
				if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
					$qAlbum .= $order[ $_GET['order'] ];
				else
					$qAlbum .= $order['pos'];
					

				$rAlbum = $sql->query ( $qAlbum );
				
				$n = $sql->num_rows ( $rAlbum );
				$tpl->assign ( "n", $n );
				
				if ( $n > 0 )
				{
				
					$j = 0;
					$lPos = 0;
					$lArtiest = '';
					while ( $album = $sql->fetch_assoc ( $rAlbum ) )
					{
						if ( ( !isset ( $_GET['order'] ) || $_GET['order'] == "pos" || $_GET['order'] == "" ) && $album['pos'] != $lPos + 1 )
						{
							$n = $album['pos'] - ( $lPos + 1 );
							for ( $i = 0; $i < $n; $i++ )
							{
								$tpl->newblock ( "l.pos.row" );
								$tpl->assign ( array (
									"nr" => $album['pos'] + $i - 1,
									"album" => "<b>[niet aangetroffen in database]</b>"
								) );
							}
						}
					
						$tpl->newblock ( "l.pos.row" );
						$tpl->assign ( array (
							"i" => ++$j,
							"album_id" => $album['album_id'],
							"album" => $album['album'],
							"recensies" => not_null ( $album['recensies'] ),
							
							
							
							"nr" => $lPos != $album['pos'] ? aanvullen ( $album['pos'] ) : NULL,
							
							"artiest_id" => $lArtiest != $album['artiest_id'] ? $album['artiest_id'] : NULL,
							"artiest" => $lArtiest != $album['artiest'] ? $album['artiest'] : NULL,
							
							"jaar" => not_null ( $album['jaar'] )
						) );
						
						$lArtiest = $album['artiest'];
						$lPos = $album['pos'];
					}
				}
			}
			
			## Aantal keer genoemd
			elseif ( $lijst['type'] == "ak" )
			{
				$tpl->newblock ( "l.ak" );
				$tpl->assign ( array ( 
					"omschrijving" => $lijst['omschrijving'],
					"bron" => $lijst['bron'],
					"jaar" => $lijst['jaar'],
					"url_muzieklijstjes" => empty ( $lijst['url'] ) ? NULL : "http://www.muzieklijstjes.nl/" . $lijst['url'],
					"lijst_id" => $lijst['id']
				) );
				
				
				#  Haal de deelnemers op
				
				$qDeelnemer = "
				SELECT
					DISTINCT(r.id) as id,
					recensent,
					nRecensie
				FROM
					lijstenI as li,
					lijsten as l,
					recensent as r
				WHERE
					li.lijsten_id = l.id AND
					li.recensent_id = r.id AND
					l.lijst_id = " . $_GET['lijst'] . "
				ORDER BY sRecensent ASC";
				$rDeelnemer = $sql->query ( $qDeelnemer );
				$nDeelnemer = $sql->num_rows ( $rDeelnemer );
				
				if ( $nDeelnemer > 0 )
				{
					
					$i = 0;
					while ( $deelnemer = $sql->fetch_assoc ( $rDeelnemer ) )
					{
						$tpl->newblock ( "l.ak.deelnemer" );
						$tpl->assign ( array (
							"i" => ++$i,
							
							"recensent_id" => $deelnemer['id'],
							"recensent" => $deelnemer['recensent'],
							"nRecensie" => $deelnemer['nRecensie']
						) );
					}
					
				}
				
				
				## Haal nummers op
				$qAlbum = "
				SELECT
					l.id as lijst_id,
				
					l.ak as ak,
					l.punten as punten,
					
					a.id as album_id,
					a.album as album,
					a.recensies as recensies,
					
					ar.id as artiest_id,
					ar.artiest as artiest,
					a.jaar
				FROM
					( lijsten as l,
					album as a )
					LEFT JOIN artiest as ar ON ar.id = a.artiest_id
				WHERE
					l.lijst_id = " . $lijst['id'] . " AND
					a.id = l.album_id
				ORDER BY ";
				
				## Order
				$order = array (
					"album" => "album, ar.sArtiest",
					"artiest" => "ar.sArtiest, album",
					"jaar" => "jaar, ar.sArtiest, album",
					"pos" => "(ak+0) DESC, punten DESC, ar.sArtiest, album",
					"recensie" => "recensies DESC, ar.sArtiest, album",
					"punten" => "punten DESC, (ak+0) DESC, album ASC, ar.sArtiest ASC"
				);
			
				if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
					$qAlbum .= $order[ $_GET['order'] ];
				else
					$qAlbum .= $order['pos'];
				
				
				$rAlbum = $sql->query ( $qAlbum );
				
				$n = $sql->num_rows ( $rAlbum );
				$tpl->assign ( "n", $n );
				
				if ( $n > 0 )
				{			
					if ( empty ( $_GET['order'] ) || $_GET['order'] == "pos" )
					{				
						$i = 0;
						$j = 0;
						
						$lAk = 0;
						$lPunten = 0;
						$lArtiest = '';
						
						while ( $album = $sql->fetch_assoc ( $rAlbum ) )
						{
							if ( $album['ak'] != $lAk || $album['punten'] != $lPunten )
								$i++;
						
							$tpl->newblock ( "l.ak.row" );
							$tpl->assign ( array (
								"album_id" => $album['album_id'],
								"album" => $album['album'],
								"recensies" => not_null ( $album['recensies'] ),
								
								
								"lijst_id" => $album['lijst_id'],
								
								"i" => ++$j,
								"nr" => ( $album['ak'] != $lAk || $album['punten'] != $lPunten ) ? $i : NULL,							
								"ak" => $album['ak'],
								"punten" => $album['punten'],
								
								"artiest_id" => $lArtiest != $album['artiest_id'] ? $album['artiest_id'] : NULL,
								"artiest" => $lArtiest != $album['artiest'] ? $album['artiest'] : NULL,
								
								"jaar" => not_null ( $album['jaar'] )
							) );
							
							$lArtiest = $album['artiest'];
							
							$lAk = $album['ak'];
							$lPunten = $album['punten'];
						}
					}
					else
					{
						## Genereer cache voor positie
						
						## Haal alle unieke AK's op
						$order = array ( );
						while ( $album = $sql->fetch_assoc ( $rAlbum ) )
						{
							$temp = aanvullen ( $album['ak'], 4, 0 ) . aanvullen ( $album['punten'], 4, 0 );
							
							if ( !is_array ( $order[ $temp ] ) )
							{
								$order[ $temp ] = 1;
							}
						}
						
						## Sorteer van hoog naar laag
						krsort ( $order );
						
						$i = 0;
						## Geef nummer aan AK
						foreach ( $order as $ak => $useless )
						{
							$order[ $ak ] = ++$i;
						}
						
						
						## Maak NU de lijst
						mysql_data_seek ( $rAlbum, 0 );
						
						$i = 0;
						$j = 0;
						
						while ( $album = $sql->fetch_assoc ( $rAlbum ) )
						{
							if ( $album['ak'] != $lAk )
								$i++;
								
							$pos = $order [ ( aanvullen ( $album['ak'], 4, 0 ) . aanvullen ( $album['punten'], 4, 0 ) ) ];
						
							$tpl->newblock ( "l.ak.row" );
							$tpl->assign ( array (
								"album_id" => $album['album_id'],
								"album" => $album['album'],
								"recensies" => not_null ( $album['recensies'] ),
								
								"lijst_id" => $album['lijst_id'],
								
								"i" => ++$j,
								"nr" => ( $pos != $lPos ) ? $order [ ( aanvullen ( $album['ak'], 4, 0 ) . aanvullen ( $album['punten'], 4, 0 ) ) ] : NULL,							
								"ak" => $album['ak'],
								"punten" => $album['punten'],
								
								"artiest_id" => $lArtiest != $album['artiest_id'] ? $album['artiest_id'] : NULL,
								"artiest" => $lArtiest != $album['artiest'] ? $album['artiest'] : NULL,
								
								"jaar" => not_null ( $album['jaar'] )
							) );
							
							$lArtiest = $album['artiest'];
							$lPos = $pos;
						}
						
					}
				}
			}

			## Geen positie
			else
			{
				$tpl->newblock ( "l.gp" );
				$tpl->assign ( array ( 
					"lijst_id" => $lijst['id'],
					"omschrijving" => $lijst['omschrijving'],
					"bron" => $lijst['bron'],
					"jaar" => $lijst['jaar'],
					"url_muzieklijstjes" => empty ( $lijst['url'] ) ? NULL : "http://www.muzieklijstjes.nl/" . $lijst['url'],
				) );
				
				## Haal nummers op
				$qAlbum = "
				SELECT
					a.id as album_id,
					a.album as album,
					a.recensies as recensies,
					
					ar.id as artiest_id,
					ar.sArtiest as sArtiest,
					ar.artiest as artiest,
					a.jaar
				FROM
					( lijsten as l,
					album as a )
					LEFT JOIN artiest as ar ON ar.id = a.artiest_id
				WHERE
					l.lijst_id = " . $lijst['id'] . " AND
					a.id = l.album_id
				ORDER BY ";
					
				$order = array (
					"album" => "album, sArtiest, jaar",
					"artiest" => "sArtiest, album, jaar",
					"jaar" => "jaar, album, sArtiest",
					"recensie" => "recensies DESC, ar.sArtiest, album"					
				);
					
				if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
					$qAlbum .= $order[ $_GET['order'] ];
				else
					$qAlbum .= $order['artiest'];
				
				$rAlbum = $sql->query ( $qAlbum );
				
				$n = $sql->num_rows ( $rAlbum );
				$tpl->assign ( "n", $n );
								
				if ( $n > 0 )
				{
					$i = 0;
					$lArtiest = '';
					
					while ( $album = $sql->fetch_assoc ( $rAlbum ) )
					{
						$tpl->newblock ( "l.gp.row" );
						$tpl->assign ( array (
							"album_id" => $album['album_id'],
							"album" => $album['album'],
							"recensies" => not_null ( $album['recensies'] ),
							
							"nr" => ++$i,
							
							"artiest_id" => $lArtiest != $album['artiest_id'] ? $album['artiest_id'] : NULL,
							"artiest" => $lArtiest != $album['artiest'] ? $album['artiest'] : NULL,
							
							"jaar" => not_null ( $album['jaar'] )
						) );
						
						$lArtiest = $album['artiest'];
					}
				}				
			}			
		}
	}
?>
