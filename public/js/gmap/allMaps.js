// Declare global variables
var map;
var markers = [];
var polygons = [];

// Initialize the map

function initMap() {
	map = new google.maps.Map(document.getElementById("allMap"), {
		center: { lat: -2.548926, lng: 118.014863 },
		zoom: 5,
		mapTypeId: google.maps.MapTypeId.HYBRID,
	});
}

// Menambahkan event listener untuk memanggil fungsi handlePeriodetahunChange saat terjadi perubahan pada elemen #periodetahun
$("#periodetahun").on("change", handlePeriodetahunChange);

function handlePeriodetahunChange() {
	initMap();
	$("#panelData1").addClass("collapse");
	$("#panelData2").addClass("collapse");
	var periodetahun = $(this).val();
	var url = "/admin/map/getAllMapByYears/" + periodetahun;

	// Make an AJAX request to retrieve marker data and polygons
	$.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		success: function (data) {
			handleMarkerData(data);
		},
	});
}

// Handle marker data and create markers and polygons
function handleMarkerData(data) {
	// Remove existing markers and polygons from the map
	removeMarkers();
	removePolygons();

	// Iterate over the data to create markers and polygons
	$.each(data, function (index, dataRealisasi) {
		if (dataRealisasi.latitude && dataRealisasi.longitude) {
			createMarker(dataRealisasi);
		}

		if (dataRealisasi.polygon) {
			createPolygon(dataRealisasi);
		}
	});

	// Check for polygon intersections
	// var intersections = map.findPolygonIntersections();
	// if (intersections.length > 0) {
	// 	console.log("There are intersections between polygons!");
	// 	// Perform additional actions for intersecting polygons
	// 	// You can access the intersecting polygons using intersections array
	// }
}

// Create a marker on the map
function createMarker(dataRealisasi) {
	var marker = new google.maps.Marker({
		position: {
			lat: parseFloat(dataRealisasi.latitude),
			lng: parseFloat(dataRealisasi.longitude),
		},
		map: map,
		// Set other properties of the marker here
	});
	// Assign the id property from dataRealisasi to the marker object
	marker.id = dataRealisasi.id;
	marker.npwp = dataRealisasi.npwp;
	marker.perioderiph = dataRealisasi.perioderiph;
	marker.latitude = dataRealisasi.latitude;
	marker.longitude = dataRealisasi.longitude;
	marker.no_ijin = dataRealisasi.no_ijin;
	marker.no_perjanjian = dataRealisasi.no_perjanjian;
	marker.nama_lokasi = dataRealisasi.nama_lokasi;

	marker.nama_petani = dataRealisasi.nama_petani;
	marker.nama_kelompok = dataRealisasi.nama_kelompok;
	marker.nama_lokasi = dataRealisasi.nama_lokasi;

	marker.altitude = dataRealisasi.altitude;
	marker.luas_kira = dataRealisasi.luas_kira;
	marker.mulaitanam = dataRealisasi.mulaitanam;
	marker.akhirtanam = dataRealisasi.akhirtanam;
	marker.luas_tanam = dataRealisasi.luas_tanam;
	marker.varietas = dataRealisasi.varietas;
	marker.mulaipanen = dataRealisasi.mulaipanen;
	marker.akhirpanen = dataRealisasi.akhirpanen;
	marker.volume = dataRealisasi.volume;

	marker.dataFotoTanam = dataRealisasi.fotoTanam;
	marker.dataFotoProduksi = dataRealisasi.fotoProduksi;

	marker.company = dataRealisasi.company;

	// Add a click event listener to the marker
	marker.addListener("click", function () {
		showMarkerDetails(marker);
	});
}

// Create a polygon on the map
function createPolygon(dataRealisasi) {
	var polygon = new google.maps.Polygon({
		paths: JSON.parse(dataRealisasi.polygon),
		strokeColor: "#FF0000",
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: "#FF0000",
		fillOpacity: 0.35,
		map: map,
	});

	// Add a click event listener to the polygon
	polygon.addListener("click", function () {
		zoomToPolygon(polygon);
	});
}

// Show marker details in a modal
function showMarkerDetails(marker) {
	var geocoder = new google.maps.Geocoder();
	var latlng = marker.getPosition();
	geocoder.geocode({ location: latlng }, function (results, status) {
		if (status === "OK") {
			if (results[0]) {
				var address = results[0].formatted_address;
				marker.alamat = address;
				console.log("Alamat: " + address);
				// Send an AJAX request to get the marker data
				$.ajax({
					url: "/admin/map/getLocationData/" + marker.id,
					type: "GET",
					dataType: "json",
					success: function (data) {
						var alamat = marker.alamat;
						// Set the modal content with the marker details
						var markerId = marker.id;
						var npwp = marker.npwp;
						var no_ijin = marker.no_ijin;
						var perioderiph = marker.perioderiph;
						var no_perjanjian = marker.no_perjanjian;
						var nama_lokasi = marker.nama_lokasi;

						var nama_petani = marker.nama_petani;
						var nama_kelompok = marker.nama_kelompok;
						var altitude = marker.altitude;
						var luas_kira = marker.luas_kira;
						var mulaitanam = marker.mulaitanam;
						var akhirtanam = marker.akhirtanam;
						var luas_tanam = marker.luas_tanam;
						var varietas = marker.varietas;
						var mulaipanen = marker.mulaipanen;
						var akhirpanen = marker.akhirpanen;
						var volume = marker.volume;
						var fotoTanam = marker.dataFotoTanam;
						var fotoTanamHtml = "";
						var fotoProduksi = marker.dataFotoProduksi;
						var fotoProduksiHtml = "";

						var company = marker.company;

						// Update the modal elements with the marker data
						$("#company").text(company);
						$("#no_ijin").text(no_ijin);
						$("#perioderiph").text(perioderiph);
						$("#pks").text(no_perjanjian);
						$("#kelompok").text(nama_kelompok);
						$("#petani").text(nama_petani);
						$("#lokasi").text(nama_lokasi);
						$("#alamat").text(alamat);
						$("#varietas").text(varietas);
						$("#mulaitanam").text(mulaitanam);
						$("#akhirtanam").text(akhirtanam);
						$("#luas_tanam").text(luas_tanam);
						$("#mulaipanen").text(mulaipanen);
						$("#akhirpanen").text(akhirpanen);
						$("#volume").text(volume);
						fotoTanam.forEach(function (foto) {
							fotoTanamHtml += `
								<div class="col mb-4">
									<div class="card shadow-2" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
										<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('/storage/uploads/${npwp}/${perioderiph}/${foto.filename}'); background-size: cover; background-repeat: no-repeat; background-position: center;"></div>
										<a href="/storage/uploads/${npwp}/${perioderiph}/${foto.filename}" style="position: absolute; top: 10px; right: 10px; target="blank" class="mr-1 btn btn-warning btn-xs btn-icon waves-effect waves-themed" data-toggle="tooltip" data-original-title="Layar Penuh">
											<i class="fal fa-expand"></i>
										</a>
									</div>
								</div>`;
						});

						fotoProduksi.forEach(function (foto) {
							fotoProduksiHtml += `
								<div class="col mb-4">
									<div class="card shadow-2" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
										<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('/storage/uploads/${npwp}/${perioderiph}/${foto.filename}'); background-size: cover; background-repeat: no-repeat; background-position: center;"></div>
										<a href="/storage/uploads/${npwp}/${perioderiph}/${foto.filename}" style="position: absolute; top: 10px; right: 10px; target="blank" class="mr-1 btn btn-warning btn-xs btn-icon waves-effect waves-themed" data-toggle="tooltip" data-original-title="Layar Penuh">
											<i class="fal fa-expand"></i>
										</a>
									</div>
								</div>`;
						});

						$("#galleryFotoTanam").html(
							fotoTanamHtml + fotoProduksiHtml
						);

						console.log(fotoTanam);
						// Show the modal
						// $("#markerModal").modal("show");
						$("#panelData1").removeClass("collapse");
						$("#panelData2").removeClass("collapse");
						zoomToMarker(marker);
					},
				});
			} else {
				console.log("Tidak ada hasil ditemukan");
			}
		} else {
			console.log("Geocoder gagal dengan status: " + status);
		}
	});
}

// Zoom the map to fit the marker
function zoomToMarker(marker) {
	map.setZoom(18);
	map.setCenter(marker.getPosition());
}

// Zoom the map to fit the polygon bounds
function zoomToPolygon(polygon) {
	var bounds = new google.maps.LatLngBounds();
	polygon.getPath().forEach(function (latLng) {
		bounds.extend(latLng);
	});
	map.fitBounds(bounds);
}

// Remove all markers from the map
function removeMarkers() {
	map.getMarkers().forEach(function (marker) {
		marker.setMap(null);
	});
}

// Remove all polygons from the map
function removePolygons() {
	map.getPolygons().forEach(function (polygon) {
		polygon.setMap(null);
	});
}

// Extend the Map object to add a getMarkers() method
google.maps.Map.prototype.getMarkers = function () {
	var markers = [];
	for (var i = 0; i < this.overlayMapTypes.length; i++) {
		var overlay = this.overlayMapTypes.getAt(i);
		if (overlay instanceof google.maps.Marker) {
			markers.push(overlay);
		} else if (overlay instanceof google.maps.Polygon) {
			overlay.getPath().forEach(function (path) {
				if (path instanceof google.maps.Marker) {
					markers.push(path);
				}
			});
		}
	}
	return markers;
};

// Extend the Map object to add a getPolygons() method
google.maps.Map.prototype.getPolygons = function () {
	var polygons = [];
	for (var i = 0; i < this.overlayMapTypes.length; i++) {
		var overlay = this.overlayMapTypes.getAt(i);
		if (overlay instanceof google.maps.Polygon) {
			polygons.push(overlay);
		}
	}
	return polygons;
};

// Call the initMap function to initialize the map
initMap();
