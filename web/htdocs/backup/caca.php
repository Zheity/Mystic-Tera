<?php 
$fp = fsockopen("localhost", 8080, $errno, $errstr, 30); 
if (!$fp) { 
    echo "$errstr ($errno)<br />\n"; 
} else { 
    $out = "POST /index.php HTTP/1.1\r\n"; 
$out .= "Host: 188.95.248.60:8080\r\n"; 
$out .= "Accept: */*\r\n"; 
$out .= "Content-Type: text/xml; charset=utf-8\r\n"; 
$out .= "Content-Length: 183\r\n\r\n"; 
$out .= '<?xml version="1.0" encoding="UTF-8"?><login-request><account>sara</account><password>f3329818d419a34fcb14f7a20fbe82b3</password><game>16</game><ip>201.253.92.185</ip></login-request>';
    fwrite($fp, $out); 
    while (!feof($fp)) { 
        echo fgets($fp, 128); 
    } 
    fclose($fp); 
} 
?>