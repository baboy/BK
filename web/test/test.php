<?php
$url = "rtmp://10.33.0.132:1935/tvie/test";
$url = parse_url($url);
$m3u8 = "http://".$url["host"].$url["path"]."/sd.m3u8";
var_dump($m3u8);

var_dump($_GET);

http://www.cibntvm.com/suntv/channel
{
english_name: "CCTV1",
chinese_name: "央视综合",
icon: "http://stream.suntv.tvmining.com/icon/CCTV1.png",
picture: "http://stream.suntv.tvmining.com/picture/CCTV1.jpg",
live_url: "http://stream.suntv.tvmining.com/approve/live",
vod_url: "http://stream.suntv.tvmining.com/approve/vod",
epg_url: "http://stream.suntv.tvmining.com/approve/epginfo",
picture_url: "http://stream.suntv.tvmining.com/approve/capture",
picshot_url: "http://stream.suntv.tvmining.com/approve/picshot",
tag: "央视"
},
http://www.cibntvm.com/suntv/public/livePlayer.html

array(7) {
  ["english_name"]=>
  string(5) "CCTV1"
  ["title"]=>
  string(12) "央视综合"
  ["live_src"]=>
  string(45) "http://stream.suntv.tvmining.com/approve/live"
  ["poster"]=>
  string(50) "http://stream.suntv.tvmining.com/picture/CCTV1.jpg"
  ["vod_src"]=>
  string(44) "http://stream.suntv.tvmining.com/approve/vod"
  ["shot_src"]=>
  string(48) "http://stream.suntv.tvmining.com/approve/capture"
  ["random"]=>
  string(13) "1420797105609"
}




http://stream.suntv.tvmining.com/approve/live?channel=CCTV1&type=iptv&suffix=m3u8&access_token=QmRubnQwN2xvZ3RBRnc1OFUyNDF3MjdnOTgxNDIwNzk2OTA5ODU1
array(4) {
  ["channel"]=>
  string(5) "CCTV1"
  ["type"]=>
  string(4) "iptv"
  ["suffix"]=>
  string(4) "m3u8"
  ["access_token"]=>
  string(52) "QmRubnQwN2xvZ3RBRnc1OFUyNDF3MjdnOTgxNDIwNzk2OTA5ODU1"
}

http://stream.suntv.tvmining.com/approve/vod?channel=CCTV1&startTime=1420299613&endTime=1420302348&type=iptv&suffix=m3u8&access_token=QmRubnQyOERDcU5EQGdILkprdTlfU0hURG51Q0thXzZnRi1adEs1OHYyNDFJMjdhOTgxNDIwNzk2OTU2NDMz
array(6) {
  ["channel"]=>
  string(5) "CCTV1"
  ["startTime"]=>
  string(10) "1420299613"
  ["endTime"]=>
  string(10) "1420302348"
  ["type"]=>
  string(4) "iptv"
  ["suffix"]=>
  string(4) "m3u8"
  ["access_token"]=>
  string(84) "QmRubnQyOERDcU5EQGdILkprdTlfU0hURG51Q0thXzZnRi1adEs1OHYyNDFJMjdhOTgxNDIwNzk2OTU2NDMz"
}

