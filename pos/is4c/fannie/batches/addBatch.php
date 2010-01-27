<?
include('../src/funct1Mem.php');
$db = mysql_connect('localhost','root');
mysql_select_db('is4c_op',$db);

foreach ($_POST AS $key => $value) {
    $$key = $value;
    //echo $key . ': '. $$key . "<br>";
}
if($batchType == 6){
  $discounttype = 2;
}else{
  $discounttype = 1;
}

$addBatchQ = "INSERT INTO batches(startDate,endDate,batchName,batchType,discountType)
              VALUES('$startDate','$endDate','$batchName',$batchType,$discounttype)";
//echo $addBatchQ;
$addBatchR = mysql_query($addBatchQ);

$selNewBatchQ = "SELECT MAX(batchID) FROM batches";
//echo $selNewBatchQ;
$selNewBatchR = mysql_query($selNewBatchQ);
$selNewBatchW = mysql_fetch_row($selNewBatchR);

$batchID = $selNewBatchW[0];
//echo $batchID;

if($_POST['submit']=="submit"){
   foreach ($_POST AS $key => $value) {
     $batchID = $_POST['batchID'];
      //echo '<br>I should not be here:'.$batchID;
     if(substr($key,0,4) == 'sale'){
        $$key = $value;
        $upc1 = substr($key,4);
        //echo $key . ": ".$value . "<br>";
        $queryTest = "UPDATE batchList SET salePrice = $value WHERE upc = '$upc1'";
        //echo $queryTest . "<br>";
        $resultTest = mysql_query($queryTest);
      }
   }

   //$upBItemsQ = "UPDATE batchList SET salePrice=$salePrice WHERE upc = '$upc'";
   //echo $upBItemsQ;
   //$upBItemsR = mssql_query($upBItemsQ);
}

//echo '<br>I am here' . $batchID;
$selBItemsQ = "SELECT b.*,p.*  from batchList as b LEFT JOIN 
               products as p ON b.upc = p.upc WHERE batchID = $batchID ";
               //ORDER BY b.upc";

//echo $selBItemsQ;
$selBItemsR = mysql_query($selBItemsQ);

echo "<form action=batches.php method=POST>";
echo "<table border=1>";
echo "<th>UPC<th>Description<th>Normal Price<th>Sale Price";
while($selBItemsW = mysql_fetch_row($selBItemsR)){
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
