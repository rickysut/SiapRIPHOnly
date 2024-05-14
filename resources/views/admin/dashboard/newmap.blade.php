


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
	<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOT3nitE_dWhe_EMbJ3MMOtASYfI8fc4M&libraries=drawing,geometry,marker&loading=async" ></script>
</head>
<body>
	<div id="allMap" style="height: 500px; width: 100%;" class="shadow-sm border-1"></div>

	{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}
	<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
	<script type="text/javascript">
		function initMap() {
			console.log('initmap')
			map = new google.maps.Map(document.getElementById("allMap"), {
				center: { lat: -2.548926, lng: 118.014863 },
				zoom: 5,
				// mapTypeId: google.maps.MapTypeId.HYBRID,
			});
		}

		$(document).ready(function() {
			initMap();
		});
	</script>
</body>
</html>
