<?php # Member Editing Page



// A page to view and edit a member's details.
$page_title='Edit a Member';
include ('./includes/header.html');

// Check for a valid user ID, through GET or POST.

if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // Accessed through view_users.php.
	$id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Accessed through form submission.
	$id = $_POST['id'];
} else { // No valid ID, kill the script.
	echo '<h1 id="mainhead">Page Error</h1>
	<p class="error">This page has been accessed in error.</p>
	<p class="error">You have to <b><a href="auto_mem_find.php">find a member</a></b> before you can edit them.</p><p><br /><br /></p>'; 
	include ('./includes/footer.html');
	exit();
}

require_once('mysql_connect.php'); // Connect to the database.

if (isset($_POST['submitted'])) { // If the form has been submitted, check the new data and update the record.
	
	// Initialize the errors array.
	$errors = array();
	
	// Validate the form data.
	if (empty($_POST['first_name'])) {
		
		$errors[] = 'You left their first name blank.';
		
	} else {
		$fn = escape_data($_POST['first_name']); // Store the first name.
	}
	
	if (empty($_POST['last_name'])) {
		
		$errors[] = 'You left their last name blank.';
		
	} else {
		$ln = escape_data($_POST['last_name']); // Store the last name.
	}
	if ((isset($_POST['charge_ok'])) && ($_POST['charge_ok'] = 'on')) {$ChargeOk = 1;} elseif ((isset($_POST['charge_ok'])) && ($_POST['charge_ok'] = 'off')) {$ChargeOk = 0;} else {$ChargeOk = 0;} 
	if (!isset($_POST['checks_ok'])) {$_POST['checks_ok'] = 'off';}
	if (empty($_POST['discount'])) {
		
		$errors[] = 'You left their discount blank.';
		
	} elseif ($_POST['discount'] > 15) {
	
		$errors[] = 'You entered a discount greater than the maximum.';
	
	} else {
		$d = escape_data($_POST['discount']); // Store the discount.
	}
	
	if (empty($errors)) {
		if ($_POST['staff'] == 6) {$Type = 'reg';} else {$Type = 'pc';}	
		$staff = $_POST['staff'];
		$memtype = $_POST['memtype'];
		if ($_POST['checks_ok'] == 'on') {$WriteCheck=1;} else {$WriteCheck=0;}
		$query = "UPDATE custdata SET FirstName='$fn', LastName='$ln', WriteChecks=$WriteCheck, discount=$d, memType=$memtype, Type='$Type', staff=$staff, ChargeOk=$ChargeOk WHERE id=$id";
		$result = @mysql_query($query);
			
		if (mysql_affected_rows() == 1) { // If the query was successful.
				
			echo '<h1 id="mainhead">Edit a Member</h1>
			<p>The member has been edited.</p><p><br /><br /></p>';
				
		} else { // The query was unsuccessful.
				
			echo '<h1 id="mainhead">System Error</h1>
			<p class="error">There are two possibilities:<br />
			<b>1.)</b> The member could not be edited due to a system error.<br />
			<b>2.)</b> Nothing was changed.</p>';
			echo '<p>' . mysql_error() . '<br /><br />Quere: ' . $query . '</p>';

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
$query = "SELECT * FROM custdata WHERE id=$id";
$query2 = "SELECT staff_no, staff_desc FROM staff ORDER BY staff_no ASC";
$query3 = "SELECT memtype, memDesc FROM memtype ORDER BY memtype ASC";
$result = @mysql_query($query);
$result2 = @mysql_query($query2);
$result3 = @mysql_query($query3);

if (mysql_num_rows($result) == 1) {  // Valid id show the form.
	
	// Get the user's information.
	$row = mysql_fetch_array($result);
	
	// Create the form.
	if ($row["ChargeOk"] == 1) {$ChargeOk = ' CHECKED';} else {$ChargeOk = '';}
	if ($row["WriteChecks"] == 1) {$ChecksOk = ' CHECKED';} else {$ChecksOk = '';}
	echo '<h2>Edit a Member.</h2>
	<form action="auto_mem_edit.php" method="post">
	<p>Card Number: ' . $row["CardNo"] . '</p>
	<p>First Name: <input type="text" name="first_name" size="15" maxlength="15" value="' . $row["FirstName"] . '" /></p>
	<p>Last Name: <input type="text" name="last_name" size="15" maxlength="30" value="' . $row["LastName"] . '" /></p>';
	if ($row["staff"] == 1 || $row["staff"] == 2 || $row["staff"] == 5) {echo '<p>House Charge? <input type="checkbox" name="charge_ok"' . $ChargeOk . ' /></p>';}
	echo '<p>Write Checks? <input type="checkbox" name="checks_ok"' . $ChecksOk . ' /></p>
	<p>Discount: <input type="text" name="discount" size="3" maxlength="2" value="' . $row["Discount"] . '" />%</p>
	<p>Member Type: <select name="staff">';
	while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		echo '<option value='. $row2['staff_no'];
		if ($row2['staff_no'] == $row['staff']) {echo ' SELECTED';}
		echo '>' . $row2['staff_desc'];
	}
	echo '</select>
	<p>Member Status: <select name="memtype">';
	while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC)) {
		echo '<option value='. $row3['memtype'];
		if ($row3['memtype'] == $row['memType']) {echo ' SELECTED';}
		echo '>' . $row3['memDesc'];
	}
	echo '</select>';
	echo '<p><input type="submit" name="submit" value="Submit" /></p>
	<input type="hidden" name="submitted" value="TRUE" />
	<input type="hidden" name="id" value="' . $id . '" />
	</form>';
	
} else { // Not a valid Member ID
	echo '<h1 id="mainhead">Page Error</h1>
	<p class="error">This page has been accessed in error.</p><p><br /><br /></p>';
}
mysql_close(); // Close the DB connection.

include ('./includes/footer.html');

?>
