<?php

echo "<hr>Debug Info: dump of array name and value pairs<br><pre>";
echo "Parameters: ";print_r($_REQUEST);
echo "Session: ";print_r($_SESSION);
echo "Server: "; print_r($_SERVER);
echo "</pre><hr>";

?>