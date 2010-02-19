<?php
	$html='<!DOCTYPE HTML>
<html>
	<head>
		<link href="screen.css" media="screen" rel="stylesheet" type="text/css"/>
		<title>Synchronization</title>
	</head>
	<body>
		<h1>Synchronization</h1>
		<ul>
			<li><a href="reload.php?t=products">Products</a></li>
			<li><a href="reload.php?t=custdata">Membership</a></li>
			<li><a href="reload.php?t=employees">Employees</a></li>
			<li><a href="reload.php?t=departments">Departments</a></li>
			<li><a href="reload.php?t=subdepts">Subdepartments</a></li>
			<li><a href="reload.php?t=tenders">Tenders</a></li>
		</ul>
	</body>
</html>';
	
	print_r($html);
?>