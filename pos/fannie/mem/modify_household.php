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

// Household Editing Page



// A page to view and edit a household's details.
$page_title='Edit a Household';
include ('./includes/header.html');

// Check for a valid user ID, through GET or POST.

if ( (isset($_GET['cardno'])) && (is_numeric($_GET['cardno'])) ) { // Accessed through view_users.php.
	$cn = $_GET['cardno'];
} elseif ( (isset($_POST['cardno'])) && (is_numeric($_POST['cardno'])) ) { // Accessed through form submission.
	$cn = $_POST['cardno'];
} else { // No valid Card Number, ask for one.
	echo '<form action="auto_mem_modify.php" method="post"><br /><br />
	<h3><center>Which household would you like to modify?</center></h3>
	<h3><center><input type="text" name="cardno" size="4" maxlength="4" /></center><br /><br /></h3>
	<center><input type="submit" name="submit" value="Submit!" /></center>
	</form>';
	include ('./includes/footer.html');
	exit();
	
}

require_once ($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php'); // Connect to the database.

if (isset($_POST['submitted'])) { // If the form has been submitted, check the new data and update the record.
	
	// Initialize the errors array.
	$errors = array();
	
	// How many records are we editing?
	if ((isset($_POST['ps'])) && (isset($_POST['ss']))) { // Both Primary and Secondary. 
		if (is_numeric($_POST['ssid'])) { 
			$num_records = 2;
		} elseif ($_POST['ssid'] = 'insert') { // Mixed Update and Insert
			$num_records = 'mixed';
		}
		// Validate the form data.
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
		if (($_POST['ps_checks_ok']) == 'on') {$psWriteCheck = 1;} else {$psWriteCheck = 0;}
		if (!isset($_POST['ss_checks_ok'])) {$_POST['ss_checks_ok'] = 'off';}
		if (($_POST['ss_checks_ok']) == 'on') {$ssWriteCheck = 1;} else {$ssWriteCheck = 0;}
		if (!isset($_POST['ps_charge_ok'])) {$_POST['ps_charge_ok'] = 'off';}
		if (!isset($_POST['ss_charge_ok'])) {$_POST['ss_charge_ok'] = 'off';}
		if ($_POST['ps_charge_ok'] == 'on') {$pscharge = 1;}
		else {$pscharge = 0;}
		if ($pscharge == 1) {$pslimit=9999;} else {$pslimit=0;}
		if ($_POST['ss_charge_ok'] == 'on') {$sscharge = 1;}
		else {$sscharge = 0;}
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
		if (($_POST['ps_checks_ok']) == 'on') {$psWriteCheck = 1;} else {$psWriteCheck = 0;}
		if (!isset($_POST['ps_charge_ok'])) {$_POST['ps_charge_ok'] = 'off';}
		if (($_POST['ps_charge_ok'] == 'on') && ($_POST['ps_staff'] == 0 || 3 || 4 || 6)) {
			$errors[] = 'Non-staff members cannot house charge.';
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
		$psstaff = $_POST['ps_staff'];
		if ($psstaff == 6) {$psType='reg';} else {$psType='pc';}
		if ($pscharge == 1) {$pslimit=9999;} else {$pslimit=0;}
	}
	if (empty($errors)) {
		$psid = $_POST['psid'];
		$ssid = $_POST['ssid'];
		switch ($num_records) {
			case '2':
				$query1 = "UPDATE custdata SET FirstName='$psfn', LastName='$psln', WriteChecks=$psWriteCheck, discount=$psd, memType=$psmemtype, Type='$psType', staff=$psstaff, memDiscountLimit=$pslimit WHERE id=$psid";
				$query2 = "UPDATE custdata SET FirstName='$ssfn', LastName='$ssln', WriteChecks=$ssWriteCheck, discount=$ssd, memType=$ssmemtype, Type='$ssType', staff=$ssstaff, memDiscountLimit=$sslimit WHERE id=$ssid";
				$result1 = @mysql_query($query1);
				$affected = mysql_affected_rows();
				$result2 = @mysql_query($query2);
				$affected .= mysql_affected_rows();

			break;
			
			case '1':
				$query1 = "UPDATE custdata SET FirstName='$psfn', LastName='$psln', WriteChecks=$psWriteCheck, discount=$psd, memType=$psmemtype, Type='$psType', staff=$psstaff, memDiscountLimit=$pslimit WHERE id=$psid";
				$result1 = @mysql_query($query1);
				$affected = mysql_affected_rows();
			break;

			case 'mixed':
				$query1 = "UPDATE custdata SET FirstName='$psfn', LastName='$psln', WriteChecks=$psWriteCheck, discount=$psd, memType=$psmemtype, Type='$psType', staff=$psstaff, memDiscountLimit=$pslimit WHERE id=$psid";
				$query2 = "INSERT INTO custdata (CardNo, FirstName, LastName, WriteChecks, discount, memType, Type, staff, memDiscountLimit, personNum) VALUES ($cn, '$ssfn', '$ssln', $ssWriteCheck, $ssd, $ssmemtype, '$ssType', $ssstaff, $sslimit, 2)";
				$result1 = @mysql_query($query1);
				$affected = mysql_affected_rows();
				$result2 = @mysql_query($query2);
				$affected .= mysql_affected_rows();

			break;
		}

			
		if ($affected != 0) { // If the query was successful.
				
			echo '<h1 id="mainhead">Edit a Household</h1>
			<p>The household has been edited.</p><p><br /><br /></p>';
				
		} else { // The query was unsuccessful.
				
			echo '<h1 id="mainhead">System Error</h1>
			<p class="error">There are two possibilities:<br />
			<b>1.)</b> The household could not be edited due to a system error.<br />
			<b>2.)</b> Nothing was changed.</p>';
			// echo '<p>' . mysql_error() . '<br /><br />Query: ' . $query1 . '</p>';

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
$query = "SELECT * FROM custdata WHERE cardno=$cn ORDER BY personNum ASC";
$result = @mysql_query($query);

$num_rows = mysql_num_rows($result);
if (($num_rows == 2)) {  // Valid id show the form.
	echo '<h2>Edit a Household.</h2>
	<h3>Card Number: ' . $cn . '</h3>';
	// Get the user's information.
	while ($row = mysql_fetch_array($result)) {
		
		$query2 = "SELECT staff_no, staff_desc FROM staff ORDER BY staff_no ASC";
		$query3 = "SELECT memtype, memDesc FROM memtype ORDER BY memtype ASC";
		$result2 = @mysql_query($query2);
		$result3 = @mysql_query($query3);
		if ($row['personNum'] == 1) {$position = 'ps_'; $title = 'Primary Shareholder';} elseif ($row['personNum'] == 2) {$position = 'ss_'; $title = 'Secondary Shareholder';}
		// Create the form.
		if ($row["ChargeOk"] == 1) {$ChargeOk[$position] = ' CHECKED';} else {$ChargeOk[$position] = '';}
		if ($row["WriteChecks"] == 1) {$ChecksOk[$position] = ' CHECKED';} else {$ChecksOk[$position] = '';}
		
		echo '<form action="auto_mem_modify.php" method="post">
		<h3><u><input type="checkbox" name="' . substr($position, 0, -1) . '" CHECKED />  ' . $title . '</u></h3>
		<p>First Name: <input type="text" name="' . $position . 'first_name" size="15" maxlength="15" value="' . $row["FirstName"] . '" /></p>
		<p>Last Name: <input type="text" name="' . $position . 'last_name" size="15" maxlength="30" value="' . $row["LastName"] . '" /></p>';
		if ($row["staff"] == 1 || $row["staff"] == 2 || $row["staff"] == 5) {echo '<p>House Charge? <input type="checkbox" name="' . $position . 'charge_ok"' . $ChargeOk[$position] . ' /></p>';}
		echo '<p>Write Checks? <input type="checkbox" name="' . $position . 'checks_ok"' . $ChecksOk[$position] . ' /></p>
		<p>Discount: <input type="text" name="' . $position . 'discount" size="3" maxlength="2" value="' . $row["Discount"] . '" />%</p>
		<p>Member Type: <select name="' . $position . 'staff">';
		while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
			echo '<option value='. $row2['staff_no'];
			if ($row2['staff_no'] == $row['staff']) {echo ' SELECTED';}
			echo '>' . $row2['staff_desc'];
		}
		echo '</select>
		<p>Member Status: <select name="' . $position . 'memtype">';
		while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC)) {
			echo '<option value='. $row3['memtype'];
			if ($row3['memtype'] == $row['memType']) {echo ' SELECTED';}
			echo '>' . $row3['memDesc'];
		}
		echo '</select>';
		echo '<input type="hidden" name="' . substr($position, 0, -1) . 'id" value="' . $row['id'] . '" /><br /><br />';
		
	}
	echo '<p><input type="submit" name="submit" value="Submit" /></p>
		<input type="hidden" name="submitted" value="TRUE" />
		<input type="hidden" name="cardno" value="' . $cn . '" /></form><br /><br />';
} elseif ($num_rows == 1) { // One member listed.
	echo '<h2>Edit a Household.</h2>
	<h3>Card Number: ' . $cn . '</h3>';
	// Get the user's information.
	while ($row = mysql_fetch_array($result)) {
		
		$query2 = "SELECT staff_no, staff_desc FROM staff ORDER BY staff_no ASC";
		$query3 = "SELECT memtype, memDesc FROM memtype ORDER BY memtype ASC";
		$result2 = @mysql_query($query2);
		$result3 = @mysql_query($query3);
		if ($row['personNum'] == 1) {$position = 'ps_'; $title = 'Primary Shareholder';}
		// Create the form.
		if ($row["ChargeOk"] == 1) {$ChargeOk[$position] = ' CHECKED';} else {$ChargeOk[$position] = '';}
		if ($row["WriteChecks"] == 1) {$ChecksOk[$position] = ' CHECKED';} else {$ChecksOk[$position] = '';}
		
		echo '<form action="auto_mem_modify.php" method="post">
		<h3><u><input type="checkbox" name="' . substr($position, 0, -1) . '" CHECKED />  ' . $title . '</u></h3>
		<p>First Name: <input type="text" name="' . $position . 'first_name" size="15" maxlength="15" value="' . $row["FirstName"] . '" /></p>
		<p>Last Name: <input type="text" name="' . $position . 'last_name" size="15" maxlength="30" value="' . $row["LastName"] . '" /></p>';
		if ($row["staff"] == 1 || $row["staff"] == 2 || $row["staff"] == 5) {echo '<p>House Charge? <input type="checkbox" name="' . $position . 'charge_ok"' . $ChargeOk[$position] . ' /></p>';}
		echo '<p>Write Checks? <input type="checkbox" name="' . $position . 'checks_ok"' . $ChecksOk[$position] . ' /></p>
		<p>Discount: <input type="text" name="' . $position . 'discount" size="3" maxlength="2" value="' . $row["Discount"] . '" />%</p>
		<p>Member Type: <select name="' . $position . 'staff">';
		while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
			echo '<option value='. $row2['staff_no'];
			if ($row2['staff_no'] == $row['staff']) {echo ' SELECTED';}
			echo '>' . $row2['staff_desc'];
		}
		echo '</select>
		<p>Member Status: <select name="' . $position . 'memtype">';
		while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC)) {
			echo '<option value='. $row3['memtype'];
			if ($row3['memtype'] == $row['memType']) {echo ' SELECTED';}
			echo '>' . $row3['memDesc'];
		}
		echo '</select>';
		echo '<input type="hidden" name="' . substr($position, 0, -1) . 'id" value="' . $row['id'] . '" /><br /><br />';
		
	}
	$position = 'ss_';
	$title = 'Secondary Shareholder';
	$query2 = "SELECT staff_no, staff_desc FROM staff ORDER BY staff_no ASC";
	$query3 = "SELECT memtype, memDesc FROM memtype ORDER BY memtype ASC";
	$result2 = @mysql_query($query2);
	$result3 = @mysql_query($query3);
	// Create the form.
	$ChecksOk[$position] = ' CHECKED';
	echo '<form action="auto_mem_modify.php" method="post">
	<h3><u><input type="checkbox" name="' . substr($position, 0, -1) . '" />  ' . $title . '</u></h3>
	<p>First Name: <input type="text" name="' . $position . 'first_name" size="15" maxlength="15" /></p>
	<p>Last Name: <input type="text" name="' . $position . 'last_name" size="15" maxlength="30" /></p>';
	echo '<p>Write Checks? <input type="checkbox" name="' . $position . 'checks_ok"' . $ChecksOk[$position] . ' /></p>
	<p>Discount: <input type="text" name="' . $position . 'discount" size="3" maxlength="2" value="2" />%</p>
	<p>Member Type: <select name="' . $position . 'staff">';
	while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		echo '<option value='. $row2['staff_no'];
		if ($row2['staff_no'] == 0) {echo ' SELECTED';}
		echo '>' . $row2['staff_desc'];
	}
	echo '</select>
	<p>Member Status: <select name="' . $position . 'memtype">';
	while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC)) {
		echo '<option value='. $row3['memtype'];
		if ($row3['memtype'] == 1) {echo ' SELECTED';}
		echo '>' . $row3['memDesc'];
	}
	echo '</select>';
	echo '<input type="hidden" name="' . substr($position, 0, -1) . 'id" value="insert" /><br /><br />';
	echo '<p><input type="submit" name="submit" value="Submit" /></p>
		<input type="hidden" name="submitted" value="TRUE" />
		<input type="hidden" name="cardno" value="' . $cn . '" /></form><br /><br />';
	
} elseif ($num_rows > 2) { // Too many matches
	echo '<h1 id="mainhead">Page Error</h1>
	<p class="error">You are trying to modify a household with more than two members.</p><p><br />
	Query 1: ' . $query . '<br />
	Query 2: ' . $query2 . '<br />
	Query 3: ' . $query3 . '<br />
	$num_rows: ' . $num_rows . '<br /></p>';
} else { // Not a valid Member ID
	echo '<h1 id="mainhead">Page Error</h1>
	<p class="error">You are trying to modify a non-existant household.</p><p><br /><br /></p>';
}

mysql_close(); // Close the DB connection.

include ('./includes/footer.html');

?>
