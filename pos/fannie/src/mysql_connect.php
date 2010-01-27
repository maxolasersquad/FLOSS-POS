<?php # Script 8.1 - mysql_connect.php (Second version after 7.2)

// This file contains the database access information.
// This file also establishes a connection to MySQL and selects the database.

// Set the database access information as constants.
DEFINE ('DB_USER', 'is4cserver');
DEFINE ('DB_PASSWORD', '');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'is4c_op');

// Make the connection.
$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) or die ('Could not connect to MySQL.: ' . mysql_error() );

// Select the database.
@mysql_select_db (DB_NAME) or die ('Could not select the database: ' . msql_error() );

// Create a function for escaping the data.
function escape_data ($data) {
	
	// Address Magic Quotes.
	if (ini_get('magic_quotes_gpc')) {
		$data = stripslashes($data);
	}
	
	// Check for mysql_real_escape_string() support.
	if (function_exists('mysql_real_escape_string')) {
		global $dbc; // Need the connection.
		$data = mysql_real_escape_string (trim($data), $dbc);
	} else {
		$data = mysql_escape_string (trim($data));
	}
	
	// Return the escaped value.
	return $data;
} // End of function.

?>

