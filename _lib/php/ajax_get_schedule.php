<?php 

$debbug = (isset($_POST['debug']))?$_POST['debug']:0;
$selectedStartStation = (isset($_POST['stationid']))?$_POST['stationid']:"Sunnyvale Caltrain";
$selectedDirection = (isset($_POST['directionid']))?$_POST['directionid']:"north";
$curTime = date('H:i:s');
$direction = array("north" => 0, "south" => 1);

if($debbug){ ?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript"> 
<?php } ?>

<?php
require_once('db.class.php/db.class.php');
$DB = new db();
$sql = "SELECT calendar.service_id as csid,
							 trips.trip_id as tid,
							 trips.direction_id as did,
							 stop_times.stop_id as sid,
							 stop_times.departure_time as dtime,
							 stop_times.departure_time as atime
					FROM calendar 
					LEFT JOIN trips ON calendar.service_id = trips.service_id
					LEFT JOIN stop_times ON stop_times.trip_id = trips.trip_id 
					WHERE calendar.".strtolower(date('l'))." = 1 
					AND stop_times.stop_id = '".$selectedStartStation."'
					AND stop_times.departure_time > '".$curTime."'
					AND trips.direction_id = ".$direction[$selectedDirection]."
					ORDER BY stop_times.departure_time";
					
$res = $DB->query($sql);
$ret = '{"arrivals":['."\n";
$retArr = array();
$debugconly = '';
while($r = $DB->fetchNextObject($res)){
	$debugconly .= $r->did." -> ".$r->csid." -> ".$r->tid." -> ".$r->sid." -> ".date('g:i a',strtotime($r->dtime))." / ".date('g:i a', strtotime($r->atime))."</br >";
	$retArr []= '{"departuretime":"'.$r->dtime.'", "directionid":"'.$r->did.'"}'."\n";
}
$ret .= implode(",", $retArr);
$ret .= ']}'."\n";
?>
<?php if($debbug){ ?>
var t = <?php echo $ret;?>;
console.log(t);
</script>
</head>
<body>
<?php echo $debugconly; ?>
</body>
</html>
<?php }else{ 

echo $ret;

}
?>



