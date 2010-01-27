<?
include('src/funct1Mem.php');
$batchID = $_GET['batchID'];

if($_POST['submit']=="submit"){
   foreach ($_POST AS $key => $value) {
     $batchID = $_POST['batchID'];
     if(substr($key,0,4) == 'sale'){
        $$key = $value;
        $upc1 = substr($key,4);
        //echo $key . ": ".$value . "<br>";
	$queryTest = "UPDATE batchList SET salePrice = $value WHERE upc = '$upc1'";
        //echo $queryTest . "<br>";
        $resultTest = mssql_query($queryTest);
      }
   }   
   
   //$upBItemsQ = "UPDATE batchList SET salePrice=$salePrice WHERE upc = '$upc'";
   //echo $upBItemsQ;
   //$upBItemsR = mssql_query($upBItemsQ);
}

$selBItemsQ = "SELECT b.*,p.*  from batchList as b LEFT JOIN 
               Products as p ON b.upc = p.upc WHERE batchID = $batchID 
               ORDER BY b.upc";

$selBItemsR = mssql_query($selBItemsQ);

echo "<form action=batches.php method=POST>";
echo "<table border=1>";
echo "<th>UPC<th>Description<th>Normal Price<th>Sale Price";
while($selBItemsW = mssql_fetch_row($selBItemsR)){
   $upc = $selBItemsW[1];
   $field = 'sale'.$upc;
   echo "<tr><td>$selBItemsW[1]</td><td>$selBItemsW[5]</td>";
   echo "<td>$selBItemsW[6]</td><td><input type=text name=$field field value=$selBItemsW[3]></td></tr>";
   echo "<input type=hidden name=upc value='$upc'>";
}
echo "<input type=hidden value=$batchID name=batchID>";
echo "<tr><td><input type=submit name=submit value=submit></td></tr>";
echo "</form>";



?>
