// refactored map.js
let myMap;
const markers = [];
const polygons = [];

function initMap() {
	myMap = new google.maps.Map(document.getElementById("myMap"), {
		center: { lat: -2.5489, lng: 118.0149 },
		zoom: 5,
		mapTypeId: google.maps.MapTypeId.SATELLITE,
	});

	setupDrawingManager();
	createMarkers();
	createPolygon();
}

function setupDrawingManager() {
	const drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.DEFAULT,
		drawingControl: true,
		drawingControlOptions: {
			position: google.maps.ControlPosition.TOP_CENTER,
			drawingModes: ["marker", "polygon"],
		},
		polygonOptions: {
			fillColor: "#fd3995",
			strokeColor: "#fd3995",
			strokeWeight: 2,
			fillOpacity: 0.5,
			editable: true,
			draggable: true,
		},
	});
	drawingManager.setMap(myMap);

	google.maps.event.addListener(
		drawingManager,
		"markercomplete",
		function (marker) {
			markers.push(marker);
			marker.setDraggable(true);
			addMarkerListeners(marker);
		}
	);

	google.maps.event.addListener(
		drawingManager,
		"polygoncomplete",
		function (polygon) {
			polygons.push(polygon);
			addPolygonListeners(polygon);
		}
	);
}

function addMarkerListeners(marker) {
	google.maps.event.addListener(marker, "click", function () {
		myMap.setCenter(marker.getPosition());
		updateCoordinates(marker.getPosition());
	});

	google.maps.event.addListener(marker, "drag", function () {
		updateCoordinates(marker.getPosition());
	});
}

function updateCoordinates(position) {
	document.getElementById("latitude").value = position.lat();
	document.getElementById("longitude").value = position.lng();
}

function createMarkers() {
	const latitude = document.getElementById("latitude").value;
	const longitude = document.getElementById("longitude").value;
	if (latitude !== "" && longitude !== "") {
		const position = new google.maps.LatLng(latitude, longitude);
		const marker = new google.maps.Marker({
			position: position,
			map: myMap,
			draggable: true,
		});
		markers.push(marker);
		myMap.setCenter(position);
		myMap.setZoom(18);
		addMarkerListeners(marker);
	}
}

function createPolygon() {
	let polygonCoords = document.getElementById("polygon").value;
	if (polygonCoords !== "") {
		const parsedCoords = JSON.parse(polygonCoords);
		if (polygon) {
			// Hapus polygon yang ada jika sudah ada
			polygon.setMap(null);
		}
		polygon = new google.maps.Polygon({
			paths: parsedCoords,
			strokeColor: "#0000FF",
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: "#FF0000",
			fillOpacity: 0.35,
			editable: true,
			map: myMap,
		});
		addPolygonListeners(polygon);
	}
	// const polygonCoords = document.getElementById("polygon").value;
	// if (polygonCoords !== "") {
	// 	polygonCoords = JSON.parse(polygonCoords);
	// 	const polygon = new google.maps.Polygon({
	// 		paths: polygonCoords,
	// 		strokeColor: "#0000FF",
	// 		strokeOpacity: 0.8,
	// 		strokeWeight: 2,
	// 		fillColor: "#FF0000",
	// 		fillOpacity: 0.35,
	// 		editable: true,
	// 		map: myMap,
	// 	});
	// 	polygons.push(polygon);
	// 	addPolygonListeners(polygon);
	// }
}

function addPolygonListeners(polygon) {
	google.maps.event.addListener(polygon, "click", function () {
		document.getElementById("polygon").value = JSON.stringify(
			polygon.getPath().getArray()
		);
		calculatePolygonArea(polygon);
	});

	google.maps.event.addListener(polygon.getPath(), "set_at", function () {
		document.getElementById("polygon").value = JSON.stringify(
			polygon.getPath().getArray()
		);
		calculatePolygonArea(polygon);
	});

	google.maps.event.addListener(polygon.getPath(), "insert_at", function () {
		document.getElementById("polygon").value = JSON.stringify(
			polygon.getPath().getArray()
		);
		calculatePolygonArea(polygon);
	});
}

function calculatePolygonArea(polygon) {
	const luas = google.maps.geometry.spherical.computeArea(polygon.getPath());
	document.getElementById("luas_kira").value = (luas / 10000).toFixed(2);
}
