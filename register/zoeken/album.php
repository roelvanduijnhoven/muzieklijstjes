<?
	$template = "main/zoeken/album";
	include "../inc/inc.php";

	if ( !empty ( $_POST['album'] ) || !empty ( $_GET['album'] ) )
	{
		if ( empty ( $_POST['album'] ) ) $_POST['album'] = $_GET['album'];

		$tpl->assign ( "vAlbum", stripslashes ( $_POST['album'] ) );

		$_POST['album'] = addslashes ( $_POST['album'] );


		## Basisquery
		$qAlbum = "SELECT a.id as album_id, album, jaar, ar.id as artiest_id, artiest, a.recensies as recensies, a.lijsten as lijsten FROM album as a LEFT JOIN artiest AS ar ON a.artiest_id = ar.id WHERE";


		## Zoekwoorden
		if ( is_numeric ( strpos ( $_POST['album'], " " ) ) )
		{
			$keywords = explode ( " ", $_POST['album'] );

			foreach ( $keywords as $keyword )
			{
				if ( strlen ( $keyword ) > 2 )
					$wAlbum .= " album LIKE '%" .$keyword . "%' AND";
			}

			$qAlbum .= substr ( $wAlbum, 0, -3 );
		}
		elseif ( strlen ( $_POST['album'] ) > 3 )
			$qAlbum .= " album LIKE '%" . $_POST['album'] . "%' ";

		$qAlbum .= "ORDER BY ";


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
			$qAlbum .= $order['album'];


		## Fetch data
		$rAlbum = $sql->query ( $qAlbum );

		$n = $sql->num_rows ( $rAlbum );
		if ( $n > 0 )
		{
			$tpl->assign ( "i_album", $n );

			$i = 0;
			$lArtiest = '';
			while ( $album = $sql->fetch_assoc ( $rAlbum ) )
			{
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
