<?
//
//
// Copyright (C) 2007  
// authors: Christof Van Rabenau - Whole Foods Cooperative, 
// Joel Brock - People's Food Cooperative
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
//

include('../src/functions.php');
include('../src/datediff.php');

?>

<HTML>
<BODY BGCOLOR = 'FFCC99' > <font SIZE=2>
<?
//header("Content-Disposition: inline; filename=reportDateXL.xls");
//header("Content-Description: PHP3 Generated Data");
//header("Content-type: application/vnd.ms-excel; name='excel'");

echo "<form action=reportDate.php name=datelist method=post>";
echo "<input name=date type=text id=date >";

echo "<input name=Submit type=submit value=submit>";
echo "</form>";

if(isset($_POST['date'])){
	$date = $_POST['date'];
	echo "Date entered: ".$date;
}
if(($_POST['date'] == '1969-12-31') || (!isset($_POST['date']))){
	$date = date('Y-m-d');
	echo "Date entered: ".$date;
}

echo "<br>";

if(strpbrk($date, "-") == false){
	$dateArray = explode("/",$date);
	$db_date = date('Y-m-d', mktime(0, 0, 0, $dateArray[0], $dateArray[1], $dateArray[2])); 
} 
elseif (strpbrk($date, "/") == false){
	$dateArray = explode("-",$date);
	$db_date = date('Y-m-d', mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]));
}

//echo "<a href=reportDateXL.php?datediff=$datediff&date=$date>Click here for Excel version</a>";
echo "<br>Report run " .date('Y-m-d'). " for ";

$db = mysql_connect('localhost','root','');
mysql_select_db('is4c_log',$db);

//////////////////////////////////
//
//
//  Let's crunch some numbers... 
//
//
//////////////////////////////////



/** 
 * total sales 
 * Gross = total of all inventory depts. 1-15 (at ACG)
 * Hash = Equity pmt.(45) + General Donations + Tri-met(40) + Gift Cert. sales(44) + Bottle Deposits & Returns(41,42,43)
 * Net = Gross + Hash - All discounts - Coupons(IC & MC) - Gift Cert. Tender - Store Charge
 */

$grossQ = "SELECT ROUND(sum(total),2) as GROSS_sales
	FROM dtransactions 
	WHERE date(datetime) = '".$db_date."' 
	AND department <= 35
	AND department <> 0
	AND trans_status <> 'X'
	AND emp_no <> 9999";

	$results = mysql_query($grossQ);
	$row = mysql_fetch_row($results);
	$gross = $row[0];

/**
 * sales of inventory departments
 */

$inventoryDeptQ = "SELECT d.department,t.dept_name,ROUND(sum(d.total),2) AS total,ROUND((SUM(d.total)/$gross)*100,2) as pct
   	FROM dtransactions AS d, is4c_op.departments AS t
	WHERE d.department = t.dept_no
	AND date(d.datetime) = '".$db_date."'
	AND d.department <= 35 
	AND d.department <> 0
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	GROUP BY d.department, t.dept_name";

/** 
 * Sales for non-inventory departments 
 */

$noninventoryDeptQ = "SELECT d.department,t.dept_name,ROUND(sum(total),2) as total 
	FROM dtransactions as d,is4c_op.departments as t 
	WHERE d.department = t.dept_no
	AND date(d.datetime) = '".$db_date."'
	AND d.department > 35 
	AND d.department <> 0
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	GROUP BY d.department, t.dept_name";

/* 
 * pull tender report.
 */

$tendersQ = "SELECT t.TenderName as tender_type,ROUND(-sum(d.total),2) as total,COUNT(*) as count
	FROM dtransactions as d,is4c_op.tenders as t 
	WHERE d.trans_subtype = t.TenderCode
	AND date(d.datetime) = '".$db_date."'
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999
	GROUP BY t.TenderName";

$transCountQ = "SELECT COUNT(d.total) as transactionCount
	FROM dtransactions AS d
	WHERE date(d.datetime) = '".$db_date."'
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	AND d.upc = 'DISCOUNT'";

	$transCountR = mysql_query($transCountQ);
	$row = mysql_fetch_row($transCountR);
	$count = $row[0];

$basketSizeQ = "SELECT ROUND(($gross/$count),2) AS basket_size";

/**
 * Sales of equity
 * ACG Equity dept. = 45
 */

$sharePaymentsQ = "SELECT d.card_no,t.dept_name,ROUND(sum(total),2) as total 
	FROM dtransactions as d JOIN is4c_op.departments as t ON d.department = t.dept_no
	WHERE date(datetime) = '".$db_date."'
	AND d.department = 45
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	GROUP BY d.card_no, t.dept_name";

/*
$shareCountQ = "SELECT COUNT(total) AS peopleshare_count
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND description = 'MEMBERSHIP EQUITY'
	AND trans_status <> 'X'
	AND emp_no <> 9999";

	$shareCountR = mysql_query($shareCountQ);
	$row = mysql_fetch_row($shareCountR);
	$shareCount = $row[0];
*/
/**
 * Discounts by member type;
 */

$percentsQ = "SELECT c.discount AS volunteer_discount,(ROUND(SUM(d.unitPrice),2)) AS totals 
	FROM dtransactions AS d LEFT JOIN is4c_op.custdata AS c 
	ON d.card_no = c.CardNo 
	WHERE date(d.datetime) = '".$db_date."'
	AND c.staff NOT IN(1,2,5)
	AND d.voided = '5'
	AND d.trans_subtype <> 'IC'
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999 
	GROUP BY c.discount
	WITH ROLLUP";

$memtypeQ = "SELECT m.memDesc as memType,ROUND(SUM(d.total),2) AS Sales 
	FROM dtransactions d INNER JOIN
  		is4c_op.custdata c ON d.card_no = c.CardNo INNER JOIN
  		is4c_op.memtype m ON c.memType = m.memtype
	WHERE date(d.datetime) = '".$db_date."'
  	AND d.trans_type IN('I','D')
  	AND d.trans_status <>'X'
  	AND d.department <= 35 AND d.department <> 0
  	AND d.upc <> 'DISCOUNT'
  	AND d.emp_no <> 9999
	GROUP BY c.memtype";

/*
$MADcouponQ = "SELECT (ROUND(SUM(unitPrice),2)) AS MAD_Coupon_total
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."' 
	AND trans_subtype = 'IC'
	AND voided = 9
	AND trans_status <> 'X'
	AND emp_no <> 9999";
*/

/**
 * Customer Services - Tri-met, stamps, sisters of the road coupons
 */

$trimetQ = "SELECT SUM(quantity) AS trimet_count, ROUND(SUM(total),2) AS trimet_sales
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND department = 40 
	AND trans_status <> 'X'
	AND emp_no <> 9999";

$stampsQ = "SELECT SUM(quantity) AS stamp_count, ROUND(SUM(total),2) AS stamp_sales
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."' 
	AND trans_status <> 'X'
	AND emp_no <> 9999
	AND description LIKE 'Stamp%'";

$sistersQ = "SELECT SUM(quantity) AS sisters_count, ROUND(SUM(total),2) AS sisters_sales
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND trans_status <> 'X'
	AND emp_no <> 9999
	AND upc = 3200";

/**
 * Miscellaneus - store charges, R/As, returns
 */

$storeChargeQ = "SELECT COUNT(total) AS storechg_count, ROUND(-SUM(d.total),2) AS storechg_total
	FROM dtransactions AS d
	WHERE d.trans_subtype = 'MI'
	AND card_no = 9999
	AND d.trans_status <> 'X'
	AND date(d.datetime) = '".$db_date."'
	AND d.emp_no <> 9999";

$raQ = "SELECT COUNT(total) AS RA_count, ROUND(SUM(total),2) as RA_total
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND department = 45
	AND trans_status <> 'X'
	AND emp_no <> 9999";

$returnsQ = "SELECT SUM(quantity) AS returns_count, ROUND(sum(l.total),2) as returns_total
	FROM dtransactions as l 
	WHERE date(datetime) = '".$db_date."'
	AND l.department < 20 AND l.department <> 0
	AND l.trans_status = 'R'
	AND l.emp_no <> 9999";


////////////////////////////
//
//
//  NOW....SPIT IT ALL OUT....
//
//
////////////////////////////


echo $db_date . '<br>';
echo '<font size = 2>';
echo "<p>";
echo '------------------------------<br>';
echo '<h4>Sales - Gross, Hash, & NET</h4>';
include('net.php');
echo '------------------------------<br>';
echo '<h4>Sales by Inventory Dept.</h4>';
select_to_table($inventoryDeptQ,0,'FFCC99');
echo '<h4>Sales by Non-Inventory Dept.</h4>';
select_to_table($noninventoryDeptQ,0,'FFCC99');
echo '------------------------------<br>';
echo '<h4>Tender Report</h4>';
select_to_table($tendersQ,0,'FFCC99');									// sales by tender type
select_to_table($transCountQ,0,'FFCC99');								// transaction count
select_to_table($basketSizeQ,0,'FFCC99');								// basket size
echo '------------------------------<br>';
echo '<h4>Membership & Discount Totals</h4><br>';
echo "<table border=0><font size=2>";
echo "<tr><td>member total</td><td align=right>".money_format('%n',$mem_total)."</td></tr>";
echo "<tr><td>staff total</td><td align=right>".money_format('%n',$staff_total)."</td></tr>";
echo "<tr><td>working mem total</td><td align=right>".money_format('%n',$wm_total)."</td></tr>";
echo "<tr><td>sister_orgs total</td><td align=right>".money_format('%n',$sister_org)."</td></tr>";
//echo "<tr><td>MAD coupon</td><td align=right>".money_format('%n',$MADcoupon)."</td></tr>";
echo "<tr><td>&nbsp;</td><td align=right>+___________</td></tr>";
echo "<tr><td><b>total discount</td><td align=right>".money_format('%n',$totalDisc)."</b></td></tr></font></table>";
select_to_table($percentsQ,0,'FFCC99');									// discounts awarded by percent
//select_to_table($memtypeQ,0,'FFCC99');	
select_to_table($sharePaymentsQ,0,'FFCC99');							// peopleshare payments
//echo '<b>Share count = '.$shareCount.'</b>';							// peopleshare count
echo '<br>------------------------------<br>';
echo '<h4>Miscellaneous</h4>';
select_to_table($returnsQ,0,'FFCC99');									// total returns
//select_to_table($raQ,0,'FFCC99');										// R/A total
select_to_table($storeChargeQ,0,'FFCC99');								// store charges
select_to_table($trimetQ,0,'FFCC99');									// Tri-Met sales
//select_to_table($stampsQ,0,'FFCC99');									// Stamps sales
select_to_table($sistersQ,0,'FFCC99');									// Sisters sales
echo '</font>';


 
?>
</font>
</body>
</html>
