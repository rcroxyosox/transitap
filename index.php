<?php require_once('_lib/php/static_functions.php');
echo 'cocks';
 ?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

<script type="text/javascript" src="_lib/js/jquery-1.6.4.min.js"></script>
<script type="text/javascript" src="_lib/js/jquery.countdown.js"></script>
<!--
<script type="text/javascript" src="_lib/js/mapping.js"></script>
<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=true">
</script>
--> 
<script type="text/javascript">
$(document).ready(function(){
	
	var startStation = "Sunnyvale Caltrain";
	var stopStation = "California Ave Caltrain";
	
	// reset the start location
	$.each($("#startloc option"), function(i){
		v = $(this).val();
		if(v == startStation){ $(this).attr('selected', 'selected'); }
	})
	
	// reset the end location
	$.each($("#endloc option"), function(i){
		v = $(this).val();
		if(v == stopStation){ $(this).attr('selected', 'selected'); }
	})
	
	// dont allow going to same staying
	$('.stationpickers').change(function(){
		$('.stationpickers option').removeAttr('disabled');
		v = $(this).find('option:selected').val();
		$('.stationpickers').not($(this)).find('option[value="'+v+'"]').attr({'disabled':'disabled'});
	})
		
	expireCounter = function(){
		$('#timer').countdown('destroy');
		getTimeLeft({});
	}
	
	showTimer = function(timeStamp){
		$('#timer').countdown('destroy');
		austDay = new Date(timeStamp);
		$('#timer').countdown({until: austDay, layout: '{hnn}{sep}{mnn}{sep}{snn}', compact: true, onExpiry:expireCounter, timeSeparator:":"});		
	}
	
	getTimeLeft = function(options){
		
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {stationid:options.stationid,
							 destid:options.destid},
				url: "_lib/php/ajax_get_schedule.php",
				success: function(scheduleData){
							console.log(scheduleData);
							notrains = (scheduleData.departures.length == 0);
							if(!notrains){ // should never happend
								
								nextDate = (options.whichDate)?whichDate:scheduleData.date;
								nextTime = (options.whichTime)?options.whichTime:scheduleData.departures[0].departure_time;
								showTimer(nextDate+" "+nextTime);
								$("#stdtime").text(scheduleData.departures[0].departure_time_nice);
								
							}else{
								$('#timer').text("Schedule cannot be received at this time: ");
							}
				},
				error: function(e){
					alert("oops something went wrong: "+e);
				}
			});
	}
	
	$('.stationpickers').trigger('change');
	getTimeLeft({stationid:startStation, destid:stopStation});
	
	$('#getsched').click(function(){
		getTimeLeft({stationid:$("#startloc option:selected").val(), destid:$("#endloc option:selected").val()});
	});
	
})
</script>
<link href="_lib/css/style.css" rel="stylesheet" type="text/css">
</head>
<!--<body>
	<input type="button" value="markers" id="gen" />
  <input type="button" value="generate nearest" id="closest" />
  <div id="map_canvas" style="width:500px; height:500px;"></div>
  <div id="loc"></div> -->
  
  <div id="wrapper" class="iphone3">
  	<div id="screen">
      
      <div id="topbuttonsblock">
      <!--Add the buttons -->
      </div>
      
      <div id="bodyblock">
      <!--Main body -->
      	<div id="preheading">Your next train will be here at <span id="stdtime"></span></div>
        <div id="timer"></div>
        <div id="smtime"><?php echo date("g:i a"); ?></div>
        
        <div id="formblock">
        <label>Start at:<?php echo getStopsDrop('startloc'); ?></label>
        <label>End at:<?php echo getStopsDrop('endloc'); ?></label>
        <input name="getsched" id="getsched" type="button" value="Get Schedule">
        </div>
        
      </div>
      
    </div>
  </div>
</body>
<!-- here is on master from the remote...again -->
</html>
