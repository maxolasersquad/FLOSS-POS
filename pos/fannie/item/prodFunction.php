<?
/*******************************************************************************

    Copyright 2007 Authors: Christof Von Rabenau - Whole Foods Co-op Duluth, MN
	Joel Brock - People's Food Co-op Portland, OR

	This is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This software is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
//	TODO -- Add javascript for batcher product entry popup window		~joel 2007-08-21

include_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');

function itemParse($upc){
    if(is_numeric($upc)){
		$upc = str_pad($upc,13,0,STR_PAD_LEFT);
        $queryItem = "SELECT * FROM products WHERE upc = '$upc'";
    }else{
        $queryItem = "SELECT * FROM products WHERE description LIKE '%$upc%' ORDER BY description";
    }
    $resultItem = mysql_query($queryItem);
   	$num = mysql_num_rows($resultItem);
   

    if($num == 0 || !$num){
        noItem();
        echo "<head><title>Enter New Item</title><link rel='STYLESHEET' href='../src/style.css' type='text/css'></head>";
        echo "<BODY onLoad='putFocus(0,1);'>";
        echo "<div id='box'><font color='red' size='3'>Item not found.  You are creating a new one.  </font>";
		if ($_SESSION["popup"] == 1) {
			$_SESSION["popup"] = 0;
			echo "<a href='javascript: self.close ()'>close</a>";
		} else {
			echo "<a href='../index.php'><font size='-1'>cancel</font></a>";
		}
		echo "<form name=pickSubDepartment action=auto_insertItem.php method=post>";
        echo "<table border=0 cellpadding=5>";
		echo "<tr><td align=right><b>UPC</b></td><td><font color='red'></font>
			<input type=text value=$upc name=upc></td>";
		echo "</tr><tr><td><b>Description</b></td><td>
			<input type=text size=30 name=descript></td>";
		echo "<td><b>Member Price</b></td><td>$<input type=text name=memberPrice size=6></td></tr><tr><td><b>Regular Price</b></td><td><input type=text name=price size=6></table></div><div id='box'>";
        echo "<table width='100%' border=0 cellpadding=5><tr>";
		echo "<th colspan=6>Dept & subDept</th></tr><tr><td colspan=6 align=center>";	
		/**
			**	BEGIN CHAINEDSELECTOR CLASS
			**/
				require($_SERVER["DOCUMENT_ROOT"].'/src/chainedSelectors.php');

				$DatabaseLink = mysql_connect("localhost","root");
				if(!$DatabaseLink)
				{
					print("Unable to connect to database!<br>\n");
					exit();
				}

				//select test database
				if(!(mysql_select_db("is4c_op", $DatabaseLink)))
				{
					print("Unable to use the test database!<br>\n");
					exit();
				}
				//prepare names
				$selectorNames = array(
					CS_FORM=>"pickSubDepartment", 
					CS_FIRST_SELECTOR=>"department", 
					CS_SECOND_SELECTOR=>"subdepartment");

				//		$department = $rowItem[12];
				//		$subdepartment = $rowItem[27];

				//query database, assemble data for selectors
				$Query = "SELECT d.dept_no AS dept_no,d.dept_name AS dept_name,s.subdept_no AS subdept_no,s.subdept_name AS subdept_name	
					FROM is4c_op.departments AS d, is4c_op.subdepts AS s
					WHERE d.dept_no = s.dept_ID
					ORDER BY d.dept_no, s.subdept_no";
			    if(!($DatabaseResult = mysql_query($Query, $DatabaseLink)))
			    {
			        print("The query failed!<br>\n");
			        exit();
			    }

			    while($row = mysql_fetch_object($DatabaseResult))
			    {
			    	$selectorData[] = array(
						CS_SOURCE_ID=>$row->dept_no, 
					    CS_SOURCE_LABEL=>$row->dept_name, 
					    CS_TARGET_ID=>$row->subdept_no, 
						CS_TARGET_LABEL=>$row->subdept_name);
				}            

		    	//instantiate class
		    	$subdept = new chainedSelectors(
					$selectorNames, 
			        $selectorData);
				?>
					<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
					<html>
					<head>
					<script type="text/javascript" language="JavaScript">
					<?php
					    $subdept->printUpdateFunction($row); //rowItem
					?>
					</script>
					</head>
					<body>
					<!-- <form name="pickSubDepartment" action="insertItem.php"> -->
					<?php
					    $subdept->printSelectors($row); //rowItem
					?>
					<!-- <input type="submit">
					</form> -->
					<script type="text/javascript" language="JavaScript">
					<?php
					    $subdept->initialize();
					?>
					</script>
					</body>
					</html>
				<?php
		   	   /**
				**	CHAINEDSELECTOR CLASS ENDS . . . . . . . NOW
				**/
		echo"</td></tr><tr><th>FS</th><th>Scale</th><th>QtyFrc</th><th>NoDisc</th><th>InUse</th>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=center><input type=checkbox value=1 name=FS";
        echo "></td><td align=center><input type=checkbox value=1 name=Scale";
        echo "></td><td align=center><input type=checkbox value=1 name=QtyFrc";
        echo "></td><td align=center><input type=checkbox value=1 name=NoDisc";
        echo "></td><td align=center><input type=checkbox value=1 name=inUse CHECKED";
        echo "></td>";
        // echo "<td align=center><input type=text align=right size=4 name=likeCode></td>";
        echo "</tr><tr><td align=right>$<input type=text value=0 name=deposit size=5></td><td colspan=5 align=left>Bottle deposit amount</td></tr>";
        echo "<tr><td><input type='submit' name='submit' value='submit'></td><td colspan=5>&nbsp;</td></tr> ";
        echo "</tr></table></div>";

    }elseif($num > 1){
        moreItems($upc);
			for($i=0;$i < $num;$i++){
        		$rowItem= mysql_fetch_array($resultItem);
	    		$upc = $rowItem['upc'];
	    		echo "<a href='../index.php?upc=$upc'>" . $upc . " </a>- " . $rowItem['description'] . " -- $" .$rowItem['normal_price']. "<br>";
    		}
    }else{
        oneItem($upc);
        	$likeCodeQ = "SELECT u.*,l.likeCodeDesc FROM upclike as u, likecodes as l
                      WHERE u.likecode = l.likecode and u.upc = '$upc'";
        	//echo $likeCodeQ; 
			$likeCodeR = mysql_query($likeCodeQ);
			$likeCodeRow= mysql_fetch_row($likeCodeR);
   			$likeCodeNum = mysql_num_rows($likeCodeR);

   	 		$listCodeQ = "SELECT * from likecodes";
   	 		$listCodeR = mysql_query($listCodeQ);
   	 		$listCodeRow = mysql_fetch_row($likeCodeR);

			$rowItem = mysql_fetch_array($resultItem);
			//echo $rowItem['upc'] . " - " . $rowItem['description'] . "<br>";
		echo "<head><title>Update Item</title>";
		
		echo "</head>";
        echo "<BODY onLoad='putFocus(0,2);'>";
        echo "<form name=pickSubDepartment action=auto_updateItems.php method=post>";
        echo "<div id='box'><table border=0 cellpadding=5 cellspacing=0>";
        echo "<tr><td align=right><b>UPC</b></td><td><font color='red'>".$rowItem[0]."</font><input type=hidden value='$rowItem[0]' name=upc></td>";
        echo "<td>&nbsp;</td><td>&nbsp;</td></tr><tr><td><b>Description</b></td><td><input type=text size=30 value='" . htmlspecialchars($rowItem[1], ENT_QUOTES) . "' name=descript>$rowItem[1]</td>";
        echo "<td><b>Price</b></td><td>$<input type=text value='$rowItem[2]' name=price size=10></td><td><b>Member Price</b></td><td>$<input type=text value='$rowItem[31]' name=memberPrice size=10></td></tr>";
			if($rowItem[6] <> 0){
	   			echo "<tr><td><font color=green><b>Sale Price:</b></font></td><td><font color=green>$rowItem[6]</font></td><td>";
           		echo "<font color=green>End Date:</td><td><font color=green>$rowItem[11]</font></td><tr>";
			}
		echo "</table></div><div id='box'>";
        echo "<table border=0 cellpadding=5 cellspacing=0 width='100%'><tr>";
        echo "<th>Dept & SubDept</th><th>FS</th><th>Scale</th><th>QtyFrc</th><th>NoDisc</th><th>In Use</th>";
        echo "</tr>";
        echo "<tr align=top>";
    	echo "<td align=left>";	
	   /**
		**	BEGIN CHAINEDSELECTOR CLASS
		**/
			require($_SERVER["DOCUMENT_ROOT"].'/src/chainedSelectors.php');

			$DatabaseLink = mysql_connect("localhost","root");
			if(!$DatabaseLink)
			{
				print("Unable to connect to database!<br>\n");
				exit();
			}
			if(!(mysql_select_db("is4c_op", $DatabaseLink)))
			{
				print("Unable to use the test database!<br>\n");
				exit();
			}
			$selectorNames = array(
				CS_FORM=>"pickSubDepartment", 
				CS_FIRST_SELECTOR=>"department", 
				CS_SECOND_SELECTOR=>"subdepartment");

			$Query = "SELECT d.dept_no AS dept_no,d.dept_name AS dept_name,s.subdept_no AS subdept_no,s.subdept_name AS subdept_name	
				FROM is4c_op.departments AS d, is4c_op.subdepts AS s
				WHERE d.dept_no = s.dept_ID
				ORDER BY d.dept_no, s.subdept_no";

		    if(!($DatabaseResult = mysql_query($Query, $DatabaseLink)))
		    {
		        print("The query failed!<br>\n");
		        exit();
		    }
		    while($row = mysql_fetch_object($DatabaseResult))
		    {
		    	$selectorData[] = array(
					CS_SOURCE_ID=>$row->dept_no, 
				    CS_SOURCE_LABEL=>$row->dept_name, 
				    CS_TARGET_ID=>$row->subdept_no, 
					CS_TARGET_LABEL=>$row->subdept_name);
			}            

	    	$subdept = new chainedSelectors(
				$selectorNames, 
		        $selectorData);
			?>
				<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
				<html>
				<head>
				<script type="text/javascript" language="JavaScript">
				<?php
				    $subdept->printUpdateFunction($rowItem);
				?>
				</script>
				</head>
				<body>
				<!-- <form name="pickSubDepartment" action="auto_updateItems.php"> -->
				<?php
				    $subdept->printSelectors($rowItem);
				?>
				<!-- <input type="submit">
				</form> -->
				<script type="text/javascript" language="JavaScript">
				<?php
				    $subdept->initialize();
				?>
				</script>
				</body>
				</html>
			<?php			
	   	   /**
			**	CHAINEDSELECTOR CLASS ENDS . . . . . . . NOW
			**/
//                echo " </td>";
                echo "</td><td align=center><input type=checkbox value=1 name=FS";
                        if($rowItem["foodstamp"]==1){
                                echo " checked";
                        }
                echo "></td><td align=center><input type=checkbox value=1 name=Scale";
                        if($rowItem["scale"]==1){
                                echo " checked";
                        }
                echo "></td><td align=center><input type=checkbox value=1 name=QtyFrc";
                        if($rowItem["qttyEnforced"]==1){
                                echo " checked";
                        }
                echo "></td><td align=center><input type=checkbox value=0 name=NoDisc";
                        if($rowItem["discount"]==0){
                                echo " checked";
                        }
                echo "></td><td align=center><input type=checkbox value=1 name=inUse";
                        if($rowItem["inUse"]==1){
                                echo " checked";
                        }
		                echo "></td><td align=center>";
//						if(!empty($likeCodeRow[1])){
//							$likecode = $likeCodeRow[1];
//						}else{
//							$likecode = '';
//						}
// 						echo "<input type=text align=right size=4 value='$likecode' name=likeCode>";
//						echo "</td><td>$likeCodeRow[2]</td><td><a href=./testSales.php?upc=$upc target=blank>Click for History</a></tr>";

                echo "</td></tr><tr><td>&nbsp;</td><td colspan='2' align='right'>$<input type='text'";
 					if (!isset($rowItem[25]) || $rowItem[25] == 0) {
						echo "value='0'";
					} else {
						echo "value='$rowItem[25]'"; 
					}
				echo "name='deposit' size='5'></td>";
				echo "<td colspan='3' align='left'>Bottle deposit</td></tr>";
               	echo "<tr><td><input type='submit' name='submit' value='submit'>&nbsp;<a href='../index.php'><font size='-1'>cancel</font></a>";
				echo "</td><td colspan=5>&nbsp;</td></tr></table></div> "; 
	}
    return $num;
}

function likedtotable($query,$border,$bgcolor)
{
        $results = mysql_query($query) or
                die("<li>errorno=".mysql_errno()
                        ."<li>error=" .mysql_error()
                        ."<li>query=".$query);
        $number_cols = mysql_num_fields($results);
        //display query
        //echo "<b>query: $query</b>";
        //layout table header
        echo "<table border = $border bgcolor=$bgcolor>\n";
        echo "<tr align left>\n";
        /*for($i=0; $i<5; $i++)
        {
                echo "<th>" . mysql_field_name($results,$i). "</th>\n";
        }
        echo "</tr>\n"; *///end table header
        //layout table body
        while($row = mysql_fetch_row($results))
        {
                echo "<tr align=left>\n";
                echo "<td >";
                        if(!isset($row[0]))
                        {
                                echo "NULL";
                        }else{
                                 ?>
                                 <a href="itemMaint.php?upc=<? echo $row[0]; ?>">
                                 <? echo $row[0]; ?></a>
                        <? echo "</td>";
                        }
                for ($i=1;$i<$number_cols-1; $i++)
                {
                echo "<td>";
                        if(!isset($row[$i])) //test for null value
                        {
                                echo "NULL";
                        }else{
                                echo $row[$i];
                        }
                        echo "</td>\n";
                } echo "</tr>\n";
        } echo "</table>\n";
}

function noItem()
{
   	echo "<h3>No Items Found</h3>";
}

function moreItems($upc)
{
    echo "More than 1 item found for:<h3> " . $upc . "</h3><br>";
}

function oneItem($upc)
{
    echo "One item found for: " . $upc;
}

//
// PHP INPUT DEBUG SCRIPT  -- very helpful!
//

/*
function debug_p($var, $title) 
{
    print "<h4>$title</h4><pre>";
    print_r($var);
    print "</pre>";
}  

debug_p($_REQUEST, "all the data coming in");
*/
?>

