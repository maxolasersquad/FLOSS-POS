<html>
<head>
<link rel="stylesheet" href="../style.css" type="text/css" />
</head>
<body>
<?
include('../src/funct1Mem.php');

if(isset($_GET['batchID'])){
   $batchID = $_GET['batchID'];
}

//echo $batchID;
if (isset($_POST['datechange'])){
  $batchID = $_POST['batchID'];
  $startdate = $_POST['startdate'];
  $enddate = $_POST['enddate'];
  
  $dateQ = "update batches set startdate='$startdate',
            enddate='$enddate' where batchID=$batchID";
  $dateR = mysql_query($dateQ);
}
else if(isset($_POST['submit'])){
   foreach ($_POST AS $key => $value) {
     $batchID = $_POST['batchID'];
     
     //echo "values".$key . ": ".$value . "<br>";
     if(substr($key,0,4) == 'sale'){
        $$key = $value;
        $upc1 = substr($key,4);
	$queryTest = "UPDATE batchList SET salePrice = $value WHERE upc = '$upc1' and batchID = $batchID";
        //echo $queryTest . "<br>";
	$resultTest = mysql_query($queryTest);
      }

     if(substr($key,0,3) == 'del'){
       $$key = $value;
       $upc1 = substr($key,3);
       $delItmQ = "DELETE FROM batchList WHERE upc = '$upc1' and batchID = $batchID";
       $delItmR = mysql_query($delItmQ);
     }
   }   
}

$batchInfoQ = "SELECT * FROM batches WHERE batchID = $batchID";
$batchInfoR = mysql_query($batchInfoQ);
$batchInfoW = mysql_fetch_row($batchInfoR);


$selBItemsQ = "SELECT b.*,p.*  from batchList as b LEFT JOIN 
               products as p ON b.upc = p.upc WHERE batchID = $batchID 
               ORDER BY b.listID DESC";
//echo $selBItemsQ;
$selBItemsR = mysql_query($selBItemsQ);

echo "<form action=batches.php method=POST>";
echo "<table border=0 cellspacing=10 cellpadding=0>";
echo "<tr><td>Batch Name: <font color=blue>$batchInfoW[3]</font></td>";
echo "<form action=batches.php method=post>";
echo "<td>Start Date: <input type=text name=startdate value=\"$batchInfoW[1]\" size=18></td>";
echo "<td>End Date: <input type=text name=enddate value=\"$batchInfoW[2]\" size=10></td>";
echo "<td><input type=submit value=\"Change Dates\" name=datechange></td></tr>";
echo "<input type=hidden name=batchID value=$batchID>";
echo "</form>";
echo "<th>UPC<th>Description<th>Normal Price<th>Sale Price<th>Delete";
echo "<form action=batches.php method=POST>";
while($selBItemsW = mysql_fetch_row($selBItemsR)){
   $upc = $selBItemsW[1];
   $field = 'sale'.$upc;
   $del = 'del'.$upc;
   //echo $del;
   echo "<tr><td align=center>$selBItemsW[1]</td><td>$selBItemsW[6]</td>";
   echo "<td align=right>$selBItemsW[7]</td><td align=center><input type=text name=$field field value=$selBItemsW[3] size=8></td>";
   echo "<input type=hidden name=upc value='$upc'>";
   echo "<td><input type=checkbox value=1 name=$del></td></tr>";
}
echo "<input type=hidden value=$batchID name=batchID>";
echo "<tr><td><input type=submit name=submit value=submit></td>";
echo "<td><a href=forceBatch.php?batchID=$batchID target=blank>Force Batch Now</a></td>";
echo "<td><a href=resetBatch.php?batchID=$batchID target=blank>Reset Batch Now</a></td>";
//echo "<td><a href='auto_index.php target=blank>Return to batch list</a></td>";
echo "</tr></form>";



?>
</body>
</html>