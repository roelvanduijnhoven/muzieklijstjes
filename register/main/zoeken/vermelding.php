<?
	$template = "main/zoeken/vermelding";
	include "../../inc/inc.php";
	
	if ( is_numeric ( $_GET['vermelding'] ) )
	{
		$tpl->assign ( array (
			"vermelding" => $_GET['vermelding']
		) );
	
		## Basisquery
		$qAlbum = "SELECT a.id as album_id, album, jaar, ar.id as artiest_id, artiest, a.recensies as recensies, a.lijsten as lijsten FROM album as a LEFT JOIN artiest AS ar ON a.artiest_id = ar.id WHERE a.lijsten = " . $_GET['vermelding'];

		$qAlbum .= " ORDER BY ";
		
		## Sorteren
		$order = array (
			"album" => "album, sArtiest",
			"artiest" => "sArtiest, album",
			"lijsten" => "lijsten DESC, album, sArtiest",
			"jaar" => "jaar ASC, sArtiest ASC, album",
			"recensies" => "recensies DESC, album ASC, sArtiest ASC"
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
				$tpl->assign ( "i_vermelding" , $n );
			
				$tpl->newblock ( "album.row" );
				$tpl->assign ( array (
					"nr" => ++$i,
					
					"album_id" => $album['album_id'],
					"album" => $album['album'],
					"jaar" => empty ( $album['jaar'] ) ? "" : $album['jaar'],
					"recensies" => empty ( $album['recensies'] ) ? "" : $album['recensies'],
					
					"artiest_id" => $album['artiest_id'],
					"artiest" => $lArtiest != $album['artiest'] ? $album['artiest'] : NULL,
					
					"lijsten" => $album['lijsten']
				) );
				
				$lArtiest = $album['artiest'];
			}
		}
		
	}
?>