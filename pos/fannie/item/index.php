<?php
	$backoffice=array();

	/*
	 * Use $_POST to delete/insert/update product
	 * Set status message
	 * If insert/update, return to product entry
	 * If delete, return to empty page
	 * 
	 * Or, use $_POST to search database
	 * If exact match, pull up product details, list similar products
	 * If loose match, pull up list of choices
	 * If no match, set status, return to empty page
	 * 
	 * Editing multiple products at the same time should be a separate feature/plugin based on need 
	 */

	require_once($_SERVER["DOCUMENT_ROOT"].'/src/htmlparts.php');
	
	/*
	 * form.php has code displaying the product entry form
	 * results.php has code displaying search results
	 * sql.php has code for searching databases
	 */
	
	require_once('form.php');
	require_once('results.php');
	require_once('sql.php');
	
	if (isset($_REQUEST['a']) && $_REQUEST['a']=='delete') {
	} else if (isset($_REQUEST['a']) && $_REQUEST['a']=='insert') {
	} else if (isset($_REQUEST['a']) && $_REQUEST['a']=='search') {
		search(&$backoffice);
	} else if (isset($_REQUEST['a']) && $_REQUEST['a']=='update') {
	} else  {
	}
	
	$html='<!DOCTYPE HTML>
<html>
	<head>';
	
	$html.=head();
	
	$html.='
		<title>IS4C - Item Maintenance</title>
	</head>
	<body>';
	
	$html.=body();
	
	$html.='
		<div id="page_panel">
			<p class="status"/>
			<!-- sketching out the search form -->
			<form action="./" method="post" name="search">
				<input name="a" type="hidden" value="search"/> 
				<input name="q" type="text" value=""/>
				<select name="t" size=1>
					<option disabled value="upc_description_sku">UPC/Description/Item Number</option>
					<option selected value="upc">UPC</option>
					<option disabled value="description">Description</option>
					<option disabled value="item number">Item Number</option>
					<option disabled value="brand">Brand</option>
					<option disabled value="section">Section</option>
					<option disabled value="vendor">Vendor</option>
					<option disabled value="ask">You can ask for more</option>
				</select>
				<input type="submit" value="search"/>
			</form>';
	
	$html.=form(&$backoffice);
	
	$html.=results(&$backoffice);
	
	$html.='
			<hr/>
			<h6>Screenshot of the Wedge Co-op\'s Item Maintenance page</h6>
			<img alt="Screenshot of Item Maintenance at the Wedge Co-op" src="teaser.png"/>
		</div>';
	
	$html.=foot();
	
	$html.='
	</body>
</html>';
	
	print_r($html);
?>