<?php
/*******************************************************************************

    Copyright 2007 People's Food Co-op, Portland, Oregon.

    This file is part of Fannie.

    IS4C is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IS4C is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
 
//include($_SERVER["DOCUMENT_ROOT"].'/src/funct1Mem.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');

$date = '2007-08-05';

$trans = "SELECT description, quantity, unitPrice, total FROM is4c_log.dtransactions 
	WHERE date(datetime) = ". $date ."
	AND register_no = 1 AND emp_no = 7008
	and trans_no = 20";

receipt_to_table($trans, "SELECT * FROM employees", 1, 'FFFFFF');

?> 