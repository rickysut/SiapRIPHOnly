@extends('t2024.layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

{{-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> --}}
@endsection

@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('commitment_show')

	@php
		$npwp = str_replace(['.', '-'], '', $data['npwpCompany']);
	@endphp
	<div class="row">
		<div class="col-12">
			<div class="panel" id="panel-data">
				<div class="panel-hdr">
					<h2>
						Data Tanam <span class="fw-300">
							<i>Lokasi</i>
						</span>
					</h2>
					<div class="panel-toolbar">
						@include('t2024.partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="row d-flex flex-row justify-content-between">
							<div class="col-md-4 mb-5">
								<div class="mb-5" id="myMap" style="height: 400px; width: 100%;"></div>
							</div>
							<div class="col-md-8">
								<form action="" method="POST" enctype="multipart/form-data">
									@csrf
									<input type="hidden" name="form_action" value="form1">
									<input type="hidden" name="npwp_company" value="{{$data['pks']->npwp}}">
									<input type="hidden" name="no_ijin" value="{{$data['pks']->no_ijin}}">
									<input type="hidden" name="poktan_id" value="{{$data['pks']->poktan_id}}">
									<input type="hidden" name="pks_id" value="{{$data['pks']->id}}">
									<input type="hidden" name="anggota_id" value="{{$data['spatial']->kode_spatial}}">
									<input type="hidden" name="lokasi_id" value="{{$data['lokasi']->id}}">
									<div class="row" hidden>
										<div class="col-12 mb-5">
											<div class="form-group">
												<label class="form-label" for="kabupaten_id">
													Pilih Kabupaten
												</label>
												<select class="select2-kabupaten form-control" id="kabupaten_id">
													<option value="" hidden></option>
												</select>
											</div>
										</div>
										<div class="col-md-6 mb-5">
											<div class="form-group">
												<label class="form-label" for="kecamatan_id">
													Pilih Kecamatan
												</label>
												<select class="select2-kecamatan form-control" id="kecamatan_id">
													<option value="" hidden></option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label" for="spatial_data">
													Pilih Lokasi Lahan
												</label>
												<select class="select2-spatial form-control" id="spatial_data">
													<option value="" hidden></option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-12">
											<h5 class="font-weight-bold">
												Data Lokasi
											</h5>
										</div>
										<div class="form-group col-md-6">
											<label>Pemilik/Pengelola</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-user"></i></span>
												</div>
												<input type="text" value="{{$data['anggota']->nama_petani}}"
													name="nama_petani" id="nama_petani"
													class="font-weight-bold form-control form-control-sm" readonly />
											</div>
											<span class="help-block">Nama Petani Pemilik/Pengelola Lokasi sesuai Database Simethris.</span>
										</div>
										<div class="form-group col-md-6 ">
											<label>NIK Pemilik/Pengelola</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-address-card"></i></span>
												</div>
												<input type="text" value="{{$data['anggota']->ktp_petani}}"
													name="nik_petani" id="nik_petani"
													class="font-weight-bold form-control form-control-sm" readonly />
											</div>
											<span class="help-block">NIK Petani Pemilik/Pengelola Lokasi sesuai Database Simethris.</span>
										</div>
										<div class="form-group col-md-6 ">
											<label>Kode Lokasi</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-map-signs"></i></span>
												</div>
												<input type="text" value="{{$data['spatial']->kode_spatial}}"
													name="kode_lokasi" id="kode_lokasi"
													class="font-weight-bold form-control form-control-sm" readonly />
											</div>
											<span class="help-block">Kode Spatial untuk lokasi ini menurut Database Simethris.</span>
										</div>
										<div class="form-group col-md-6 ">
											<label>Luas Area (m2)<sup class="text-info"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-ruler-combined"></i></span>
												</div>
												<input type="text" value="{{$data['spatial']->luas_lahan}}"
													name="luas_kira" id="luas_kira" readonly
													class="font-weight-bold form-control form-control-sm" />
											</div>
											<span class="help-block">Luas bidang diukur menurut Database.</span>
										</div>
										<div class="form-group col-md-6 ">
											<label>Latitude <sup class="text-info"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-map-marker"></i></span>
												</div>
												<input type="text" value="{{$data['spatial']->latitude}}"
													name="latitude" id="latitude" readonly
													class="font-weight-bold form-control form-control-sm" />
											</div>
											<span class="help-block">Koordinat Lintang lokasi</span>
										</div>
										<div class="form-group col-md-6">
											<label>Longitude <sup class="text-info"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-map-marker-alt"></i></span>
												</div>
												<input type="text" value="{{$data['spatial']->longitude}}"
													name="longitude" id="longitude" readonly
													class="font-weight-bold form-control form-control-sm" />
											</div>
											<span class="help-block">Koordinat Bujur lokasi</span>
										</div>
										<div class="form-group col-md-6 ">
											<label>Polygon<sup class="text-info"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-draw-polygon"></i></span>
												</div>
												<input type="text" value="{{$data['spatial']->polygon}}"
												name="polygon" id="polygon" readonly
												class="font-weight-bold form-control form-control-sm" />
											</div>
											<span class="help-block">Kurva bidang lahan yang ditanami.</span>
										</div>
										<div class="form-group col-md-6 ">
											<label>Altitude (mdpl) <sup class="text-danger"> **</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-ruler-vertical"></i></span>
												</div>
												<input type="text" value="{{$data['spatial']->altitude}}"
													name="altitude" id="altitude"
													class="font-weight-bold form-control form-control-sm" readonly/>
											</div>
											<span class="help-block">Ketinggian lokasi lahan (rerata ketinggain dpl)</span>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-12">
											<h5 class="font-weight-bold">Realisasi Tanam</h5>
										</div>
										<div class="form-group col-md-6">
											<label>Pengelola</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-user"></i></span>
												</div>
												<input type="text" value="{{$data['lokasi']->nama_petani}}"
													name="nama_petani_riph" id="nama_petani_riph"
													class="font-weight-bold form-control form-control-sm" readonly />
											</div>
											<span class="help-block">Nama Petani Pengelola Lokasi sesuai Data Rencana Tanam RIPH.</span>
										</div>
										<div class="form-group col-md-6 ">
											<label>NIK Pengelola</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-address-card"></i></span>
												</div>
												<input type="text" value="{{$data['lokasi']->ktp_petani}}"
													name="nik_petani_riph" id="nik_petani_riph"
													class="font-weight-bold form-control form-control-sm" readonly />
											</div>
											<span class="help-block">NIK Petani Pengelola Lokasi sesuai Data Rencana Tanam RIPH.</span>
										</div>
										<div class="form-group col-md-4">
											<label for="mulai_tanam">Tanggal Awal Tanam<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="mulai_tanam" id="mulai_tanam" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal mulai penanaman.</span>
										</div>
										<div class="form-group col-md-4">
											<label for="akhir_tanam">Tanggal Akhir Tanam<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="akhir_tanam" id="akhir_tanam" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal akhir penanaman.</span>
										</div>
										<div class="form-group col-md-4">
											<label for="luas_lahan">Luas Tanam (ha)<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-ruller"></i></span>
												</div>
												{{-- tambahkan ini
												max="{{ $anggota->luas_lahan - $anggota->datarealisasi->sum('luas_lahan') }}"
												untuk pembatasan dan aktifkan script --}}
												<input type="number" step="0.001" value="" name="luas_lahan" id="luas_lahan" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Luas lahan yang ditanami.</span>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-12">
											<h5 class="font-weight-bold">Realisasi Produksi</h5>
										</div>
										<div class="form-group col-md-4">
											<label for="mulai_tanam">Tanggal Awal Produksi<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="mulai_panen" id="mulai_panen" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal dimulainya pemanenan.</span>
										</div>
										<div class="form-group col-md-4">
											<label for="akhir_tanam">Tanggal Akhir Produksi<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="" name="akhir_panen" id="akhir_panen" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal akhir dilaksanakannya pemanenan.</span>
										</div>
										<div class="form-group col-md-4">
											<label for="volume">Volume Produksi (ton)<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-balance-scale"></i></span>
												</div>
												{{-- tambahkan ini
												max="{{ $anggota->luas_lahan - $anggota->datarealisasi->sum('luas_lahan') }}"
												untuk pembatasan dan aktifkan script --}}
												<input type="number" step="0.001" value="" name="volume" id="volume" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Total produksi yang diperoleh.</span>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-12">
											<h5 class="font-weight-bold">Penyaluran Hasil Produksi</h5>
										</div>
										<div class="form-group col-md-4">
											<label for="volume">Untuk Benih (ton)<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-seedling"></i></span>
												</div>
												<input type="number" step="0.001" value="" name="vol_benih" id="vol_benih" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Total produksi yang disimpan sebagai benih.</span>
										</div>
										<div class="form-group col-md-4">
											<label for="volume">Untuk Dijual (ton)<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-seedling"></i></span>
												</div>
												<input type="number" step="0.001" value="" name="vol_jual" id="vol_jual" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Total produksi yang dilepas ke konsumsi.</span>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-container show">
					<form action="" method="POST" enctype="multipart/form-data">

						<div class="panel-content">
							<div class="row">
								<div class="col-md-6">

								</div>
								<div class="col-md-6 border-left">

								</div>
							</div>
						</div>
						<div class="card-footer">
							<div class="d-flex justify-content-between align-items-center">
								<div class="d-none d-md-block">
									<span class="small mr-3"><span class="text-info mr-1"> *</span>: Autogenerate by System</span>
									<span class="small"><span class="text-danger mr-1"> *</span>: Wajib diisi</span>
								</div>
								<div class="justify-content-end ml-auto">
									<button class="btn btn-sm btn-primary" role="button" type="submit">
										<i class="fa fa-save mr-1"></i>Simpan
									</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent
{{-- <script src="{{ asset('js/gmap/map.js') }}"></script> --}}


<script>
	$(document).ready(function() {
		$("#kabupaten_id").select2({
			placeholder: "pilih kabupaten",
			allowClear: true
		});
		$("#kecamatan_id").select2({
			placeholder: "pilih kecamatan",
			allowClear: true
		});
		$("#spatial_data").select2({
			placeholder: "pilih lokasi",
			allowClear: true
		});

		var kabupatenSelect = $('#kabupaten_id');
		var kecamatanSelect = $('#kecamatan_id');
		var spatialSelect = $('#spatial_data');

		$.get('/wilayah/getAllKabupaten', function(data) {
			$.each(data, function(key, value) {
				var option = $('<option>', {
					value: value.kabupaten_id,
					text: value.nama
				});
				kabupatenSelect.append(option);
			});
		});

		kabupatenSelect.change(function() {
			var selectedKabupatenId = kabupatenSelect.val();
			kecamatanSelect.empty();
			spatialSelect.empty();

			kecamatanSelect.append($('<option>', {
				value: '',
				text: 'pilih kec'
			}));

			spatialSelect.append($('<option>', {
				value: '',
				text: 'pilih lokasi'
			}));
			$.get('/wilayah/getKecamatanByKabupaten/' + selectedKabupatenId, function(data) {
				$.each(data, function(key, value) {
					var option = $('<option>', {
						value: value.kecamatan_id,
						text: value.nama_kecamatan
					});
					kecamatanSelect.append(option);
				});
			});
		});

		kecamatanSelect.change(function() {
			var selectedKecamatanId = kecamatanSelect.val();
			spatialSelect.empty();

			spatialSelect.append($('<option>', {
				value: '',
				text: 'pilih spatial'
			}));

			$.get('/2024/datafeeder/getSpatialByKecamatan/' + selectedKecamatanId, function(data) {
				$.each(data, function(key, value) {
					var option = $('<option>', {
						value: value.kode_spatial,
						text: value.kode_spatial
					});
					spatialSelect.append(option);
				});
			});
		});

		var latitudeInput = $('#latitude');
		var longitudeInput = $('#longitude');
		var polygonInput = $('#polygon');
		var luasLahanInput = $('#luas_kira');
		var namaPetani = $('#nama_petani');
		var ktpPetani = $('#ktp_petani');

		spatialSelect.change(function() {
			var selectedSpatialKode = spatialSelect.val();
			var realKode = selectedSpatialKode.replace(/-/g, '');

			$.get('/2024/datafeeder/getSpatialByKode/' + realKode, function(data) {
				latitudeInput.val(data.latitude);
				longitudeInput.val(data.longitude);
				polygonInput.val(data.polygon);
				luasLahanInput.val(data.luas_lahan);
				namaPetani.text(data.nama_petani);
				ktpPetani.val(data.ktp_petani);

				clearMarkers();
				createMarker();
				createPolygon();
			});
		});

		function clearMarkers() {
			markers.forEach(marker => marker.setMap(null));
			markers.length = 0;
		}
	});

	let myMap;
	const markers = [];
	let polygon;

	function initMap() {
		myMap = new google.maps.Map(document.getElementById("myMap"), {
			center: { lat: -2.5489, lng: 118.0149 },
			zoom: 5,
			mapTypeId: google.maps.MapTypeId.SATELLITE,
			draggable: false,
			disableDefaultUI: true,
		});

		createMarker();
		createPolygon();
	}

	function createMarker() {
		const latitude = parseFloat(document.getElementById("latitude").value);
		const longitude = parseFloat(document.getElementById("longitude").value);
		if (!isNaN(latitude) && !isNaN(longitude)) {
			const position = new google.maps.LatLng(latitude, longitude);
			const marker = new google.maps.Marker({
				position: position,
				map: myMap,
				draggable: false,
			});
			markers.push(marker);
			myMap.setCenter(position);
			myMap.setZoom(18);
		}
	}

	function createPolygon() {
		let polygonCoords = document.getElementById("polygon").value;
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
{{-- aktifkan untuk pembatasan max input luas lahan
	document.getElementById("luas_lahan").addEventListener("input", function() {
		var maxLuasLahan = parseFloat({{ $data['anggota']->luas_lahan - $data['anggota']->datarealisasi->sum('luas_lahan') }});
		var inputLuasLahan = parseFloat(this.value);
		if (inputLuasLahan > maxLuasLahan) {
			this.value = maxLuasLahan;
		}
	}); --}}
@endsection
