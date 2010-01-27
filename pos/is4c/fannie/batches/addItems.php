<html>
<head>
<link rel="stylesheet" href="../style.css" type="text/css" />
</head>
<body>
<?
include_once('../src/funct1Mem.php');

foreach ($_POST AS $key => $value) {
    $$key = $value;
    //echo $key . ': '. $$key . "<br>";
}

$db = mysql_connect('localhost','root');
mysql_select_db('is4c_op',$db);

$maxBatchIDQ = "SELECT MAX(batchID) FROM batches";
$maxBatchIDR = mysql_query($maxBatchIDQ);
$maxBatchIDW = mysql_fetch_array($maxBatchIDR);

$batchID = $maxBatchIDW[0];

$batchInfoQ = "SELECT * FROM batches WHERE batchID = $batchID";
$batchInfoR = mysql_query($batchInfoQ);
$batchInfoW = mysql_fetch_row($batchInfoR);
 
//$batchID = 1;
if(isset($_GET['batchID'])){
   $batchID = $_GET['batchID'];
}

if(isset($_GET['submit'])){
   $upc = $upc =str_pad($_GET['upc'],13,0,STR_PAD_LEFT);
   $salePrice = $_GET['saleprice'];
   if(isset($_GET['delete'])){
      $del = $_GET['delete'];
   }
   ;
?>   <script language="javascript">
    parent.frames[1].location.reload();
    </script>
<?
} else {
	$upc = '';
	$salePrice = '';
	$del = 0;
}


echo "<form action=addItems.php action=GET>";
echo "<table border=0><tr><td><b>Sale Price: </b><input type=text name=saleprice size=6></td>";
echo "<td><b>UPC: </b><input type=text name=upc></td>";
echo "<input type=hidden name=batchID value=$batchID>";
echo "<td><b>Delete</b><input type=checkbox name=delete value=1>";
echo "<td><input type=submit name=submit value=submit></td></tr></table>";

//echo "this is upc" . $upc;

$selBListQ = "SELECT * FROM batchList WHERE upc = $upc 
              AND batchID = $batchID";
$selBListR = mysql_query($selBListQ);
$selBListN = mysql_num_rows($selBListR);

$startDate = $batchInfoW[1];
$endDate = $batchInfoW[2];

$checkItemQ = "SELECT l.* FROM batchList AS l JOIN batches AS b ON b.batchID = l.batchID
               where upc = $upc and b.endDate >= '$startDate'";
$checkItemR = mysql_query($checkItemQ);
$checkItemN = mysql_num_rows($checkItemR);
$checkItemW = mysql_fetch_row($checkItemR);


if($del == 1){
   $delBListQ = "DELETE FROM batchList WHERE upc = $upc AND
                batchID = $batchID";
   $delBListR = mysql_query($delBListQ);
}else{
      if($selBListN == 0){
         $insBItemQ = "INSERT INTO batchList(upc,batchID,salePrice)
                   VALUES('$upc',$batchID,$salePrice)";
         //echo $insBItemQ;
         $insBItemR = mysql_query($insBItemQ);
      }else{
         $upBItemQ = "UPDATE batchList SET salePrice=$salePrice WHERE upc = '$upc' 
                   AND batchID = $batchID";
         //echo $upBItemQ;
         $upBItemR = mysql_query($upBItemQ);
      }
}
?>
</body>
</html>