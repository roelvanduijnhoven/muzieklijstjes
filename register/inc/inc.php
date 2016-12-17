<?php

	header('Content-Type: text/html; charset=utf-8');

	$rel = "http://www.muzieklijstjes.dev/register/";
	$abs = $_SERVER['DOCUMENT_ROOT'] . "/register/";


	# Hier komt het cache-meganisme

  //de cache wordt hiermee permanent uitgeschakeld ivm. problemen met te veel bestanden
	$_CACHE['ignore'] = true;
	if ( $_CACHE['ignore'] == false )
	{

		$post = $_POST;
		ksort ( $post );

		$get = $_GET;
		ksort ( $get );

		$merged = array_merge ( $post, $get );
		$postget = '';

		foreach ( $merged as $key => $value )
		{
			$postget .= $key . $value;
		}

		$file_name = $_SERVER['PHP_SELF'];
		$hash = md5 ( $file_name . $postget );
		$file_location = $abs . "cache/" . $hash . ".txt";

		if ( file_exists ( $file_location ) )
		{
			echo file_get_contents ( $file_location );
			exit;
		}
		else
		{
			$_CACHE['file_hash'] = $hash;
			$_CACHE['do_cache'] = true;

		}

	}



	## Include functies
	include $abs . "inc/func.php";


	## Start timer
	$pageTmr = getMicroTime ( );


	## Connect met de database en start de template class
	include $abs . "inc/db.php";
	include $abs . "inc/template.php";


	## Pagina opbouwen
	register_shutdown_function ( "output_page" );


	## Moet er een global-model pagina opgebouwd worden ?
	if ( is_integer ( $pos = strpos ( $template, "/" ) ) )
	{
		$globalTemplate = substr ( $template, 0, $pos ) . "/";
		$template = substr ( $template, $pos + 1, strlen ( $template ) );
	}

	if ( empty ( $globalTemplate ) )
		$tpl = new TemplatePower ( $abs . "tpl/" . $template . ".html" );
	else
	{
		$tpl = new TemplatePower ( $abs . "tpl/" . $globalTemplate . "global.html" );
		$tpl->assigninclude ( "include", $abs . "tpl/" . $globalTemplate . $template . ".html" );
	}

	$tpl->prepare ( );
	$tpl->assignglobal ( "url", $rel );

  // Required for all pages

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
