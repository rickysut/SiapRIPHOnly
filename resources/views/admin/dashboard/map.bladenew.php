@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
	{{-- modal show data --}}
	<!-- Modal -->
	<!-- Modal -->
<!-- Modal -->
	<div class="modal fade" id="markerModal" tabindex="-1" role="dialog" aria-labelledby="markerModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-left" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title fw-700" id="nama_lokasi"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="card no-shadow" id="card-1">
						<div class="card-body">
							<ul class="list-group mt-0">
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Nama Lokasi</a>
									<span class="fw-500" id="nama_lokasi"></span>
								</li>
								<li class="list-group-item p-2">
									<a class="text-muted">Lokasi Tanam: </a><br>
									<span class="fw-500" id="alamat"></span><br>
									<span class="help-block">Alamat menurut data Peta Goggle berdasarkan titik kordinat yang diberikan.</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Perusahaan</a>
									<span class="fw-500" id="company"></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">RIPH</a>
									<span class="fw-500" id="no_ijin"></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Periode RIPH</a>
									<span class="fw-500" id="perioderiph"></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">PKS</a>
									<span class="fw-500" id="no_perjanjian"></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Kelompoktani</a>
									<span class="fw-500" id="nama_kelompok"></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Petani</a>
									<span class="fw-500" id="nama_petani"></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Varietas ditanam</a>
									<span class="fw-500" id="varietas"></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Tanggal Tanam</a>
									<span>
										<span class="fw-500 mr-1" id="mulaitanam"></span>s.d
										<span class="fw-500 ml-1" id="akhirtanam"></span>
									</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Luas Tanam</a>
									<span>
										<span class="fw-500 mr-1" id="luas_tanam"></span>ha
									</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Tanggal Panen</a>
									<span>
										<span class="fw-500 mr-1" id="mulaipanen"></span>s.d
										<span class="fw-500 ml-1" id="akhirpanen"></span>
									</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-2">
									<a class="text-muted">Volume Produksi</a>
									<span>
										<span class="fw-500 mr-1" id="volume"></span>ton
									</span>
								</li>
							</ul>
						</div>
					</div>
					<div class="panel" id="panel-1">
						<div class="panel-hdr">
							<h2>Foto-foto</h2>
						</div>
						<div class="panel-container">
							<div class="panel-content">
								<div class="row row-cols-1 row-cols-md-2 js-lightgallery" id="galleryFotoTanam">
								</div>
								<div class="row row-cols-1 row-cols-md-2 js-lightgallery" id="galleryFotoProduksi">
								</div>
							</div>
						</div>
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
			// Declare global variables
			var map;
			var markers = [];
			var polygons = [];
			var infoWindow = new google.maps.InfoWindow();

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

				var contentString = '<div id="content" style="max-height: 300px; overflow-y: auto;">' +
					'<h1 id="firstHeading" class="firstHeading">' + dataRealisasi.nama_lokasi + '</h1>' +
					'<ul class="list-group">' +
					'<li class="list-group-item"><b>Perusahaan: </b>' + dataRealisasi.company + '</li>' +
					'<li class="list-group-item"><b>Petani: </b>' + dataRealisasi.nama_petani + '</li>' +
					'<li class="list-group-item"><b>Kelompok Tani: </b>' + dataRealisasi.nama_kelompok + '</li>' +
					'<li class="list-group-item"><b>Luas Tanam: </b>' + dataRealisasi.luas_tanam + ' ha</li>' +
					'<li class="list-group-item"><b>Volume Produksi: </b>' + dataRealisasi.volume + ' ton</li>' +
					'</ul>' +
					'</div>';

				marker.addListener('click', function() {
					infoWindow.close();
					infoWindow.setContent(contentString);
					infoWindow.open(map, marker);
				});

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
		});
	</script>
@endif
@endsection
