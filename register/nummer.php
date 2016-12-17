<?
	$template = "main/nummer";
	include "inc/inc.php";


	if ( $_GET['nummer'] && $_GET['jaar'] && $_GET['tijdschrift'] )
	{
		$nummer = $_GET['nummer'];
		$jaar = $_GET['jaar'];
		$tijdschrift_id = $_GET['tijdschrift'];

		# Haal tijdschrift info
		$qTijdschrift = "SELECT id, tijdschrift FROM tijdschrift WHERE id='" . $tijdschrift_id . "'";
		$rTijdschrift = $sql->query ( $qTijdschrift );

		$tijdschrift = $sql->fetch_assoc ( $rTijdschrift );


		$qRecensie = "
		SELECT
			al.id as album_id,
			al.album as album,
			al.lijsten as lijsten,

			ar.id as artiest_id,
			ar.artiest as artiest,

			rc.waardering as waardering,

			rt.id as recensent_id,
			rt.recensent as recensent,
			rt.aRecensent as aRecensent,

			t.tijdschrift as tijdschrift
		FROM
			( recensie as rc,
			album as al,
			tijdschrift as t )

			LEFT JOIN recensent AS rt ON rc.recensent_id = rt.id
			LEFT JOIN artiest AS ar ON al.artiest_id = ar.id
		WHERE
			rc.album_id = al.id AND
			rc.tijdschrift_id = t.id AND

			rc.tijdschrift_id = '" . $tijdschrift_id . "' AND
			rc.jaar = '" . $jaar . "' AND
			rc.nummer = '" . $nummer . "'
		ORDER BY ";

		$order = array (
			"album" => "album, ar.sArtiest, recensent",
			"artiest" => "ar.sArtiest, album, recensent",
			"recensent" => "sRecensent, ar.sArtiest, album",
			"waardering" => "waardering DESC, album, ar.sArtiest, sRecensent",
			"lijsten" => "lijsten DESC, ar.sArtiest, album ASC"
		);

		if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
			$qRecensie .= $order[ $_GET['order'] ];
		else
			$qRecensie .= $order['artiest'];

		$rRecensie = $sql->query ( $qRecensie );

		if ( $sql->num_rows ( $rRecensie ) > 0 )
		{
			## Gegevens
			$tpl->newblock ( "nummer_" );

			$tpl->assign ( array (
				"tijdschrift" => $tijdschrift['tijdschrift'],
				"tijdschrift_id" => $tijdschrift['id'],

				"nummer" => $nummer,
				"jaar" => $jaar
			) );


			$i = 0;
			$lArtiest = '';
			while ( $recensie = $sql->fetch_assoc ( $rRecensie ) )
			{
				$tpl->newblock ( "nummer_recensie" );
				$tpl->assign ( array (
					"nr" => ++$i,

					"artiest_id" => $recensie['artiest_id'],
					"artiest" => $lArtiest != $recensie['artiest'] ? inkorten ( $recensie['artiest'], 30 ) : NULL,

					"album_id" => $recensie['album_id'],
					"album" => inkorten ( $recensie['album'], 42 ),
					"lijsten" => not_null ( $recensie['lijsten'] ),

					"recensent_id" => $recensie['recensent_id'],
					"recensent" => $recensie['recensent'],
					"aRecensent" => inkorten ( $recensie['recensent'], 17 ),
					"waardering" =>not_null ( $recensie['waardering'] )
				) );

				$lArtiest = $recensie['artiest'];
			}

		}
		else
			$tpl->newblock ( "nummer_geeninformatie" );
	}
?>
