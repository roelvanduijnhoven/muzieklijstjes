<?
	$template = "main/zoeken/artiest";
	include "../inc/inc.php";

	if ( !empty ( $_POST['artiest'] ) || !empty ( $_GET['artiest'] ) )
	{
		if ( empty ( $_POST['artiest'] ) ) $_POST['artiest'] = $_GET['artiest'];
		$_POST['artiest'] = addslashes ( $_POST['artiest'] );

		$tpl->assign ( "vArtiest", $_POST['artiest'] );

		$qArtiest = "SELECT id, artiest, albums, recensies, lijsten FROM artiest WHERE ";

		if ( is_numeric ( strpos ( $_POST['artiest'], " " ) ) )
		{
			$keywords = explode ( " ", $_POST['artiest'] );

			foreach ( $keywords as $keyword )
			{
				//if ( strlen ( $keyword ) > 2 )
					$wArtiest .= " artiest LIKE '%" .$keyword . "%' AND";
			}

			$qArtiest .= substr ( $wArtiest, 0, -3 );
		}
		else if ( strlen ( $_POST['artiest'] ) > 2 )
			$qArtiest .= " artiest LIKE '%" . $_POST['artiest'] . "%' ";
                else {
                        $qArtiest .= " FALSE ";
                }

		$qArtiest .= " ORDER BY ";

		$order = array (
			"artiest" => "sArtiest, albums DESC, recensies DESC, lijsten DESC",
			"albums" => "albums DESC, artiest, recensies DESC, lijsten DESC",
			"recensies" => "recensies DESC, sArtiest, albums DESC, lijsten DESC",
			"lijsten" => "lijsten DESC, sArtiest, albums DESC, recensies DESC"
		);

		if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
			$qArtiest .= $order[ $_GET['order'] ];
		else
			$qArtiest .= $order['artiest'];

		$rArtiest = $sql->query ( $qArtiest );


		$n = $sql->num_rows ( $rArtiest );
		if ( $n > 0 )
		{
			$tpl->assign ( "i_artiest", $n );

			$i = 0;
			while ( $artiest = $sql->fetch_assoc( $rArtiest ) )
			{
				$tpl->newblock ( "artiest.row" );
				$tpl->assign (
					array (
						"nr" => ++$i,

						"artiest_id" => $artiest['id'],
						"artiest" => ucfirst ( $artiest['artiest'] ),

						"albums" => not_null ( $artiest['albums'] ),
						"recensies" => not_null ( $artiest['recensies'] ),
						"lijsten" => not_null ( $artiest['lijsten'] )
					)
				);
			}
		}
		else
			$tpl->newblock ( "artiest.norow" );
	}
?>
