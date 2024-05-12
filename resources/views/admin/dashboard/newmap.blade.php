


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ isset($mapkey) ? $mapkey->key : 'Default Key' }}&libraries=drawing,geometry,marker" ></script>
</head>
<body>
	<div id="allMap" style="height: 500px; width: 100%;" class="shadow-sm border-1"></div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script>
		function initMap() {
			map = new google.maps.Map(document.getElementById("allMap"), {
				center: { lat: -2.548926, lng: 118.014863 },
				zoom: 5,
				mapTypeId: google.maps.MapTypeId.HYBRID,
			});
		}

		$(document).ready(function() {
			initMap();
		});
	</script>
</body>
</html>
