<?php

include("reload.php");

if (synctable("products") == 1) {
	echo "Yay!  It worked";
}
else {
	echo "Nope.  Broken.";
}
?>