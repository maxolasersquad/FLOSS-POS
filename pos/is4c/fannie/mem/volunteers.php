<html>
<head>
<Title>Volunteer timesheets</Title>
<link rel="stylesheet" href="../style.css" type="text/css" />
</head>
<body>
<?php
include('../src/functions.php');

if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
}else{
      foreach ($_GET AS $key => $value) {
          $$key = $value;
      }
}

if(isset($_POST['submit'])){
//	print_r(array_combine($id,$hours));
	$comb_arr = array_combine($id,$hours);
	foreach ($comb_arr as $key => $value) {
		mysql_query("UPDATE custdata SET SSI = (SSI + ".$value.") WHERE id = ".$key);
	}
}

setlocale(LC_MONETARY, 'en_US');

$query = "SELECT CardNo, LastName, FirstName, SSI, id FROM is4c_op.custdata WHERE staff IN(3,6) ORDER BY LastName";

$queryR = mysql_query($query);

echo "<form action=auto_volunteers.php method=POST>";
echo "<table border=0 cellspacing=3 cellpadding=3 align=center>";
echo "<th>Card No<th>Last Name<th>First Name<th>Hours";
while($query = mysql_fetch_row($queryR)){
	echo "<tr><td>".$query[0]."</td>";
	echo "<td>".$query[1]."</td>";
	echo "<td>".$query[2]."</td>";
	echo "<td>".$query[3]."</td>";
	echo "<td><input size=4 name='hours[]' id='hours'></td>";
	echo "<td><input type=hidden name='id[]' value=".$query[4]."></tr></tr>";
}
echo "<tr><td><input type=submit name=submit value=submit></td></tr>";
echo "</table></form>";


//
// PHP INPUT DEBUG SCRIPT  -- very helpful!
//
/*
function debug_p($var, $title) 
{
    print "<p>$title</p><pre>";
    print_r($var);
	mysql_error();
    print "</pre>";
}  

debug_p($_REQUEST, "all the data coming in");
*/
?>