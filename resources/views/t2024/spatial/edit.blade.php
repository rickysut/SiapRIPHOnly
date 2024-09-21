@extends('t2024.layouts.admin')
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('spatial_data_access')

	<div class="row">
		<div class="col-12">
			<div class="panel" id="panel-peta">
				<div class="panel-container show">
					<div class="panel-content">
						<form action="{{route('2024.spatial.updatesingle')}}" enctype="multipart/form-data" method="POST" id="formSubmit">
							@csrf
							<div class="row d-flex justify-content-between">
								<div class="col-lg-5 mb-5">
									<div id="myMap" style="height: 400px; width: 100%;"></div>
								</div>
								<div class="col-lg-7">
									<input class="form-control" type="hidden" id="latitude" name="latitude" value="{{$spatial->latitude}}" readonly>
									<input class="form-control" type="hidden" id="longitude" name="longitude" value="{{$spatial->longitude}}" readonly>
									<input class="form-control" type="hidden" id="polygon" name="polygon" value="{{$spatial->polygon}}" readonly>
									<input class="form-control" type="hidden" id="provinsi_id" name="provinsi_id" value="{{$spatial->provinsi_id}}" readonly>
									<input class="form-control" type="hidden" id="kabupaten_id" name="kabupaten_id" value="{{$spatial->kabupaten_id}}" readonly>
									<input class="form-control" type="hidden" id="kecamatan_id" name="kecamatan_id" value="{{$spatial->kecamatan_id}}" readonly>
									<input class="form-control" type="hidden" id="kelurahan_id" name="kelurahan_id" value="{{$spatial->kelurahan_id}}" readonly>
									<div class="form-group row">
										<label for="kode_spatial" class="col-sm-3 col-form-label">Kode Spatial</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="kode_spatial" name="kode_spatial" value="{{$spatial->kode_spatial}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="altitude" class="col-sm-3 col-form-label">Altitude (mdpl)</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="altitude" name="altitude" value="{{$spatial->altitude}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="luas_lahan" class="col-sm-3 col-form-label">Luas Lahan (m2)</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="luas_lahan" name="luas_lahan" value="{{$spatial->luas_lahan}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="provinsi_nama" class="col-sm-3 col-form-label">Provinsi</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="provinsi_nama" name="provinsi_nama" value="{{$spatial->provinsi->nama}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="kabupaten_nama" class="col-sm-3 col-form-label">Kabupaten</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="kabupaten_nama" name="kabupaten_nama" value="{{$spatial->kabupaten->nama_kab}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="kecamatan_nama" class="col-sm-3 col-form-label">Kecamatan</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="kecamatan_nama" name="kecamatan_nama" value="{{$spatial->kecamatan->nama_kecamatan}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="kelurahan_nama" class="col-sm-3 col-form-label">Desa</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="kelurahan_nama" name="kelurahan_nama" value="{{$spatial->desa->nama_desa}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="nama_petani" class="col-sm-3 col-form-label">Petani Pengelola</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="nama_petani" name="nama_petani" value="{{$spatial->nama_petani}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="ktp_petani" class="col-sm-3 col-form-label">KTP Pengelola</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="ktp_petani" name="ktp_petani" value="{{$spatial->ktp_petani}}" readonly>
										</div>
									</div>
									<div class="form-group row">
										<label for="komoditas" class="col-sm-3 col-form-label">Komoditas</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="komoditas" name="komoditas" value="{{$spatial->komoditas}}">
										</div>
									</div>
									<div class="form-group row">
										<label for="nama_petugas" class="col-sm-3 col-form-label">Petugas Pemetaan</label>
										<div class="col-sm-9">
											<input class="form-control" type="text" id="nama_petugas" name="nama_petugas" value="{{$spatial->nama_petugas}}">
										</div>
									</div>
									<div class="form-group row">
										<label for="tgl_peta" class="col-sm-3 col-form-label">Tanggal Pemetaan</label>
										<div class="col-sm-9">
											<input class="form-control" type="date" id="tgl_peta"  name="tgl_peta"value="{{$spatial->tgl_peta}}">
										</div>
									</div>
									<div class="form-group row">
										<label for="tgl_tanam" class="col-sm-3 col-form-label">Jadwal Tanam</label>
										<div class="col-sm-9">
											<input class="form-control" type="date" id="tgl_tanam" name="tgl_tanam" value="{{$spatial->tgl_tanam}}">
										</div>
									</div>

									<div class="d-flex justify-content-between mt-3">
										<div></div>
										<div>
											<button class="btn btn-primary" id="btnSubmit">Simpan</button>
										</div>
									</div>

								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="row" hidden>
		<div class="col-12">
			<div class="panel" id="panel-data" >
				<div class="panel-hdr">
					<h2>
						Data Lokasi
					</h2>
				</div>
				<div class="panel-container show">
					<table id="kmlTable" class="table table-sm table-bordered table-hover table-striped w-100">
						<thead>
							<tr>
								<th>Id</th>
								<th>Komoditas</th>
								<th>Poktan</th>
								<th>No</th>
								<th>Nama Petani</th>
								<th>Luas</th>
								<th>X</th>
								<th>Y</th>
								<th>Kecamatan</th>
								<th>Desa</th>
							</tr>
						</thead>
						<tbody id="kmlTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent
<script>
	$(document).ready(function() {

		var luasLahan = parseFloat($('#luas_lahan').val());
		var luasHektare = (luasLahan / 10000).toFixed(4);
		$('#luas_lahan').val(luasHektare);

		initMap();
	});
	let marker;
	let polygon;
	let myMap;

	function initMap() {
		var latitude = parseFloat($('#latitude').val());
		var longitude = parseFloat($('#longitude').val());
		var polygonCoordinates = JSON.parse($('#polygon').val());

		var myMap = new google.maps.Map(document.getElementById("myMap"), {
			center: { lat: latitude, lng: longitude },
			zoom: 10,
			mapTypeId: google.maps.MapTypeId.HYBRID,
			fullscreenControl: true,
			mapTypeControl: false,
			streetViewControl: false,
			zoomControl: false,
			scaleControl: true,
			rotateControl: false,
		});

		// Menampilkan marker
		var marker = new google.maps.Marker({
			position: { lat: latitude, lng: longitude },
			map: myMap,
			title: 'Lokasi'
		});

		// Menampilkan polygon
		var polygon = new google.maps.Polygon({
			paths: polygonCoordinates,
			strokeColor: "#FF0000",
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: "#FF0000",
			fillOpacity: 0.35,
		});
		polygon.setMap(myMap);

		// Mendapatkan batas (bounds) polygon
		var bounds = new google.maps.LatLngBounds();
		polygonCoordinates.forEach(function(coord) {
			bounds.extend(coord);
		});

		// Menyesuaikan peta untuk menampilkan semua koordinat polygon
		myMap.fitBounds(bounds);
	}

</script>

@endsection
