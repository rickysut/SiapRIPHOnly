@extends('t2024.layouts.admin')
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
{{-- @can('spatial_data_access') --}}
	<div class="row">
			<div id="myMap" style="height:500px; width: 100%;"></div>
		<div class="panel">
			<div class="panel-container">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="panel" id="panel-peta">
				<div class="panel-container show">
					<form action="">
					<div class="accordion" id="data-laporan">
						<div class="card">
							<div class="card-header">
								<div class="card-title" data-toggle="collapse" data-target="#data-realisasi" aria-expanded="true">
									<div class="d-flex flex-row align-items-center">
										<div class="info-card-text">
											<div class="text-truncate text-truncate-lg">Data Realisasi: {{$data->kode_spatial}}</div>
										</div>
									</div>
									<span class="ml-auto align-self-start">
										<span class="collapsed-reveal">
											<i class="fal fa-chevron-up fs-xl"></i>
										</span>
										<span class="collapsed-hidden">
											<i class="fal fa-chevron-down fs-xl"></i>
										</span>
									</span>
								</div>
							</div>
							<div id="data-realisasi" class="collapse show" data-parent="#data-laporan" style="">
								<div class="card-body">
									<ul class="list-group mb-3">
										<li class="list-group-item d-flex justify-content-between align-item-start">
											<span class="text-muted">Nama Petani</span>
											<span class="fw-bold" id="">{{$data->spatial->nama_petani}}</span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-item-start">
											<span class="text-muted">NIK Petani</span>
											<span class="fw-bold" id="">{{$data->ktp_petani}}</span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-item-start">
											<span class="text-muted">Luas Lahan (m2)</span>
											<span class="fw-bold" id="">{{ number_format($data->luas_lahan, 0, ',', '.') }}</span>
										</li>
									</ul>
									<div class="row flex">
										<input type="file" accept="image/*" capture="camera" id="cameraInput" style="display:none;">
										<div class="col-12 mb-3">
											<label for="mulai_tanam">Tanggal Awal Tanam</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="mulai_tanam" id="mulai_tanam" class="font-weight-bold form-control form-control-lg bg-white">
											</div>
											<span class="help-block">Tanggal mulai penanaman.</span>
										</div>
										<div class="col-12 mb-3">
											<label for="akhir_tanam">Tanggal Akhir Tanam</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="akhir_tanam" id="akhir_tanam" class="font-weight-bold form-control form-control-lg  bg-white">
											</div>
											<span class="help-block">Tanggal akhir penanaman.</span>
										</div>
										<div class="col-12 mb-3">
											<label for="luas_tanam">Luas Tanam (m2)</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-ruler"></i></span>
												</div>
												<input type="number" value="" name="luas_tanam" id="luas_tanam" class="font-weight-bold form-control form-control-lg bg-white">
											</div>
											<span class="help-block">Luas lahan yang ditanami.</span>
										</div>
										<div class="col-12 mb-3">
											<label for="mulai_tanam">Tanggal Awal Produksi</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="mulai_panen" id="mulai_panen" class="font-weight-bold form-control form-control-lg bg-white">
											</div>
											<span class="help-block">Tanggal dimulainya pemanenan.</span>
										</div>
										<div class="col-12 mb-3">
											<label for="akhir_tanam">Tanggal Akhir Produksi</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="akhir_panen" id="akhir_panen" class="font-weight-bold form-control form-control-lg bg-white">
											</div>
											<span class="help-block">Tanggal akhir dilaksanakannya pemanenan.</span>
										</div>
										<div class="col-12 mb-3">
											<label for="volume">Volume Produksi (ton)</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-balance-scale"></i></span>
												</div>

												<input type="number" step="1" value="" name="volume" id="volume" class="font-weight-bold form-control form-control-lg bg-white">
											</div>
											<span class="help-block">Total produksi yang diperoleh.</span>
										</div>
										<div class="col-12 mb-3">
											<label for="volume">Untuk Benih (ton)</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-seedling"></i></span>
												</div>
												<input type="number" value="" name="vol_benih" id="vol_benih" class="font-weight-bold form-control form-control-lg bg-white" disabled="">
											</div>
											<span class="help-block">Total produksi yang disimpan sebagai benih.</span>
										</div>
										<div class="col-12 mb-3">
											<label for="volume">Untuk Dijual (ton)</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-truck-loading"></i></span>
												</div>
												<input type="number" value="" name="vol_jual" id="vol_jual" class="font-weight-bold form-control form-control-lg bg-white">
											</div>
											<span class="help-block">Total produksi yang dilepas ke konsumsi.</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-content">
						<button class="btn btn-primary btn-block">Simpan</button>
					</div>
					</form>
				</div>
			</div>

		</div>
	</div>

{{-- @endcans --}}

@endsection

<!-- start script for this page -->
@section('scripts')
@parent
<script>
	$(document).ready(function() {
		$('#vol_benih').prop('disabled', true);
    	// $('#vol_jual').prop('readonly', true);

		function updateVolJual() {
			var volume = parseFloat($('#volume').val());
			var volBenih = parseFloat($('#vol_benih').val()) || 0; // Treat null as 0

			if (!isNaN(volume) && volume >= 0 && volBenih >= 0 && volBenih <= volume) {
				$('#vol_jual').val(volume - volBenih);
			} else {
				$('#vol_jual').val('');
			}
		}

		// Reset vol_benih and vol_jual on change of volume
		$('#volume').on('input', function() {
			var volume = parseFloat($(this).val());
			if (isNaN(volume) || volume < 0) {
				$(this).val('').attr('placeholder', 'Masukkan nilai volume yang valid.');
				$('#vol_benih').val('').prop('disabled', true);
				$('#vol_jual').val('').prop('readonly', true);
			} else {
				$(this).attr('placeholder', '');
				$('#vol_benih').prop('disabled', false);
				$('#vol_jual').prop('readonly', false);
				updateVolJual();
			}
		});

		// Reset vol_jual if vol_benih is changed
		$('#vol_benih').on('input', function() {
			var volBenih = parseFloat($(this).val());
			var volume = parseFloat($('#volume').val());
			if (isNaN(volBenih) || volBenih < 0 || volBenih > volume) {
				$(this).val('').attr('placeholder', 'Masukkan nilai vol benih yang valid.');
			} else {
				$(this).attr('placeholder', '');
				updateVolJual();
			}
		});

		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				function(position) {
					console.log("Latitude: " + position.coords.latitude);
					console.log("Longitude: " + position.coords.longitude);

					var thisLat = position.coords.latitude;
					var thisLong = position.coords.longitude;
					$('#latitude').val(thisLat);
					$('#longitude').val(thisLong);
					$('#gpstatus').html('GPS status <span class="text-success font-weight-bold">Aktif</span>');

					initMap(thisLat, thisLong);
				},
				function(error) {
					console.error("Error Code = " + error.code + " - " + error.message);
					$('#gpstatus').html('GPS status <span class="text-danger font-weight-bold">Tidak Aktif/Tidak Diijinkan</span>');
				}
			);
		} else {
			console.log("Geolocation is not supported by this browser.");
			$('#gpstatus').html('Perangkat <span class="text-danger font-weight-bold">Tidak mendukung</span> Fitur ini.');
		}

		var lat = parseFloat('{{$data->spatial->latitude}}');
		var lng = parseFloat('{{$data->spatial->longitude}}');
		var poly = JSON.parse('{{$data->spatial->polygon}}'.replace(/&quot;/g,'"'));
		var kodeId = '{{$data->kode_spatial}}';
		console.log(lat, lng, poly, kodeId);
	});
	let myMap;
	const markers = [];
	let polygon;

	function initMap() {
		myMap = new google.maps.Map(document.getElementById("myMap"), {
			center: { lat: -2.5489, lng: 118.0149 },
			zoom: 5,
			mapTypeId: google.maps.MapTypeId.SATELLITE,
			mapTypeControl: false,
			streetViewControl: false,
			scaleControl: true,
			rotateControl: false,
			styles: [
				{
					featureType: 'all',
					elementType: 'labels',
					stylers: [{ visibility: 'off' }]
				}
			]
		});

		cameraBtn();
		createMarker();
		createPolygon();
	}

	function cameraBtn() {
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
		button.title = 'Take a Photo';
		controlDiv.appendChild(button);

		var icon = document.createElement('i');
		icon.className = 'fas fa-camera';
		icon.style.fontSize = '18px';
		icon.style.margin = '10px';
		button.appendChild(icon);

		button.addEventListener('click', function() {
			document.getElementById('cameraInput').click();
		});

		controlDiv.index = 1;
		myMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
	}

	function createMarker() {
		const latitude = parseFloat('{{$data->spatial->latitude}}');
		const longitude = parseFloat('{{$data->spatial->longitude}}');
		const kodeId = '{{$data->kode_spatial}}';

		if (!isNaN(latitude) && !isNaN(longitude)) {
			const position = new google.maps.LatLng(latitude, longitude);
			const marker = new google.maps.Marker({
				position: position,
				map: myMap,
				draggable: false,
				label: {
					text: kodeId,
					color: "white", // Set the label text color to white
					fontSize: "14px", // Optional: Adjust the font size
					fontWeight: "bold" // Optional: Make the label bold
				}
			});
			markers.push(marker);
			myMap.setCenter(position);
			myMap.setZoom(18);
		}
	}

	function createPolygon() {
		let polygonCoords = '{{$data->spatial->polygon}}'.replace(/&quot;/g,'"');
		if (polygonCoords !== "") {
			try {
				const parsedCoords = JSON.parse(polygonCoords).map(coord => ({ lat: coord.lat, lng: coord.lng }));
				if (polygon) {
					polygon.setMap(null);
				}
				polygon = new google.maps.Polygon({
					paths: parsedCoords,
					strokeColor: "#0000FF",
					strokeOpacity: 0.8,
					strokeWeight: 2,
					fillColor: "#FF0000",
					fillOpacity: 0.35,
					map: myMap,
					editable: false,
					draggable: false,
				});
				const bounds = new google.maps.LatLngBounds();
				parsedCoords.forEach(point => bounds.extend(point));
				myMap.fitBounds(bounds);
			} catch (e) {
				console.error("Invalid polygon coordinates: ", e);
			}
		}
	}

	window.addEventListener('load', function() {
		initMap();
	});
</script>

@endsection
