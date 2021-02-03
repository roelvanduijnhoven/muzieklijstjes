<?php

$qCanon = "
SELECT *
FROM
(
	SELECT
		a.id AS album_id,
		album,
		ar.id AS artiest_id,
		sArtiest,
		artiest,
		COUNT( * ) as AK,
		a.jaar as jaar
	FROM
		lijsten AS l,
		lijstenB AS lb,
		album AS a
		LEFT JOIN artiest AS ar ON ar.id = a.artiest_id
	WHERE
		album_id = a.id AND
		lb.lijst IN ('CL12', 'CR20', 'G11', 'H33', 'Hu40', 'J69', 'M70', 'N82
', 'N82', 'P30', 'RC22', 'RS072', 'SP34', 'U51') AND
		l.lijst_id = lb.id
	GROUP BY
		l.album_id
	ORDER BY AK DESC, album ASC
	LIMIT 0, 15
) AS maintable";


$order = array (
	"pos" => " ORDER BY AK DESC, sArtiest ASC",
	"titel" => " ORDER  BY album ASC, sArtiest ASC, jaar",
	"artiest" => " ORDER  BY sArtiest ASC, album ASC, jaar",
	"jaar" => " ORDER  BY jaar, sArtiest, album"
);

$rCanon = $sql->query ( $qCanon );
$nCanon = $sql->num_rows ( $rCanon );

# Er zijn nummers aanwezig voor een Canon
if ( $nCanon > 0 )
{
	$pos = 0;



	$order = array ( );
	while ( $canon = $sql->fetch_assoc ( $rCanon ) )
	{
		if ( !isset ( $order[ $canon['AK'] ] ) )
		{
			$order[ $canon['AK'] ] = 1;
		}
	}

	## Sorteer van hoog naar laag
	krsort ( $order );

	$i = 0;
	foreach ( $order as $ak => $useless )
	{
		$order[ $ak ] = ++$i;
	}


	## Maak NU de lijst
	mysql_data_seek ( $rCanon, 0 );


	$i = 0;
	$lAK = '';
	$tArtiest = '';
	while ( $canon = $sql->fetch_assoc ( $rCanon ) )
	{
		$AK = $order [ $canon['AK'] ];

		$tpl->newblock ( "yearlist.row" );
		$tpl->assign ( array (
			"nr" => ++$i,
			"AK" => $canon['AK'],
			"album_id" => $canon['album_id'],
			"album" => inkorten ( $canon['album'], 45 ),
			"jaar" => not_null ( $canon['jaar'] )
		) );

		if ( $lAK != $AK )
			$tpl->assign ( "pos", $AK );

		if ( $tArtiest != $canon['artiest_id'] )
		{
			$tpl->assign ( array (
				"artiest_id" => $canon['artiest_id'],
				"artiest" => $canon['artiest']
			) );
		}

		$tArtiest = $canon['artiest_id'];

		$lAK = $AK;
	}
}
