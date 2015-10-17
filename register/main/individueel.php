<?php
	
	$template = "main/individueel";
	include "../inc/inc.php";
	
	
	if ( is_numeric ( $_GET['lijst'] ) && is_numeric ( $_GET['recensent'] ) )
	{

		# Recensent informatie
		
		$qRecensent = "SELECT id, recensent, url FROM recensent WHERE id = " . $_GET['recensent'];
		$rRecensent = $sql->query ( $qRecensent );
		
		if ( $sql->num_rows ( $rRecensent ) == 1 )
		{
		
			$recensent = $sql->fetch_assoc ( $rRecensent );	
		
			$tpl->newblock ( "individueel" );
			$tpl->assign ( array (
				"lijst_id" => $_GET['lijst'],
				"recensent_id" => $_GET['recensent']
			) );
			


			# Haal het lijstje zelf op
			
			$qLijst = "
			SELECT
				al.id as album_id,
				al.album as album,
				
				al.jaar as jaar,
				
				ar.id as artiest_id,
				ar.artiest as artiest,
				
				t.tijdschrift as tijdschrift,
				
				lb.id as lijstB_id,
				lb.lijst as lijst,
				lb.omschrijving as omschrijving,
				
				li.pos as pos		
				
			FROM
			
				( lijstenI as li,
				lijsten as l,
				lijstenB as lb )
				
				LEFT JOIN album as al ON l.album_id = al.id
				LEFT JOIN artiest as ar ON al.artiest_id = ar.id
				LEFT JOIN tijdschrift t ON lb.bron = t.tijdschrift
				
			WHERE
			
				li.lijsten_id = l.id AND
				l.lijst_id = lb.id AND
				
				li.recensent_id = " . $_GET['recensent'] . "
			ORDER BY lb.id, ";
			
			# Order
			
			$order = array (
				"album" => "album, ar.sArtiest",
				"artiest" => "ar.sArtiest, album",
				"jaar" => "jaar, ar.sArtiest, album",
				"pos" => "(li.pos+0) ASC, album, ar.sArtiest",
				"recensie" => "recensies DESC, ar.sArtiest, album",
			);
		
			if ( isset ( $_GET['order'] ) && !empty ( $order[ $_GET['order'] ] ) )
				$qLijst .= $order[ $_GET['order'] ];
			else
				$qLijst .= $order['pos'];

			
			$rLijst = $sql->query ( $qLijst );
			if ( $sql->num_rows ( $rLijst ) > 0 )
			{
			
				$i = 0;
				$lLb_id = 0;
				
				while ( $lijst = $sql->fetch_assoc ( $rLijst ) )
				{
				
					if ( $lLb_id != $lijst['lijstB_id'] )
					{
						$tpl->newblock ( "individueel.newgroup" );
						$tpl->assign ( array (
							"lijst" => $lijst['omschrijving'],
							"lijstB_id" => $lijst['lijstB_id']
						) );
						
						$i = 0;
					}
				
					$tpl->newblock ( "individueel.row" );
					$tpl->assign ( array (
					
						"nr" => ++$i,
						"pos" => $lijst['pos'],
						
						"jaar" => $lijst['jaar'],
						"album_id" => $lijst['album_id'],
						"album" => $lijst['album'],
						
						"artiest_id" => $lijst['artiest_id'],
						"artiest" => $lijst['artiest']
					
					) );
					
					$lLb_id = $lijst['lijstB_id'];
				
				}
			
			}			
		}
	}
	
?>