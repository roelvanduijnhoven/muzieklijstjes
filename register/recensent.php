<?
	$template = "main/recensent";
	include "inc/inc.php";


	if ( isset ( $_GET['recensent'] ) && !empty ( $_GET['recensent'] ) )
	{
		$qRecensent = "SELECT id, nRecensie, recensent, aRecensent, url FROM recensent WHERE id='" . $_GET['recensent'] . "'";
		$rRecensent = $sql->query ( $qRecensent );

		if ( $sql->num_rows ( $rRecensent ) )
		{
			# Laat recensentnaam, link en url zien

			$recensent = $sql->fetch_assoc ( $rRecensent );

			$tpl->assign ( array (
				"recensent_id" => $recensent['id'],
				"nRecensie" => $recensent['nRecensie'],
				"recensent" => $recensent['recensent'],
				"aRecensent" => $recensent['aRecensent'],
				"url_muzieklijstjes" => empty ( $recensent['url'] ) ? NULL : "http://www.muzieklijstjes.nl/" . $recensent['url']
			) );


			# Laat zien welke genres en kenmerken bij deze recensent horen

			$qKenmerken = "SELECT id as id, kenmerk FROM kenmerk as k, kenmerk2recensent as k2r WHERE k.id = k2r.kenmerk_id AND k2r.recensent_id = " . $recensent['id'] . " ORDER BY kenmerk ASC";
			$rKenmerken = $sql->query ( $qKenmerken );

			$qGenres = "SELECT id, genre FROM genre as g, genre2recensent as g2r WHERE g.id = g2r.genre_id AND g2r.recensent_id = " . $recensent['id'] . " ORDER BY genre ASC";
			$rGenres = $sql->query ( $qGenres );


			# Haal 1e rij op
			$genres = $sql->fetch_assoc ( $rGenres );
			$kenmerken = $sql->fetch_assoc ( $rKenmerken );

			while (
				$kenmerken ||
				$genres
				  )
			{
				$tpl->newblock ( "recensent.eigenschappen" );


				if ( $kenmerken['id'] > 0 )
				{
					$tpl->newblock ( "recensent.kenmerk" );
					$tpl->assign ( array (
						"kenmerk_id" => $kenmerken['id'],
						"kenmerk" => $kenmerken['kenmerk']
					) );
				}

				if ( $genres['id'] > 0 )
				{
					$tpl->newblock ( "recensent.genre" );
					$tpl->assign ( array (
						"genre_id" => $genres['id'],
						"genre" => $genres['genre']
					) );
				}


				# Haal volgende rij op
				$genres = $sql->fetch_assoc ( $rGenres );
				$kenmerken = $sql->fetch_assoc ( $rKenmerken );
			}


			# Laat zien aan welke individuele lijstjes deze recensetn heeft gemaakt
			$qIndividueel = "
			SELECT
				lb.id as lijst_id,
				lb.omschrijving as omschrijving,
				lb.bron as bron,
				lb.jaar as jaar
			FROM
				lijstenI as li,
				lijsten as l,
				lijstenB as lb
			WHERE
				li.lijsten_id = l.id AND
				l.lijst_id = lb.id AND
				li.recensent_id = " . $recensent['id'] . "
			GROUP BY lb.id
			ORDER BY lb.bron ASC, lb.jaar ASC";

			$rIndividueel = $sql->query ( $qIndividueel );

			if ( $sql->num_rows ( $rIndividueel ) > 0 )
			{
				$i = 0;

				while ( $individueel = $sql->fetch_assoc ( $rIndividueel ) )
				{
					$tpl->newblock ( "recensent.individueel.row" );
					$tpl->assign ( array (
						"nr" => ++$i,

						"recensent_id" => $recensent['id'],

						"lijst_id" => $individueel['lijst_id'],
						"jaar" => $individueel['jaar'],
						"bron" => $individueel['bron'],
						"omschrijving" => $individueel['omschrijving']
					) );
				}
			}
			else
				$tpl->newblock ( "recensent.individueel.empty" );


			# Laat zien welke recensies door deze recensent zijn geschreven

			$qTitel = "
			SELECT
				ar.id AS artiest_id,
				ar.artiest AS artiest,

				al.id as album_id,
				al.album AS album,
				al.lijsten AS lijsten,

				rc.nummer AS nummer,
				rc.jaar AS jaar,

				t.tijdschrift as tijdschrift,
				t.id as tijdschrift_id
			FROM
				( album as al,
				recensie as rc,
				recensent as rt,
				tijdschrift as t )

				LEFT JOIN artiest as ar ON al.artiest_id = ar.id
			WHERE
				rc.recensent_id = rt.id AND
				rc.album_id = al.id AND
				rt.id = ". $_GET['recensent'] . " AND
				rc.tijdschrift_id = t.id
			ORDER BY ";

			$order = array (
				"album" => "album, sArtiest, nummer",
				"artiest" => "sArtiest, album, nummer",
				"nummer" => "tijdschrift ASC, jaar, (nummer+0) ASC, sArtiest, album",
				"lijst" => "lijsten DESC, sArtiest, album, nummer",
				"tijdschrift" => "tijdschrift, sArtiest, album, nummer"
			);

			if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
				$qTitel .= $order[ $_GET['order'] ];
			else
				$qTitel .= $order['artiest'];

			$rTitel = $sql->query ( $qTitel );

			$n = $sql->num_rows ( $rTitel );
			if ( $n > 0 )
			{
				$i = 0;
				$lArtiest = '';
				while ( $titel = $sql->fetch_assoc ( $rTitel ) )
				{
					$tpl->newblock ( "recensent_recensie" );

					$tpl->assign ( array (
						 "nr" => ++$i,
						 "nummer" => $titel['nummer'],
						 "jaar" => $titel['jaar'],
						 "tijdschrift_id" => $titel['tijdschrift_id'],
						 "lijsten" => not_null ( $titel['lijsten'] ),
						 "aTijdschrift" => substr ( $titel['tijdschrift'], 0, 3 ),
						 "tijdschrift" => $titel['tijdschrift'],

						"artiest_id" => $titel['artiest_id'],
						"artiest" => $lArtiest != $titel['artiest'] ? inkorten ( $titel['artiest'], 40 ) : NULL,

						"album_id" => $titel['album_id'],
						"album" => inkorten ( $titel['album'], 40 )
					) );

					$lArtiest = $titel['artiest'];
				}
			}
		}
	}
?>
