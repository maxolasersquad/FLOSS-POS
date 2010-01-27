<?
$_SESSION['batchID'] = 1;

include('../src/funct1Mem.php');
$batchListQ= "SELECT b.batchID,b.batchName,b.batchType,DATE(b.startDate),b.endDate 
          FROM batches as b
          ORDER BY b.batchID DESC";

$batchListR = mysql_query($batchListQ,$db);

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
		        	<option value=1>Regular Sale</option>
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
echo "<table border=0 cellspacing=3 cellpadding =3>";
echo "<th>Batch Name<th>Batch Type<th>Start Date<th>End Date";
while($batchListW = mysql_fetch_row($batchListR)){
   	$start = $batchListW[3];
   	$end = $batchListW[4];
   	echo "<tr><td><a href=display.php?batchID=$batchListW[0] target=_blank>";
   	echo "$batchListW[1]</a></td>";
   	echo "<td>$batchListW[2]</td>";
   	echo "<td>$batchListW[3]</td>";
   	echo "<td>$batchListW[4]</td>";
//  echo "<td><a href='batchList.php?batchID=$batchListW[0]'>Print</a></td>";
   	echo "<td><a href='deleteBatch.php?batchID=$batchListW[0]'><font size='-1'>Delete</font></a></td></tr>";
}
echo "</table>";
?>