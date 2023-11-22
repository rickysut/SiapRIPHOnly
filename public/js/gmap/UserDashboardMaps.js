function initMap() {
	map = new google.maps.Map(document.getElementById("allMap"), {
		center: { lat: -2.548926, lng: 118.014863 },
		zoom: 5,
		mapTypeId: google.maps.MapTypeId.HYBRID,
	});
}

$("#periodetahun").on("change", handlePeriodetahunChange);

function handlePeriodetahunChange() {
	initMap();
	$("#panelData1").addClass("collapse");
	$("#panelData2").addClass("collapse");
	var periodetahun = $(this).val();
	var url = "/admin/mapDataByYear/" + periodetahun;

	// Make an AJAX request to retrieve marker data and polygons
	$.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		success: function (data) {
			$.each(data, function (index, dataRealisasi) {
				if (dataRealisasi.latitude && dataRealisasi.longitude) {
					var marker = new google.maps.Marker({
						position: {
							lat: parseFloat(dataRealisasi.latitude),
							lng: parseFloat(dataRealisasi.longitude),
						},
						map: map,
						id: dataRealisasi.id,
						npwp: dataRealisasi.npwp,
						perioderiph: dataRealisasi.perioderiph,
						latitude: dataRealisasi.latitude,
						longitude: dataRealisasi.longitude,
						no_ijin: dataRealisasi.no_ijin,
						no_perjanjian: dataRealisasi.no_perjanjian,
						nama_lokasi: dataRealisasi.nama_lokasi,
						dataFotoTanam: dataRealisasi.fotoTanam,
						dataFotoProduksi: dataRealisasi.fotoProduksi,

						nama_petani: dataRealisasi.nama_petani,
						nama_kelompok: dataRealisasi.nama_kelompok,
						nama_lokasi: dataRealisasi.nama_lokasi,

						altitude: dataRealisasi.altitude,
						luas_kira: dataRealisasi.luas_kira,
						mulaitanam: dataRealisasi.tgl_tanam,
						akhirtanam: dataRealisasi.tgl_akhir_tanam,
						luas_tanam: dataRealisasi.luas_tanam,
						varietas: dataRealisasi.varietas,
						mulaipanen: dataRealisasi.tgl_panen,
						akhirpanen: dataRealisasi.tgl_akhir_panen,
						volume: dataRealisasi.volume,

						company: dataRealisasi.company,
					});

					marker.addListener("click", function () {
						map.setZoom(15);
						map.panTo(marker.getPosition());

						// Send an AJAX request to get the marker data

						$.ajax({
							url: "/admin/mapDataById/" + dataRealisasi.id,
							type: "GET",
							dataType: "json",
							success: function (data) {
								// Create a string containing the marker data
								var markerId = marker.id;
								var npwp = marker.npwp;
								var no_ijin = marker.no_ijin;
								var perioderiph = marker.perioderiph;
								var no_perjanjian = marker.no_perjanjian;
								var nama_lokasi = marker.nama_lokasi;
								var fotoTanam = marker.dataFotoTanam;
								var fotoTanamHtml = "";
								var fotoProduksi = marker.dataFotoProduksi;
								var fotoProduksiHtml = "";

								var nama_petani = marker.nama_petani;
								var nama_kelompok = marker.nama_kelompok;
								var nama_lokasi = marker.nama_lokasi;
								var mulaitanam = marker.mulaitanam;
								var akhirtanam = marker.akhirtanam;
								var luas_tanam = marker.luas_tanam;
								var varietas = marker.varietas;
								var mulaipanen = marker.mulaipanen;
								var akhirpanen = marker.akhirpanen;
								var volume = marker.volume;

								var company = marker.company;

								// Set the modal content to the marker details
								// $("#markerModal #markerId").text(markerId); sample jika mengguakan modal
								$("#company").text(company);
								$("#no_ijin").text(no_ijin);
								$("#perioderiph").text(perioderiph);
								$("#pks").text(no_perjanjian);
								$("#kelompok").text(nama_kelompok);
								$("#petani").text(nama_petani);
								$("#lokasi").text(nama_lokasi);
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

								console.log(npwp);
								// Show the modal
								// $("#markerModal").modal("show");
								$("#panelData1").removeClass("collapse");
								$("#panelData2").removeClass("collapse");
							},
						});
					});
				}

				if (dataRealisasi.polygon) {
					var polygon = new google.maps.Polygon({
						paths: JSON.parse(dataRealisasi.polygon),
						strokeColor: "#FF0000",
						strokeOpacity: 0.8,
						strokeWeight: 2,
						fillColor: "#FF0000",
						fillOpacity: 0.35,
						map: map,
					});

					polygon.addListener("click", function () {
						var bounds = new google.maps.LatLngBounds();
						polygon.getPath().forEach(function (latLng) {
							bounds.extend(latLng);
						});
						map.fitBounds(bounds);
					});
				}
			});
		},
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
			// If the overlay is a polygon, iterate over its paths and add any markers to the list
			overlay.getPath().forEach(function (path) {
				if (path instanceof google.maps.Marker) {
					markers.push(path);
				}
			});
		}
	}
	return markers;
};

// Extend the Map object to add a

// Extend the Map object to add a getMarkers() method
google.maps.Map.prototype.getMarkers = function () {
	var markers = [];
	for (var i = 0; i < this.overlayMapTypes.length; i++) {
		var overlay = this.overlayMapTypes.getAt(i);
		if (overlay instanceof google.maps.Marker) {
			markers.push(overlay);
		} else if (overlay instanceof google.maps.Polygon) {
			// If the overlay is a polygon, iterate over its paths and add any markers to the list
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
initMap();
