<?php
$debug = 0;
require_once('db.class.php/db.class.php');
$DB = new db();

// get the stops
function getStopsDrop($name){
	global $DB;
	$ret = '<select name="'.$name.'" id="'.$name.'" class="stationpickers">';
	$sql = "SELECT * FROM stops WHERE 1 ORDER BY stop_id";
	$res= $DB->query($sql);
	while($r = $DB->fetchNextObject($res)){
		$shortName = substr($r->stop_id, 0, strpos($r->stop_id, " Caltrain"));
		$ret .= '<option value="'.$r->stop_id.'">'.$shortName.'</option>';
	}
	$ret .= '</select>';
	return $ret;
}

if($debug){
	echo getStopsDrop($name);
}

?>