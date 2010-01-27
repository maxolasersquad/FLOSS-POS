<?
setlocale(LC_MONETARY, 'en_US');
//$db_date = '2007-03-19';

$db = mysql_connect('localhost','root','');
mysql_select_db('is4c_log',$db);

/** 
 * total sales 
 * Gross = total of all inventory depts. 1-15 (at PFC)
 * Hash = People Shares + General Donations + Customers Svcs. + gift certs. sold + Bottle Deposits & Returns + Comm. Rm. fees
 * Net = Gross + Everything else + R/A (45) - Market EBT (37) - Charge pmts.(35) - All discounts - Coupons(IC & MC) - 
 * 		Gift Cert. Tender - Store Charge
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
//
//	BEGIN STAFF_TOTAL	
//	Total Staff discount given less the needbased and MAD discount
//
$staffQ = "SELECT (SUM(d.unitPrice)) AS staff_total
	FROM dtransactions AS d, is4c_op.custdata AS c 
	WHERE d.card_no = c.CardNo 
	AND date(d.datetime) = '".$db_date."'
	AND d.upc = 'DISCOUNT'
	AND c.staff IN(1,2,5)
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999";

$lessQ = "SELECT (SUM(d.unitPrice) * -1) AS TOT
	FROM dtransactions AS d, is4c_op.custdata AS c
	WHERE d.card_no = c.CardNo
	AND date(d.datetime) = '".$db_date."'
	AND c.staff IN(1,2,5)
	AND d.voided IN(9,10)
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999";

	$staffR = mysql_query($staffQ);
	$row = mysql_fetch_row($staffR);
	$staff = $row[0];
	if (is_null($staff)) {
		$staff = 0;
	}
	$lessR = mysql_query($lessQ);
	$row = mysql_fetch_row($lessR);
	$less = $row[0];
	if (is_null($less)) {
		$less = 0;
	}
	
$staff_totalQ = "SELECT ($staff + $less) AS staff_total";
	
	$staff_totalR = mysql_query($staff_totalQ);
	$row = mysql_fetch_row($staff_totalR);
	$staff_total = $row[0];
	if (is_null($staff_total)) {
		$staff_total = 0;
	}
//
//	END STAFF_TOTAL
//

//
//	BEGIN MEM_TOTAL
//
$memQ = "SELECT SUM(d.total) as mem_tot
	FROM dtransactions d, is4c_op.custdata c
	WHERE d.card_no = c.CardNo
	AND date(d.datetime) = '".$db_date."'
	AND d.upc = 'DISCOUNT'
	AND c.memType IN(1,2)
	AND c.staff = 0
	AND d.emp_no <> 9999
	AND d.trans_status <> 'X'";

	$memR = mysql_query($memQ);
	$row = mysql_fetch_row($memR);
	$mem_total = $row[0];
	if(is_null($mem_total)) {
		$mem_total = 0;
	}
//
//	BEGIN WM_TOTAL
//
$wmQ = "SELECT SUM(d.unitPrice) AS wms 
	FROM dtransactions AS d, is4c_op.custdata AS c 
	WHERE d.card_no = c.CardNo 
	AND date(d.datetime) = '".$db_date."'
	AND d.upc = 'DISCOUNT'
	AND c.staff IN(3,4,6)
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999";

// Haus - 08-03-2007
$totaldiscountQ="SELECT SUM(unitPrice) AS totaldisc
	FROM dtransactions
	WHERE date(datetime) = '" . $db_date . "'
	AND upc = 'DISCOUNT'
	AND trans_status <> 'X'
	AND emp_no <> 9999";
$totaldiscountR=mysql_query($totaldiscountQ);
$row = mysql_fetch_row($totaldiscountR);
$totaldiscount=$row[0];
// End Haus

$lessQ = "SELECT (SUM(d.unitPrice) * -1) AS TOT
	FROM dtransactions AS d, is4c_op.custdata AS c
	WHERE d.card_no = c.CardNo
	AND date(d.datetime) = '".$db_date."'
	AND c.staff NOT IN(3,4,6)
	AND d.voided IN(9,10)
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999";

	$wmR = mysql_query($wmQ);
	$row = mysql_fetch_row($wmR);
	$wms = $row[0];
	
	$lessR = mysql_query($lessQ);
	$row = mysql_fetch_row($lessR);	
	$less = $row[0];
	
	if (is_null($wms)) {
		$wms = 0;
	}
	if (is_null($less)) {
		$less = 0;
	}

$wm_totalQ = "SELECT ($wms + $less) AS hoo_total";
	
	$wm_totalR = mysql_query($wm_totalQ);
	$row = mysql_fetch_row($wm_totalR);
	$wm_total = $row[0];
	if (is_null($wm_total)) {
		$wm_total = 0;
	}
//
//	END WM_TOTAL
//

//
//	BEGIN SISTER_ORGS
//
$sisterQ = "SELECT (ROUND(SUM(d.unitPrice),2)) AS sister_orgs
	FROM dtransactions AS d, is4c_op.custdata AS c
	WHERE d.card_no = c.CardNo 
	AND (date(d.datetime) = '".$db_date."') 
	AND d.upc = 'DISCOUNT' 
	AND c.memType = 6
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999";

	$sisterR = mysql_query($sisterQ);
	$row = mysql_fetch_row($sisterR);
	$sister= $row[0];
	if (is_null($sister)) {
		$sister = 0;
	}

$lessQ = "SELECT (SUM(d.unitPrice) * -1) AS TOT
	FROM dtransactions AS d, is4c_op.custdata AS c
	WHERE d.card_no = c.CardNo
	AND date(d.datetime) = '".$db_date."'
	AND c.memType = 6
	AND d.voided IN(9,10)
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999";

	$lessR = mysql_query($lessQ);
	$row = mysql_fetch_row($lessR);	
	$less = $row[0];
	if (is_null($less)) {
		$less = 0;
	}

$sister_orgQ = "SELECT ($sister + $less) AS sister_orgs";

	$sister_orgR = mysql_query($sister_orgQ);
	$row = mysql_fetch_row($sister_orgR);
 	$sister_org = $row[0];
	if (is_null($sister_org)) {
		$sister_org = 0;
	}
//
//	END SISTER_ORGS
//


$MADcouponQ = "SELECT ROUND(SUM(unitPrice),2) AS MAD_Coupon_total
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."' 
	AND voided = 9
	AND trans_status <> 'X'
	AND emp_no <> 9999";

	$MADcouponR = mysql_query($MADcouponQ);
	$row = mysql_fetch_row($MADcouponR);
	$MADcoupon = $row[0];
	if (is_null($MADcoupon)) {
		$MADcoupon = 0;
	}

$foodforallQ = "SELECT ROUND(SUM(unitPrice),2) AS FoodForAll_total
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."' 
	AND voided = 10
	AND trans_status <> 'X'
	AND emp_no <> 9999";

	$foodforallR = mysql_query($foodforallQ);
	$row = mysql_fetch_row($foodforallR);
	$foodforall = $row[0];
	if (is_null($foodforall)) {
		$foodforall = 0;
	}

/* (haus 08-03-07) $totalDiscQ = "SELECT ($mem_total + $staff_total + $sister_org + $wm_total + $MADcoupon + $foodforall) as total_discounts";

	$totalDiscR = mysql_query($totalDiscQ);
	$row = mysql_fetch_row($totalDiscR);
	$totalDisc = $row[0];
*/ $totalDisc = $totaldiscount;
$couponsQ = "SELECT ROUND(SUM(total),2) AS coupons
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND trans_subtype IN('IC','MC','TC')
	AND trans_status <> 'X'
	AND emp_no <> 9999";
	
	$couponsR = mysql_query($couponsQ);
	$row = mysql_fetch_row($couponsR);
	$coupons = $row[0];
	if (is_null($coupons)) {
		$coupons = 0;
	}

$chgQ = "SELECT ROUND(SUM(total),2) AS chg
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND trans_subtype IN('MI')
	AND trans_status <> 'X'
	AND emp_no <> 9999";

	$chgR = mysql_query($chgQ);
	$row = mysql_fetch_row($chgR);
	$chg = $row[0];
	if (is_null($chg)) {
		$chg = 0;
	}

/*
$RAQ = "SELECT ROUND(SUM(total),2) as RAs
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND department IN(45)
	AND trans_status <> 'X'
	AND emp_no <> 9999";

	$RAR = mysql_query($RAQ);
	$row = mysql_fetch_row($RAR);
	$RA = $row[0];
	if (is_null($RA)) {
		$RA = 0;
	}
*/	
	
//	Other = Chrg Payments + Market EBT	
/*
$otherQ = "SELECT ROUND(SUM(total),2) as other
	FROM dtransactions
	WHERE date(datetime) = '".$db_date."'
	AND department IN(35,37)
	AND trans_status <> 'X'
	AND emp_no <> 9999";
	
	$otherR = mysql_query($otherQ);
	$row = mysql_fetch_row($otherR);
	$other = $row[0];
	if (is_null($other)) {
		$other = 0;
	}
*/

//$netQ = "SELECT ($gross + $hash + $totalDisc + $coupons + $strchg + $RA + $other) as NET_TOTAL";
$netQ = "SELECT ($gross + $totalDisc + $coupons + $chg) as NET_TOTAL";

	$netR = mysql_query($netQ);
	$row = mysql_fetch_row($netR);
	$net = $row[0];

echo "<table border=0><tr><td>gross total</td><td align=right>".money_format('%n',$gross)."</td></tr>";
//echo "<tr><td>hash total</td><td align=right>".money_format('%n',$hash)."</td></tr>";
echo "<tr><td>totalDisc</td><td align=right>".money_format('%n',$totalDisc)."</td></tr>";
echo "<tr><td>coupon & gift cert. tenders</td><td align=right>".money_format('%n',$coupons)."</td></tr>";
echo "<tr><td>store & house charges</td><td align=right>".money_format('%n',$chg)."</td></tr>";
//echo "<tr><td>rcvd/accts</td><td align=right>".money_format('%n',$RA)."</td></tr>";
//echo "<tr><td>mkt EBT & chg pmts</td><td align=right>".money_format('%n',$other)."</td></tr>";
echo "<tr><td>&nbsp;</td><td align=right>+___________</td></tr>";
echo "<tr><b><td><b>net total</b></td><td align=right><b>".money_format('%n',$net)."</b></td></b></tr></table>";
?>
