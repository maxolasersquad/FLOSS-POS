<?php
/*******************************************************************************

    Copyright 2007 Alberta Cooperative Grocery, Portland, Oregon.

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


// A page to create a member.
$page_title='Create a Member';
include ('./includes/header.html');

// Check for a valid user ID, through GET or POST.

require_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php'); // Connect to the database.

if (isset($_POST['submitted'])) { // If the form has been submitted, check the data and create the record.
	
	// Initialize the errors array.
	$errors = array();
	$cn = $_POST['card_no'];
	$query = "SELECT * FROM custdata WHERE cardno=" . $cn;
	$result = @mysql_query($query);
	if (mysql_num_rows($result) != 0) {
		$errors[] = 'This member number is already in use, please select a different number, or <a href="auto_mem_modify.php?cardno=' . $cn . '">edit</a> the existing household.';
	}
	// How many records are we adding?
	if ((isset($_POST['ps'])) && (isset($_POST['ss']))) { // Both Primary and Secondary. 
		// Validate the form data.
		$num_records = 2;
		if ((empty($_POST['ps_first_name'])) || (empty($_POST['ss_first_name']))){
			
			$errors[] = 'You left one or both of their first names blank.';
			
		} else {
			$psfn = escape_data($_POST['ps_first_name']); // Store the first names.
			$ssfn = escape_data($_POST['ss_first_name']); // Store the first names.
		}
		
		if ((empty($_POST['ps_last_name'])) || (empty($_POST['ss_last_name']))){
			
			$errors[] = 'You left one or both of their last names blank.';
			
		} else {
			$psln = escape_data($_POST['ps_last_name']); // Store the last names.
			$ssln = escape_data($_POST['ss_last_name']); // Store the last names.
		}
		if (!isset($_POST['ps_checks_ok'])) {$_POST['ps_checks_ok'] = 'off';}
		if (($_POST['ps_checks_ok']) == 'on') {$psWriteCheck = 1;}
		if (!isset($_POST['ss_checks_ok'])) {$_POST['ss_checks_ok'] = 'off';}
		if (($_POST['ss_checks_ok']) == 'on') {$ssWriteCheck = 1;}
		if (!isset($_POST['ps_charge_ok'])) {$_POST['ps_charge_ok'] = 'off';}
		if (!isset($_POST['ss_charge_ok'])) {$_POST['ss_charge_ok'] = 'off';}
		if (($_POST['ps_charge_ok'] == 'on') && (($_POST['ps_staff'] == 0) || ($_POST['ps_staff'] == 3) || ($_POST['ps_staff'] == 4) || ($_POST['ps_staff'] == 6))) {
			$errors[] = 'Non-staff members cannot house charge.';
			$pscharge = 0;
		} else {
			if ($_POST['ps_charge_ok'] == 'on') {$pscharge = 1;}
			else {$pscharge = 0;}
		}
		if (($_POST['ss_charge_ok'] == 'on') && (($_POST['ss_staff'] == 0) || ($_POST['ss_staff'] == 3) || ($_POST['ss_staff'] == 4) || ($_POST['ss_staff'] == 6))) {
			$errors[] = 'Non-staff members cannot house charge.';
			$sscharge = 0;
		} else {
			if ($_POST['ss_charge_ok'] == 'on') {$sscharge = 1;}
			else {$sscharge = 0;}
		}
		if ($pscharge == 1) {$pslimit=9999;} else {$pslimit=0;}
		if ($sscharge == 1) {$sslimit=9999;} else {$sslimit=0;}
		if ((empty($_POST['ps_discount'])) || (empty($_POST['ss_discount']))) {
			
			$errors[] = 'You left one or both of their discounts blank.';
			
		} elseif (($_POST['ps_discount'] > 15) || ($_POST['ss_discount'] > 15)) {
		
			$errors[] = 'You entered a discount greater than the maximum.';
		
		} else {
			$psd = escape_data($_POST['ps_discount']); // Store the discounts.
			$ssd = escape_data($_POST['ss_discount']); // Store the discounts.
		}
		$psmemtype = $_POST['ps_memtype'];
		$psstaff = $_POST['ps_staff'];
		if ($psstaff == 6) {$psType='reg';} else {$psType='pc';}
		$ssmemtype = $_POST['ss_memtype'];
		$ssstaff = $_POST['ss_staff'];
		if ($ssstaff == 6) {$ssType='reg';} else {$ssType='pc';}

		
	} elseif ((isset($_POST['ps'])) && (!isset($_POST['ss']))) {// Only a primary.
		// Validate the form data.
		$num_records = 1;
		if (empty($_POST['ps_first_name'])) {
			
			$errors[] = 'You left their first name blank.';
			
		} else {
			$psfn = escape_data($_POST['ps_first_name']); // Store the first name.
		}
		
		if (empty($_POST['ps_last_name'])) {
			
			$errors[] = 'You left their last name blank.';
			
		} else {
			$psln = escape_data($_POST['ps_last_name']); // Store the last name.
			
		}
		if (!isset($_POST['ps_checks_ok'])) {$_POST['ps_checks_ok'] = 'off';}
		if (($_POST['ps_checks_ok']) == 'on') {$psWriteCheck = 1;}
		if (!isset($_POST['ps_charge_ok'])) {$_POST['ps_charge_ok'] = 'off';}
		if (($_POST['ps_charge_ok'] == 'on') && (($_POST['ps_staff'] == 0) || ($_POST['ps_staff'] == 3) || ($_POST['ps_staff'] == 4) || ($_POST['ps_staff'] == 6))) {
			$errors[] = 'Non-staff members cannot house charge.';
			$pscharge = 0;
		} else {
			if ($_POST['ps_charge_ok'] == 'on') {$pscharge = 1;}
			else {$pscharge = 0;}
		}
		if (empty($_POST['ps_discount'])) {
			
			$errors[] = 'You left their discount blank.';
			
		} elseif ($_POST['ps_discount'] > 15) {
		
			$errors[] = 'You entered a discount greater than the maximum.';
		
		} else {
			$psd = escape_data($_POST['ps_discount']); // Store the discount.
			
		}
		$psmemtype = $_POST['ps_memtype'];
		isset($_POST['ps_staff'])?$psstaff = $_POST['ps_staff']:$ps_staff='0';
		if ($ps_staff == 6) {$psType='reg';} else {$psType='pc';}
		if ($pscharge == 1) {$pslimit=9999;} else {$pslimit=0;}

	}
	if (empty($errors)) {
		$ps = "($cn, 1, '$psfn', '$psln', $psWriteCheck, $psd, $psmemtype, '$psType', $ps_staff, $pscharge, $pslimit)";
		if ($num_records == 2) {$ss = ", ($cn, 2, '$ssfn', '$ssln', $ssWriteCheck, $ssd, $ssmemtype, '$ssType', $ssstaff, $sscharge, $sslimit)";}
		elseif ($num_records == 1) {$ss = '';}
		$query = "INSERT INTO custdata (CardNo, personNum, FirstName, LastName, WriteChecks, discount, memType, Type, staff, ChargeOk, MemDiscountLimit) VALUES " . $ps . $ss;
echo $query . '~' . $ps . '~' . $ss;
		$result = @mysql_query($query);
			
		if ((mysql_affected_rows() == 1) || (mysql_affected_rows() == 2)) { // If the query was successful.
				
			echo '<h1 id="mainhead">Create a Member</h1>
			<p>The member(s) have been created.</p><p><br /><br /></p>';
				
		} else { // The query was unsuccessful.
				
			echo '<h1 id="mainhead">System Error</h1>
			<p class="error">The member could not be edited due to a system error.<br />';
			echo '<p>' . mysql_error() . '<br /><br />Query: ' . $query . '</p>';

		}
	
		
	} else { // Report the errors.
		
		echo '<h1 id="mainhead">Error!!</h1>
		<p class="error">The following error(s) occurred:<br />';
		foreach ($errors as $msg) { // Print each error.
			echo " - $msg<br />\n";
		}
		echo '</p><p>Please try again.</p><p><br /></p>';
			
	} // End of if (empty($errors)) IF.
		
} // End of submit conditional.

// Always show the form.

// Retrieve the user's information.
//$query = "SELECT staff_no, staff_desc FROM staff ORDER BY staff_no ASC";
$query2 = "SELECT memtype, memDesc FROM memtype ORDER BY memtype ASC";
$query3 = "SELECT max(cardno) AS max FROM custdata WHERE cardno < 9999";
$query4 = "SELECT staff_no, staff_desc FROM staff ORDER BY staff_no ASC";
$query5 = "SELECT memtype, memDesc FROM memtype ORDER BY memtype ASC";
//$result = @mysql_query($query);
$result2 = @mysql_query($query2);
$result3 = @mysql_query($query3);
$result4 = @mysql_query($query4);
$result5 = @mysql_query($query5);

// Show the form.
	
	// Get the user's information.

	$row3 = mysql_fetch_array($result3);
	$max = $row3['max'];
	$max = $max + 1;
	// Create the form.

	echo '<h2>Create a Member.</h2><br />
	<form action="auto_mem_create.php" name="create_member" method="post">
	<p>Card Number: <input type="text" name="card_no" size="4" maxlength="4" value="' . $max . '" /></p>
	<h3><u><input type="checkbox" name="ps" CHECKED />  Primary Shareholder</u></h3>
	<p>First Name: <input type="text" name="ps_first_name" size="15" maxlength="15" /></p>
	<p>Last Name: <input type="text" name="ps_last_name" size="15" maxlength="30" /></p>';
	echo '<p>House Charge? <input type="checkbox" name="ps_charge_ok" /></p>';
	echo '<p>Write Checks? <input type="checkbox" name="ps_checks_ok" CHECKED /></p>
	<p>Discount: <input type="text" name="ps_discount" size="3" maxlength="2" value="2" />%</p>
	<!--<p>Member Type: <select name="ps_staff">-->';
	/*while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo '<option value='. $row['staff_no'] . '>' . $row['staff_desc'];
	}*/
	echo '</select>
	<p>Member Status: <select name="ps_memtype">';
	while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		echo '<option value='. $row2['memtype'];
		if ($row2['memtype'] == 1) {echo ' SELECTED';}
		echo '>' . $row2['memDesc'];
	}
	echo '</select><br /><br />
	<div style="display: none"><h3><u><input type="checkbox" name="ss" />  Secondary Shareholder</u></h3>
	<p>First Name: <input type="text" name="ss_first_name" size="15" maxlength="15" /></p>
	<p>Last Name: <input type="text" name="ss_last_name" size="15" maxlength="30" /></p>';
	echo '<p>House Charge? <input type="checkbox" name="ss_charge_ok" /></p>';
	echo '<p>Write Checks? <input type="checkbox" name="ss_checks_ok" CHECKED /></p>
	<p>Discount: <input type="text" name="ss_discount" size="3" maxlength="2" value="2" />%</p>
	<p>Member Type: <select name="ss_staff">';
	while ($row4 = mysql_fetch_array($result4, MYSQL_ASSOC)) {
		echo '<option value='. $row4['staff_no'] . '>' . $row4['staff_desc'];
	}
	echo '</select>
	<p>Member Status: <select name="ss_memtype">';
	while ($row5 = mysql_fetch_array($result5, MYSQL_ASSOC)) {
		echo '<option value='. $row5['memtype'];
		if ($row5['memtype'] == 1) {echo ' SELECTED';}
		echo '>' . $row5['memDesc'];
	}
	echo '</select></div><br />';
	echo '<p><input type="submit" name="submit" value="Submit" /></p>
	<input type="hidden" name="submitted" value="TRUE" />
	</form>';
/*	
} else { // Not a valid Member ID
	echo '<h1 id="mainhead">Page Error</h1>
	<p class="error">This page has been accessed in error.</p><p><br /><br /></p>';
}*/
mysql_close(); // Close the DB connection.
?>
