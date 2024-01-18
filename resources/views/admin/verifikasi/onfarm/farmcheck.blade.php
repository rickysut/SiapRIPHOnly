@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


@endsection
@section('content')
	@include('partials.breadcrumb')
	@include('partials.subheader')
	@can('onfarm_access')
		@include('partials.sysalert')
		@php
			$npwp = str_replace(['.', '-'], '', $commitment->npwp);
			$mod_noijin = str_replace(['.', '/'], '', $commitment->no_ijin);
		@endphp
		<div class="row d-flex">
			<div class="col-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content">
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
									<label class="form-label" for="statusVerif">Tanggal Pengajuan</label>
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
				<div class="d-flex justify-content-between align-items-center mb-3">
					<ul class=" nav nav-pills" role="tablist">
						<li class="nav-item">
							<a class="btn-sm nav-link active" data-toggle="pill" href="#tab1">
								<i class="fas fa-clock mr-1"></i>
								<span class="fw-700">Peta Verifikasi</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="btn-sm nav-link" data-toggle="pill" href="#tab2">
								<i class="fas fa-check mr-1"></i>
								Peta Realisasi
							</a>
						</li>
					</ul>
					<ul class="nav nav-pills hidden-sm-down">
						<li class="nav-item">
							<a class="btn btn-sm btn-outline-info" href="">
								<i class="fas fa-undo mr-1"></i>
								ke Daftar Lokasi Sampling
							</a>
						</li>
					</ul>
				</div>
				<div class="tab-content">
					<div class="tab-pane active" id="tab1" role="tabpanel">
						<div id="panel-2" class="panel">
							<div class="panel-container show">
								<div id="myMap" style="height: 500px; width: 100%;"></div>
								<div class="panel-content card-header">
									<div class="row d-flex justify-content-between align-items-top">
										<div class="col-md-4">
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
										</div>
										<div class="col-md-4">
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
										<div class="col-md-4">
											<div class="form-group">
												<div class="input-group shadow-inset-2">
													<div class="input-group-prepend">
														<span class="input-group-text border-right-0 py-1 px-3 text-success">
															<i class="fal fa-globe"></i>
														</span>
													</div>
													<input id="mapId" name="mapId" placeholder="Id peta publik" class="form-control">
													<div class="input-group-append">

														<button class="btn btn-default waves-effect waves-themed"
															onclick="link_parser()">Open</button>
													</div>
												</div>
												<span class="help-block">contoh: 1cwFsptUJ7EdW1IoHxFB_VRHsD10TEJ0.</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tab2" role="tabpanel">
						<div id="panel-2" class="panel">
							<div class="panel-container show">
								<div class="panel-content" hidden>
									<input type="text" name="userlat" id="userlat" value="{{$anggotamitra->latitude}}">
									<input type="text" name="userlng" id="userlng" value="{{$anggotamitra->longitude}}">
									<input type="text" name="userpoly" id="userpoly" value="{{$anggotamitra->polygon}}">
								</div>
								<div id="userMap" style="height: 500px; width: 100%;"></div>
								<div class="panel-content card-header">
									<div class="row">
										<div class="form-group col-md-12">
											<label class="form-label" for="gmap">
												Peta yang dilaporkan oleh Importir.
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="card-deck">
					<div id="panel-4" class="panel card">
						<div class="panel-hdr">
							<h2>
								Lampiran<span class="fw-300"><i>Berkas PKS</i></span>
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
						<hr>
						<div class="panel-container show">
							<div class="panel-content">
								<div class="mt-3">
									<label class="form-label" for="tgl_prod">Dokumentasi</label>
									<div class="d-flex align-items-center flex-row">
										<div id="js-galleryTanam">
											<a href="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->tanam_pict) }}"
												data-sub-html="{{$anggotamitra->tanam_pict}}" title="{{$anggotamitra->tanam_pict}}">
												<img class="img-thumbnail" style="max-height: 120px"
												src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->tanam_pict) }}"
												alt="{{$anggotamitra->tanam_pict}}">
											</a>
											<a href="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->panen_pict) }}"
												data-sub-html="{{$anggotamitra->panen_pict}}" title="{{$anggotamitra->panen_pict}}">
												<img class="img-thumbnail" style="max-height: 120px;"
												src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->panen_pict) }}"
												alt="{{$anggotamitra->panen_pict}}">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div id="panel-3" class="panel">
							<div class="panel-hdr">
								<h2>Data Verifikasi</h2>
							</div>
							<form action="{{route('verification.onfarm.farmcheck.update', $veriflokasi->id)}}" method="post" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="panel-container show">
									<div class="panel-content">
										<input type="text" name="pengajuan_id" value="{{$verifikasi->id}}" hidden>
										<input type="text" name="commitcheck_id" value="{{$verifcommit->id}}" hidden>
										<input type="text" name="pkscheck_id" value="{{$verifpks->id}}" hidden>
										<input type="text" name="poktan_id" value="{{$verifpks->poktan_id}}" hidden>
										<input type="text" name="anggota_id" value="{{$anggotamitra->anggota_id}}" hidden>
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
										<input type="text" name="mod_noijin" value="{{$mod_noijin}}" hidden>
										<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
										<table class="table table-sm table-striped table-bordered w-100" id="dataVerif">
											<thead>
												<th class="text-center">Data</th>
												<th class="text-center" style="width: 30%">Data Realisasi</th>
												<th class="text-center" style="width: 33%">Data Verifikasi</th>
											</thead>
											<tbody>
												<tr>
													<td>Nama Lokasi</td>
													<td class="text-right">{{$anggotamitra->nama_lokasi}}</td>
													<td></td>
												</tr>
												<tr>
													<td>Nama Pengelola</td>
													<td class="text-right">{{$anggotamitra->masteranggota->nama_petani}}</td>
													<td></td>
												</tr>
												<tr>
													<td>Varietas</td>
													<td class="text-right">{{$anggotamitra->varietas}}</td>
													<td></td>
												</tr>
												<tr>
													<td>Latitude</td>
													<td class="text-right">{{$anggotamitra->latitude}}</td>
													<td class="text-right">
														<div class="form-group">
															<input type="text" name="latitude" id="latitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{ old('latitude', $lokasicheck->latitude) }}">
														</div>
													</td>
												</tr>
												<tr>
													<td>Longitude</td>
													<td class="text-right">{{$anggotamitra->longitude}}</td>
													<td class="text-right">
														<div class="form-group">
															<input type="text" name="longitude" id="longitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{ old('longitude', $veriflokasi->longitude) }}">
														</div>
													</td>
												</tr>
												<tr>
													<td>Altitude</td>
													<td class="text-right">{{$anggotamitra->altitude}}</td>
													<td class="text-right">
														<div class="form-group">
															<input type="text" name="altitude" id="altitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{ old('altitude', $veriflokasi->altitude) }}">
														</div>
													</td>
												</tr>
												<tr>
													<td>Polygon</td>
													<td>
														<span class="d-block text-truncate text-truncate-md">
															{{$anggotamitra->polygon}}
														</span>
													</td>
													<td class="text-right">
														<div class="form-group">
															<input type="text" name="polygon" id="polygon" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{ old('polygon', $veriflokasi->polygon) }}">
														</div>
													</td>
												</tr>
												<tr>
													<td>Luas Lahan/Tanam (ha)</td>
													<td class="text-right">{{$anggotamitra->luas_tanam}}</td>
													<td class="text-right">
														<div class="form-group">
															<input type="text" name="luas_verif" id="luas_kira" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{ old('luas_verif', $veriflokasi->luas_verif) }}">
														</div>
													</td>
												</tr>
												<tr>
													<td>Volume Produksi (ton)</td>
													<td class="text-right">{{$anggotamitra->volume}}</td>
													<td class="text-right">
														<div class="form-group">
															<input type="text" name="volume_verif" id="volume_verif" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{ old('volume_verif', $veriflokasi->volume_verif) }}">
														</div>
													</td>
												</tr>
												<tr>
													<td>
														Status Periksa
													</td>
													<td></td>
													<td>
														<div class="form-group">
															<select name="onfarmstatus" id="onfarmstatus" class="form-control-sm form-control">
																<option value="" hidden>--pilih status</option>
																<option value="Selesai" {{ old('onfarmstatus', $veriflokasi ? $veriflokasi->onfarmstatus : '') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
																<option value="Perbaikan" {{ old('onfarmstatus', $veriflokasi ? $veriflokasi->onfarmstatus : '') == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
															</select>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="panel-content">
										<div class="row">
											<div class="col-md-6">

											</div>
											<div class="form-group col-12">
												<label for="">Catatan Pemeriksaan</label>
												<textarea class="form-control form-control-sm" name="onfarmnote" id="onfarmnote" rows="3" required>{{ old('onfarmnote', $veriflokasi->onfarmnote) }}</textarea>
												<small id="helpId" class="text-muted">Berikan catatan hasil pemeriksaan lapangan.</small>
											</div>
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
		</div>
	@endcan
@endsection

@section('scripts')
	<script src="{{ asset('js/miscellaneous/lightgallery/lightgallery.bundle.js') }}"></script>
	@parent
	<script src="{{ asset('js/gmap/usermap.js') }}"></script>
	<script src="{{ asset('js/gmap/map.js') }}"></script>
	<script src="{{ asset('js/gmap/location-search.js') }}"></script>
	<script src="{{ asset('js/gmap/kml_parser.js') }}"></script>
	<script src="{{ asset('js/gmap/link_parser.js') }}"></script>
	<script>
		window.addEventListener('load', function() {
			inituserMap();
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
				url: "{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggotamitra->tanam_doc) }}",
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

	<script>
		$(document).ready(function() {
			// Initialize datatable dataPengajuan
			var table = $('#dataVerif').DataTable({
				responsive: true,
				ordering: false,
				dom:'',
			});
		});
	</script>
@endsection
