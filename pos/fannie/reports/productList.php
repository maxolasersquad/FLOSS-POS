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

include('src/functions.php');
mysql_select_db('is4c_op',$db);

if(isset($_GET['sort'])){
/*
  if(isset($_GET['XL'])){
     header("Content-Disposition: inline; filename=deptSales.xls");
     header("Content-Description: PHP3 Generated Data");
     header("Content-type: application/vnd.ms-excel; name='excel'");
  }
*/
	echo "<html><head><title>Department Movement Report</title></head>";

	foreach ($_GET AS $key => $value) {
		$$key = $value;
		//echo $key ." : " .  $value."<br>";
	}
	
	if(!isset($order)){
		$order="upc";
	}
}else{
	echo "<html><head><title>Department Movement Report</title></head>";
	
	foreach ($_POST AS $key => $value) {
		$$key = $value;
		//echo $key ." : " .  "<br>";
	}
	if(!isset($order)){
		$order="upc";
	}
}

echo "<body>";

$today = date("F d, Y");	

if(isset($allDepts)) {
	$deptArray = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,40";
	$arrayName = "ALL DEPARTMENTS";
}else{
	$deptArray = implode(",",$_POST['dept']);
	$arrayName = $deptArray;
}

echo "Report sorted by ";
echo $sort . " on ";
echo "</br>";
echo $today;
echo "</br>";
echo "Department range: ";
echo $arrayName;

include($_SERVER["DOCUMENT_ROOT"].'/PME_products.php');

//
// PHP INPUT DEBUG SCRIPT  -- very helpful!
//
/*
function debug_p($var, $title) 
{
    print "<p>$title</p><pre>";
    print_r($var);
    print "</pre>";
}  

debug_p($_REQUEST, "all the data coming in");

*/
?>

</body>
</html>
