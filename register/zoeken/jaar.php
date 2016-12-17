<?
	$template = "main/zoeken/jaar";
	include "../inc/inc.php";

	if ( is_numeric ( $_GET['jaar'] ) )
	{
		$tpl->assign ( array (
			"x_jaar" => $_GET['jaar']
		) );

		## Basisquery
		$qAlbum = "SELECT a.id as album_id, album, jaar, ar.id as artiest_id, artiest, a.recensies as recensies, a.lijsten as lijsten FROM album as a LEFT JOIN artiest AS ar ON a.artiest_id = ar.id WHERE a.jaar = " . $_GET['jaar'];

		$qAlbum .= " ORDER BY ";


		## Sorteren
		$order = array (
			"album" => "album, sArtiest",
			"artiest" => "sArtiest, album",
			"recensies" => "recensies DESC, album, sArtiest",
			"lijsten" => "lijsten DESC, album ASC, sArtiest ASC",
		);

		if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
			$qAlbum .= $order[ $_GET['order'] ];
		else
			$qAlbum .= $order['artiest'];


		## Fetch data
		$rAlbum = $sql->query ( $qAlbum );

		$n = $sql->num_rows ( $rAlbum );
		if ( $n > 0 )
		{
			$tpl->assign ( "i_jaar", $n );

			$i = 0;
			$lArtiest = '';
			while ( $album = $sql->fetch_assoc ( $rAlbum ) )
			{
				$tpl->newblock ( "album.row" );
				$tpl->assign ( array (
					"nr" => ++$i,

					"album_id" => $album['album_id'],
					"album" => $album['album'],

					"artiest_id" => $album['artiest_id'],
					"artiest" => $lArtiest != $album['artiest'] ? $album['artiest'] : NULL,

					"recensies" => not_null ( $album['recensies'] ),
					"lijsten" => not_null ( $album['lijsten'] )
				) );

				$lArtiest = $album['artiest'];
			}
		}

	}
?>
