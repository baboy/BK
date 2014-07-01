<?php
if( !isset($_GET["url"]))
    return;
$url = $_GET["url"];
$re = "/([0-9]{10,})(?:,([0-9]{10,})(?:,([0-9]{4,}))?)?/";
$n = preg_match($re, $url, $m);
$m3u8 = null;
if($n>0){
    $m3u8 = str_replace($m[0],$m[1].",".$m[2].",5000", $url);
}
header("Location: $m3u8");
exit;