@extends('t2024.layouts.admin')
@section('styles')
<style>
	.display-5{
		font-size: 1.8rem;
		font-weight: 300;
		line-height: 1.25;
	}
	.select2-container--default .select2-results {
		z-index: 1050; /* Pastikan dropdown muncul di atas modal */
	}
	.line-clamp-1 {
		display: -webkit-box;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
</style>
@endsection
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('spatial_data_access')
	<div class="modal fade" id="default-example-modal-sm-center" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm modal-dialog-right" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Pilih Kabupaten</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
				<div class="modal-body">
					{{-- <button type="button" id="select-all-btn" class="btn btn-sm btn-outline-primary mb-2">
						<i class="fal fa-square"></i> <span>Pilih semua</span>
					</button> --}}
					<div class="panel shadow-0">
						<div class="panel-container">
							<div class="panel-content custom-scroll"  style="max-height: 400px; overflow-y: auto;">
								@foreach ($indexKabupaten as $locus)
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input kabupaten-checkbox" id="{{ $locus['kabupaten_id'] }}" name="kabupaten_id">
										<label class="custom-control-label" for="{{ $locus['kabupaten_id'] }}">{{ $locus['nama_kab'] }}</label>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="detailModalLabel">Detail Data</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- Konten akan dimuat di sini -->
					<div id="infoLahan" class="mb-5"></div>
					<div id="kemitraanAktif" class="mb-5"></div>
					<div id="historyKemitraan" class="mb-5"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="locateMyLocationModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Cari Lokasi</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<label for="">Cari</label>
						<input type="text" name="locateKode" id="locateKodeLokasi" class="form-control" aria-describedby="helpId" placeholder="contoh: KDL_000001">
						<small id="helpId" class="text-muted">Cari lokasi berdasarkan Kode Lokasi</small>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					<button type="button" class="btn btn-primary" id="submitLocateKode">Cari</button>
				</div>
			</div>
		</div>
	</div>

	<section class="mb-3" id="map">
		<div class="row">
			<div class="col-12">
				<div class="panel" id="panel-1">
					<div class="panel-container">
						<div class="panel-content p-0">
							<div id="myMap" style="height:500px; width: 100%;"></div>
							<div class="row row-grid no-gutters">
								<div class="col-sm-12 col-md-6">
									<div class="px-3 py-2 d-flex align-items-center m-2">
										<div class="js-easy-pie-chart color-primary-500 position-relative d-inline-flex align-items-center justify-content-center" data-percent="100" data-piesize="" data-linewidth="5" data-linecap="butt">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
												<span class="js-percent d-block text-dark"></span>
											</div>
										</div>
										<span class="d-inline-block fs-lg ml-2 text-muted">Total Lahan</span>
										<div class="ml-auto d-inline-flex align-items-center">
											<div class="d-inline-flex flex-column ml-2 text-right fw-500">
												<span class="d-inline-block">
													<span id="totalLuas"></span> ha
												</span>
												<span class="d-inline-block">
													<span id="jmlLahan"></span> titik
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-6">
									<div class="px-3 py-2 d-flex align-items-center m-2">
										<div class="js-easy-pie-chart color-info-500 position-relative d-inline-flex align-items-center justify-content-center" id="pieChartAktif" data-percent="" data-piesize="50" data-linewidth="5" data-linecap="butt">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
												<span class="js-percent d-block text-dark"></span>
											</div>
										</div>
										<span class="d-inline-block fs-lg ml-2 text-muted">
											Lahan Aktif
										</span>
										<div class="ml-auto d-inline-flex align-items-center">
											<div class="d-inline-flex flex-column ml-2 text-right fw-500">
												<span class="d-inline-block">
													<span id="totalLahanAktif"></span> ha
												</span>
												<span class="d-inline-block">
													<span id="jmlLahanAktif"></span> titik
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-6">
									<div class="px-3 py-2 d-flex align-items-center m-2">
										<div class="js-easy-pie-chart color-warning-500 position-relative d-inline-flex align-items-center justify-content-center" id="pieChartMitra" data-percent="" data-piesize="50" data-linewidth="5" data-linecap="butt">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
												<span class="js-percent d-block text-dark"></span>
											</div>
										</div>
										<span class="d-inline-block fs-lg ml-2 text-muted">
											Lahan Bermitra
										</span>
										<div class="ml-auto d-inline-flex align-items-center">
											<div class="d-inline-flex flex-column ml-2 text-right fw-500">
												<span class="d-inline-block">
													<span id="totalLahanMitra"></span> ha
												</span>
												<span class="d-inline-block">
													<span id="jmlLahanMitra"></span> titik
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-6">
									<div class="px-3 py-2 d-flex align-items-center m-2">
										<div class="js-easy-pie-chart color-success-500 position-relative d-inline-flex align-items-center justify-content-center" id="pieChartTersedia" data-percent="" data-piesize="50" data-linewidth="5" data-linecap="butt">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
												<span class="js-percent d-block text-dark"></span>
											</div>
										</div>
										<span class="d-inline-block fs-lg ml-2 text-muted">
											{{-- <i class="fas fa-power-off color-success-500 mr-1"></i> --}}
											Lahan Tersedia
										</span>
										<div class="ml-auto d-inline-flex align-items-center">
											<div class="d-inline-flex flex-column ml-2 text-right fw-500">
												<span class="d-inline-block">
													<span id="luasTersedia"></span> ha
												</span>
												<span class="d-inline-block">
													<span id="jmlTersedia"></span> titik
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
@parent

{{-- data --}}

{{-- peta --}}
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).ready(function() {
        var allSelected = false;
		$('#select-all-btn').click(function() {
			// Toggle all checkboxes
			$('.kabupaten-checkbox').prop('checked', !allSelected);

			if (allSelected) {
				$(this).removeClass('btn-primary').addClass('btn-outline-primary');
				$(this).find('i').removeClass('fa-check-square').addClass('fa-square');
				$(this).find('span').text('Pilih semua');
			} else {
				$(this).removeClass('btn-outline-primary').addClass('btn-primary');
				$(this).find('i').removeClass('fa-square').addClass('fa-check-square');
				$(this).find('span').text('Batalkan pilihan');
			}

			allSelected = !allSelected;

			// Update map data after toggling all checkboxes
			updateMapData();
		});

		attachCheckboxListeners();

		fetchSummaryData()

        initMap();


        $('#default-example-modal-sm-center').on('shown.bs.modal', function () {
            $(".select2-placeholder-multiple").select2({
                dropdownParent: $('#default-example-modal-sm-center'),
                placeholder: "Select State",
                width: '100%'
            });
        });
    });

	function formatNumber(value, divisor = 1, decimalPlaces = 0) {
    // Divide the number and format with specified decimal places
		return new Intl.NumberFormat('id-ID', {
			minimumFractionDigits: decimalPlaces,
			maximumFractionDigits: decimalPlaces
		}).format(value / divisor);
	}

	function fetchSummaryData() {
		const baseUrl = '{{ route('2024.datafeeder.responseGetLocationInKabupatenSummary') }}';
		const selectedKabupaten = Array.from(document.querySelectorAll('.kabupaten-checkbox:checked'))
			.map(chk => chk.id);

		const url = new URL(baseUrl, window.location.origin);
		selectedKabupaten.forEach(id => url.searchParams.append('kabupaten_id[]', id));

		fetch(url, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			}
		})
		.then(response => response.json())
		.then(data => {
			// Update text content
			document.getElementById('totalLuas').textContent = formatNumber(data.totalLuas, 10000, 4);
			document.getElementById('jmlLahan').textContent = formatNumber(data.jmlLahan);
			document.getElementById('totalLahanAktif').textContent = formatNumber(data.totalLahanAktif, 10000, 4);
			document.getElementById('jmlLahanAktif').textContent = formatNumber(data.jmlLahanAktif);
			document.getElementById('totalLahanMitra').textContent = formatNumber(data.totalLahanMitra, 10000, 4);
			document.getElementById('jmlLahanMitra').textContent = formatNumber(data.jmlLahanMitra);
			document.getElementById('luasTersedia').textContent = formatNumber(data.luasTersedia, 10000, 4);
			document.getElementById('jmlTersedia').textContent = formatNumber(data.jmlTersedia);

			updatePieChart('pieChartAktif', data.prosenAktif);
			updatePieChart('pieChartMitra', data.prosenMitra);
			updatePieChart('pieChartTersedia', data.prosenTersedia);
		})
		.catch(error => console.error('Error fetching summary data:', error));
	}

	function updatePieChart(chartId, percentage) {
		const pieChart = document.getElementById(chartId);
		if (pieChart) {
			pieChart.setAttribute('data-percent', percentage.toFixed(0));

			const chartInstance = $(pieChart).data('easyPieChart');
			if (chartInstance) {
				chartInstance.update(percentage);
			} else {
				$(pieChart).easyPieChart({
					lineCap: 'butt',
					lineWidth: 5,
					size: 50,
					barColor: $(pieChart).data('color') || '#000',
				});
			}
		}
	}

    let map;
    var initialCenter = { lat: -2.5489, lng: 118.0149 };
    var initialZoom = 5;
    let markersArray = [];
    let polygonsArray = [];
	let markerCluster;
	// const markerCluster = new markerClusterer.MarkerClusterer({ markers, map });

    function filterKab(){
        var controlDiv = document.createElement('div');

        var button = document.createElement('button');
        button.style.backgroundColor = '#fff';
        button.style.border = 'none';
        button.style.outline = 'none';
        button.style.width = '40px';
        button.style.height = '40px';
        button.style.borderRadius = '2px';
        button.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
        button.style.cursor = 'pointer';
        button.style.marginRight = '10px';
        button.style.padding = '0';
        button.title = 'Pilih Kabupaten';
        controlDiv.appendChild(button);

        var icon = document.createElement('i');
        icon.className = 'fal fa-ballot-check';
        icon.style.fontSize = '18px';
        icon.style.margin = '10px';
        button.appendChild(icon);

        google.maps.event.addListener(map, 'dragend', function() {
            icon.style.color = '#000';
        });

        button.addEventListener('click', function() {
            $('#default-example-modal-sm-center').modal('show');
        });

        controlDiv.index = 2;
        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
    }

    function locateMyLocation(){
		var controlDiv = document.createElement('div');
		var button = document.createElement('button');
		button.style.backgroundColor = '#fff';
		button.style.border = 'none';
		button.style.outline = 'none';
		button.style.width = '40px';
		button.style.height = '40px';
		button.style.borderRadius = '2px';
		button.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
		button.style.cursor = 'pointer';
		button.style.marginRight = '10px';
		button.style.marginBottom = '10px';
		button.style.padding = '0';
		button.title = 'Cari Lokasi';
		controlDiv.appendChild(button);

		var icon = document.createElement('i');
		icon.className = 'fa fa-search';
		icon.style.fontSize = '18px';
		icon.style.margin = '10px';
		button.appendChild(icon);

		google.maps.event.addListener(map, 'dragend', function() {
			icon.style.color = '#000';
		});

		button.addEventListener('click', function() {
            $('#locateMyLocationModal').modal('show');
        });

		controlDiv.index = 3;
		map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv); // Changed position
	}

    function SpatialList(){
		var controlDiv = document.createElement('div');
		var button = document.createElement('button');
		button.style.backgroundColor = '#fff';
		button.style.border = 'none';
		button.style.outline = 'none';
		button.style.width = '40px';
		button.style.height = '40px';
		button.style.borderRadius = '2px';
		button.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
		button.style.cursor = 'pointer';
		button.style.marginRight = '10px';
		button.style.marginBottom = '10px';
		button.style.padding = '0';
		button.title = 'Tabular Lahan';
		controlDiv.appendChild(button);

		var icon = document.createElement('i');
		icon.className = 'fas fa-table';
		icon.style.fontSize = '18px';
		icon.style.margin = '10px';
		button.appendChild(icon);

		google.maps.event.addListener(map, 'dragend', function() {
			icon.style.color = '#000';
		});

		button.addEventListener('click', function() {
			// Open route {{route('2024.spatial.index')}}
			window.location.href = "{{ route('2024.spatial.spatialList') }}";
		});

		controlDiv.index = 4;
		map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv); // Changed position
	}

    function initMap() {
        map = new google.maps.Map(document.getElementById('myMap'), {
            mapTypeId: google.maps.MapTypeId.HYBRID,
            center: initialCenter,
            zoom: initialZoom,
            mapTypeControl: false,
            streetViewControl: false,
            scaleControl: true,
            rotateControl: false,
            styles: [
                {
                    featureType: 'all',
                    elementType: 'labels'
                    // stylers: [{ visibility: 'off' }]
                }
            ]
        });

        SpatialList();
        filterKab();
        locateMyLocation();
        attachCheckboxListeners();

		google.maps.event.addListener(map, 'bounds_changed', function() {
			console.log('viewport changes');
			const kabupatenIds = getKabupatenIds();
			if (kabupatenIds.length > 0) {
				fetchSpatialData(kabupatenIds);

			}
		});
    }

    function attachCheckboxListeners() {
		const checkboxes = document.querySelectorAll('.kabupaten-checkbox');
		checkboxes.forEach((checkbox) => {
			checkbox.addEventListener('change', updateMapData);
		});
	}

	function updateMapData() {
		const checkboxes = document.querySelectorAll('.kabupaten-checkbox');
		const selectedKabupaten = Array.from(checkboxes)
			.filter(chk => chk.checked)
			.map(chk => chk.id);

		if (selectedKabupaten.length > 0) {
			fetchSpatialData(selectedKabupaten);
			fetchSummaryData(); // Fetch summary data as well
		} else {
			clearMapMarkers();
			clearMapPolygons();
			clearMapCluster();
			map.setZoom(initialZoom);
			map.setCenter(initialCenter);

			// Clear summary data
			fetchSummaryData();
		}
	}

	function fetchSpatialData(kabupatenIds) {
		const bounds = map.getBounds();
		const viewport = bounds ? {
			north: bounds.getNorthEast().lat(),
			south: bounds.getSouthWest().lat(),
			east: bounds.getNorthEast().lng(),
			west: bounds.getSouthWest().lng()
		} : null;

		const baseUrl = '{{ route('2024.datafeeder.responseGetLocationInKabupaten') }}';
		const url = new URL(baseUrl, window.location.origin);

		kabupatenIds.forEach(id => url.searchParams.append('kabupaten_id[]', id));

		if (viewport) {
			url.searchParams.append('viewport', JSON.stringify(viewport));
		}

		fetch(url, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			}
		})
		.then(response => response.json())
		.then(data => {
			console.log(`Data fetched for viewport: ${data.length} records`);
			plotMarkersOnMap(data);
		})
		.catch(error => console.error('Error fetching spatial data:', error));
	}

	function plotMarkersOnMap(spatials) {
		if (markerCluster) {
			markerCluster.clearMarkers();
		}
		clearMapMarkers();
		// clearMapPolygons();

		const infoWindow = new google.maps.InfoWindow();

		spatials.forEach(spatial => {
			const latLng = new google.maps.LatLng(spatial.latitude, spatial.longitude);
			const markerIcon = spatial.status === 0 ? 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
			const markerColor = spatial.status === 0 ? '#00FF00' : '#FF0000';
			const marker = new google.maps.Marker({
				position: latLng,
				map: map,
				title: spatial.kode_spatial,
				icon: markerIcon,
				label: {
					text: spatial.kode_spatial,
					color: '#fff',
					fontSize: '12px',
					fontWeight: 'bold'
				}
			});
			markersArray.push(marker);

			// if (spatial.polygon) {
			// 	let polygonCoords;
			// 	try {
			// 		polygonCoords = JSON.parse(spatial.polygon).map(coord => new google.maps.LatLng(coord.lat, coord.lng));
			// 	} catch (error) {
			// 		console.error('Error parsing polygon JSON:', error);
			// 		return;
			// 	}

			// 	if (polygonCoords.length > 0) {
			// 		const polygon = new google.maps.Polygon({
			// 			paths: polygonCoords,
			// 			strokeColor: markerColor,
			// 			strokeOpacity: 0.8,
			// 			strokeWeight: 2,
			// 			fillColor: markerColor,
			// 			fillOpacity: 0.0
			// 		});

			// 		polygon.setMap(map);
			// 		polygonsArray.push(polygon);
			// 	}
			// }

			marker.addListener('click', function() {
				clearMapPolygons();
				const currentZoom = map.getZoom();
				const currentCenter = map.getCenter();

				map.setZoom(18);
				map.setCenter(marker.getPosition());

				fetchDetails(spatial.kode_spatial).then(details => {
					if (details.polygon) {
						let polygonCoords;
						try {
							polygonCoords = JSON.parse(details.polygon).map(coord => new google.maps.LatLng(coord.lat, coord.lng));
						} catch (error) {
							console.error('Error parsing polygon JSON:', error);
							return;
						}

						if (polygonCoords.length > 0) {
							console.log('Polygon Coordinates:', polygonCoords);

							const polygon = new google.maps.Polygon({
								paths: polygonCoords,
								strokeColor: markerColor,
								strokeOpacity: 0.8,
								strokeWeight: 2,
								fillColor: markerColor,
								fillOpacity: 0.0
							});

							polygon.setMap(map);
							polygonsArray.push(polygon);

							const northmostPoint = polygonCoords.reduce((northmost, coord) => {
								return coord.lat() > northmost.lat() ? coord : northmost;
							}, polygonCoords[0]);

							const infoWindowContent = `
								<ul class="list-group" style="min-width:300px;">
									<li class="list-group-item ${details.status === 0 ? 'bg-success' : 'bg-danger'} text-white">
										<span class="fw-700">${spatial.kode_spatial}</span>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
										<span class="text-muted">Status:</span>
										<span class="fw-500">${details.status === 0 ? 'Tersedia' : 'Bermitra'}</span>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
										<span class="text-muted">Petani/Pemilik:</span>
										<span class="fw-500">${details.nama_petani}</span>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
										<span class="text-muted">Luas Lahan:</span>
										<span class="fw-500">${details.luas} m2</span>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
										<span class="text-muted">Wilayah:</span>
										<span class="fw-500">${details.wilayah}</span>
									</li>
									<li class="list-group-item justify-content-center text-center">
										<button type="button" class="btn btn-info" onclick="openDetailModal('${spatial.kode_spatial}')">Detail</button>
									</li>
								</ul>
							`;

							infoWindow.setContent(infoWindowContent);
							infoWindow.setPosition(northmostPoint);
							infoWindow.open(map);

							// Tambahkan listener untuk menutup infoWindow dan mereset tampilan peta
							// google.maps.event.addListener(infoWindow, 'closeclick', function() {
							// 	map.setZoom(initialZoom);
							// 	map.setCenter(initialCenter);
							// 	clearMapPolygons(); // Clear polygons when infoWindow is closed
							// });
						}
					}
				}).catch(error => {
					console.error('Error fetching details:', error);
				});
			});
		});
		markerCluster = new markerClusterer.MarkerClusterer({
			markers: markersArray,
			map: map,
			gridSize: 70,
		});
	}

	function clearMapMarkers() {
        markersArray.forEach(marker => marker.setMap(null));
        markersArray = [];
    }

    function clearMapPolygons() {
		polygonsArray.forEach(polygon => {
			if (polygon) {
				polygon.setMap(null);
			}
		});
		polygonsArray = [];
	}

	function clearMapCluster() {
		if (markerCluster) {
			markerCluster.clearMarkers();
		}
		markerCluster = null;
		markersArray = [];
	}

	function getKabupatenIds() {
		const checkboxes = document.querySelectorAll('.kabupaten-checkbox');
		return Array.from(checkboxes)
			.filter(chk => chk.checked)
			.map(chk => chk.id);
	}

	document.getElementById('submitLocateKode').addEventListener('click', function() {
		const kodeSpatial = document.getElementById('locateKodeLokasi').value;

		if (!kodeSpatial) {
			alert('Silakan masukkan kode lokasi.');
			return;
		}

		const url = `{{ route('2024.datafeeder.responseGetLocationByKode') }}?kode_spatial=${kodeSpatial}`;

		fetch(url, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.length > 0) {
				plotMarkersOnMap(data);
				$('#locateMyLocationModal').modal('hide');
			} else {
				alert('Lokasi tidak ditemukan untuk kode lokasi yang diberikan.');
			}
		})
		.catch(error => console.error('Error fetching location data:', error));
	});

	function openDetailModal(kode_spatial) {
		const modalUrl = `{{ route('2024.datafeeder.responseGetSpatialMoreDetail', ['spatial' => '__spatial__']) }}`.replace('__spatial__', kode_spatial);

		$.getJSON(modalUrl, function(data) {
			// Info Lahan
			let infoLahanHtml = `
				<ul class="list-group">
					<li class="list-group-item d-flex justify-content-between align-items-start text-white bg-danger">
						<span class="text-uppercase">Informasi Lahan</span>
						<span class="">${data.infoLahan.status || '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kode Lahan</span>
						<span class="fw-500 col-6">${data.infoLahan.kode_spatial || '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Luas Lahan</span>
						<span class="fw-500 col-6">${data.infoLahan.luas_lahan ? data.infoLahan.luas_lahan + ' m2' : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Desa</span>
						<span class="fw-500 col-6">${data.infoLahan.desa ? data.infoLahan.desa.kelurahan_id : '-'} - ${data.infoLahan.kecamatan ? data.infoLahan.kecamatan.nama_kecamatan : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kecamatan</span>
						<span class="fw-500 col-6">${data.infoLahan.kecamatan ? data.infoLahan.kecamatan.kecamatan_id : '-'} - ${data.infoLahan.kecamatan ? data.infoLahan.kecamatan.nama_kecamatan : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kabupaten</span>
						<span class="fw-500 col-6">${data.infoLahan.kabupaten ? data.infoLahan.kabupaten.kabupaten_id : '-'} - ${data.infoLahan.kabupaten ? data.infoLahan.kabupaten.nama_kab : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Provinsi</span>
						<span class="fw-500 col-6">${data.infoLahan.provinsi ? data.infoLahan.provinsi.provinsi_id : '-'} - ${data.infoLahan.provinsi ? data.infoLahan.provinsi.nama : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Petani/Pengelola</span>
						<span class="fw-500 col-6">${data.infoLahan.nama_petani || '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kelompok Tani</span>
						<span class="fw-500 col-6">${data.infoPoktan.nama_kelompok || '-'}</span>
					</li>
				</ul>
			`;

			$('#infoLahan').html(infoLahanHtml);

			// Kemitraan Aktif
			if (data.kemitraanAktif) {
				let kemitraanAktifHtml = `
					<ul class="list-group">
						<li class="list-group-item d-flex justify-content-between align-items-start text-white bg-info">
							<span class="text-uppercase">Kemitraan Aktif</span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-start">
							<span class="text-muted col-6">Perusahaan</span>
							<span class="fw-500 col-6">${data.kemitraanAktif.nama || '-'}</span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-start">
							<span class="text-muted col-6">Nomor RIPH</span>
							<span class="fw-500 col-6">${data.kemitraanAktif.no_ijin || '-'}</span>
						</li>
						<li class="list-group-item">
							<span class="text-muted">Kegiatan dan Realisasi di Lahan</span>
							<table class="table table-hover table-striped table-bordered table-sm">
								<thead class="thead-themed">
									<tr>
										<th>Kegiatan</th>
										<th>Tanggal</th>
										<th>Jumlah</th>
										<th>Catatan</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Pengolahan Lahan</td>
										<td>${data.lokasi.lahandate ? data.lokasi.lahandate : '-'}</td>
										<td></td>
										<td>${data.lokasi.lahancomment ? data.lokasi.lahancomment : '-'}</td>
									</tr>
									<tr>
										<td>Persiapan Benih</td>
										<td>${data.lokasi.benihDate ? data.lokasi.benihDate : '-'}</td>
										<td>${data.lokasi.benihsize ? data.lokasi.benihsize : '-'} kg</td>
										<td>${data.lokasi.benihComment ? data.lokasi.benihComment : '-'}</td>
									</tr>
									<tr>
										<td>Pemasangan Mulsa</td>
										<td>${data.lokasi.mulsaDate ? data.lokasi.mulsaDate : '-'}</td>
										<td>${data.lokasi.mulsaSize ? data.lokasi.mulsaSize : '-'} roll</td>
										<td>${data.lokasi.mulsaComment ? data.lokasi.mulsaComment : '-'}</td>
									</tr>
									<tr>
										<td>Penanaman</td>
										<td>${data.lokasi.tgl_tanam ? data.lokasi.tgl_tanam : '-'}</td>
										<td>${data.lokasi.luas_tanam ? data.lokasi.luas_tanam : '-'} m2</td>
										<td>${data.lokasi.tanamComment ? data.lokasi.tanamComment : '-'}</td>
									</tr>
									<tr>
										<td>Pemupukan Pertama</td>
										<td>${data.lokasi.pupuk1Date ? data.lokasi.pupuk1Date : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Organik: ${data.lokasi.organik1 ? data.lokasi.organik1 : '-'} ton</li>
												<li>NPK: ${data.lokasi.npk1 ? data.lokasi.npk1 : '-'} kg</li>
												<li>Dolomit: ${data.lokasi.dolomit1 ? data.lokasi.dolomit1 : '-'} kg</li>
												<li>ZA: ${data.lokasi.za1 ? data.lokasi.za1 : '-'} kg</li>
										</td>
										<td>${data.lokasi.pupuk1Comment ? data.lokasi.pupuk1Comment : '-'}</td>
									</tr>
									<tr>
										<td>Pemupukan Kedua</td>
										<td>${data.lokasi.pupuk2Date ? data.lokasi.pupuk2Date : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Organik: ${data.lokasi.organik2 ? data.lokasi.organik2 : '-'} ton</li>
												<li>NPK: ${data.lokasi.npk2 ? data.lokasi.npk2 : '-'} kg</li>
												<li>Dolomit: ${data.lokasi.dolomit2 ? data.lokasi.dolomit2 : '-'} kg</li>
												<li>ZA: ${data.lokasi.za2 ? data.lokasi.za2 : '-'} kg</li>
										</td>
										<td>${data.lokasi.pupuk2Comment ? data.lokasi.pupuk2Comment : '-'}</td>
									</tr>
									<tr>
										<td>Pemupukan Ketiga</td>
										<td>${data.lokasi.pupuk3Date ? data.lokasi.pupuk3Date : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Organik: ${data.lokasi.organik3 ? data.lokasi.organik3 : '-'} ton</li>
												<li>NPK: ${data.lokasi.npk3 ? data.lokasi.npk3 : '-'} kg</li>
												<li>Dolomit: ${data.lokasi.dolomit3 ? data.lokasi.dolomit3 : '-'} kg</li>
												<li>ZA: ${data.lokasi.za3 ? data.lokasi.za3 : '-'} kg</li>
											</ul>
										</td>
										<td>${data.lokasi.pupuk3Comment ? data.lokasi.pupuk3Comment : '-'}</td>
									</tr>
									<tr>
										<td>Pengendalian OPT</td>
										<td>${data.lokasi.optDate ? data.lokasi.optDate : '-'}</td>
										<td></td>
										<td>${data.lokasi.optComment ? data.lokasi.optComment : '-'}</td>
									</tr>
									<tr>
										<td>Panen/Produksi</td>
										<td>${data.lokasi.tgl_panen ? data.lokasi.tgl_panen : '-'}</td>
										<td>${data.lokasi.volume ? data.lokasi.volume : '-'} kg</td>
										<td>${data.lokasi.prodComment ? data.lokasi.prodComment : '-'}</td>
									</tr>
									<tr>
										<td>Distribusi Hasil</td>
										<td>${data.lokasi.tgl_panen ? data.lokasi.tgl_panen : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Untuk benih: ${data.lokasi.vol_benih ? data.lokasi.vol_benih : '-'} kg</li>
												<li>Untuk Dijual: ${data.lokasi.vol_jual ? data.lokasi.vol_jual : '-'} kg</li>
											</ul>

										</td>
										<td>${data.lokasi.distComment ? data.lokasi.distComment : '-'}</td>
									</tr>
							</table>
						</li>
					</ul>
				`;
				$('#kemitraanAktif').html(kemitraanAktifHtml);
			}

			// History Kemitraan
			if (data.historyKemitraan && data.historyKemitraan.length > 0) {
				let historyKemitraanHtml = `
					<ul class="list-group">
						<li class="list-group-item d-flex justify-content-between align-items-start text-white bg-info">
							<span class="text-uppercase">Histori Kemitraan</span>
						</li>
						<li class="list-group-item">
							<table class="table table-hover table-striped table-bordered table-sm">
								<thead class="thead-themed">
									<tr>
										<th>Perusahaan</th>
										<th>Periode</th>
										<th>No Ijin</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									${data.historyKemitraan.map(item => `
										<tr>
											<td>${item.nama || '-'}</td>
											<td>${item.periodetahun || '-'}</td>
											<td>${item.no_ijin || '-'}</td>
											<td>LUNAS</td>
										</tr>
									`).join('')}
								</tbody>
							</table>
						</li>
					</ul>
				`;
				$('#historyKemitraan').html(historyKemitraanHtml);
			}

			// Tampilkan modal
			$('#detailModal').modal('show');
		});
	}

    function fetchDetails(kode_spatial) {
        return fetch(`{{ route('2024.datafeeder.responseGetSpatialDetail') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kode_spatial: kode_spatial })
        }).then(response => response.json())
        .then(data => data.details)
        .catch(error => {
            console.error('Error:', error);
            return {};
        });
    }
</script>

@endsection

{{-- {{ route('admin.task.commitments.pksmitra', $commitment->id) }} --}}
