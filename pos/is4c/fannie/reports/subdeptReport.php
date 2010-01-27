<?php
setlocale(LC_MONETARY, 'en_US');
include('../src/functions.php');
$db = mysql_connect('localhost','root');
mysql_select_db('is4c_log',$db);

//if(isset($_GET['XL'])){
//	header("Content-Disposition: inline; filename=subdeptReportXL.xls");
//	header("Content-Description: PHP3 Generated Data");
//	header("Content-type: application/vnd.ms-excel; name='excel'");
//}

?>
<html>
<head>
<title>Subdepartment Sales Report</title>
</head>
<?
?>

<html>
<head>
<Title>Subdepartment Sales Report</Title>
</head>
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

if($sort=='dept_name'){
	$order = "s.dept_name";		
}elseif($sort=='subdept_name'){ 
	$order = "s.subdept_name";
}elseif($sort=='item_count'){
	$order = "qty DESC";
}elseif($sort=='total') {
	$order = "total DESC";
}

if($_POST['allDepts'] == 1) {
	$_SESSION['deptArray'] = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,40";
} elseif(is_array($_POST['dept'])) {
	$_SESSION['deptArray'] = implode(",",$_POST['dept']);
}

$today = date("l F d, Y");	

	echo "Report run on ";
	echo $today;
	echo "</br>";
	echo "For ";
	print $date1;
	echo " through ";
	print $date2;
	echo "<br> Sort by:";
	print $sort;
	echo "  on department(s):";
	print $_SESSION['deptArray'];
	echo "</br></br>";
	
// Convert GETs to SQL-happy syntax
//$date2a = $date2 . " 23:59:59";
//$date1a = $date1 . " 00:00:00";

//if(!isset($_GET['XL'])){
//	echo "<p><a href='subdeptReportXL.php?XL=1&date1=$date1a&date2=$date2a&allDepts=$deptArray&sort=$order'>Dump to Excel Document</a></p>";
//}

//echo "<p><a href='subdeptReportXL.php?date1=$date1&date2=$date2&date1a=$date1a&date2a=$date2a&deptArray=$deptArray&order=$order'>
//	Dump to Excel Spreadsheet</a></p>";

if(isset($totals)) {
	$grossAllQ = "SELECT ROUND(sum(total),2) as GROSS_sales
		FROM dtransactions 
		WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2' 
		AND department <=20
		AND department <> 0
		AND trans_status <> 'X'
		AND emp_no <> 9999";

		$grossAllR = mysql_query($grossAllQ);
		$row = mysql_fetch_row($grossAllR);
		$grossAll = $row[0];
		
	
	$grossQ = "SELECT ROUND(sum(total),2) as GROSS_sales
		FROM dtransactions 
		WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2' 
		AND department IN(" . $_SESSION['deptArray'] . ")
		AND trans_status <> 'X'
		AND emp_no <> 9999";

		$grossR = mysql_query($grossQ);
		$row = mysql_fetch_row($grossR);
		$gross = $row[0];

	echo "<h4>Sales Totals</h4>";
	echo "<p>Overall Gross = <b>$";
	echo money_format('%n',$grossAll) . "\n";
	echo "</b></p><p>Gross Sales = <b>$";
	echo money_format('%n',$gross) . "\n";
	echo "</b><font size=-1>Gross total of selected departments</font></p><br>";
}

echo "<h4>Subdepartment Totals</h4>";

$subdeptQ = "SELECT s.dept_name AS dept,
					s.subdept_name AS subdept,
					ROUND(SUM(t.quantity),2) as qty,
					ROUND(SUM(t.total),2) as total,
					ROUND((SUM(t.total)/$gross)*100,2) AS pctDept,
					ROUND((SUM(t.total)/$grossAll)*100,2) AS pctAll
				FROM dtransactions t, is4c_op.subdeptIndex s
				WHERE t.upc = s.upc
				AND date(t.datetime) >= '$date1' AND date(t.datetime) <= '$date2'
				AND t.department IN(" . $_SESSION['deptArray'] . ")
				GROUP BY s.subdept_name
				ORDER BY $order";


/*
$export = mysql_query($subdeptQ);
$fields = mysql_num_fields($export);
for ($i = 0; $i < $fields; $i++) {
    $header .= mysql_field_name($export, $i) . "\t";
}
while($row = mysql_fetch_row($export)) {
    $line = '';
    foreach($row as $value) {                                            
        if ((!isset($value)) OR ($value == "")) {
            $value = "\t";
        } else {
            $value = str_replace('"', '""', $value);
            $value = '"' . $value . '"' . "\t";
        }
        $line .= $value;
    }
    $data .= trim($line)."\n";
}
$data = str_replace("\r","",$data);
if ($data == "") {
    $data = "\n(0) Records Found!\n";                        
}
*/


$result = mysql_query($subdeptQ,$db);
//	echo $subdeptQ;
	echo "<table border=1 cellpadding=3 cellspacing=3>\n"; //create table
	echo "<tr><td>";
	echo "<a href='subdeptReport.php?totals=$totals&date1=$date1&date2=$date2&sort=dept_name'>";
	echo "Department</a></td><td>";
	echo "<a href='subdeptReport.php?totals=$totals&date1=$date1&date2=$date2&sort=subdept_name'>";
	echo "Subdept Name</a></td><td>";
	echo "<a href='subdeptReport.php?totals=$totals&date1=$date1&date2=$date2&sort=item_count'>";
	echo "Qty</a></td><td>";
	echo "<a href='subdeptReport.php?totals=$totals&date1=$date1&date2=$date2&sort=total'>";
	echo "Total</a></td><td>";
	echo "% of dept</td><td>% of store</td>";
	echo "</tr>\n";//create table header

		while ($myrow = mysql_fetch_row($result)) { //create array from query
			printf("<tr><td>%s</td><td>%s</td><td align=right>%s</td><td align=right>%s</td><td align=right>%s</td><td align=right>%s</td></tr>\n",$myrow[0], $myrow[1],$myrow[2],$myrow[3],$myrow[4],$myrow[5]);
    		//convert row information to strings, enter in table cells
		}

	echo "</table>\n";//end table


//select_to_table($subdeptQ,1,'FFFFFF');


//
// PHP INPUT DEBUG SCRIPT  -- very helpful!
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

</body>
</html>
