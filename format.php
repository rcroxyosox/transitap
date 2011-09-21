<?php

// Get a file into an array.  In this example we'll go through HTTP to get
// the HTML source of a URL.
$lines = file('stop_times_r.csv');

function addZero($num){
	$x = strlen(trim($num));
	if($x < 2){
		return "0".trim($num);
	}elseif($x < 1){
		return "00";
	}else{
		return trim($num);
	}
}


function formatTime($timeStr){
	$time = explode(":",$timeStr);
	$hr = $time[0];
	$min = $time[1];
	$sec = substr($time[2], 0, strpos($time[2], " "));
	$ampm = substr($time[2], strpos($time[2], " "));
	
	if(strtoupper(trim($ampm)) == "PM"){
		$hr += 12;
	}

	return addZero($hr).":".addZero($min).":".addZero($sec);
}

function replace_bad($string) {
  return (string)str_replace(array("\r", "\r\n", "\n","\""), '', $string);
}

// Loop through our array, show HTML source as HTML source; and line numbers too.
foreach ($lines as $line_num => $line) {
	$lineCols = explode(",", $line);
	//echo $line."<br />";
	$ret .= replace_bad($lineCols[0]).
			 ",".replace_bad($lineCols[1]).
			 ",".formatTime($lineCols[2]).
			 ",".formatTime($lineCols[3]).
			 ",".replace_bad($lineCols[4]).
			 ",".replace_bad($lineCols[5]).
			 ",".replace_bad($lineCols[6]).
			 ",".replace_bad($lineCols[7]).
			 ",".replace_bad($lineCols[8]).
			 ",".replace_bad($lineCols[9]).
			 ",".replace_bad($lineCols[9])."\n";
		 
	if($line_num < 40) echo $ret."\n";		 
			 
}

	$f = "stop_times_r2.csv";
	$fh = fopen($f, 'w') or die("can't open file");
	fwrite($fh, $ret);
	fclose($fh);


?>