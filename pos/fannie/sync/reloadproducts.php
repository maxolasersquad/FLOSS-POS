<?php
	/* 
	 * Some places may be comfortable posting the form back to this page and 
	 * using a status message to signal a successful synchronization. At the
	 * Wedge, we're more comfortable displaying the results per lane on a 
	 * different page (synchronizeproducts.php) -jdp
	 */ 

	require_once($_SERVER["DOCUMENT_ROOT"]."/define.conf");

	$html='<!DOCTYPE HTML>
<html>
	<head>
		<link href="screen.css" media="screen" rel="stylesheet" type="text/css"/>
		<title>Synchronization - Products</title>
	</head>
	<body>
		<h1>Synchronize Products</h1>
		<form action="./synchronizeproducts.php" method="post" name="synchronize_products">';
	
// TODO - Auto generate lanes from define.conf, for now, hardcode
	$lanes=array(
		array('Name'=>'Lane 01', 'IP'=>'10.10.10.115'),
		array('Name'=>'Fake', 'IP'=>'10.10.10.100')
	);
	foreach ($lanes as $lane) {
		$html.='
			<label>'.$lane['Name'].'</label>
			<input checked name="lanes[]" type="checkbox" value="'.$lane['IP'].'"/>';
	}
	
	$html.='
			<input type="submit"/>
		</form>
		<p class="status">';
	
	$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
	if ($link) {
		$query='SELECT `synchronizationLog`.`datetime` FROM `is4c_log`.`synchronizationLog` WHERE `synchronizationLog`.`name`=\'product\' AND `synchronizationLog`.`status`=1 ORDER BY `synchronizationLog`.`datetime` DESC LIMIT 1';
		$result=mysql_query($query, $link);
		if ($result && mysql_num_rows($result)==1) {
			$row=mysql_fetch_array($result);
			$html.='Last synchronized @ '.$row['datetime'];
			
			$query='SELECT COUNT(*) AS \'count\' FROM `is4c_op`.`products` WHERE `products`.`modified`>=\''.$row['datetime'].'\'';
			$result=mysql_query($query, $link);
			if ($result && mysql_num_rows($result)==1) {
				$row=mysql_fetch_array($result);
				$html.='</p>
		<p>'.$row['count'].' unsynchronized product'.(($row['count']==1)?'':'s');
			} else {
				// Should the user be notified?  
			}
		} else {
			$html.='Unable to query synchronization log.';
		}
	} else {
		$html.='Unable to connect to main server.';		
	}

	$html.='</p>
	</body>
</html>';

	print_r($html);
?>