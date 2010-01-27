<html>
<head>
<link rel="stylesheet" href="../style.css" type="text/css" />
</head>
<body>
<?
include('../src/funct1Mem.php');

if(isset($_GET['id'])){
   $custID = $_GET['id'];
}

if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$custID = $_POST['id'];
	}   
}

$custInfoQ = "SELECT * FROM custdata WHERE id = $custID";
$custInfoR = mysql_query($custInfoQ);
$custInfo = mysql_fetch_row($custInfoR);

echo "<form action=edit.php method=POST>";
echo "<table border=1 cellspacing=3 celpadding=3><tr>";
echo "<td>Member No: ".$custInfo[0]."</td></tr>";
echo "<tr><td><input type=submit name=submit value=submit></td>";
echo "<td><a href='auto_volunteers.php target=blank>Return to volunteers list</a></td>";
echo "</tr></form>";



?>
</body>
</html>