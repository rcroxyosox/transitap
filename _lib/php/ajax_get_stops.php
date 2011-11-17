<?php
require_once('db.class.php/db.class.php');
$DB = new db();
$sql = "SELECT stop_id, stop_lat, stop_lon FROM stops WHERE 1";
$res = $DB->query($sql);
$ret = '{"stops":['."\n";
$retArr = array();
while($r = $DB->fetchNextObject($res)){
	$retArr []= '{"stopid":"'.$r->stop_id.'","stoplat":'.$r->stop_lat.',"stoplng":'.$r->stop_lon.'}'."\n";
}
$ret .= implode(',', $retArr);
$ret .= ']}'."\n";
echo $ret;
?>
