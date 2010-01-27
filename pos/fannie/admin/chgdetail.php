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

$query = "SELECT date(d.datetime) as date, SUM(d.total) as charges
	FROM custdata c, is4c_log.dtransactions d
	WHERE d.card_no = c.CardNo
	AND datetime >= '".$ps."'
	AND datetime <= '".$pe."'
	AND d.card_no = ".$cn."
	AND c.staff IN(1,2) 
	AND d.trans_subtype = 'MI'
	AND d.emp_no <> 9999 AND d.trans_status <> 'X'
	AND c.CardNo <> 9999
	GROUP BY date(d.datetime)
	ORDER BY date(d.datetime)";

$query2 = mysql_query("SELECT CardNo, LastName, FirstName FROM custdata WHERE CardNo = $cn AND staff IN(1,2)");
$row = mysql_fetch_assoc($query2);

echo "<h1>Staff: " . $row["LastName"] . ", " . $row["FirstName"] . "</h1>";
select_to_table($query,1,'eeeeee');

?>