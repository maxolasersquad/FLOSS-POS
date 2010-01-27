<?

include_once('src/funct1Mem.php');

$upc = 4011;

$testLike = test_like($upc);
//echo $testLike;

$like = find_like_code($upc);

$upc = str_pad_upc($upc);


$result = like_coded_items($upc);
 
if($like > 0){
  echo "$upc is in like code = $like";
  echo "<table>";
  while($row = mssql_fetch_row($result)){
     echo "<tr><td>$row[0]</td><td>$row[1]</td></tr>";
   }
   echo "</table>";
}else{
  echo $like . ' this item is not in the like code list';
}

?>
