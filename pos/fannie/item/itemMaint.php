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
include_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');
// include($_SERVER["DOCUMENT_ROOT"].'/src/funct1Mem.php');
include('prodFunction.php');

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

<?php

if(isset($_POST['submit'])){
    $upc = $_POST['upc'];
 
    itemParse($upc);

}elseif(isset($_GET['upc'])){
    $upc = $_GET['upc'];
    itemParse($upc);

}else{

echo "<head><title>Edit Item</title></head>";
echo "<BODY onLoad='putFocus(0,0);'>";
echo "<form action=/item/auto_itemMaint.php method=post>";
echo "<input name=upc type=text id=upc> Enter UPC/PLU or product name here<br><br>";

echo "<input name=submit type=submit value=submit>";
echo "</form>";
}

echo "</body>";
echo "</html>";
?>

