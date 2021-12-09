<?php 
$fp = fsockopen("ssl://localhost", 8080, $errno, $errstr, 30); 
if (!$fp) { 
    echo "$errstr ($errno)<br />\n"; 
} else { 
    $out = "POST /index.php HTTP/1.1\r\n"; 
$out .= "Host: 188.95.248.188:8080\r\n"; 
$out .= "Accept: */*\r\n"; 
$out .= "Content-Type: text/xml; charset=utf-8\r\n"; 
$out .= "Content-Length: 251\r\n\r\n"; 
$out .= '<?xml version="1.0" encoding="UTF-8"?><item-purchase-request><game>1002</game><userid>808</userid><charid>taran</charid><server>1</server><amount>150</amount><itemid>5</itemid><count>1</count><uniqueid>1348599345_1_2</uniqueid></item-purchase-request>';
    fwrite($fp, $out); 
    while (!feof($fp)) { 
        echo fgets($fp, 128); 
    } 
    fclose($fp); 
} 
?>