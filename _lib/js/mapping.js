$(document).ready(function(){		


		/************util**************/

		sortNumber = function(a,b){return a - b;}	
		
		/************util**************/
		
		
		var map;
		var markersArray = [];
		var infowindow;
		var seletedTrip = {};
		
		function initialize(position) {
    	
			var lat = position.coords.latitude;
     	var lng = position.coords.longitude;
			var latlng = new google.maps.LatLng(lat, lng);
			
			//console.log(lat+":"+lng);
			
			var mapOptions = {
				zoom: 12,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.TERRAIN
			};
			map =  new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			addMarker({location:latlng,infoContent:"You"});
			
			/* play with the markers */
			$('#gen').toggle(
				function(){
					markStops({lat:lat,lng:lng});	
				},
				function(){
					deleteOverlays();
			});
			
			
			
		}
		
		/* add markers onto the map */
		function addMarker(options){
			
			var marker = new google.maps.Marker({
				position: options.location,
				map: map
			});			
			
			google.maps.event.addListener(marker, 'click', function() {
				if (infowindow) infowindow.close();
				infowindow = new google.maps.InfoWindow({content: options.infoContent });
				infowindow.open(map,marker);
			});
			
			markersArray.push(marker);			

		}
		
		// Removes the overlays from the map, but keeps them in the array
		function clearOverlays() {
			if (markersArray) {
				for (i in markersArray) {
					markersArray[i].setMap(null);
				}
			}
		}
		
		// Shows any overlays currently in the array
		function showOverlays() {
			if (markersArray) {
				for (i in markersArray) {
					markersArray[i].setMap(map);
				}
			}
		}
		
		// Deletes all markers in the array by removing references to them
		function deleteOverlays() {
			if (markersArray) {
				for (i in markersArray){
					markersArray[i].setMap(null);
				}
				markersArray.length = 0;
			}
		}
		
		/* Get the stops and sort them by distance */
		distanceSort = function(options){
			stopData = options.stopData;
			field = options.field; 
			/* console.log(stopData); */
			stopsDistCalc = [];
			copyStopsArr = {"stops":[]}; 
			for(i=0;i<stopData.stops.length;i++){
				if(options.sortNumber != "undefined"){stopsDistCalc[i]=parseFloat(stopData.stops[i][field]);}
				else{stopsDistCalc[i]=stopData.stops[i][field];}
			}
			
			if(options.sortNumber != "undefined"){stopsDistCalc.sort(sortNumber);}
			else{ stopsDistCalc.sort(); }
			
			var orgKey;
		  var dist;
			
			for(j=0;j<stopsDistCalc.length; j++){
				for(k=0;k<stopData.stops.length;k++){
					if(stopData.stops[k][field] == stopsDistCalc[j]){
						orgKey = k;
						dist = stopsDistCalc[j];						
					}
				}
				
				copyStopsArr.stops[j] = new Object();
				c = 1;
				for(var key in stopData.stops[orgKey]){
					comma = (c == Object.keys(stopData.stops[orgKey]).length)? "" : ",";
					copyStopsArr.stops[j][key] = stopData.stops[orgKey][key];
					c++;
				}
			}
			/* console.log(copyStopsArr); */
			return copyStopsArr;
		}		
		
		/* mark the stops */
		getSortedStops = function(options){
			
			curPos = new google.maps.LatLng(options.lat, options.lng);
			
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {lat:options.lat,lng:options.lng},
				url: "_lib/php/ajax_get_stops.php",
				success: function(stopData){
	
						sl = stopData.stops.length;
						
						for(i=0;i<sl;i++){
							lat = stopData.stops[i].stoplat;
							lng = stopData.stops[i].stoplng;
							var stopPos = new google.maps.LatLng(lat, lng);	
							distance = google.maps.geometry.spherical.computeDistanceBetween(stopPos, curPos);
							stopData.stops[i]["stopdist"] = distance;
						}
						
						sortedByDist = distanceSort({stopData:stopData, field:"stopdist", sortNumber:true});//resort by closest
	
				}
			});			
		}
	
		
	
		/* mark the stops based on the stops obj*/
	  markStops = function(options){
			$.getJSON("_lib/php/ajax_get_stops.php", function(stopsObj) {
					//console.log(stopsObj);
					sl = stopsObj.stops.length; 
					for(i=0;i<sl;i++){
						lat = stopsObj.stops[i].stoplat;
						lng = stopsObj.stops[i].stoplng;	
						infoContent = stopsObj.stops[i].stopid;
						markerPos = latlng = new google.maps.LatLng(lat, lng);
						addMarker({location:markerPos,infoContent:infoContent});
					}
    	})
		}
	
		/* Play with the show nearest */
		$('#closest').click(function(){
			deleteOverlays();
		})
		 
		/* Init in the current position */ 
		error = function(){alert("there was an issue");}
		if (navigator.geolocation){ 
			navigator.geolocation.getCurrentPosition(initialize, error); 
		}else{ 
			alert('geolocation not supported'); 
		}
		
		//position = {coords:{latitude:37.3650972, longitude:-122.0354438}};
		//position['coords']['latitude'] = 37.3650972;
		//position['coords']['longitude'] = -122.0354438;
		//console.log(position);
		
		//initialize(position);

});