<html>
<?php
if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
}else{
      foreach ($_GET AS $key => $value) {
          $$key = $value;
      }
}

if($date0 != '') {
	$date1 = $date0;
	$date2 = $date0;
	$date0a = $date0 . " 00:00:00";

	$day = date("l",$date0a);

	$title = $day."s Hourly Sales For ".$date0;
} else {
	$title = $day."s Hourly Sales For ".$date1." thru ".$date2;	
}

echo "<HEAD>";
echo "<link href='../style.css' rel='stylesheet'' type='text/css' />";
echo "<title>".$title."</title>";
echo "</HEAD>";
echo "<BODY>";

$date1a = $date1 . " 00:00:00";
$date2a = $date2 . " 23:59:59";

$db = mysql_connect("localhost","root");
mysql_select_db("is4c_log",$db);

$num1 = 0;
$num2 = 0;

$query1="SELECT date_format(datetime,'%H') AS hour,ROUND(sum(total),2) as Sales
	FROM dtransactions
	WHERE date_format(datetime,'%W') = '$day'
	AND datetime > '$date1a'
	AND datetime < '$date2a'
	AND department <= 13
	AND department <> 0
	AND trans_status <> 'X'
	AND emp_no <> 9999
	GROUP BY hour
	ORDER BY hour";

$query2="SELECT ROUND(sum(total),2) as TotalSales
	FROM dtransactions
	WHERE datetime > '$date1a'
	AND datetime < '$date2a'
	AND date_format(datetime,'%W') = '$day'
	AND trans_status <> 'X'
	AND department <= 13
	AND department <> 0
	AND emp_no <> 9999";

$transCountQ = "SELECT date_format(datetime,'%H') AS hour,COUNT(total) as transactionCount
	FROM dtransactions
	WHERE date_format(datetime,'%W') = '$day'
	AND datetime > '$date1a'
	AND datetime < '$date2a'
	AND trans_status <> 'X'
	AND emp_no <> 9999
	AND upc = 'DISCOUNT'
	GROUP BY hour
	ORDER BY hour";

$result1 = mysql_query($query1);
$result2 = mysql_query($query2);
$result3 = mysql_query($transCountQ);
$num1 = mysql_num_rows($result1);
$num2 = mysql_num_rows($result3);
$row2 = mysql_fetch_row($result2);

echo "<center><h2>";
echo $title;
echo "</h2>";
echo "<table>";
echo "<tr align='center'><td><b>Hour</b></td><td><b>Sales</b></td><td>&nbsp</td><td><b>Pct.</b></td><td><b>Count</b></tr>";
while(($row1 = mysql_fetch_row($result1)) && ($row3 = mysql_fetch_row($result3))){	
	$sales = $row1[1];
	$gross = $row2[0];
	$count = $row3[1];
    $portSales = $sales/$gross;
    $twoperSales = $portSales * 200;
	$percentage = money_format('%i',100 * $portSales);  
	echo "<tr><td align='center'>".$row1[0]."</td><td align='right'>".$row1[1]."</td>";
    echo "<td><img src=../image.php?size=$twoperSales></td>";
    echo "<td align='right'>".$percentage." %</td>";
	echo "<td align='right'>".$count."</td></tr>";
}
echo "<tr><td>&nbsp</td></tr><tr><td><b>Gross Total:</b></td>";
echo "<td><p><b>".$gross."</b></p></td></tr>";
echo "</table></center>";

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
echo "</BODY>";
?>
</html>