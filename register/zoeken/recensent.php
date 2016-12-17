<?
	$template = "main/zoeken/recensent";
	include "../inc/inc.php";

	if ( !empty ( $_POST['recensent'] ) || !empty ( $_GET['recensent'] ) )
	{
		if ( empty ( $_POST['recensent'] ) ) $_POST['recensent'] = $_GET['recensent'];
		$_POST['recensent'] = addslashes ( $_POST['recensent'] );

		$tpl->assign ( "vRecensent", $_POST['recensent'] );

		$qRecensent = "SELECT id, nRecensie, recensent FROM recensent WHERE ";

		if ( is_numeric ( strpos ( $_POST['recensent'], " " ) ) )
		{
			$keywords = explode ( " ", $_POST['recensent'] );

			foreach ( $keywords as $keyword )
			{
				if ( strlen ( $keyword ) > 2 )
					$wRecensent .= " recensent LIKE '%" .$keyword . "%' AND";
			}

			$qRecensent .= substr ( $wRecensent, 0, -3 );
		}
		elseif ( strlen ( $_POST['recensent'] ) > 2 )
			$qRecensent .= " recensent LIKE '%" . $_POST['recensent'] . "%' ";

		$qRecensent .= " ORDER BY sRecensent ASC";
		$rRecensent = $sql->query ( $qRecensent );

		$n = $sql->num_rows ( $rRecensent );
		if ( $n > 0 )
		{
			$tpl->assign ( "i_recensent", $n );

			$i = 0;
			while ( $artiest = $sql->fetch_assoc( $rRecensent ) )
			{
				$tpl->newblock ( "artiest.row" );
				$tpl->assign (
					array (
						"nr" => ++$i,

						"recensent_id" => $artiest['id'],
						"recensent" => ucfirst ( $artiest['recensent'] ),

						"nRecensie" => $artiest['nRecensie']
					)
				);
			}
		}
		else
			$tpl->newblock ( "artiest.norow" );
	}
?>
