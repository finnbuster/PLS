<?php 
//Generate Store Locator page 
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>Pazzey's Store Locator</title>
	<link rel='stylesheet' href='style.css' type='text/css' media='all' />
	<?php $height = $_GET['height']; 
		  $height = $height - 200; ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false"
            type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
    var map;
    var markers = [];
	var side_bar_html = "";
    var infoWindow;
    var locationSelect;

    function load() {
      map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(40, -100),
        zoom: 2,
        mapTypeId: 'roadmap',
        mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
      });
      infoWindow = new google.maps.InfoWindow();
      locationSelect = document.getElementById("locationSelect");
      locationSelect.onchange = function() {
        var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
        if (markerNum != "none"){
          google.maps.event.trigger(markers[markerNum], 'click');
        }
      };
   }

   function searchLocations() {
     var address = document.getElementById("addressInput").value;
     var geocoder = new google.maps.Geocoder();
     geocoder.geocode({address: address}, function(results, status) {
       if (status == google.maps.GeocoderStatus.OK) {
        searchLocationsNear(results[0].geometry.location);
       } else {
         alert(address + 'Please Enter a Location');
       }
     });
   }

   function clearLocations() {
     infoWindow.close();
     for (var i = 0; i < markers.length; i++) {
       markers[i].setMap(null);
     }
     markers.length = 0;
	side_bar_html = "";
   }

   function searchLocationsNear(center) {
     clearLocations(); 

     var radius = document.getElementById('radiusSelect').value;
     var searchUrl = 'xmlmap.php?lat=' + center.lat() + '&lng=' + center.lng() + '&radius=' + radius;
     downloadUrl(searchUrl, function(data) {
       var xml = parseXml(data);
       var markerNodes = xml.documentElement.getElementsByTagName("marker");
       var bounds = new google.maps.LatLngBounds();
       for (var i = 0; i < markerNodes.length; i++) {
         var name = markerNodes[i].getAttribute("name");
         var address = markerNodes[i].getAttribute("address");
         var distance = parseFloat(markerNodes[i].getAttribute("distance"));
		 var moreinfo = markerNodes[i].getAttribute("moreinfo");
         var latlng = new google.maps.LatLng(
              parseFloat(markerNodes[i].getAttribute("lat")),
              parseFloat(markerNodes[i].getAttribute("lng")));

         createOption(name, distance, i);
         createMarker(latlng, name, address, moreinfo);
         bounds.extend(latlng);
       }
       map.fitBounds(bounds);
	   if (side_bar_html == "") {
	   document.getElementById("side_bar").innerHTML = '<strong>No Matches Found</strong>';}
	   else {
	   document.getElementById("side_bar").innerHTML = side_bar_html;}
      });
    }
  
    function createMarker(latlng, name, address, moreinfo) {
	  
      var html = "<b>" + name + "</b> <br/>" + address + "<br/>" + moreinfo;
	  var cleanaddy = address.replace(/<\/?[^>]+(>|$)/g, "");
	  html +='<form action="http://maps.google.com/maps" method="get"" target="_blank">'+
           '<INPUT value="Get Directions" TYPE="SUBMIT">' +
           '<input type="hidden" name="daddr" value="' + cleanaddy +
           '"/>';
      var marker = new google.maps.Marker({
        map: map,
        position: latlng
      });
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
      markers.push(marker);
    side_bar_html += '<a href="javascript:myclick(' + (markers.length-1) + ')">' + name + '<\/a><br>'+ address +'<br><br>';
    }
function myclick(i) {
  google.maps.event.trigger(markers[i], "click");
}

    function createOption(name, distance, num) {
      var option = document.createElement("option");
      option.value = num;
      option.innerHTML = name + "(" + distance.toFixed(1) + ")";
      locationSelect.appendChild(option);
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request.responseText, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function parseXml(str) {
      if (window.ActiveXObject) {
        var doc = new ActiveXObject('Microsoft.XMLDOM');
        doc.loadXML(str);
        return doc;
      } else if (window.DOMParser) {
        return (new DOMParser).parseFromString(str, 'text/xml');
      }
    }

    function doNothing() {}

    //Nothing here>
  </script>
</head>
<body style="margin:0px; padding:0px;" onload="load()" class="storelocator"> 
  <div class="storewrap">
			<div>
			 <label>Enter Postal/Zip Code or City and Province/State: </label>
			 <input type="text" id="addressInput" size="30"/><br />
		<label>Within:</label><select id="radiusSelect">
			  <option value="25" selected>25 miles</option>
			  <option value="100">100 miles</option>
			  <option value="200">200 miles</option>
			</select><br />
			<input type="button" onclick="searchLocations()" value="Search"/>
			</div>
			<div><select id="locationSelect" style="width:100%;visibility:hidden"></select></div>
		<table border="0" bordercolor="" style="background-color:" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td class="map">		  
					<div id="map" style="height:<?php echo $height; ?>px"></div>
				</td>
				<td class="side_bar"><div id="side_bar" style="height:<?php echo $height; ?>px"></div>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>