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
?>


<html>
<head>
<Title>Volunteer timesheets</Title>
<link rel="stylesheet" href="../src/style.css" type="text/css" />
<SCRIPT TYPE="text/javascript">
<!--
function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, 'width=400,height=300,scrollbars=yes,menubar=no,location=no,toolbar=no,dependent=yes');
return false;
}
//-->
</SCRIPT>

</head>
<body>
<?php
setlocale(LC_MONETARY, 'en_US');
//include($_SERVER["DOCUMENT_ROOT"].'/src/functions.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');

if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
}else{
      foreach ($_GET AS $key => $value) {
          $$key = $value;
      }
}

if(isset($_POST["Submit"])) {
	$query = mysql_query("SELECT * FROM is4c_log.payperiods WHERE periodID = ". $_POST["period"]);
	$row = mysql_fetch_array($query);
	$pay_start = $row["periodStart"];
	$pay_end = $row["periodEnd"];
} else {
	$query = mysql_query("SELECT * FROM is4c_log.payperiods WHERE curdate() >= periodEnd LIMIT 1");
	$row = mysql_fetch_array($query);
	$pay_start = $row["periodStart"];
	$pay_end = $row["periodEnd"];
}
//echo $pay_start;
//echo "<br>";
//echo $pay_end;

$link1 = "{$_SERVER['PHP_SELF']}?sort=lna";
$link2 = "{$_SERVER['PHP_SELF']}?sort=fna";
$link3 = "{$_SERVER['PHP_SELF']}?sort=cna";
$link4 = "{$_SERVER['PHP_SELF']}?sort=cga";


// Determine the sorting order.
if (isset($_GET['sort'])) { // If a non-default sort has been chosen.
	// Use existing sorting order.
	switch ($_GET['sort']) {	
		case 'lna':
		$order_by = 'c.LastName ASC';
		$link1 = "{$_SERVER['PHP_SELF']}?sort=lnd";
		break;
		case 'lnd':
		$order_by = 'c.LastName DESC';
		$link1 = "{$_SERVER['PHP_SELF']}?sort=lna";
		break;
		case 'fna':
		$order_by = 'c.FirstName ASC';
		$link2 = "{$_SERVER['PHP_SELF']}?sort=fnd";
		break;
		case 'fnd':
		$order_by = 'c.FirstName DESC';
		$link2 = "{$_SERVER['PHP_SELF']}?sort=fna";
		break;
		case 'cna':
		$order_by = 'c.CardNo ASC';
		$link3 = "{$_SERVER['PHP_SELF']}?sort=drd";
		break;
		case 'cnd':
		$order_by = 'c.CardNo DESC';
		$link3 = "{$_SERVER['PHP_SELF']}?sort=dra";
		break;
		case 'cga':
		$order_by = 'SUM(d.total) DESC';
		$link4 = "{$_SERVER['PHP_SELF']}?sort=cgd";
		break;
		case 'cgd':
		$order_by = 'SUM(d.total) ASC';
		$link4 = "{$_SERVER['PHP_SELF']}?sort=cga";
		break;
		default:
		$order_by = 'c.LastName DESC';
		break;
	}
	// $sort will be appended to the pagination links.
	$sort = $_GET['sort'];
} else { // Use the default sorting order.
	$order_by = 'c.LastName ASC';
	$sort = 'lnd';
}

$query = "SELECT c.CardNo, c.LastName, c.FirstName, s.staff_desc, SUM(d.total) as charges
	FROM custdata c, staff s, is4c_log.dtransactions d
	WHERE d.card_no = c.CardNo
	AND datetime >= '".$pay_start."'
	AND datetime <= '".$pay_end."'
	AND c.staff = s.staff_no 
	AND c.staff IN(1,2) 
	AND d.trans_subtype = 'MI'
	AND d.emp_no <> 9999 AND d.trans_status <> 'X'
	AND c.CardNo <> 9999
	GROUP BY c.LastName
	ORDER BY $order_by";

$queryR = mysql_query($query);
echo "<center><h2>Previous Pay Period</h2></center> \n";
echo "<center><h3>".strftime('%D', strtotime($pay_start))." through ".strftime('%D', strtotime($pay_end))."</h3></center> \n";
echo "<table border=0 width=95% cellspacing=0 cellpadding=5 align=center> \n";
//echo "<th>Card No<th>Last Name<th>First Name<th>Type<th>Charges \n";
// Table header.
echo '<tr>
	<td align="center"><b><a href="' . $link3 . '">Card No</a></b></td>
	<td align="center"><b><a href="' . $link1 . '">Last Name</a></b></td>
	<td align="center"><b><a href="' . $link2 . '">First Name</a></b></td>
	<td align="center"><b>Type</b></td>
	<td align="center"><b><a href="' . $link4 . '">Charges</a></b></td>
	<td>&nbsp;</td>
	</tr>';
$bg = '#eeeeee';
while($query = mysql_fetch_row($queryR)){
	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
	echo "<tr bgcolor='$bg'><td>".$query[0]."</td> \n";
	echo "<td>".$query[1]."</td> \n";
	echo "<td>".$query[2]."</td> \n";
	echo "<td>".$query[3]."</td> \n";
	echo "<td align='right'>$".money_format($query[4],2)."</td> \n";
	echo '<td align=right><a href="chgdetail.php?cn='.$query[0].'&ps='.$pay_start.'&pe='.$pay_end.'" onClick="return popup(this, \'chgdetail\')">detail</a></tr>';
}
echo "</table><br><br>";

echo "<table width=100% border=0><tr><td colspan='3' height='1' bgcolor='cccccc'></td></tr></table>";

$query = "SELECT * FROM is4c_log.payperiods WHERE periodEnd <= curdate() LIMIT 27"; 
$results = mysql_query($query) or
	die("<li>errorno=".mysql_errno()
		."<li>error=" .mysql_error()
		."<li>query=".$query);

//echo "<div id='box'>";
echo "<br><br><center><table border=0 cellpadding=0 cellspacing=0><tr><td align=center> \n";
echo "<h3>Select another pay period</h3> \n";
echo "</td></tr><tr><td align=center> \n";
echo "<form method=POST action=auto_charges.php>";
echo "<select name=period id=period> \n";
while ($row = mysql_fetch_assoc($results)) {  
	echo "<option value=" .$row["periodID"] . ">";
	echo strftime('%D', strtotime($row["periodStart"])). " --> " .strftime('%D', strtotime($row["periodEnd"]));
  	echo "</option> \n";
}
echo "</td></tr><tr><td align=center><input type=submit name=Submit value=Submit></form></td></tr></table></center>";
//echo "</div> \n";

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