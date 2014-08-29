<?php
$DATABASE_HOST = "localhost";
$DATABASE_USER = "root";
$DATABASE_PASSWORD = "";
$DATABASE = "ccro_bc";

$GLINK = "";

function dbQuery($query){
	global $DATABASE_HOST;
	global $DATABASE_USER;
	global $DATABASE_PASSWORD;
	global $DATABASE;
	global $GLINK;
	
	$returnArr = array();

	if($GLINK == ""){
		/* Connecting, selecting database */
		$link = mysql_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASSWORD)
			or die("Could not connect : " . mysqli_error($link));
		$GLINK = $link;
	}else{
		$link = $GLINK;
	}
	
	mysql_select_db($DATABASE,$link) or die("Could not select database: " . $DATABASE);
	
	/* Performing SQL query */
	$result = mysql_query($query,$link) or die("Query failed : " . mysql_error() . "<br>Query: <b>$query</b>");

	
	//if query is select
	if(@mysql_num_rows($result))
	{
		while ($row = mysql_fetch_assoc($result))
		{
			array_push($returnArr, $row);
		}		
	}
	//if query is insert
	else if(@mysql_insert_id($link))
	{
		$returnArr["mysql_insert_id"] = @mysql_insert_id($link);
	}
	//other queries
	else
	{
		return $returnArr;
	}
		

	/* Free resultset */
	@mysql_free_result($result);
	
	//return array
	return $returnArr;
}

?>
