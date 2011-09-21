<?php include('_lib/globals.php'); ?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

<script type="text/javascript" src="_lib/js/jquery-1.6.4.min.js"></script>
<script type="text/javascript" src="_lib/js/jquery.countdown.js"></script>
<!--<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=true">
</script> -->
<script type="text/javascript">
$(document).ready(function(){
	

	supports_html5_storage = function() {
		try {
			return 'localStorage' in window && window['localStorage'] !== null;
		} catch (e) {
			return false;
		}
	}
	

	expireCounter = function(){
		$('#timer').countdown('destroy');
		getSchedule({})
	}
	
	showTimer = function(timeStamp){
		console.log("done");
		austDay = new Date("<?php echo date('F j, Y'); ?> "+timeStamp);
		$('#timer').countdown({until: austDay, layout: '{hnn}{sep}{mnn}{sep}{snn}', compact: true, onExpiry:expireCounter, timeSeparator:":"});		
	}
	
	
	getSchedule = function(options){
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {stationid:"Sunnyvale Caltrain", 
							 directionid:"north", 
							 debug:"0"},
				url: "_lib/php/ajax_get_schedule.php",
				success: function(scheduleData){
					console.log(scheduleData);
						nextTime = (options.whichTime)?options.whichTime:scheduleData.arrivals[0].departuretime;
						showTimer(nextTime);
				},
				error: function(e){
					alert("oops something went wrong");
				}
			});
	}
	
	getSchedule({});
	
})
</script>
<link href="_lib/css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
	<!--<input type="button" value="markers" id="gen" />
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
      	<div id="preheading">Your next train will be here in:</div>
        <div id="timer"></div>
        
      </div>
      
    </div>
  </div>

</body>
</html>