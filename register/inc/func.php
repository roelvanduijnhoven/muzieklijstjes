<?
	/*  func.php
		--------
		bevat alle globale functies
	*/

	function getMicroTime ( )
	{
	   list ( $usec, $sec ) = explode ( " ", microtime ( ) );
	   return ( (float)$usec + (float)$sec );
	}


	function output_page ( )
	{
		global $tpl, $pageTmr, $sql, $_CACHE;

		$tpl->gotoBlock ( "_ROOT" );

		# Timers
		$totalTime = getMicroTime ( ) - $pageTmr;

		$sqlTime = $sql->getSqlTime ( );
		$phpTime = $totalTime - $sqlTime;

		$tpl->assign ( array (
			"totTmr" => round ( $totalTime, 4 ),
			"phpTmr" => round ( $phpTime, 4 ),
			"sqlTmr" => round ( $sqlTime, 4 ),

			"dag" => date ( "d" ),
			"maand" => date ( "m" ),
			"jaar" => date ( "y" )
		) );

		if ( false )
		{

			# Cache deze pagina
			$html = $tpl->getOutputContent ( );
			$file = "cache/" . $_CACHE['file_hash'] . ".txt";

			$handle = fopen ( $file, 'w' );
			fwrite ( $handle, $html );
			fclose ( $handle );

		}


		$tpl->printToScreen ( );
	}

	function aanvullen ( $cijfer, $max_getallen = 3, $aanvallen_met = "&nbsp;" )
	{
		$n = strlen ( $cijfer );

		if ( $n < $max_getallen )
			return str_repeat ( $aanvallen_met, abs ( $max_getallen - $n ) ) . $cijfer;
		else
			return $cijfer;
	}

	function inkorten ( $string, $len )
	{
		if ( strlen ( $string ) > $len )
			return substr ( $string, 0, $len ) . "...";
		else
			return $string;
	}

	function not_null ( $int )
	{
		return $int > 0 ? $int : NULL;
	}

	function mu_sort ($array, $key_sort)
	{
		$key_sorta = explode(",", $key_sort);

		$keys = array_keys($array[0]);

		// sets the $key_sort vars to the first
		for($m=0; $m < count($key_sorta); $m++){$nkeys[$m] = trim($key_sorta[$m]);}

		$n += count($key_sorta);    // counter used inside loop

		// this loop is used for gathering the rest of the
		// key's up and putting them into the $nkeys array
		for($i=0; $i < count($keys); $i++){ // start loop

			// quick check to see if key is already used.
			if(!in_array($keys[$i], $key_sorta)){

				// set the key into $nkeys array
				$nkeys[$n] = $keys[$i];

				// add 1 to the internal counter
				$n += "1";

			} // end if check

		} // end loop

		// this loop is used to group the first array [$array]
		// into it's usual clumps
		for($u=0;$u<count($array); $u++){ // start loop #1

			// set array into var, for easier access.
			$arr = $array[$u];

			// this loop is used for setting all the new keys
			// and values into the new order
			for($s=0; $s<count($nkeys); $s++){

				// set key from $nkeys into $k to be passed into multidimensional array
				$k = $nkeys[$s];

				// sets up new multidimensional array with new key ordering
				$output[$u][$k] = $array[$u][$k];

			} // end loop #2

		} // end loop #1

		// sort
		sort($output);

		// return sorted array
		return $output;
	}
?>
