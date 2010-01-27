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

setlocale(LC_MONETARY, 'en_US');
//$db_date = '2007-03-19';

$db = mysql_connect('localhost',$_SESSION["mUser"],$_SESSION["mPass"]);
mysql_select_db('is4c_log',$db);

/** 
 * total sales 
 * Gross = total of all inventory depts. 1-15 (at PFC)
 * Hash = People Shares + General Donations + Customers Svcs. + gift certs. sold + Bottle Deposits & Returns + Comm. Rm. fees
 * Net = Gross + Everything else + R/A (45) - Market EBT (37) - Charge pmts.(35) - All discounts - Coupons(IC & MC) - 
 * 		Gift Cert. Tender - Store Charge
 */
/*
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
*/
// Haus edit 08-06-07
$gross2Q = "SELECT ROUND(sum(total),2) AS GROSS
	FROM dtransactions
	WHERE date(datetime) = '" . $db_date . "'
	AND department <> 0
	AND trans_status <> 'X'
	AND emp_no <> 9999";
	$gross2R = mysql_query($gross2Q);
	$row = mysql_fetch_row($gross2R);
	$gross2 = $row[0];
// end haus edit.

/* // Haus edit 08-03-2007
$net2Q = "SELECT ROUND(SUM(total),2) AS net
	from dtransactions
	where date(datetime) = '" .$db_date. "'
	and trans_subtype <> 'IC'
	AND department <> 0
	and trans_status <> 'X'
	AND emp_no <> 9999";
*/
$net2Q = "select sum(total) from dlog where date(tdate)='" . $db_date . "' and trans_type in ('I', 'D') and trans_subtype <> 'IC' and trans_status <> 'X' and emp_no <> 9999";
	$net2R = mysql_query($net2Q);
	$row = mysql_fetch_row($net2R);
	$net2 = $row[0];
// End haus edit

//
//	BEGIN STAFF_TOTAL	
//	Total Staff discount given less the needbased and MAD discount
//
$staffQ = "SELECT (SUM(d.unitPrice)) AS staff_total
	FROM dtransactions AS d
	WHERE date(d.datetime) = '".$db_date."'
	AND d.upc = 'DISCOUNT'
	AND d.staff IN(1,2,5)
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999";

	$staffR = mysql_query($staffQ);
	$row = mysql_fetch_row($staffR);
	$staff = $row[0];
	if (is_null($staff)) {
		$staff = 0;
	}

$staff_totalQ = "SELECT ($staff) AS staff_total";
	
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
	FROM dtransactions d
	WHERE date(d.datetime) = '".$db_date."'
	AND d.upc = 'DISCOUNT'
	AND d.memType IN(1,2)
	AND d.staff = 0
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
	FROM dtransactions AS d
	WHERE date(d.datetime) = '".$db_date."'
	AND d.upc = 'DISCOUNT'
	AND d.staff IN(3,4,6)
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999";

	$wmR = mysql_query($wmQ);
	$row = mysql_fetch_row($wmR);
	$wms = $row[0];
	
	if (is_null($wms)) {
		$wms = 0;
	}

$wm_totalQ = "SELECT ($wms) AS hoo_total";
	
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
//	NON-OWNER/WORKER DISCOUNTS
//
$nonQ = "SELECT SUM(d.total) as non
	FROM dtransactions d
	WHERE date(d.datetime) = '".$db_date."'
	AND d.upc = 'DISCOUNT'
	AND d.memType = 0
	AND d.staff = 0
	AND d.emp_no <> 9999
	AND d.trans_status <> 'X'";

	$nonR = mysql_query($nonQ);
	$row = mysql_fetch_row($nonR);
	$non_total = $row[0];
	if(is_null($non_total)) {
		$non_total = 0;
	}



//
//	BEGIN SISTER_ORGS
//
$sisterQ = "SELECT (ROUND(SUM(d.unitPrice),2)) AS sister_orgs
	FROM dtransactions AS d
	WHERE (date(d.datetime) = '".$db_date."') 
	AND d.upc = 'DISCOUNT' 
	AND d.memType = 6
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999";

	$sisterR = mysql_query($sisterQ);
	$row = mysql_fetch_row($sisterR);
	$sister= $row[0];
	if (is_null($sister)) {
		$sister = 0;
	}

$sister_orgQ = "SELECT ($sister) AS sister_orgs";

	$sister_orgR = mysql_query($sister_orgQ);
	$row = mysql_fetch_row($sister_orgR);
 	$sister_org = $row[0];
	if (is_null($sister_org)) {
		$sister_org = 0;
	}
//
//	END SISTER_ORGS
//



// Haus - 08-03-2007
$totaldiscount2Q="SELECT SUM(total) AS totaldisc
	FROM dtransactions
	WHERE date(datetime) = '" . $db_date . "'
	AND upc = 'DISCOUNT'
	AND trans_status <> 'X'
	AND emp_no <> 9999";
$totaldiscount2R=mysql_query($totaldiscount2Q);
$row = mysql_fetch_row($totaldiscount2R);
$totaldiscount2=$row[0];
// End Haus

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
/*
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
*/

// Haus edit 08-06-2007
$coupons2Q = "SELECT ROUND(SUM(total),2) AS coupons
	FROM dtransactions
	WHERE date(datetime) = '" . $db_date . "'
	AND trans_subtype = 'IC'
	AND trans_status <> 'X'
	AND emp_no <> 9999";
	$coupons2R = mysql_query($coupons2Q);
	$row = mysql_fetch_row($coupons2R);
	$coupons2 = $row[0];
	if (is_null($coupons2)) {
		$coupons = 0;
	}
// End haus edit.

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

$totalDiscQ = "SELECT ($mem_total + $staff_total + $sister_org + $wm_total + $non_total) as total_discounts";

	$totalDiscR = mysql_query($totalDiscQ);
	$row = mysql_fetch_row($totalDiscR);
	$totalDisc = $row[0];


//$netQ = "SELECT ($gross + $hash + $totalDisc + $coupons + $strchg + $RA + $other) as NET_TOTAL";
$netQ = "SELECT ($gross + $totalDisc) as NET_TOTAL";

	$netR = mysql_query($netQ);
	$row = mysql_fetch_row($netR);
	$net = $row[0];

	
// Haus edit...checking (08-06-07)
echo "<table border=0><tr><td>gross total</td><td align=right>".money_format('%n',$gross2)."</td></tr>";
//echo "<tr><td>hash total</td><td align=right>".money_format('%n',$hash)."</td></tr>";
echo "<tr><td>totalDisc</td><td align=right>".money_format('%n',$totaldiscount2)."</td></tr>";
echo "<tr><td>instore coupons</td><td align=right>".money_format('%n',$coupons2)."</td></tr>";
//echo "<tr><td>store & house charges</td><td align=right>".money_format('%n',$chg)."</td></tr>";
//echo "<tr><td>rcvd/accts</td><td align=right>".money_format('%n',$RA)."</td></tr>";
//echo "<tr><td>mkt EBT & chg pmts</td><td align=right>".money_format('%n',$other)."</td></tr>";
echo "<tr><td>&nbsp;</td><td align=right>+___________</td></tr>";
echo "<tr><b><td><b>net total</b></td><td align=right><b>".money_format('%n',$net2)."</b></td></tr></table>";

// End haus edit.
// echo "<tr><b><td><b>net?</b></td><td align=right><b>" . money_format('%n',$net2) . "</b></td></b></tr></table>";
?>
