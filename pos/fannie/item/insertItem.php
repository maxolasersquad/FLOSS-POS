<?php
/*******************************************************************************

    Copyright 2005 Whole Foods Community Co-op

    This file is part of WFC's PI Killer.

    PI Killer is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    PI Killer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/

include('prodFunction.php');
// include('src/funct1Mem.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');
?>
<html>
<head>
<SCRIPT LANGUAGE="JavaScript">

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->
<!-- John Munn  (jrmunn@home.com) -->

<!-- Begin
 function putFocus(formInst, elementInst) {
  if (document.forms.length > 0) {
   document.forms[formInst].elements[elementInst].focus();
  }
 }
// The second number in the "onLoad" command in the body
// tag determines the form's focus. Counting starts with '0'
//  End -->
</script>

</head>
<BODY onLoad='putFocus(0,0);'>

<?


foreach ($_POST AS $key => $value) {
    $$key = $value;
}


$today = date("m-d-Y h:m:s");
echo $today;

if(!isset($tax)){
	$tax = 0;
}	
if(!isset($FS)){
	$FS = 0;
}
if(!isset($Scale)){
	$Scale = 0;
}
if(!isset($deposit) || is_null($deposit)){
	$deposit = 0;
}
if(!isset($QtyFrc)){
	$QtyFrc = 0;
}

$del99Q = "DELETE FROM products where upc = '$upc'";
$delISR = mysql_query($del99Q);

//echo $upc;
//echo $descript;
//echo $price;

if (!$price) $price = 0;

$query99 = "INSERT INTO products
	VALUES($upc,'$descript',$price,0,0.00,0,0.00,0,0.00,0,'','',$department,'',$tax,$FS,$Scale,0,now(),0,0,1,0,'',0,$QtyFrc,1,$subdepartment,$deposit,'')";
// echo "<br>" .$query99. "<br>";
$resultI = mysql_query($query99);

$prodQ = "SELECT * FROM products WHERE upc = ".$upc;
//echo $prodQ;
$prodR = mysql_query($prodQ);
$row = mysql_fetch_array($prodR);

		echo "<table border=0>";
        echo "<tr><td align=right><b>UPC</b></td><td><font color='red'>".$upc."</font><input type=hidden value='".$upc."' name=upc></td>";
        echo "</tr><tr><td><b>Description</b></td><td>".$descript."</td>";
        echo "<td><b>Price</b></td><td>".$price."</td></tr></table>";
        echo "<table border=0><tr>";
        echo "<th>Dept<th>subDept<th>FS<th>Scale<th>QtyFrc<th>NoDisc<th>inUse<th>deposit</b>";
        echo "</tr>";
        echo "<tr>";
       
		$dept = $row[12];
        $query2 = "SELECT * FROM departments where dept_no = ".$row[12];
        $result2 = mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		
		$subdept = $row[28];
		$query2a = "SELECT * FROM subdepts WHERE subdept_no = ".$row[28];
		$result2a = mysql_query($query2a);
		$row2a = mysql_fetch_array($result2a);
		
		echo "<td>";
        echo $dept . ' ' . $row2[1];
        echo " </td>";  

		echo "<td>";
		echo $subdept . ' ' . $row2a[1];
		echo " </td>";
		
        echo "<td align=center><input type=checkbox value=1 name=FS";
                if($row["foodstamp"]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=1 name=Scale";
                if($row["scale"]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=1 name=QtyFrc";
                if($row["qttyEnforced"]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=0 name=NoDisc";
                if($row["discount"]==0){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=1 name=inUse";
                if($row["inUse"]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=text value='$row["deposit"]' name='deposit' size='5'";
		echo "></td></tr>";
        
        echo "</table>";
        echo "<hr>";
//  tak      echo "<form action=auto_index.php method=post>"; // tak
//  tak      echo "<input name=upc type=text id=upc> Enter UPC/PLU here<br>";
//  tak      echo "<input name=submit type=submit value=submit>";
//  tak      echo "</form>";

//
//	PHP INPUT DEBUG SCRIPT -- very useful!
//

/*
function debug_p($var, $title) 
{
    print "<h4>$title</h4><pre>";
    print_r($var);
    print "</pre>";
}

debug_p($_REQUEST, "all the data coming in");
*/
?>


