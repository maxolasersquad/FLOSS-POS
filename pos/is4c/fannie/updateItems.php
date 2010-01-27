<?
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

include('src/funct1Mem.php');

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
<?
echo "<BODY onLoad='putFocus(0,0);'>";

foreach ($_POST AS $key => $value) {
    $$key = $value;
    //echo $key . ": " . $$key . "<br>";

    if($$key == 1){
       $key = 1;
    }elseif($$key == 2){
       $key = 2;
    }else{
       $key = 0;
    }

    if(!isset($key)){
	$value = 0;
    }

}

$today = date("m-d-Y h:m:s");

if(!isset($Scale)){
	$Scale = 0;
}

if(!isset($FS)){
	$FS=0;
}

if(!isset($NoDisc)){
	$NoDisc=1;
}

if(!isset($inUse)){
	$inUse = 0;
}

if(!isset($QtyFrc)){
	$QtyFrc = 0;
}

if(!isset($deposit)){
	$deposit = 0;
}

$query = "UPDATE products 
	SET description = '$descript', 
	normal_price=$price,
	tax='$tax',
	scale='$Scale',
	foodstamp='$FS',
	department = '$department',
	subdept = '$subdepartment',
	inUse = '$inUse',
    qttyEnforced = '$QtyFrc',
    discount='$NoDisc',
	modified=now(),
	deposit='$deposit'
	where upc ='$upc'";
echo $query;
$result = mysql_query($query,$db);

$query1 = "SELECT * FROM products WHERE upc = " .$upc;
$result1 = mysql_query($query1,$db);
$row = mysql_fetch_array($result1);

echo "<table border=0>";
        echo "<tr><td align=right><b>UPC</b></td><td><font color='red'>".$row[0]."</font><input type=hidden value='$row[0]' name=upc></td>";
        echo "</tr><tr><td><b>Description</b></td><td>$row[1]</td>";
        echo "<td><b>Price</b></td><td>$$row[2]</td></tr></table>";
        echo "<table border=0><tr>";
        echo "<th>Dept<th>subDept<th>FS<th>Scale<th>QtyFrc<th>NoDisc<th>inUse<th>deposit</b>";
        echo "</tr>";
        echo "<tr>";
        $dept=$row[12];
        $query2 = "SELECT * FROM departments where dept_no = " .$dept;
        $result2 = mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		
		$subdept=$row[28];
		$query2a = "SELECT * FROM subdepts WHERE subdept_no = " .$subdept;
		$result2a = mysql_query($query2a);
		$row2a = mysql_fetch_array($result2a);
		
		echo "<td>";
        echo $dept . ' ' . 
		$row2['dept_name'];
        echo " </td>";  

		echo "<td>";
		echo $subdept . ' ' .
		$row2a['subdept_name'];
		echo " </td>";

		echo "<td align=center><input type=checkbox value=1 name=FS";
                if($row[15]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=1 name=Scale";
                if($row[16]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=1 name=QtyFrc";
                if($row[26]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=0 name=NoDisc";
                if($row[21]==0){
                        echo " checked";
                }
        echo "></td><td align=center><input type=checkbox value=1 name=inUse";
                if($row[27]==1){
                        echo " checked";
                }
        echo "></td><td align=center><input type=text value='$row[25]' name=deposit size='5'";
		echo "></td></tr>";

        //echo "<tr><td>" . $row[4] . "</td><td>" . $row[5]. "</td><td>" . $row[6] ."</td><td>" . $row[7] . "</td><td>" . $row[8] . "</td></tr>";
        //echo "<tr><td>" . $row[9] . "</td><td>" . $row[10] . "</td><td>" . $row[11] . "</td><td>" . $row[12] . "</td>";
        
        echo "</table>";
        //echo "I am here.";
		echo "<hr>"; 
		echo "<form action=auto_itemMaint.php method=post>";
        echo "<input name=upc type=text id=upc> Enter UPC/PLU here<br>";
        echo "<input name=submit type=submit value=submit>";
        echo "</form>";

//
//	PHP INPUT DEBUG FUNCTION -- very helpful!
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
