<?
	$template = "main/artiest";
	include "inc/inc.php";

	if ( is_numeric ( $_GET['artiest'] ) )
	{
		$rArtiest = $sql->query ( "SELECT id, artiest FROM artiest WHERE id = " . $_GET['artiest'] );
		$artiest = $sql->fetch_assoc ( $rArtiest );

		$tpl->assign ( array (
			"id" => $_GET['artiest'],
			"artiest" => $artiest['artiest'],
		) );


		## albums
		$qAlbum = "SELECT id, album, jaar, recensies, lijsten FROM album WHERE artiest_id = " . $_GET['artiest'] . " ORDER BY ";

		$order = array (
			"album" => "album, jaar, recensies DESC, lijsten DESC",
			"jaar" => "jaar, album, recensies DESC, lijsten DESC",
			"recensies" => "recensies DESC, album, jaar, lijsten DESC",
			"lijsten" => "lijsten DESC, album, jaar, recensies DESC"
		);

		if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
			$qAlbum .= $order[ $_GET['order'] ];
		else
			$qAlbum .= $order['album'];

		$rAlbum = $sql->query ( $qAlbum );
		if ( $sql->num_rows ( $rAlbum ) > 0 )
		{
			$i = 0;
			while ( $album = $sql->fetch_assoc ( $rAlbum ) )
			{

				$tpl->newblock ( "artiest.row" );
				$tpl->assign ( array (
					"nr" => ++$i,
					"album_id" => $album['id'],
					"album" => $album['album'],
					"jaar" => not_null ( $album['jaar'] ),

					"recensies" => not_null ( $album['recensies'] ),
					"lijsten" => not_null ( $album['lijsten'] )
				) );
			}
		}
		else
			$tpl->newblock ( "artiest.norow" );
	}
?>
