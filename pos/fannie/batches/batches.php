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
window.open(href, windowname, 'width=510,height=455,scrollbars=yes,menubar=no,location=no,toolbar=no,dependent=yes');
return false;
}
//-->
</SCRIPT>
</head>
<body>
<?
// include($_SERVER["DOCUMENT_ROOT"].'/src/funct1Mem.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');

if(isset($_GET['batchID'])){
   $batchID = $_GET['batchID'];
}

//echo $batchID;
if (isset($_POST['datechange'])){
  $batchID = $_POST['batchID'];
  $startdate = $_POST['startdate'];
  $enddate = $_POST['enddate'];
  
  $dateQ = "update batches set startdate='$startdate',
            enddate='$enddate' where batchID=$batchID";
  $dateR = mysql_query($dateQ);
}
else if(isset($_POST['submit'])){
   foreach ($_POST AS $key => $value) {
     $batchID = $_POST['batchID'];
     
     //echo "values".$key . ": ".$value . "<br>";
     if(substr($key,0,4) == 'sale'){
        $$key = $value;
        $upc1 = substr($key,4);
	$queryTest = "UPDATE batchList SET salePrice = $value WHERE upc = '$upc1' and batchID = $batchID";
        //echo $queryTest . "<br>";
	$resultTest = mysql_query($queryTest);
      }

     if(substr($key,0,3) == 'del'){
       $$key = $value;
       $upc1 = substr($key,3);
       $delItmQ = "DELETE FROM batchList WHERE upc = '$upc1' and batchID = $batchID";
       $delItmR = mysql_query($delItmQ);
     }
   }   
}

$batchInfoQ = "SELECT * FROM batches WHERE batchID = $batchID";
$batchInfoR = mysql_query($batchInfoQ);
$batchInfoW = mysql_fetch_row($batchInfoR);


$selBItemsQ = "SELECT b.*,p.*  from batchList as b LEFT JOIN 
               products as p ON b.upc = p.upc WHERE batchID = $batchID 
               ORDER BY b.listID DESC";
//echo $selBItemsQ;
$selBItemsR = mysql_query($selBItemsQ);

echo "<form action=batches.php method=POST>";
echo "<table border=0 cellspacing=0 cellpadding=5>";
echo "<tr><td>Batch Name: <font color=blue>$batchInfoW[3]</font></td>";
echo "<form action=batches.php method=post>";
echo "<td>Start Date: <input type=text name=startdate value=\"$batchInfoW[1]\" size=18></td>";
echo "<td>End Date: <input type=text name=enddate value=\"$batchInfoW[2]\" size=10></td>";
echo "<td><input type=submit value=\"Change Dates\" name=datechange></td></tr>";
echo "<input type=hidden name=batchID value=$batchID>";
echo "</form>";
echo "<th>UPC<th>Description<th>Normal Price<th>Sale Price<th>Delete";
echo "<form action=batches.php method=POST>";
$bg = '#eeeeee';
while($selBItemsW = mysql_fetch_row($selBItemsR)){
	$upc = $selBItemsW[1];
	$field = 'sale'.$upc;
	$del = 'del'.$upc;
	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
	echo "<tr bgcolor='$bg'>";
	if(!$selBItemsW[6]) {
		$_SESSION["popup"] = 1;
		echo "<td align=center><a href='../item/itemMaint.php?upc=$selBItemsW[1]' onClick='return popup(this, \"addItem\")'>$selBItemsW[1]</a></td>";
		echo "<td><font color=red>NO PRODUCT RECORD FOR THIS ITEM</font></td>";  
	} else {
		echo "<td align=center>$selBItemsW[1]</td><td>$selBItemsW[6]</td>";
	}
	echo "<td align=right>$selBItemsW[7]</td><td align=center><input type=text name=$field field value=$selBItemsW[3] size=8></td>";
	echo "<input type=hidden name=upc value='$upc'>";
	echo "<td><input type=checkbox value=1 name=$del></td></tr>";
}
echo "<input type=hidden value=$batchID name=batchID>";
echo "<tr><td><input type=submit name=submit value=submit></td>";
echo "<td><a href=forceBatch.php?batchID=$batchID target=blank>Force Batch Now</a></td>";
echo "<td><a href=resetBatch.php?batchID=$batchID target=blank>Reset Batch Now</a></td>";
//echo "<td><a href='auto_index.php target=blank>Return to batch list</a></td>";
echo "</tr></form>";



?>
</body>
</html>