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

$_SESSION['batchID'] = 1;

// include($_SERVER["DOCUMENT_ROOT"].'/src/funct1Mem.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');

//------------------------------------

// How many records per page.
$display = 10;

$query = "SELECT COUNT(batchID) FROM batches ORDER BY batchID ASC"; // Count the number of records.
$result = @mysql_query($query); // Run the query.
$row = mysql_fetch_array($result, MYSQL_NUM); // Retrieve the query.
$num_records = $row[0]; // Store the results.

// Determine how many pages there are.
if (isset($_GET['np'])) { // Already been determined.
	$num_pages = $_GET['np'];
} else { // Need to determine.
	
	// Calculate the number of pages.
	if ($num_records > $display) { // If there are more than one page of records.
		$num_pages = ceil ($num_records/$display);
	} else {
		$num_pages = 1; // There is only one page.
	}
} // End of page count IF.

// Determine where the page is starting.
if (isset($_GET['s'])) { // If we've been through this before.
	$start = $_GET['s'];
} else { // If this is the first time.
	$start = 0;
}

//------------------------------------

$batchListQ= "SELECT b.batchID,b.batchName,b.batchType,DATE(b.startDate),b.endDate 
	FROM batches as b
	ORDER BY b.batchID DESC
	LIMIT $start, $display";

$batchListR = mysql_query($batchListQ);

$maxBatchQ = "SELECT max(batchID) FROM batches";
$maxBatchR = mysql_query($maxBatchQ);
$maxBatchW = mysql_fetch_row($maxBatchR);
$newBatch = $maxBatchW[0] + 1; 

?>

<form name='addBatch' action = 'display.php?batchID=<? echo $newBatch; ?>' method='POST' target=_blank>
	<table>
		<tr>
			<td>&nbsp;</td>
			<td>Batch Name</td>
			<td>Start Date</td>
			<td>End Date</td>
		</tr>
		<tr>
			<td>&nbsp;
				<select name=batchType>
		        	<option value=1>CAP Sale</option>
		        	<option value=1>Regular Sale</option>
		        	<option value=1>Price Change</option>
				</select>
			</td>
			<td><input type=text name=batchName></td>
	     	<td><input name="startDate" onfocus="showCalendarControl(this);" type="text" size=10></td>
	     	<td><input name="endDate" onfocus="showCalendarControl(this);" type="text" size=10></td>
	     	<td><input type=submit name=submit value=Add></td>
		</tr>
	</table>
</form>

<?
echo "<table border=0 cellspacing=0 cellpadding=5 width=90%>";
echo "<th>Batch Name<th>Batch Type<th>Start Date<th>End Date";
$bg = '#eeeeee';
while($batchListW = mysql_fetch_row($batchListR)){
   	$start = $batchListW[3];
   	$end = $batchListW[4];
   	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
	echo "<tr bgcolor='$bg'><td><a href=display.php?batchID=$batchListW[0] target=_blank>";
   	echo "$batchListW[1]</a></td>";
   	echo "<td>$batchListW[2]</td>";
   	echo "<td>$batchListW[3]</td>";
   	echo "<td>$batchListW[4]</td>";
//	echo "<td><a href='batchList.php?batchID=$batchListW[0]'>Print</a></td>";
//	echo "<td><a href='deleteBatch.php?batchID=$batchListW[0]'><font size='-1'>Delete</font></a>";
	echo "</td></tr>";
}
echo "</table>";


// Make the links to other pages, if necessary.
if ($num_pages > 1) {
	echo '<br /><p>';
	// Determine what page the script is on.
	$current_page = ($start/$display) + 1;
	
	// If it's not on the first page, make a Previous button.
/*	if ($current_page != 1) {
		echo '<a href="auto_index.php?s=' . ($start - $display) . '&np=' . $num_pages . '">Previous</a> ';
	}
*/	
	// Make all the numbered pages.
	for ($i = 1; $i <= $num_pages; $i++) {
		if ($i != $current_page) {
		echo '<a href="auto_index.php?s=' . ($display * ($i - 1)) . '&np=' . $num_pages . '">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	}
	
	// If it's not the last page, make a Next button.
/*	if ($current_page != $num_pages) {
		echo '<a href="auto_index.php?s=' . ($start + $display) . '&np=' . $num_pages . '">Next</a> ';
	}
*/	echo '</p>';
} // End of links section.
?>