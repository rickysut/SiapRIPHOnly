@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

{{-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> --}}
@endsection

@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
@can('commitment_show')

	@php
		$npwp = str_replace(['.', '-'], '', $npwpCompany);
	@endphp
	<div class="row">
		<div class="col-12">
			<div class="panel" id="panel-spasial">
				<div class="panel-hdr">
					<h2>
						Data Spasial <span class="fw-300">
							<i>Lokasi</i>
						</span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content card-header">
						<div class="row">
							<div class="form-group col-md-12">
								<label class="form-label" for="gmap">
									Pilih lokasi dan Buat Peta Polygon bidang lahan dari lokasi yang dipilih
									<sup class="text-danger"> *</sup>
								</label>
								<div id="myMap" style="height: 500px; width: 100%;"></div>
							</div>
						</div>
					</div>
					<div class="panel-content card-header">
						<form id="location-search-form">
							<div class="form-group mb-5" title="Cari lokasi yang diinginkan">
								<div class="input-group bg-white shadow-inset-2">
									<div class="input-group-prepend">
										<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
											<i class="fal fa-search"></i>
										</span>
									</div>
									<input id="searchBox" placeholder="cari lokasi..."
										class="form-control border-left-0 bg-transparent pl-0" >
									<div class="input-group-append">
										<button class="btn btn-default waves-effect waves-themed"
											type="submit">Search</button>
									</div>
								</div>
								<span class="help-block">Cari lokasi di peta</span>
							</div>
						</form>
						<div class="row d-flex flex-row justify-content-between">
							<div class="col-md-6">
								<div class="form-group">
									<div class="input-group bg-white shadow-inset-2">
										<div class="input-group-prepend">
											<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
												<i class="fal fa-upload"></i>
											</span>
										</div>
										<div class="custom-file">
											<input type="file" accept=".kml" id="kml_file" placeholder="ambil berkas KML..." onchange="kml_parser()"
												class="custom-file-input border-left-0 bg-transparent pl-0" >
											<label class="custom-file-label text-muted" for="inputGroupFile01">ambil berkas KML...</label>
										</div>
									</div>
									<span class="help-block">Unggah berkas KML</span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<div class="input-group bg-grey shadow-inset-2">
										<div class="input-group-prepend">
											<span class="input-group-text border-right-0 py-1 px-3 text-success">
												<i class="fal fa-globe"></i>
											</span>
										</div>
										<input id="mapId" name="mapId" placeholder="contoh: 1cwFsptUJ7EdW1IoHxFB_VRHsD10TEJ0" class="form-control" disabled>
										<div class="input-group-append">

											<button class="btn btn-default waves-effect waves-themed" disabled
												onclick="link_parser()">Open</button>
										</div>
									</div>
									<span class="help-block">Fitur masih dalam pengembangan.</span>
								</div>
							</div>
						</div>
					</div>
					<form action="{{route('admin.task.updateLokasiTanam', $lokasi->id)}}" method="POST" enctype="multipart/form-data">
						@csrf
						@method('PUT')
						<input type="hidden" name="form_action" value="form1">
						<input type="hidden" name="npwp_company" value="{{$pks->npwp}}">
						<input type="hidden" name="no_ijin" value="{{$pks->no_ijin}}">
						<input type="hidden" name="poktan_id" value="{{$pks->poktan_id}}">
						<input type="hidden" name="pks_id" value="{{$pks->id}}">
						<input type="hidden" name="anggota_id" value="{{$anggota->anggota_id}}">
						<input type="hidden" name="lokasi_id" value="{{$anggota->id}}">
						<div class="panel-content">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Latitude <sup class="text-info"> *</sup></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fal fa-map-marker"></i></span>
											</div>
											<input type="text" value="{{$lokasi->latitude}}"
												name="latitude" id="latitude" readonly
												class="font-weight-bold form-control form-control-sm" />
										</div>
										<span class="help-block">Koordinat Lintang lokasi</span>
									</div>
									<div class="form-group">
										<label>Longitude <sup class="text-info"> *</sup></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fal fa-map-marker-alt"></i></span>
											</div>
											<input type="text" value="{{$lokasi->longitude}}"
												name="longitude" id="longitude" readonly
												class="font-weight-bold form-control form-control-sm" />
										</div>
										<span class="help-block">Koordinat Bujur lokasi</span>
									</div>
									<div class="form-group">
										<label>Polygon<sup class="text-info"> *</sup></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fal fa-draw-polygon"></i></span>
											</div>
											<input type="text" value="{{$lokasi->polygon}}"
											name="polygon" id="polygon" readonly
											class="font-weight-bold form-control form-control-sm" />
										</div>
										<span class="help-block">Kurva bidang lahan yang ditanami.</span>
									</div>
									<div class="form-group">
										<label>Luas Perkiraan (ha)<sup class="text-info"> *</sup></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fal fa-ruler-combined"></i></span>
											</div>
											<input type="text" value="{{$lokasi->luas_kira}}"
												name="luas_kira" id="luas_kira" readonly
												class="font-weight-bold form-control form-control-sm" />
										</div>
										<span class="help-block">Luas bidang diukur oleh sistem.</span>
									</div>
								</div>
								<div class="col-md-6 border-left">
									<div class="form-group">
										<label>Nama Lokasi <sup class="text-danger"> **</sup></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fal fa-map-signs"></i></span>
											</div>
											<input type="text" value="{{$lokasi->nama_lokasi}}"
												name="nama_lokasi" id="nama_lokasi"
												class="font-weight-bold form-control form-control-sm bg-white" />
										</div>
										<span class="help-block">berikan Nama/ID untuk lokasi ini.</span>
									</div>
									<div class="form-group">
										<label>Altitude (mdpl) <sup class="text-danger"> **</sup></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fal fa-ruler-vertical"></i></span>
											</div>
											<input type="text" value="{{$lokasi->altitude}}"
												name="altitude" id="altitude"
												class="font-weight-bold form-control form-control-sm" />
										</div>
										<span class="help-block">Ketinggian lokasi lahan (rerata ketinggain dpl)</span>
									</div>
									<div class="row">
										<div class="form-group col-md">
											<label for="mulai_tanam">Tanggal Awal Tanam<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="{{$lokasi->mulai_tanam}}" name="mulai_tanam" id="mulai_tanam" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal mulai penanaman.</span>
										</div>
										<div class="form-group col-md">
											<label for="akhir_tanam">Tanggal Akhir Tanam<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="{{$lokasi->akhir_tanam}}" name="akhir_tanam" id="akhir_tanam" class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal akhir penanaman.</span>
										</div>
									</div>
									<div class="form-group">
										<label for="luas_lahan">Luas Tanam (ha)<sup class="text-danger"> *</sup></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fal fa-map"></i></span>
											</div>
											{{-- tambahkan ini
											max="{{ $anggota->luas_lahan - $anggota->datarealisasi->sum('luas_lahan') }}"
											untuk pembatasan dan aktifkan script --}}
											<input type="number" step="0.001" value="{{$lokasi->luas_lahan}}" name="luas_lahan" id="luas_lahan" class="font-weight-bold form-control form-control-sm bg-white"/>
										</div>
										<span class="help-block">Luas lahan yang ditanami.</span>
									</div>
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
<script src="{{ asset('js/gmap/map.js') }}"></script>
<script src="{{ asset('js/gmap/location-search.js') }}"></script>
<script src="{{ asset('js/gmap/kml_parser.js') }}"></script>
<script src="{{ asset('js/gmap/link_parser.js') }}"></script>

<script>
	window.addEventListener('load', function() {
		initMap();
	});
	// aktifkan untuk pembatasan max input luas lahan
	// document.getElementById("luas_lahan").addEventListener("input", function() {
	// 	var maxLuasLahan = parseFloat({{ $anggota->luas_lahan - ($anggota->datarealisasi->sum('luas_lahan') - $lokasi->luas_lahan) }});
	// 	var inputLuasLahan = parseFloat(this.value);
	// 	if (inputLuasLahan > maxLuasLahan) {
	// 		this.value = maxLuasLahan;
	// 	}
	// });
</script>
@endsection
