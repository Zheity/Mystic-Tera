<?php
$myFile = "log_post.txt"; 
$fh = fopen($myFile, 'w') or die("can't open file"); 
fwrite($fh, "\n"); 
$headers = apache_request_headers(); 
foreach ($headers as $h => $v)
fwrite($fh, "$h: $v\n");
fwrite($fh, print_r($HTTP_RAW_POST_DATA,1));
fclose($fh);
require('../lib/Framework/SiteHandler.php');
?>