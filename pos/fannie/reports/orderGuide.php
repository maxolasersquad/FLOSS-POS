<?php
/*******************************************************************************

    Copyright 2007 People's Food Co-op, Portland, Oregon.

    This file is part of Fannie.

    IS4C is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IS4C is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/

//include($_SERVER["DOCUMENT_ROOT"].'/src/functions.php');
require($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');


?>
<html>
<head>
<title>Department Movement Report</title>
<link rel="STYLESHEET" href="../src/style.css" type="text/css">
</head>
<?
?>

<html>
<head>
<title>Department Movement Report</title>
<link rel="STYLESHEET" href="../src/style.css" type="text/css"></head>
<?
if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
}else{
      foreach ($_GET AS $key => $value) {
          $$key = $value;
      }
}

?>

<body>

<?php
$today = date("F d, Y");	

//$_SESSION['deptArray'] = 0;

if(is_array($_POST['dept'])) {
	$_SESSION['deptArray'] = implode(",",$_POST['dept']);
	$arrayName = $_SESSION['deptArray'];
} 

echo "Report generated " . $today;
echo "</br>";
echo "From ";
print $date1;
echo " to ";
print $date2;
echo "</br></br>";


$link1 = "{$_SERVER['PHP_SELF']}?sort=upa";
$link2 = "{$_SERVER['PHP_SELF']}?sort=dsa";
$link3 = "{$_SERVER['PHP_SELF']}?sort=qta";

if (isset($_GET['sort'])) { 
	switch ($_GET['sort']) {
		case 'upa':
		$order_by = 'p.upc ASC';
		$link1 = "{$_SERVER['PHP_SELF']}?sort=upd";
		break;
		case 'upd':
		$order_by = 'p.upc DESC';
		$link1 = "{$_SERVER['PHP_SELF']}?sort=upa";
		break;
		case 'dsa':
		$order_by = 'p.description ASC';
		$link2 = "{$_SERVER['PHP_SELF']}?sort=dsd";
		break;
		case 'dsd':
		$order_by = 'p.description DESC';
		$link2 = "{$_SERVER['PHP_SELF']}?sort=dsa";
		break;
		case 'qta':
		$order_by = 'SUM(t.quantity) ASC';
		$link3 = "{$_SERVER['PHP_SELF']}?sort=qtd";
		break;
		case 'qtd':
		$order_by = 'SUM(t.quantity) DESC';
		$link3 = "{$_SERVER['PHP_SELF']}?sort=qta";
		break;
		default:
		$order_by = 'SUM(t.quantity) DESC';
		break;
	}
	$sort = $_GET['sort'];
} else { 
	$order_by = 'SUM(t.quantity) DESC';
	$sort = 'qta';
}


$query = "SELECT  
		p.upc AS PLU,
		p.description AS Description,
		SUM(t.quantity) AS Qty,
		t.discounttype AS sale
		FROM is4c_log.dtransactions t, is4c_op.products p
		WHERE t.upc = p.upc
		AND date(t.datetime) >= '$date1' AND date(t.datetime) <= '$date2' 
		AND p.department IN('" .$_SESSION["deptArray"]."') 
		AND p.inUse = 1
		AND t.upc NOT LIKE '%DP%'
		AND t.emp_no <> 9999 AND t.trans_status <> 'X'
		GROUP BY t.upc
		ORDER BY $order_by";

$result = mysql_query($query);

if (!$result) {
	$message  = 'Invalid query: ' . mysql_error() . "\n";
	$message .= 'Whole query: ' . $query;
          		die($message);
}
// Table header.
echo '<table align="center" width="100%" cellspacing="0" cellpadding="5" border="1">
<tr>
<td align="center"><b><a href="' . $link1 . '&date1=' . $date1 . '&date2=' . $date2 . '">UPC/PLU</a></b></td>
<td align="center"><b><a href="' . $link2 . '&date1=' . $date1 . '&date2=' . $date2 . '">Description</a></b></td>
<td align="center"><b><a href="' . $link3 . '&date1=' . $date1 . '&date2=' . $date2 . '">Quantity</a></b></td>
<td>Backstock</td>
<td>Frontstock</td>
<td>ORDER</td>
</tr>';

// Fetch and print all the records.
$bg = '#eeeeee'; // Set background color.
while ($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
	if($row["sale"] != 1) {
		echo '<tr bgcolor="' . $bg . '">
			<td align="center">' .$row['PLU']. '</td>
			<td align="left">' . $row['Description'] . '</td>
			<td align="right">' . number_format($row['Qty'],2) . '</td>
			<td>&nbsp;</td>	<td>&nbsp;</td>	<td>&nbsp;</td>
			</tr>';
	} else {
		echo '<tr bgcolor="#BBBBBB">
			<td align="center"><b>' .$row['PLU']. '</b></td>
			<td align="left"><b>' . $row['Description'] . ' - SALE</b></td>
			<td align="right"><b>' . number_format($row['Qty'],2) . '</b></td>
			<td>&nbsp;</td>	<td>&nbsp;</td>	<td>&nbsp;</td>
			</tr>';

	}
}



?>
</body>
</html>
