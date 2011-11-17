<?php

$debug = (isset($_POST['debug']))?$_POST['debug']:0;
$selectedStartStation = (isset($_POST['stationid']))?$_POST['stationid']:"Sunnyvale Caltrain";
$selectedStopStation = (isset($_POST['destid']))?$_POST['destid']:"California Ave Caltrain";
$departureTime = (isset($_POST['departuretime']))?$_POST['departuretime']:date("G:i:s");
$arrivalTime = (isset($_POST['arrivaltime']))?$_POST['arrivaltime']:"24:00:00";
$direction = array("north" => 0, "south" => 1);
$directionFlip = array_flip($direction);

require_once('db.class.php/db.class.php');
$DB = new db();

// get the sched
function getSched($settings = array("date" => false, "departure_time" => false, "arrival_time"=>false)){	
	
global $DB;
global $direction;
global $directionFlip;
global $departureTime;
global $arrivalTime;
global $selectedStartStation;
global $selectedStopStation;
global $debug;

$date = $settings['date'];
$departureTime = ($settings['departure_time'])?$settings['departure_time']:$departureTime;
$arrivalTime = ($settings['arrival_time'])?$settings['arrival_time']:$arrivalTime;

$day = (!$date)?date('l'):date('l',$date);	
$curTime = (!$date)?date('H:i:s'):date('H:i:s',$date);

// get the service id based on the day
$sid = $DB->queryUniqueValue("SELECT service_id FROM calendar WHERE ".$day." = 1");

$t = 'Select stop_times.trip_id as tid, 
stop_times.stop_id as sid, 
stop_times.stop_sequence as ss,
stop_times.arrival_time as atime,
TIME_FORMAT(stop_times.arrival_time,"%h:%i %p") as atime_nice,
stop_times.departure_time as dtime,
TIME_FORMAT(stop_times.departure_time,"%h:%i %p") as dtime_nice,
trips.direction_id as did,
stops.stop_lat as lat,
stops.stop_lon as lng,
stops.stop_desc as stopdesc,
stops.zone_id as zone 
FROM stop_times
LEFT JOIN stops ON stop_times.stop_id = stops.stop_id
LEFT JOIN trips ON stop_times.trip_id = trips.trip_id 
Where trips.service_id = "'.$sid.'"
AND stop_times.arrival_time < "24:00:00"
AND stop_times.departure_time < "24:00:00"
ORDER BY stop_times.departure_time';


$rt = $DB->query($t);
$trips = array();
$nextTrips = array();
$nextRoutes = array();

$nextTrips['summary'] = array("day" => $day, "service" => $sid);

while($row = $DB->fetchNextObject($rt)){
	$trips['trips'][$row->tid][$row->sid] = array("stop_sequence"=>$row->ss,
																								 "direction"=>$directionFlip[$row->did],
																								 "arrival_time"=>$row->atime,
																								 "departure_time"=>$row->dtime,
																							 	 "arrival_time_nice"=>$row->atime_nice,
																								 "departure_time_nice"=>$row->dtime_nice,
																								 "lat"=>$row->lat,
																								 "lng"=>$row->lng,
																								 "zone"=>$row->zone,
																								 "desc"=>$row->stopdesc);
}

// filter
$c = 0;
foreach($trips['trips'] as $k => $v){
	if($trips['trips'][$k][$selectedStartStation]["stop_sequence"] < $trips['trips'][$k][$selectedStopStation]["stop_sequence"] // filter direction 
		&& $trips['trips'][$k][$selectedStartStation]["departure_time"] > $departureTime // filter departure
		&& isset($trips['trips'][$k][$selectedStopStation]) //filter stops there
		&& $trips['trips'][$k][$selectedStopStation]["arrival_time"] < $arrivalTime){ // filter arival
		
		// format the summary
		$nextTrips['summary']['start_station'] = $trips['trips'][$k][$selectedStartStation];
		$nextTrips['summary']['start_station']['name'] = $selectedStartStation;
		$nextTrips['summary']['start_station']['departure_time'] = $departureTime;
		$nextTrips['summary']['stop_station'] = $trips['trips'][$k][$selectedStopStation];
		$nextTrips['summary']['stop_station']['name'] = $selectedStopStation;
		$nextTrips['summary']['stop_station']['arrival_time'] = $arrivalTime;
		
		// format the departures
		$nextRoutes[$k]['departure_time'] = $trips['trips'][$k][$selectedStartStation]['departure_time'];
		$nextRoutes[$k]['departure_time_nice'] = $trips['trips'][$k][$selectedStartStation]['departure_time_nice'];
		
		$nextTrips['trips'][$k] = $v;
		$nextTrips['summary']['direction'] = $trips['trips'][$k][$selectedStartStation]['direction'];
		$c++;
	}
}

unset($nextTrips['summary']['start_station']['arrival_time']);
unset($nextTrips['summary']['stop_station']['departure_time']);


/* 
	print '<pre>';
	print_r($nextTrips);
	print '</pre>'; 
 */

return $nextRoutes;

}

$datePlusOne = strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " +1 day");
$ret = '{"date":"'.date('F j, Y', $datePlusOne).'","departures":['."\n";
$retarr = array();

$res = (count(getSched()) < 1)?getSched(array("date" => $datePlusOne, "departure_time" => "00:00:00", "arrival_time"=>"24:00:00")):getSched();
foreach($res as $k => $v){
	$retarr []= '{"tripid" : "'.$k.'", "departure_time" : "'.$v['departure_time'].'", "departure_time_nice" : "'.$v['departure_time_nice'].'"}'."\n";
}
$ret .= implode(",",$retarr);
$ret .= ']}'."\n";
echo $ret;

?>