<?
$_SESSION['batchID'] = 1;

include('src/funct1Mem.php');
$batchListQ= "SELECT b.batchID,b.batchName,t.typeDesc,b.startDate,b.endDate 
          FROM batches as b JOIN batchType as t ON b.batchType = t.batchTypeID
          ORDER BY b.batchID DESC";

$batchListR = mssql_query($batchListQ,$db);

$maxBatchQ = "SELECT max(batchID) FROM batches";
$maxBatchR = mssql_query($maxBatchQ);
$maxBatchW = mssql_fetch_row($maxBatchR);
$newBatch = $maxBatchW[0] + 1; 

?>
<html>
<head>
<link href="CalendarControl/CalendarControl.css"
      rel="stylesheet" type="text/css">
<script src="CalendarControl/CalendarControl.js"
        language="javascript"></script>
</head>
<body>
<link href="CalendarControl/CalendarControl.css"
      rel="stylesheet" type="text/css">
<script src="CalendarControl/CalendarControl.js"
        language="javascript"></script>

<form name='addBatch' action = 'display.php?batchID=<? echo $newBatch; ?>' method='POST'>
<table><tr><td>Batch Type</td><td>Batch Name</td><td>Start Date</td><td>End Date</td></tr>
<tr><td><select name=batchType>
          <option value=1>CAP</option>
          <option value=2>HA</option>
          <option value=3>Other Sale</option>
          <option value=4>Price Change</option>
          <option value=5>Delete</option>
          <option value=6>MOS</option>
      </select></td><td><input type=text name=batchName></td>
     <td><input name="startDate" onfocus="showCalendarControl(this);" type="text"></td>
     <td><input name="endDate" onfocus="showCalendarControl(this);" type="text"></td>
     <td><input type =submit name=submit value =Add></td></tr>
</table>

<?
echo "<table border=1>";
echo "<th>Batch Name<th>Batch Type<th>Start Date<th>End Date";
while($batchListW = mssql_fetch_row($batchListR)){
   echo "<tr><td><a href=display.php?batchID=$batchListW[0]>";
   echo "$batchListW[1]</a></td>";
   echo "<td>$batchListW[2]</td>";
   echo "<td>$batchListW[3]</td>";
   echo "<td>$batchListW[4]</td></tr>";
}
?>

