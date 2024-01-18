@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>



@endsection
@section('content')
	@include('partials.breadcrumb')
	@include('partials.subheader')
	@can('online_access')
		@include('partials.sysalert')
		@php
			$npwp = str_replace(['.', '-'], '', $commitment->npwp);
		@endphp
		<div class="row d-flex">
			<div class="col-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content" >
							<div class="row d-flex justify-content-between">
								<div class="form-group col-md-4">
									<label class="form-label" for="no_pengajuan">Nomor Pengajuan</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="no_pengajuan"
											value="{{$verifikasi->no_pengajuan}}" disabled>
									</div>
									<span class="help-block">Nomor Pengajuan Verifikasi.</span>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label" for="no_ijin">Nomor RIPH</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="no_ijin"
											value="{{$verifikasi->no_ijin}}" disabled>
									</div>
									<span class="help-block">Nomor Pengajuan Verifikasi.</span>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label" for="">Tanggal Pengajuan</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-calendar-day"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="created_at"
											value="{{$verifikasi->created_at}}" disabled>
									</div>
									<span class="help-block">Status Pemeriksaan</span>
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
						{{-- <div class="panel-content card-header">
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
						</div> --}}
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
										<span class="text-muted">Latitude</span>
										<div class="form-group">
											<input readonly type="text" name="latitude" id="latitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->latitude}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">longitude</span>
										<div class="form-group">
											<input readonly type="text" name="longitude" id="longitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->longitude}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Polygon</span>
										<div class="form-group">
											<input readonly type="text" name="polygon" id="polygon" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->polygon}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Luas pada Peta</span>
										<div class="form-group">
											<input readonly type="text" name="luas_kira" id="luas_kira" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->luas_kira}} ha">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Altitude</span>
										<div class="form-group">
											<input readonly type="text" name="altitude" id="altitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->altitude}} mdpl">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Nama Lokasi/Lahan</span>
										<div class="form-group">
											<input readonly type="text" name="nama_lokasi" id="nama_lokasi" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->nama_lokasi}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Varietas ditanam</span>
										<div class="form-group">
											<input readonly type="text" name="varietas" id="varietas" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->varietas}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Tanggal Tanam</span>
										<div class="form-group">
											<input readonly type="text" name="tgl_tanam" id="tgl_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->tgl_tanam}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Luas Tanam dilaporkan</span>
										<div class="form-group">
											@if ($anggotamitra->luas_tanam)
												<input readonly type="text" name="luas_tanam" id="luas_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->luas_tanam}} ha">
											@else
												<input readonly type="text" name="luas_tanam" id="luas_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data">
											@endif
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Volume Produksi</span>
										<div class="form-group">
											@if ($anggotamitra->volume)
												<input readonly type="text" name="luas_tanam" id="luas_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->volume}} ton">
											@else
												<input readonly type="text" name="volume" id="volume" class="text-right form-control form-control-sm" placeholder="tidak ada data">
											@endif
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Anggota Pengelola</span>
										<div class="form-group">
											<input readonly type="text" name="nama_petani" id="nama_petani" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->masteranggota->nama_petani}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">NIK Pengelola</span>
										<div class="form-group">
											<input readonly type="text" name="nik_petani" id="nik_petani" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->masteranggota->ktp_petani}}">
										</div>
									</li>
								</ul>
								<div class="mt-3">
									<label class="form-label" for="tgl_prod">Dokumentasi</label>
									<div class="d-flex align-items-center flex-row">
										<div id="js-galleryTanam">
											<a href="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->tanam_pict) }}"
												data-sub-html="{{$anggotamitra->tanam_pict}}" title="{{$anggotamitra->tanam_pict}}">
												<img class="img-responsive img-thumbnail" style="max-height: 120px"
												src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->tanam_pict) }}"
												alt="{{$anggotamitra->tanam_pict}}">
											</a>
											<a href="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->panen_pict) }}"
												data-sub-html="{{$anggotamitra->panen_pict}}" title="{{$anggotamitra->panen_pict}}">
												<img class="img-responsive img-thumbnail" style="max-height: 120px"
												src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->panen_pict) }}"
												alt="{{$anggotamitra->panen_pict}}">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="panel-4" class="panel card">
						<div class="panel-hdr">
							<h2>
								Lampiran<span class="fw-300"><i>Berkas</i></span>
							</h2>
							<div class="panel-toolbar">
								<select id="pdf-select" class="form-control form-control-sm">
									<option value="">Select PDF file</option>
								</select>
							</div>
						</div>
						@if ($anggotamitra->tanam_doc)
							<div class="panel-container show card-body embed-responsive embed-responsive-16by9">
								<iframe class="embed-responsive-item" id="pdf-iframe"
									src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->tanam_doc) }}"
									frameborder="0" width="100%">
								</iframe>
							</div>
						@else
							<div class="panel-container show">
								<div class="panel-content text-center">
									<h3 class="text-danger">Tidak ada berkas dilampirkan</h2>
								</div>
							</div>
						@endif
					</div>
				</div>
				<div id="panel-5" class="panel">
					<div class="panel-hdr">
						<h2>Hasil Pemeriksaan</h2>
					</div>
					<div class="panel-container show">
						<form action="{{route('verification.data.lokasicheck.store')}}" method="post" enctype="multipart/form-data">
							@csrf
							<div class="panel-content">
								<div class="row mb-3">
									<div class="col-md-4">
										<input type="text" name="pengajuan_id" value="{{$verifikasi->id}}" hidden>
										<input type="text" name="verifcommit_id" value="{{$verifcommit->id}}" hidden>
										<input type="text" name="verifpks_id" value="{{$verifpks->id}}" hidden>
										<input type="text" name="poktan_id" value="{{$verifpks->poktan_id}}" hidden>
										<input type="text" name="anggotamitra_id" value="{{$anggotamitra->anggota_id}}" hidden>
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
										<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
										<div class="form-group">
											<label for="">Statu Periksa</label>
											<select name="onlinestatus" id="onlinestatus" class="form-control-sm form-control" required>
												<option value="" hidden>--pilih status</option>
												<option value="Selesai">Selesai</option>
												<option value="Perbaikan">Perbaikan</option>
											</select>
											<small id="helpId" class="text-muted">Pilih status Pemeriksaan (biarkan kosong jika belum selesai diperiksa).</small>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-12">
										<label for="">Catatan Pemeriksaan</label>
										<textarea class="form-control form-control-sm" name="onlinenote" id="onlinenote" rows="7" required></textarea>
										<small id="helpId" class="text-muted">Berikan catatan hasil pemeriksaan secara administratif atas data dan dokumen yang diunggah.</small>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<div class="d-flex justify-content-between">
									<div class="col-sm-6"></div>
									<div class="col-sm-6 text-right">
										<a class="btn btn-sm btn-warning" href="" role="button"><i class="fal fa-times text-align-center mr-1"></i> Batalkan</a>
										<button class="btn btn-sm btn-primary" type="submit" onclick="return confirm('Anda telah memeriksa seluruh bagian yang diperlukan?')">
											<i class="fas fa-upload text-align-center mr-1"></i>Simpan
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

@section('scripts')
<script src="{{ asset('js/miscellaneous/lightgallery/lightgallery.bundle.js') }}"></script>
	@parent
	<script src="{{ asset('js/gmap/map.js') }}"></script>
	<script src="{{ asset('js/gmap/location-search.js') }}"></script>
	<script src="{{ asset('js/gmap/kml_parser.js') }}"></script>
	<script src="{{ asset('js/gmap/link_parser.js') }}"></script>
	<script>
		window.addEventListener('load', function() {
			initMap();
		});
	</script>
	<script>
		const pdfSelect = document.getElementById('pdf-select');
		const pdfIframe = document.getElementById('pdf-iframe');
		const pdfUrls = [
			{
				url: "{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->tanam_doc) }}",
				name: "Dokumen Tanam"
			},
			{
				url: "{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->panen_doc) }}",
				name: "Dokumen Panen"
			}
		];

		pdfUrls.forEach(function (item) {
			const option = document.createElement('option');
			option.value = item.url;
			option.textContent = item.name;
			pdfSelect.appendChild(option);
		});

		// Set the default value to the Tanam Doc url
		pdfSelect.value = pdfUrls[0].url;

		pdfSelect.addEventListener('change', function () {
			const pdfUrl = this.value;
			pdfIframe.src = pdfUrl;
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
