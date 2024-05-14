@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
{{-- <script src="{{ asset('js/gmap/js.js') }}"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/Turf.js/6.3.0/turf.min.js"></script>

@endsection
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheaderwithfilter')
@include('partials.sysalert')
{{-- @can('commitment_show')  --}}
	<div class="row">
		<div class="col-12">
			<div class="panel" id="panel-1">
				<div class="panel-container show">
					<div id="allMap" style="height: 500px; width: 100%;" class="shadow-sm border-1"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="row d-flex align-items-top">
		<div class="col-md-6 collapse" id="panelData1">
			<div class="card text-left">
				<div class="card-body">
					<ul class="list-group">
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Nomor RIPH</span>
							<span class="fw-500" id="no_ijin"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Periode RIPH</span>
							<span class="fw-500" id="perioderiph"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Nomor Perjanjian</span>
							<span class="fw-500" id="pks"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Kelompoktani</span>
							<span class="fw-500" id="kelompok"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Petani</span>
							<span class="fw-500" id="petani"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Mulai Tanam</span>
							<span class="fw-500" id="mulaitanam"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Akhir Tanam</span>
							<span class="fw-500" id="akhirtanam"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Luas Tanam (ha)</span>
							<span class="fw-500" id="luas_tanam"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Nama Lokasi</span>
							<span class="fw-500" id="lokasi"></span>
						</li>
						<li class="list-group-item">
							<a class="text-muted">Lokasi Tanam: </a><br>
							<span class="fw-500" id="alamat"></span><br>
							<span class="help-block">Alamat menurut data Peta Goggle berdasarkan titik kordinat yang diberikan.</span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Varietas ditanam</span>
							<span class="fw-500" id="varietas"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Mulai Panen</span>
							<span class="fw-500" id="mulaipanen"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Akhir Panen</span>
							<span class="fw-500" id="akhirpanen"></span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="text-muted">Volume (ton)</span>
							<span class="fw-500" id="volume"></span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6 collapse" id="panelData2">
			<div class="card text-left">
				<div class="card-body">
					<div class="row row-cols-1 row-cols-md-2 js-lightgallery" id="galleryFotoTanam">
					</div>
					<div class="row row-cols-1 row-cols-md-2 js-lightgallery" id="galleryFotoProduksi">
					</div>
				</div>
			</div>
		</div>
	</div>

{{-- @endcan --}}

@endsection

<!-- start script for this page -->
@section('scripts')
<script src="{{ asset('js/miscellaneous/lightgallery/lightgallery.bundle.js') }}"></script>
@parent
{{-- <script src="{{ asset('js/gmap/clickMap.js') }}"></script> --}}

<script>
    $(document).ready(function() {
        $("#periodetahun").select2({
            placeholder: "--Pilih tahun",
        });
		// $("#company").select2({
        //     placeholder: "--Pilih Pelaku Usaha",
        // });
		// Add an event listener to the periodetahun select element
			//

		var $initScope = $('#js-lightgallery');
		if ($initScope.length)
		{
			$initScope.justifiedGallery(
			{
				border: -1,
				rowHeight: 150,
				margins: 8,
				waitThumbnailsLoad: true,
				randomize: false,
			}).on('jg.complete', function()
			{
				$initScope.lightGallery(
				{
					thumbnail: true,
					animateThumb: true,
					showThumbByDefault: true,
				});
			});
		};
		$initScope.on('onAfterOpen.lg', function(event)
		{
			$('body').addClass("overflow-hidden");
		});
		$initScope.on('onCloseAfter.lg', function(event)
		{
			$('body').removeClass("overflow-hidden");
		});


		//data peta


    });
</script>

@if (Auth::user()->roles[0]->title == 'User')
	<script>
		$(document).ready(function() {
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
		});
	</script>
@else
	<script>
		$(document).ready(function() {
			$("#periodetahun").on("change", handlePeriodetahunChange);
			var map;
			var markers = [];
			var polygons = [];
			var infoWindow = new google.maps.InfoWindow();

			function initMap() {
				map = new google.maps.Map(document.getElementById("allMap"), {
					center: { lat: -2.548926, lng: 118.014863 },
					zoom: 5,
					// mapTypeId: google.maps.MapTypeId.HYBRID,
					mapId: 'allMap',
				});
			}
			function handlePeriodetahunChange() {
				initMap();
				$("#panelData1").addClass("collapse");
				$("#panelData2").addClass("collapse");
				var periodetahun = $(this).val();
				var url = "/admin/map/getAllMapData/" + periodetahun;
				$.ajax({
					url: url,
					type: "GET",
					dataType: "json",
					success: function (data) {
						handleMarkerData(data);
					},
				});
			}
			function handleMarkerData(data) {
				removeMarkers();
				removePolygons();

				$.each(data, function (index, dataRealisasi) {
					if (dataRealisasi.latitude && dataRealisasi.longitude) {
						createMarker(dataRealisasi);
						createPolygon(dataRealisasi);
					}
				});
			}

			function createMarker(dataRealisasi) {

				var marker = new google.maps.marker.AdvancedMarkerElement ({
					position: {
						lat: parseFloat(dataRealisasi.latitude),
						lng: parseFloat(dataRealisasi.longitude),
					},
					map: map,
				});
				markerId = parseFloat(dataRealisasi.id);

				marker.addListener("click", function () {
					showMarkerDetails(marker, markerId);
				});
			}
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

				polygon.addListener("click", function () {
					zoomToPolygon(polygon);
				});
			}

			function showMarkerDetails(marker, markerId) {
				var geocoder = new google.maps.Geocoder();
				var latlng = marker.position;

				geocoder.geocode({ 'location': latlng }, function(results, status) {
					if (status === 'OK') {
						if (results[0]) {
							var address = results[0].formatted_address;
							console.log("Alamat:", address);
							$.ajax({
								url: "/admin/map/getSingleMarker/" + markerId,
								type: "GET",
								dataType: "json",
								success: function (data) {
									if (data.length > 0) {
										var markerData = data[0];
										var options = { year: 'numeric', month: 'long', day: 'numeric' };
										var startDate = new Date(markerData.mulaitanam);
										var endDate = new Date(markerData.akhirtanam);
										var awalPanen = new Date(markerData.mulaipanen);
										var akhirPanen = new Date(markerData.akhirpanen);

										var formattedStartDate = new Date(markerData.mulaitanam).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
										var formattedEndDate = new Date(markerData.akhirtanam).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
										var formattedAwalPanen = new Date(markerData.mulaipanen).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
										var formattedAkhirPanen = new Date(markerData.akhirpanen).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });

										var content =
											'<div id="content">' +
												'<div id="siteNotice">' +
												"</div>" +
												'<h1 id="" class="subheader-title mb-3">'+ markerData.nama_lokasi + '</h1>' +
												'<div id="bodyContent">' +
													'<ul class="list-group">' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Perusahaan</span>' +
															'<span class="fw-500" id="company">' + markerData.company + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Nomor RIPH</span>' +
															'<span class="fw-500" id="no_ijin">' + markerData.no_ijin + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Periode RIPH</span>' +
															'<span class="fw-500" id="perioderiph">' + markerData.perioderiph + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Nomor Perjanjian</span>' +
															'<span class="fw-500" id="pks">' + markerData.no_perjanjian + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Kelompok Tani</span>' +
															'<span class="fw-500" id="kelompok">' + markerData.nama_kelompok + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Petani</span>' +
															'<span class="fw-500" id="petani">' + markerData.nama_petani + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Mulai Tanam</span>' +
															'<span class="fw-500" id="mulaitanam">' + formattedStartDate + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Akhir Tanam</span>' +
															'<span class="fw-500" id="akhirtanam">' + formattedEndDate + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Luas Tanam (ha)</span>' +
															'<span class="fw-500" id="luas_tanam">' + markerData.luas_tanam + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Nama Lokasi</span>' +
															'<span class="fw-500" id="lokasi">' + markerData.nama_lokasi + '</span>' +
														'</li>' +
														'<li class="list-group-item">' +
															'<a class="text-muted">Lokasi Tanam: </a><br>' +
															'<span class="fw-500" id="alamat">' + address + '</span><br>' +
															'<span class="help-block">Alamat menurut data Peta Google berdasarkan titik kordinat yang diberikan.</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Varietas ditanam</span>' +
															'<span class="fw-500" id="varietas">' + markerData.varietas + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Mulai Panen</span>' +
															'<span class="fw-500" id="mulaipanen">' + formattedAwalPanen + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Akhir Panen</span>' +
															'<span class="fw-500" id="akhirpanen">' + formattedAkhirPanen + '</span>' +
														'</li>' +
														'<li class="list-group-item d-flex justify-content-between align-items-center">' +
															'<span class="text-muted">Volume (ton)</span>' +
															'<span class="fw-500" id="volume">' + markerData.volume + '</span>' +
														'</li>' +
													'</ul>'
												"</div>" +
										    "</div>";

											if (markerData.fotoTanam.length > 0 || markerData.fotoProduksi.length > 0) {
												content += "<div class='panel-hdr'>" +
															"<h2>Foto-foto</h2>" +
														"</div>" +
														"<div class='panel-container'><div class='panel-content'>";

												$.each(markerData.fotoTanam, function(index, foto) {
													content += "<img class='js-lightgallery' src='" + foto.url + "' alt='Foto Tanam " + index + "'><br>";
												});

												$.each(markerData.fotoProduksi, function(index, foto) {
													content += "<img class='js-lightgallery' src='" + foto.url + "' alt='Foto Produksi " + index + "'><br>";
												});

												content += "</div></div>";
											}

										infoWindow.close();
										infoWindow.setContent(content);
										infoWindow.open(map, marker);
									} else {
										console.log("Data marker tidak ditemukan");
									}
									zoomToMarker(marker);
								},
							});
						} else {
							console.log('Alamat tidak ditemukan');
						}
					} else {
						console.log('Geocoder gagal dengan kode: ' + status);
					}
				});
			}

			function zoomToMarker(marker) {
				map.setZoom(18);
				map.setCenter(marker.position);
			}

			function zoomToPolygon(polygon) {
				var bounds = new google.maps.LatLngBounds();
				polygon.getPath().forEach(function (latLng) {
					bounds.extend(latLng);
				});
				map.fitBounds(bounds);
			}

			function removeMarkers() {
				map.getMarkers().forEach(function (marker) {
					marker.setMap(null);
				});
			}

			function removePolygons() {
				map.getPolygons().forEach(function (polygon) {
					polygon.setMap(null);
				});
			}

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
		});
	</script>
@endif
@endsection
