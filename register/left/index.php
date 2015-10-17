<?php

	$_CACHE['ignore'] = true;
	$template = "left/index";
	include "../inc/inc.php";
	
	
	$qTijdschrift = "SELECT id, tijdschrift, waardering FROM tijdschrift";
	$rTijdschrift = $sql->query ( $qTijdschrift );
	
	if ( $sql->num_rows ( $rTijdschrift ) > 0 )
	{
		while ( $tijdschrift = $sql->fetch_assoc ( $rTijdschrift ) )
		{
			if ( $tijdschrift['waardering'] > 0 )
			{
				$tpl->newblock ( "w.tijdschrift" );
				$tpl->assign ( $tijdschrift );
			}
			
			$tpl->newblock ( "tijdschrift" );
			$tpl->assign ( $tijdschrift );
		}
	}
?>