<?
	$template = "main/zoeken/waardering";
	include "../inc/inc.php";

	if ( is_numeric ( $_GET['tijdschrift'] ) && is_numeric ( $_GET['waardering'] ) )
	{
		$tpl->assign ( array (
			"tijdschrift_id" => $_GET['tijdschrift'],
			"waardering" => $_GET['waardering']
		) );

		## Basisquery
		$qAlbum = "SELECT a.id as album_id, a.album as album, a.jaar as jaar, a.recensies as recensies, a.lijsten as lijsten, ar.id as artiest_id, ar.artiest as artiest, r.waardering as waardering FROM recensie as r, album as a, artiest as ar WHERE r.album_id = a.id AND a.artiest_id = ar.id AND r.tijdschrift_id = " . $_GET['tijdschrift'] . " AND r.waardering = " . $_GET['waardering'];

		$qAlbum .= " ORDER BY ";


		## Sorteren
		$order = array (
			"album" => "album, sArtiest",
			"artiest" => "sArtiest, album",
			"recensies" => "recensies DESC, album, sArtiest",
			"lijsten" => "lijsten DESC, album, sArtiest",
			"jaar" => "jaar ASC, sArtiest ASC, album"
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

			$i = 0;
			$lArtiest = '';
			while ( $album = $sql->fetch_assoc ( $rAlbum ) )
			{
				$tpl->assign ( "i_waardering", $n );

				$tpl->newblock ( "album.row" );
				$tpl->assign ( array (
					"nr" => ++$i,

					"album_id" => $album['album_id'],
					"album" => $album['album'],
					"jaar" => not_null ( $album['jaar'] ),

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
