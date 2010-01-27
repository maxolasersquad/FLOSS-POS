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
/*
if(isset($_GET['sort'])){
  if(isset($_GET['XL'])){
     header("Content-Disposition: inline; filename=deptSales.xls");
     header("Content-Description: PHP3 Generated Data");
     header("Content-type: application/vnd.ms-excel; name='excel'");
  }
}*/
?>
<html>
<head>
<title>Fannie - Period Report</title>
</head>
<?
?>

<html>
<head>
<Title>Fannie - Period Report</Title>
<link rel="stylesheet" href="../src/style.css" type="text/css" />
</head>
<?
include($_SERVER["DOCUMENT_ROOT"].'/src/functions.php');
include($_SERVER["DOCUMENT_ROOT"].'/src/datediff.php');

if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$$key = $value;
		//echo $key ." : " .  $value"<br>";
	}

	//$order = "ROUND(SUM(t.total),2) DESC";
}else{
      foreach ($_GET AS $key => $value) {
          $$key = $value;
	      //echo $key ." : " .  $value."<br>";
      }
}

echo "<body>";

setlocale(LC_MONETARY, 'en_US');
	$today = date("F d, Y");	

// Page header

	echo "Report run on ";
	echo $today;
	echo "</br>";
	echo "For ";
	print $date1;
	echo " through ";
	print $date2;
	echo "</br></br></br>";

/*
	if(!isset($_GET['XL'])){
	echo "<p><a href='deptSales.php?XL=1&sort=$sort&date1=$date1&date2=$date2&deptStart=$deptStart&deptEnd=$deptEnd&pluReport=$pluReport&order=$order'>Dump to Excel Document</a></p>";
	
	} 
*/	
		
	
	$date2a = $date2 . " 23:59:59";
	$date1a = $date1 . " 00:00:00";

	if (isset($sales)) {
		$grossQ = "SELECT sum(total) as GROSS_sales
			FROM is4c_log.dtransactions 
    		WHERE datetime >= '$date1a' AND datetime <= '$date2a' 
			AND department <= 35
			AND department <> 0
			AND trans_status <> 'X'
			AND emp_no <> 9999";

			$results = mysql_query($grossQ);
			$row = mysql_fetch_row($results);
			$gross = $row[0];
/*
		$hashQ = "SELECT ROUND(sum(total),2) AS HASH_sales
			FROM is4c_log.dtransactions
			WHERE datetime >= '$date1a' AND datetime <= '$date2a' 
			AND department IN(34,36,38,40,41,42,43,44)
			AND trans_status NOT IN('X')
			AND emp_no <> 9999";

		$hashR = mysql_query($hashQ);
		$row = mysql_fetch_row($hashR);
		$hash = $row[0];
*/
		$netQ = "SELECT ROUND(SUM(TOT),2) as NET_sales 
			FROM(
		  		SELECT * FROM (
		    		SELECT SUM(total) as TOT
		    		FROM is4c_log.dtransactions
		    		WHERE datetime >= '$date1a' AND datetime <= '$date2a' 
		    		AND department >= 45 AND department <> 0
		    		AND trans_status <> 'X'
		    		AND emp_no <> 9999
		    	) AS gross
			UNION
			SELECT * FROM (
				SELECT -SUM(total) AS TOT
					FROM is4c_log.dtransactions
					WHERE datetime >= '$date1a' AND datetime <= '$date2a' 
					AND trans_subtype IN('IC')
					AND trans_status <> 'X'
					AND emp_no <> 9999
				) AS coupons
			UNION
		  	SELECT * FROM (
		    	SELECT SUM(total) AS TOT
		    		FROM is4c_log.dtransactions
		    		WHERE datetime >= '$date1a' AND datetime <= '$date2a' 
		    		AND trans_status <> 'X'
		    		AND upc = 'DISCOUNT'
		    		AND emp_no <> 9999
					AND trans_subtype <> 'IC'
		    	) AS discounts
			) AS SUMALL";


		$netR = mysql_query($netQ);
		$row = mysql_fetch_row($netR);
		$net = $row[0];

		if ($gross == 0 || !$gross ) $gross = 1;

		 // sales of inventory departments
		$invtotalsQ = "SELECT d.department,t.dept_name,ROUND(sum(d.total),2) AS total,ROUND((SUM(d.total)/$gross)*100,2) as pct
			FROM is4c_log.dtransactions AS d, is4c_op.departments AS t
			WHERE d.department = t.dept_no
			AND date(d.datetime) >= '$date1' AND date(d.datetime) <= '$date2' 
			AND d.department <= 35 AND d.department <> 0
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			GROUP BY d.department, t.dept_name";

		$gross = 0;

		// Sales for non-inventory departments 
		$noninvtotalsQ = "SELECT d.department,t.dept_name,ROUND(sum(total),2) as total 
			FROM is4c_log.dtransactions as d join is4c_op.departments as t ON d.department = t.dept_no
			WHERE datetime >= '$date1a' AND datetime <= '$date2a' 
			AND d.department > 35 AND d.department <> 0
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			GROUP BY d.department, t.dept_name";
		
		echo '<h2>Sales Totals</h2>';
	
		echo '<table><tr><td align="right">Gross Sales =</td><td align="right"><b>';
		echo money_format('%n',$gross) . "\n";
//		echo '</b></td></tr><tr><td align="right">Hash Sales =</td><td align="right"><b>';
//		echo money_format('%n',$hash) . "\n";
		echo '</b></td></tr><tr><td align="right">Net Sales =</td><td align="right"><b>';
		echo money_format('%n',$net) . "\n";
		
//		include('net.php');

		echo '</b></td></tr></table><h4>Inventory Department Totals</h4>';
		echo '<p>';
		select_to_table($invtotalsQ,1,'FFFFFF');
		echo '</p>';
		echo '<h4>Non-Inventory Department Totals</h4>';
		select_to_table($noninvtotalsQ,1,'FFFFFF');
	} 
			
	if(isset($tender)) {
		if ($gross == 0 || !$gross ) $gross = 1;

		$tendertotalsQ = "SELECT t.TenderName as tender_type,ROUND(-sum(d.total),2) as total,ROUND((-SUM(d.total)/$gross)*100,2) as pct
			FROM is4c_log.dtransactions as d ,is4c_op.tenders as t 
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.trans_status <> 'X' 
			AND d.emp_no <> 9999
			AND d.trans_subtype = t.TenderCode
			GROUP BY t.TenderName";

		$gross = 0;

		$transcountQ = "SELECT COUNT(d.total) as transactionCount
			FROM is4c_log.dtransactions AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			AND d.upc = 'DISCOUNT'";

		$transcountR = mysql_query($transcountQ);
		$row = mysql_fetch_row($transcountR);
		$count = $row[0];

		$basketsizeQ = "SELECT ROUND(($gross/$count),2) AS basket_size";

		$basketsizeR = mysql_query($basketsizeQ);
		$row = mysql_fetch_row($basketsizeR);
		$basketsize = $row[0];
		
		echo '<h1>Tender Report + Basket Size</h1>';
		select_to_table($tendertotalsQ,1,'FFFFFF');
		echo '<br><p>Transaction count&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.$count;
		echo '</b></p><p>Basket size&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.money_format('%n',$basketsize);
		echo '</p>';

	}		
			
	if(isset($discounts)) {

		// Total discount
		$disctotalQ = "SELECT ROUND(-SUM(d.total),2) AS totalDiscounts
			FROM is4c_log.dtransactions d INNER JOIN
	  		is4c_op.custdata c ON d.card_no = c.CardNo INNER JOIN
	  		is4c_op.memtype m ON c.memType = m.memtype
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a' 
	  		AND d.trans_status <>'X'
			AND d.upc = 'DISCOUNT' 
			AND c.personnum = 1
			AND d.emp_no <> 9999
			GROUP BY d.upc";

		$disctotalR = mysql_query($disctotalQ);
		$row = mysql_fetch_row($disctotalR);
		$disctotal = $row[0];

		// Discounts by member type;
		$memtypeQ = "SELECT m.memDesc as memberType,ROUND(-SUM(d.total),2) AS discount 
			FROM is4c_log.dtransactions d INNER JOIN
			is4c_op.custdata c ON d.card_no = c.CardNo INNER JOIN
	  		is4c_op.memtype m ON c.memType = m.memtype
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a' 
			AND d.upc = 'DISCOUNT'
	  		AND d.trans_status <>'X'
	  		AND d.emp_no <> 9999
			GROUP BY m.memDesc, d.upc";

		// percentage breakdown
		$percentQ = "SELECT c.discount AS discount,ROUND(-SUM(d.total),2) AS totals 
			FROM is4c_log.dtransactions AS d, is4c_op.custdata AS c 
			WHERE d.card_no = c.CardNo 
			AND d.datetime >= '$date1a' AND d.datetime <= '$date2a' 	
			AND d.upc = 'DISCOUNT'
			AND d.trans_status <> 'X' 
			AND d.emp_no <> 9999 
			GROUP BY c.discount";

		// staff discount
		$staffQ = "SELECT ROUND(-SUM(d.unitPrice),2) AS staff_total 
			FROM is4c_log.dtransactions AS d, is4c_op.custdata AS c 
			WHERE d.card_no = c.CardNo 
			AND d.datetime >= '$date1a' AND d.datetime <= '$date2a' 
			AND c.staff = 1
			AND d.upc = 'DISCOUNT'
			AND d.trans_status <> 'X' 
			AND d.emp_no <> 9999";

		$staffR = mysql_query($staffQ);
		$row = mysql_fetch_row($staffR);
		$staffdisc = $row[0];
			
		//HOO discount
		$hoodiscQ = "SELECT ROUND(-SUM(d.total),2) 
			FROM is4c_log.dtransactions AS d, is4c_op.custdata AS c
			WHERE d.card_no = c.CardNo
			AND d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND c.staff = 3
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			AND d.upc = 'DISCOUNT'";
		
		$hoodiscR = mysql_query($hoodiscQ);
		$row = mysql_fetch_row($hoodiscR);
		$hoodisc = $row[0];

		echo '<h1>Discount Report</h1>';
		echo '<p>Total discounts&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.$disctotal;
		echo '</b></p><h4>Discounts By Percentage</h4>';
		select_to_table($percentQ,1,'FFFFFF');
		echo '<p>Staff discount total&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.$staffdisc;
		echo '</b></p><p>Volunteer discount total&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.$hoodisc;
		echo '</b></p><h4>Discounts By Member Type</h4>';
		select_to_table($memtypeQ,1,'FFFFFF');
		
	}
	
	if(isset($equity)){	
	
		$sharetotalsQ = "SELECT d.datetime AS datetime,d.card_no AS cardno,c.LastName AS lastname,ROUND(sum(total),2) as total 
			FROM is4c_log.dtransactions as d, is4c_op.custdata AS c
			WHERE d.card_no = c.CardNo
			AND c.personNum = 1
			AND d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department = 45 
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			GROUP BY d.datetime";

		$sharetotalQ = "SELECT ROUND(SUM(d.total),2) AS Total_share_pmt
			FROM is4c_log.dtransactions AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department = 45
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";

		$sharetotalR = mysql_query($sharetotalQ);
		$row = mysql_fetch_row($sharetotalR);
		$sharetotal = $row[0];

		$sharecountQ = "SELECT COUNT(d.total) AS peopleshareCount
			FROM is4c_log.dtransactions AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department = 45
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";
				
		$sharecountR = mysql_query($sharecountQ);
		$row = mysql_fetch_row($sharecountR);
		$sharecount = $row[0];
		
		echo '<h1>Equity Report</h1>';
		echo '<h4>Sales of Member Shares</h4>';
		select_to_table($sharetotalsQ,1,'FFFFFF');
		echo '<p>Total member share payments = <b>'.$sharetotal;
		echo '</b></p><p>Member Share count&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.$sharecount;
		echo '</b></p>';
		
	}
/*
	if(isset($services)) {
		$custsvcsQ = "SELECT d.upc AS UPC,d.description AS description,CONCAT(SUM(d.quantity),' X ',d.unitPrice) AS countXprice,ROUND(SUM(d.total),2) AS total
			FROM is4c_log.dtransactions AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department = 20
			AND d.trans_type <> 'D'
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			GROUP BY d.upc";
		
		echo '<h1>Stamps, Tri-Met, Sisters</h1>';
		select_to_table($custsvcsQ,1,'FFFFFF');

	} 
*/	
//
// PHP INPUT DEBUG SCRIPT  -- very helpful!
//

function debug_p($var, $title) 
{
    print "<p>$title</p><pre>";
    print_r($var);
    print "</pre>";
}  

debug_p($_REQUEST, "all the data coming in");


?>
</body>
</html>
