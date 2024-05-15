@extends('layouts.admin')
@section('styles')
	<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@endsection
@section('content')
	@include('partials.subheader')
	@can('online_access')
		@include('partials.sysalert')
		@php
			$npwp = str_replace(['.', '-'], '', $realNpwp);
		@endphp
		<div class="row d-flex">
			<div class="col-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content" >
							<div class="row d-flex justify-content-between">
								<div class="form-group col-md-4">
									<label class="form-label" for="no_ijin">Nomor RIPH</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="no_ijin"
											value="{{$noIjin}}" disabled>
									</div>
									<span class="help-block">Nomor Ijin (RIPH).</span>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label" for="no_perjanjian">Nomor Perjanjian (PKS)</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="no_perjanjian" value="{{$pks}}" disabled>
									</div>
									<span class="help-block">Nomor Perjanjian Kerjasama.</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>Peta Lokasi</h2>
						<div class="panel-toolbar">
							@include('partials.globaltoolbar')
						</div>
					</div>
					<div class="panel-container show">
						<div id="myMap" style="height: 500px; width: 100%;"></div><hr>
						{{--
						<div class="panel-content card-header">
							<div class="row">
								<div class="form-group col-md-12">
									<label class="form-label" for="gmap">
										Pilih lokasi dan Buat Peta Polygon bidang lahan dari lokasi yang dipilih
										<sup class="text-danger"> *</sup>
									</label>
								</div>
							</div>
						</div>
						<div class="panel-content">
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
												<input type="file" id="kml_file" placeholder="ambil berkas KML..." onchange="kml_parser()"
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
											<input id="mapId" name="mapId" placeholder="contoh: 1cwFsptUJ7EdW1IoHxFB_VRHsD10TEJ0" class="form-control">
											<div class="input-group-append">

												<button class="btn btn-default waves-effect waves-themed"
													onclick="link_parser()">Open</button>
											</div>
										</div>
										<span class="help-block">Pastikan tautan yang Anda berikan telah diatur agar dapat dilihat oleh publik pada aplikasi google map.</span>
									</div>
								</div>
							</div>
						</div>
						--}}
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="card-deck">
					<div id="panel-3" class="panel card">
						<div class="panel-hdr">
							<h2>
								Data<span class="fw-300"><i>Realisasi</i></span>
							</h2>
							<div class="panel-toolbar">
								@include('partials.globaltoolbar')
							</div>
						</div>
						<div class="panel-container show">
							<div class="panel-content">
								<ul class="list-group">
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Nama Lokasi/Lahan</span>
										<div class="form-group">
											<input readonly type="text" name="nama_lokasi" id="nama_lokasi" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->nama_lokasi}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Petani</span>
										<div class="form-group">
											<input readonly type="text" name="nama_petani" id="nama_petani" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->masteranggota->nama_petani}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">NIK Pengelola</span>
										<div class="form-group">
											<input readonly type="text" name="nik_petani" id="nik_petani" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->masteranggota->ktp_petani}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Latitude</span>
										<div class="form-group">
											<input readonly type="text" name="latitude" id="latitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->latitude}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">longitude</span>
										<div class="form-group">
											<input readonly type="text" name="longitude" id="longitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->longitude}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Polygon</span>
										<div class="form-group">
											<input readonly type="text" name="polygon" id="polygon" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->polygon}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Luas pada Peta</span>
										<div class="form-group">
											<input readonly type="text" name="luas_kira" id="luas_kira" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->luas_kira}} ha">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Altitude</span>
										<div class="form-group">
											<input readonly type="text" name="altitude" id="altitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->altitude}} mdpl">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Varietas ditanam</span>
										<div class="form-group">
											<input readonly type="text" name="varietas" id="varietas" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->pks->varietas->nama_varietas}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Mulai Tanam</span>
										<div class="form-group">
											<input readonly type="text" name="mulai_tanam" id="mulai_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->mulai_tanam}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Akhir Tanam</span>
										<div class="form-group">
											<input readonly type="text" name="akhir_tanam" id="akhir_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->akhir_tanam}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Luas Tanam dilaporkan</span>
										<div class="form-group">
											@if ($lokasi->luas_lahan)
												<input readonly type="text" name="luas_lahan" id="luas_lahan" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->luas_lahan}} ha">
											@else
												<input readonly type="text" name="luas_lahan" id="luas_lahan" class="text-right form-control form-control-sm" placeholder="tidak ada data">
											@endif
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Mulai Panen</span>
										<div class="form-group">
											<input readonly type="text" name="mulai_panen" id="mulai_panen" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->mulai_panen}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Akhir Panen</span>
										<div class="form-group">
											<input readonly type="text" name="akhir_panen" id="akhir_panen" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->akhir_panen}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Volume Produksi</span>
										<div class="form-group">
											@if ($lokasi->volume)
												<input readonly type="text" name="luas_tanam" id="luas_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$lokasi->volume}} ton">
											@else
												<input readonly type="text" name="volume" id="volume" class="text-right form-control form-control-sm" placeholder="tidak ada data">
											@endif
										</div>
									</li>
								</ul>
								<div class="mt-3">
									<label class="form-label" for="tgl_prod">Dokumentasi</label>
									<div class="d-flex align-items-center flex-row">
										<div id="js-galleryTanam">
											{{-- foreach --}}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="panel-4" class="panel card">
						<div class="panel-hdr">
							<h2>
								Lampiran<span class="fw-300"><i>Foto</i></span>
							</h2>
						</div>
						<div class="panel-container show">
							<div class="panel-content">
								<div id="js-lightgallery">
									@foreach ($fotoTanams as $foto)
										<a class="" href="{{ asset('storage/uploads/'.$npwp.'/'.$foto->datarealisasi->commitment->periodetahun.'/'.$foto->filename) }}" data-sub-html="The free in pointed they their for the so fame.">
											<img class="img-responsive" src="{{ asset('storage/uploads/'.$npwp.'/'.$foto->datarealisasi->commitment->periodetahun.'/'.$foto->filename) }}" alt="{{$foto->filename}}">
										</a>
									@endforeach
									@foreach ($fotoProduksis as $foto)
										<a class="" href="{{ asset('storage/uploads/'.$npwp.'/'.$foto->datarealisasi->commitment->periodetahun.'/'.$foto->filename) }}" data-sub-html="The free in pointed they their for the so fame.">
											<img class="img-responsive" src="{{ asset('storage/uploads/'.$npwp.'/'.$foto->datarealisasi->commitment->periodetahun.'/'.$foto->filename) }}" alt="{{$foto->filename}}">
										</a>
									@endforeach
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	@endcan
@endsection

@section('scripts')
<script src="{{ asset('js/miscellaneous/lightgallery/lightgallery.bundle.js') }}"></script>
	@parent
	<script src="{{ asset('js/gmap/map.js') }}"></script>
	{{-- <script src="{{ asset('js/gmap/location-search.js') }}"></script> --}}
	<script src="{{ asset('js/gmap/kml_parser.js') }}"></script>
	<script src="{{ asset('js/gmap/link_parser.js') }}"></script>
	<script>
		window.addEventListener('load', function() {
			initMap();
		});
	</script>

	

	<script>
		$(document).ready(function()
		{
			var $initScope = $('#js-lightgallery');
			if ($initScope.length)
			{
				$initScope.justifiedGallery(
				{
					border: -1,
					rowHeight: 100,
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
		});
	</script>
	<!-- gallery Tanam -->
	<script>
		$(document).ready(function() {
			var $initScope = $('#js-galleryTanam');
			if ($initScope.length) {
				$initScope.justifiedGallery({
					border: -1,
					rowHeight: 75,
					margins: 8,
					waitThumbnailsLoad: true,
					randomize: false,
				}).on('jg.complete', function() {
					$initScope.lightGallery({
						thumbnail: true,
						animateThumb: true,
						showThumbByDefault: true,
					});
				});
			};
			$initScope.on('onAfterOpen.lg', function(event) {
				$('body').addClass("overflow-hidden");
			});
			$initScope.on('onCloseAfter.lg', function(event) {
				$('body').removeClass("overflow-hidden");
			});
		});
	</script>
	<!-- gallery Tanam -->
@endsection
