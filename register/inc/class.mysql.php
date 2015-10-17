<?
	class mysql
	{
		var $conn = NULL;
		
		var $tmr = 0;
		var $queryArr = array ( );
	
		function connect ( $host = "localhost", $user = "root", $pass = "" )
		{
			$sTime = $this->__getMicroTime ( );
			
			$this->conn = mysql_connect ( $host, $user, $pass ) or $this->__error ( mysql_error ( $this->conn ) );
			
			$this->tmr += $this->__getMicroTime ( ) - $sTime;
		}
		
		function select_db ( $database )
		{
			$sTime = $this->__getMicroTime ( );		
		
			mysql_select_db ( $database, $this->conn ) or $this->__error ( mysql_error ( $this->conn ) );
			
			$this->tmr += $this->__getMicroTime ( ) - $sTime;			
		}
		
		function query ( $query, $echoSQL = false )
		{
			$sTime = $this->__getMicroTime ( );
			
			if ( $echoSQL == true )
				echo $query;
					
			$return = mysql_query ( $query, $this->conn ) or $this->__error ( 'query: ' . $query . '<br>Error: ' . mysql_error ( $this->conn ) );
			
			$qUsage = $this->__getMicroTime ( ) - $sTime;
			
			$queryArr[] = array (
				"q" => $query,
				"tmr" => $qUsage
			);
			
			$this->tmr += $qUsage;
			
			return $return;
		}
		
		function fetch_assoc ( $result )
		{
			return mysql_fetch_assoc ( $result );
		}
		
		function num_rows ( $result )
		{
			return mysql_num_rows ( $result );
		}
		
		function result ( $result, $row )
		{
			return mysql_result ( $result, $row );
		}
		
		function getSqlTime ( )
		{
			return $this->tmr;
		}
		
		function __error ( $errorMsg )
		{
			die ( "Mysql failure:<br>\n-----------------------------<br>\n<i>" . $errorMsg . "</i>" );
		}
		
		function __getMicroTime ( )
		{
		   list ( $usec, $sec ) = explode ( " ", microtime ( ) );
		   return ( (float)$usec + (float)$sec );
		}
	}
?>