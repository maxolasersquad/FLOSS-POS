<?
include('src/funct1Mem.php');


$batchID = $_GET['batchID'];
//echo $batchID;

if($_POST['submit']=="submit"){
   foreach ($_POST AS $key => $value) {
     $batchID = $_POST['batchID'];

        //echo "values".$key . ": ".$value . "<br>";
     if(substr($key,0,4) == 'sale'){
        $$key = $value;
        $upc1 = substr($key,4);
	$queryTest = "UPDATE batchList SET salePrice = $value WHERE upc = '$upc1' and batchID = $batchID";
        //echo $queryTest . "<br>";
	$resultTest = mssql_query($queryTest);
      }

     if(substr($key,0,3) == 'del'){
       $$key = $value;
       $upc1 = substr($key,3);
       $delItmQ = "DELETE FROM batchList WHERE upc = '$upc1' and batchID = $batchID";
       $delItmR = mssql_query($delItmQ);
     }
   }   
}
$batchInfoQ = "SELECT * FROM batches WHERE batchID = $batchID";
$batchInfoR = mssql_query($batchInfoQ);
$batchInfoW = mssql_fetch_row($batchInfoR);


$selBItemsQ = "SELECT b.*,p.*  from batchList as b LEFT JOIN 
               Products as p ON b.upc = p.upc WHERE batchID = $batchID 
               ORDER BY b.listID DESC";
//echo $selBItemsQ;
$selBItemsR = mssql_query($selBItemsQ);

echo "<form action=batches.php method=POST>";
echo "<table border=1>";
echo "<tr><td>Batch Name: <font color=blue>$batchInfoW[3]</font></td><td>Start Date: </td><td>End Date: </td></tr>";
echo "<th>UPC<th>Description<th>Normal Price<th>Sale Price<th>Delete";
while($selBItemsW = mssql_fetch_row($selBItemsR)){
   $upc = $selBItemsW[1];
   $field = 'sale'.$upc;
   $del = 'del'.$upc;
   //echo $del;
   echo "<tr><td>$selBItemsW[1]</td><td>$selBItemsW[6]</td>";
   echo "<td>$selBItemsW[7]</td><td><input type=text name=$field field value=$selBItemsW[3]></td>";
   echo "<input type=hidden name=upc value='$upc'>";
   echo "<td><input type=checkbox value=1 name=$del></td></tr>";
}
echo "<input type=hidden value=$batchID name=batchID>";
echo "<tr><td><input type=submit name=submit value=submit></td><td><a href=forceBatch.php?batchID=$batchID target=blank>Force Sale Batch Now</a></td></tr>";
echo "</form>";



?>
