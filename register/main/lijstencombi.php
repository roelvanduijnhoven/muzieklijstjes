<?
	$template = "main/lijstencombi";
	include "../inc/inc.php";
	
	
	# Bouw de lijst op uit geselecteerde lijsten
	
	if ( isset ( $_POST['lijsten'] ) && count ( $_POST['lijsten'] > 0 ) )
	{
		# Genereer de WHERE uit de query
		
		if ( is_array ( $_POST['lijsten'] ) )
		{
			$where = "lb.id = " . addslashes ( implode ( " OR lb.id = ", $_POST['lijsten'] ) );
		}
		else
		{
			$_GET['lijsten'] = explode ( "|", addslashes ( $_GET['lijsten'] ) );
			$where = substr ( implode ( " OR lb.id = ", $_GET['lijsten'] ), 4 );
		}
		
		
		# SQL
		
		$qLijstenC = "
		SELECT
			a.id AS album_id,
			album,
			ar.id AS artiest_id,
			artiest,
			count(  *  ) as AK,
			a.jaar as jaar
		FROM
			( lijstenB AS lb,
			lijsten AS l,
			album AS a )
			LEFT JOIN artiest AS ar ON ar.id = a.artiest_id
		WHERE
			album_id = a.id AND
			l.lijst_id = lb.id AND
			( " . $where . " )
		GROUP  BY
			l.album_id
		ORDER  BY
			AK DESC,
			artiest ASC,
			album ASC
		LIMIT
			0,1000";
			
		$rLijstenC = $sql->query ( $qLijstenC );
		$nLijstenC = $sql->num_rows ( $rLijstenC );
		
		# Er zijn nummers aanwezig voor een Lijst
		if ( $nLijstenC > 0 )
		{
			$order = array (
				"titel" => "album,artiest,jaar",
				"artiest" => "artiest,album,jaar",
				"jaar" => "jaar,artiest,album"
			);	
			
			$tpl->newblock ( "l.lijst" );
			
			# Er moet gesorteerd worden op de top 1000,
			# de Lijst moet dus afgekapt worden na 1000 nummers en DAN pas gesorteerd worden
			
			if ( isset ( $_GET['order'] ) &&  isset ( $order[ $_GET['order'] ] ) )
			{
				# Stop de lijst in een array
				
				$pos = 0;
				$tLijstenC = array ( );
				
				while ( $lijstenC = $sql->fetch_assoc ( $rLijstenC ) )
				{
					if ( $lijstenC['AK'] != $sAK )
					{
						$pos++;
					}
					
					$lijstenC['pos'] = $pos;
					$tLijstenC[] = $lijstenC;
					
					//echo "<pre>" . print_r ( $lijstenC, 1 ) . "</pre>";
						
					$sAK = $lijstenC['AK'];
					
				}
				
				
				# Sorteer deze array met een multisort functie
				
				if ( !empty ( $order[ $_GET['order'] ] ) )
					$sort = $order[ $_GET['order'] ];
				
				$tLijstenC = mu_sort ( $tLijstenC, $sort );
				
				
				# Bouw de lijst opnieuw op
				$i = 0;
				foreach ( $tLijstenC as $lijstenC )
				{
					$tpl->newblock ( "l.lijst.row" );
					$tpl->assign ( array (
						"nr" => ++$i,
						"AK" => $lijstenC['AK'],
						"album_id" => $lijstenC['album_id'],
						"album" => inkorten ( $lijstenC['album'], 45 ),
						"jaar" => not_null ( $lijstenC['jaar'] )
					) );
					
					if ( $lijstenC['pos'] != $lPos )
						$tpl->assign ( "pos", $lijstenC['pos'] );
					
					if ( $tArtiest != $lijstenC['artiest_id'] )
					{
						$tpl->assign ( array (
							"artiest_id" => $lijstenC['artiest_id'],
							"artiest" => inkorten ( $lijstenC['artiest'], 40 )
						) );
					}
					
					$tArtiest = $lijstenC['artiest_id'];
					$lPos = $lijstenC['pos'];
				}
			}
			
			
			# De lijst moet gesorteerd worden op positie
			
			else
			{
				$i = 0;
				$lAK = 0;
				$pos = 0;
				$tArtiest = '';
				
				while ( $lijstenC = $sql->fetch_assoc ( $rLijstenC ) )
				{
					if ( $lAK != $lijstenC['AK'] )
						++$pos;
					
					$tpl->newblock ( "l.lijst.row" );
					$tpl->assign ( array (
						"nr" => ++$i,
						"AK" => $lijstenC['AK'],
						"album_id" => $lijstenC['album_id'],
						"album" => inkorten ( $lijstenC['album'], 45 ),
						"jaar" => $lijstenC['jaar']
					) );
	
					if ( $lAK != $lijstenC['AK'] )
						$tpl->assign ( "pos", $pos );
					
					if ( $tArtiest != $lijstenC['artiest_id'] )
					{
						$tpl->assign ( array (
							"artiest_id" => $lijstenC['artiest_id'],
							"artiest" => $lijstenC['artiest']
						) );
					}
					
					$tArtiest = $lijstenC['artiest_id'];
					
					$lAK = $lijstenC['AK'];
				}
			}
		}
	}
	
	
	# Laat de gebruiker de lijsten selecteren
	
	else
	{
		$tpl->newblock ( "l.select" );
		
		$qLijst = "
		SELECT				
			id,
			omschrijving,
			jaar,
			bron,
			jaar,
			url,
			type,
			canon
		FROM
			lijstenb
		ORDER BY ";
		
		$order = array (
			"bron" => "bron, jaar, omschrijving",
			"jaar" => "jaar, omschrijving",
			"omschrijving" => "omschrijving",
			"canon" => "canon DESC, jaar, bron",
			"type" => "type, bron, jaar"
		);				
			
		if ( isset ( $_GET['sort'] ) && !empty ( $order[ $_GET['sort'] ] ) )
			$qLijst .= $order[ $_GET['sort'] ];
		else
			$qLijst .= $order['bron'];
		
			
		$rLijst = $sql->query ( $qLijst );
		
		if ( $sql->num_rows ( $rLijst ) > 0 )
		{
			$i = 0;
			while ( $lijst = $sql->fetch_assoc ( $rLijst ) )
			{
				$tpl->newblock ( "l.select.row" );
				
				$tpl->assign ( array ( 
					"nr" => $i++,
					
					"lijst_id" => $lijst['id'],
					"lijst" => $lijst['omschrijving'],						
					"lijst_url" => empty ( $lijst['url'] ) ? NULL : "http://www.muzieklijstjes.nl/" . $lijst['url'] . ".htm",
					"jaar" => $lijst['jaar'],
											
					"aBron" => inkorten ( $lijst['bron'], 10 ),
					"bron" => $lijst['bron'],
					
					"typelijst" => $lijst['type'],
					"canon" => $lijst['canon'] == true ? "x" : NULL
				) );
			}
		}
	}
	
?>