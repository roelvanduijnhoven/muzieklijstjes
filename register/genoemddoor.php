<?php

	$template = "main/genoemddoor";
	include "inc/inc.php";


	if ( is_numeric ( $_GET['lijst'] ) )
	{

		# Lijst

		$qLijst = "
		SELECT
			lb.id as lijst_id,
			omschrijving,

			al.id as album_id,
			album,

			ar.id as artiest_id,
			artiest
		FROM
			lijsten as l,
			lijstenB as lb,
			album as al,
			artiest as ar
		WHERE
			l.lijst_id = lb.id AND
			l.album_id = al.id AND
			al.artiest_id = ar.id AND
			l.id = " . $_GET['lijst'];

		$rLijst = $sql->query ( $qLijst );

		if ( $sql->num_rows ( $rLijst ) == 1 )
		{

			# Algemene informatie

			$lijst = $sql->fetch_assoc ( $rLijst );


			$tpl->assign ( array (
				"lijst_id" => $_GET['lijst'],

				"bLijst_id" => $lijst['lijst_id'],
				"lijst" => $lijst['omschrijving'],

				"album_id" => $lijst['album_id'],
				"album" => $lijst['album'],

				"artiest_id" => $lijst['artiest_id'],
				"artiest" => $lijst['artiest']
			) );


			# Lijstje met recensenten

			$qRecensent = "
			SELECT
				r.id as id,
				recensent,
				pos
			FROM
				lijstenI as li,
				recensent as r
			WHERE
				li.recensent_id = r.id AND
				li.lijsten_id = " . $_GET['lijst'] . "
			ORDER BY ";


			# Sorteren
			$order = array (
				"pos" => "pos ASC, sRecensent ASC",
				"recensent" => "sRecensent ASC"
			);

			if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
				$qRecensent .= $order[ $_GET['order'] ];
			else
				$qRecensent .= $order['pos'];


			$rRecensent = $sql->query ( $qRecensent );

			if ( $sql->num_rows ( $rRecensent ) > 0 )
			{

				$i = 0;
				while ( $recensent = $sql->fetch_assoc ( $rRecensent ) )
				{
					$tpl->newblock ( "genoemddoor.row" );
					$tpl->assign ( array (
						"nr" => ++$i,

						"pos" => $recensent['pos'],

						"recensent_id" => $recensent['id'],
						"recensent" => $recensent['recensent']
					) );
				}

			}
		}
	}

?>
