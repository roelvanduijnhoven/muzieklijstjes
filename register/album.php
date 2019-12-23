<?
	$template = "main/album";
	include "inc/inc.php";

	if ( isset ( $_GET['album'] ) && !empty ( $_GET['album'] ) )
	{
		$qAlbum = "
		SELECT
			al.id as album_id,
			al.album as album,
			al.titelnummer as titelnummer,
			al.materiaal as materiaal,
			al.jaar as jaar,
			al.url as url,

			ar.id as artiest_id,
			ar.artiest as artiest
		FROM
			album as al
			LEFT JOIN artiest AS ar ON al.artiest_id = ar.id
		WHERE
			al.id = '" . $_GET['album'] . "'";
		$rAlbum = $sql->query ( $qAlbum );

		# Album bestaat
		if ( $sql->num_rows ( $rAlbum ) > 0 )
		{
			# Statische informatie
			$album = $sql->fetch_assoc ( $rAlbum );

			$tpl->assign ( array (
				"album" => $album['album'],
				"titelnummer" => $album['titelnummer'],
				"materiaal" => $album['materiaal'],
				"albumJaar" => $album['jaar'],

				"artiest_id" => $album['artiest_id'],
				"artiest" => ( $album['artiest_id'] == 0 ) ? "#" : $album['artiest'],
				"url_muzieklijstjes" => empty ( $album['url'] ) ? NULL : "http://www.muzieklijstjes.nl/Tips/" . $album['url'] . ".htm"
			) );

			# Recenties
			$qRecensie = "
			SELECT
				rc.nummer as nummer,
				rc.jaar as jaar,
				rc.waardering as waardering,

				rt.id as recensent_id,
				rt.recensent as recensent,
				rt.aRecensent as aRecensent,

				t.id as tijdschrift_id,
				t.tijdschrift as tijdschrift,

				ru.rubriek as rubriek,
				ru.aRubriek as aRubriek
			FROM
				( recensie as rc,
				tijdschrift as t )

				LEFT JOIN recensent AS rt ON rc.recensent_id = rt.id
				LEFT JOIN rubriek AS ru ON rc.rubriek = ru.id
			WHERE
				rc.tijdschrift_id = t.id AND
				album_id = '" . $_GET['album'] . "'
			ORDER BY ";


			## Order
			$rOrder = array (
				"tijdschrift" => "tijdschrift ASC, (jaar+0) ASC, (nummer+0) ASC",
				"jaargave" => "(jaar+0) ASC, (nummer+0) ASC, tijdschrift ASC",
				"rubriek" => "rubriek ASC, (jaar+0) ASC, (nummer+0) ASC, tijdschrift ASC",
				"recensent" => "sRecensent ASC, (jaar+0) ASC, (nummer+0) ASC, tijdschrift ASC",
				"waardering" => "waardering DESC, (jaar+0) ASC, (nummer+0) ASC"
			);

			if ( isset ( $_GET['rOrder'] ) && !empty ( $rOrder[ $_GET['rOrder'] ] ) )
				$qRecensie .= $rOrder[ $_GET['rOrder'] ];
			else
				$qRecensie .= $rOrder['jaargave'];


			$rRecensie = $sql->query ( $qRecensie );

			if ( $sql->num_rows ( $rRecensie ) > 0 )
			{
				$tpl->newblock ( "album_recensie" );
				$tpl->assign ( "album_id", $album['album_id'] );

				$i = 0;
				while ( $recensie = $sql->fetch_assoc ( $rRecensie ) )
				{
					$tpl->newblock ( "album_recensie.row" );
					$tpl->assign ( array (
						"nr" => ++$i,

						"tijdschrift_id" => $recensie['tijdschrift_id'],
						"aTijdschrift" => substr ( $recensie['tijdschrift'], 0, 1 ),
						"tijdschrift" => $recensie['tijdschrift'],

						"nummer" => $recensie['nummer'],
						"jaar" => $recensie['jaar'],

						"recensent_id" => $recensie['recensent_id'],
						"recensent" => $recensie['recensent'],

						"waardering" => not_null ( $recensie['waardering'] ),

						"rubriek" => inkorten ( $recensie['rubriek'], 21 ),
						"aRubriek" => $recensie['aRubriek']
					) );
				}
			}


			# In lijsten genoemd
			$qLijst = "
			SELECT
				lb.id as lijst_id,
				lb.omschrijving as omschrijving,
				lb.jaar as jaar,
				lb.bron as bron,
				lb.jaar as jaar,
				lb.url as url,
				l.ak as ak,
				l.pos as pos,
				lb.type as type,
				lb.canon as canon
			FROM
				lijsten as l
				LEFT JOIN lijstenB as lb ON l.lijst_id = lb.id
			WHERE
				l.album_id = '" . $_GET['album'] . "' ORDER BY ";


			$order = array (
				"bron" => "lb.bron, lb.lijst, lb.jaar",
				"jaar" => "lb.jaar, lb.lijst, lb.bron",
				"lijst" => "lb.omschrijving, lb.jaar, lb.bron",
				"canon" => "lb.canon DESC, lb.bron, lb.lijst, lb.jaar"
			);

			if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
				$qLijst .= $order[ $_GET['order'] ];
			else
				$qLijst .= $order['jaar'];


			$rLijst = $sql->query ( $qLijst );

			if ( $sql->num_rows ( $rLijst ) > 0 )
			{
				$tpl->newblock ( "album_lijst" );
				$tpl->assign ( "album_id", $_GET['album'] );

				$i = 0;

				while ( $lijst = $sql->fetch_assoc ( $rLijst ) )
				{
					$tpl->newblock ( "album_lijst.row" );
					$tpl->assign ( array (
						"nr" => ++$i,

						"lijst_id" => $lijst['lijst_id'],
						"lijst" => $lijst['omschrijving'],
						"lijst_url" => empty ( $lijst['url'] ) ? NULL : "http://www.muzieklijstjes.nl/" . $lijst['url'] . ".htm",
						"jaar" => $lijst['jaar'],

						"aBron" => inkorten ( $lijst['bron'], 10 ),
						"bron" => $lijst['bron'],

						"ak" => not_null ( $lijst['ak'] ),
						"pos" => not_null ( $lijst['pos'] ),
						"gPos" => $lijst['type'] == "gp" ? "x" : NULL,

						"canon" => $lijst['canon'] == true ? "x" : NULL
					) );
				}
			}
		}
	}
?>
