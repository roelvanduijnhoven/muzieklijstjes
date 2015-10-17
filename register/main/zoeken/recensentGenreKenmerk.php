<?
	$template = "main/zoeken/recensentGenreKenmerk";
	include "../../inc/inc.php";
	
	
	if ( isset ( $_GET['zoek'] ) )
	{
		# Als er gezocht wordt op zowel genre's als op kenmerken
		if ( isset ( $_GET['genre'] ) && isset ( $_GET['kenmerk'] ) && count ( $_GET['genre'] ) > 0 && count ( $_GET['kenmerk'] ) > 0 )
		{
			$query = "
				SELECT
					r.id as id,
					r.recensent as recensent
				FROM
					recensent AS r,
					genre2recensent AS g2r
				WHERE
					g2r.recensent_id = r.id
					AND (";
			
			$qWhere = '';
			$nGenre = 0;
			foreach ( $_GET['genre'] as $id => $useless )
			{
				$qWhere .= "g2r.genre_id = " . $id . " OR ";
				
				$nGenre++;
			}
			
			$query .= substr ( $qWhere, 0, -4 );
			
			$query .= "
					)
					AND r.id IN
						(
							SELECT
								r.id
							FROM
								recensent AS r,
								kenmerk2recensent AS k2r
							WHERE
								k2r.recensent_id = r.id
								AND (";
			
			
			$qWhere = '';
			$nKenmerk = 0;
			foreach ( $_GET['kenmerk'] as $id => $useless )
			{
				$qWhere .= "k2r.kenmerk_id = " . $id . " OR ";
				
				$nKenmerk++;
			}
			
			$query .= substr ( $qWhere, 0, -4 );
			
			$query .= "
								)
								GROUP BY r.id
								HAVING COUNT(*) = " . $nKenmerk . "
						)
				GROUP BY r.id
				HAVING COUNT(*) = " . $nGenre . "
				ORDER BY r.sRecensent ASC";
		}
		
		elseif ( isset ( $_GET['genre'] ) && count ( $_GET['genre'] ) > 0 )
		{
			$query = "
			SELECT
				r.id as id,
				r.recensent as recensent
			FROM
				recensent AS r,
				genre2recensent AS g2r
			WHERE
				g2r.recensent_id = r.id
				AND (";
			
			$nGenre = 0;
			foreach ( $_GET['genre'] as $id => $useless )
			{
				$query .= "g2r.genre_id = " . $id . " OR ";
				
				$nGenre++;
			}
			
			$query = substr ( $query, 0, -4 );
			$query .= "
				)
			GROUP BY r.id
			HAVING COUNT(*) = " . $nGenre . "
			ORDER BY r.sRecensent ASC";
		}
		
		elseif ( isset ( $_GET['kenmerk'] ) && count ( $_GET['kenmerk'] ) > 0 )
		{
			$query = "
			SELECT
				r.id as id,
				r.recensent as recensent
			FROM
				recensent AS r,
				kenmerk2recensent AS k2r
			WHERE
				k2r.recensent_id = r.id
				AND (";
				
			$nKenmerk = 0;
			foreach ( $_GET['kenmerk'] as $id => $useless )
			{
				$query .= "k2r.kenmerk_id = " . $id . " OR ";
				
				$nKenmerk++;
			}
			
			$query = substr ( $query, 0, -4 );
			$query .= "
				)
			GROUP BY r.id
			HAVING COUNT(*) = " . $nKenmerk . "
			ORDER BY r.sRecensent ASC";
		}
		
	
		
		# klaar met het generern van de sql!!!!!,
		#             znu de resultaten weergeven
		$rRecensent = $sql->query ( $query );
		$nRecensent = $sql->num_rows ( $rRecensent );
		
		$tpl->newblock ( "result" );
		if ( $nRecensent > 0 )
		{
			$tpl->assign ( "i_recensent", $nRecensent );
			
			$i = 0;
			while ( $recensent = $sql->fetch_assoc ( $rRecensent ) )
			{
				$tpl->newblock ( "result.row" );
				$tpl->assign ( array (
					"nr" => ++$i,
					"id" => $recensent['id'],
					"recensent" => $recensent['recensent']
				) );
			}
		}
		else
		{
			$tpl->newblock ( "result.norow" );
		}
	}
	
	
	# Genereer het zoekgedeelte
	
	else
	{
		$qKenmerken = "SELECT id, kenmerk FROM kenmerk ORDER BY kenmerk ASC";
		$rKenmerken = $sql->query ( $qKenmerken );
		
		$qGenres = "SELECT id, genre FROM genre ORDER BY genre ASC";
		$rGenres = $sql->query ( $qGenres );
		
		
		$tpl->newblock ( "zoek" );
		
		
		# Haal 1e rij op
		$genres = $sql->fetch_assoc ( $rGenres );		
		$kenmerken = $sql->fetch_assoc ( $rKenmerken );
		
		while (
			$kenmerken || 
			$genres 
			  )
		{
			$tpl->newblock ( "zoek.row" );
			
			
			if ( $kenmerken['id'] > 0 )
			{
				$tpl->newblock ( "zoek.row.kenmerk" );
				$tpl->assign ( array (
					"kenmerk_id" => $kenmerken['id'],
					"kenmerk" => $kenmerken['kenmerk']
				) );
			}
			
			if ( $genres['id'] > 0 )
			{
				$tpl->newblock ( "zoek.row.genre" );
				$tpl->assign ( array (
					"genre_id" => $genres['id'],
					"genre" => $genres['genre']
				) );
			}			
			
			
			# Haal volgende rij op
			$genres = $sql->fetch_assoc ( $rGenres );		
			$kenmerken = $sql->fetch_assoc ( $rKenmerken );
		}
	}
?>