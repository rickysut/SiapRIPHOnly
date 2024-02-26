@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ optional($mapkey)->key ?? 'Default Key' }}&libraries=drawing,geometry"></script>


@endsection
@section('content')
	@include('partials.breadcrumb')
	@include('partials.subheader')
	@can('online_access')
		@include('partials.sysalert')
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
											value="{{$veriflokasi->anggotamitra->pksmitra->commitmentbackdate->no_ijin}}" disabled>
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
											value="{{$veriflokasi->anggotamitra->pksmitra->commitmentbackdate->no_ijin}}" disabled>
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
											value="{{$veriflokasi->pengajuanv2->created_at}}" disabled>
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
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="card-deck">
					<div id="panel-3" class="panel card">
						<div class="panel-hdr">
							<h2>
								Data<span class="fw-300"><i>Spasial</i></span>
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
											<input readonly type="text" name="latitude" id="latitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->latitude}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">longitude</span>
										<div class="form-group">
											<input readonly type="text" name="longitude" id="longitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->longitude}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Polygon</span>
										<div class="form-group">
											<input readonly type="text" name="polygon" id="polygon" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->polygon}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Luas pada Peta</span>
										<div class="form-group">
											<input readonly type="text" name="luas_kira" id="luas_kira" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->luas_kira}} ha">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Altitude</span>
										<div class="form-group">
											<input readonly type="text" name="altitude" id="altitude" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->altitude}} mdpl">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Nama Lokasi/Lahan</span>
										<div class="form-group">
											<input readonly type="text" name="nama_lokasi" id="nama_lokasi" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->nama_lokasi}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Varietas ditanam</span>
										<div class="form-group">
											<input readonly type="text" name="varietas" id="varietas" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->varietas}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Tanggal Tanam</span>
										<div class="form-group">
											<input readonly type="text" name="tgl_tanam" id="tgl_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->tgl_tanam}}">
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Luas Tanam dilaporkan</span>
										<div class="form-group">
											@if ($veriflokasi->anggotamitra->luas_tanam)
												<input readonly type="text" name="luas_tanam" id="luas_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->luas_tanam}} ha">
											@else
												<input readonly type="text" name="luas_tanam" id="luas_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data">
											@endif
										</div>
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span class="text-muted">Volume Produksi</span>
										<div class="form-group">
											@if ($veriflokasi->anggotamitra->volume)
												<input readonly type="text" name="luas_tanam" id="luas_tanam" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$veriflokasi->anggotamitra->volume}} ton">
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
											<input readonly type="text" name="nik_petani" id="nik_petani" class="text-right form-control form-control-sm" placeholder="tidak ada data" value="{{$anggotamitra->masteranggota->nik_petani}}">
										</div>
									</li>
								</ul>
								<div class="mt-3">
									<label class="form-label" for="tgl_prod">Dokumentasi</label>
									<div class="d-flex align-items-center flex-row">
										<div id="js-galleryTanam">

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
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
									src="{{ url('storage/docs/' . $commitment->periodetahun . '/commitment_'.$commitment->id.'/pks/'.$anggotamitra->pks_mitra_id.'/tanam/'.$anggotamitra->tanam_doc) }}"
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
						<form action="{{route('admin.task.onlinev2.location.update',$veriflokasi->id)}}" method="post" enctype="multipart/form-data">
							@csrf
							@method('PUT')
							<div class="panel-content">
								<div class="row">
									<div class="col-md-6">
										<div hidden>
											<input type="text" name="pengajuan_id" value="{{$veriflokasi->id}}">
											<input type="text" name="pengajuan_id" value="{{$veriflokasi->pengajuan_id}}">
											<input type="text" name="commitmentbackdate_id" value="{{$veriflokasi->verifcommit_id}}">
											<input type="text" name="pksmitra_id" value="{{$veriflokasi->verifpks_id}}">
											<input type="text" name="anggotamitra_id" value="{{$veriflokasi->anggotamitra_id}}">
										</div>
										<div class="form-group">
											<label for="">Status</label>
											<select name="onlinestatus" id="onlinestatus" class="form-control-sm form-control" required>
												<option value="" hidden>--pilih status</option>
												<option value="Selesai"
													{{ $veriflokasi && $veriflokasi->onlinestatus == 'Selesai' ? 'selected' : '' }}>
													Selesai
												</option>
												<option value="Perbaikan"
													{{ $veriflokasi && $veriflokasi->onlinestatus == 'Perbaikan' ? 'selected' : '' }}>
													Selesai
												</option>
											</select>
											<small id="helpId" class="text-muted">ilih status Pemeriksaan (biarkan kosong jika belum selesai diperiksa).</small>
										</div>
									</div>
									<div class="form-group col-md-6">
										<label for="">Catatan Pemeriksaan</label>
										<textarea class="form-control form-control-sm" name="onlinenote" id="onlinenote" rows="7" required>{{ old('onlinenote', $veriflokasi ? $veriflokasi->onlinenote : '') }}</textarea>
										<small id="helpId" class="text-muted">Berikan catatan hasil pemeriksaan secara administratif atas data dan dokumen yang diunggah.</small>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<div class="form-group">
									<label class="form-label h6">Konfirmasi & Simpan</label>
									<div class="input-group">
										<input type="text" class="form-control form-control-sm" placeholder="ketik username Anda di sini" id="validasi" name="validasi"required>
										<div class="input-group-append">
											<a class="btn btn-sm btn-warning" href="" role="button"><i class="fal fa-times text-align-center mr-1"></i> Batalkan</a>
										</div>
										<div class="input-group-append">
											<button class="btn btn-sm btn-primary" type="submit" onclick="return validateInput()">
												<i class="fas fa-upload text-align-center mr-1"></i>Simpan
											</button>
										</div>
									</div>
									<span class="help-block">Dengan ini kami menyatakan verifikasi pada bagian ini telah SELESAI.</span>
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

<script>
	const pdfSelect = document.getElementById('pdf-select');
	const pdfIframe = document.getElementById('pdf-iframe');
	const pdfUrls = [
		{
			url: "{{ url('storage/docs/' . $commitment->periodetahun . '/commitment_'.$commitment->id.'/pks/'.$anggotamitra->pks_mitra_id.'/tanam/'.$anggotamitra->tanam_doc) }}",
			name: "Dokumen Tanam"
		},
		{
			url: "{{ url('storage/docs/' . $commitment->periodetahun . '/commitment_'.$commitment->id.'/pks/'.$anggotamitra->pks_mitra_id.'/panen/'.$anggotamitra->panen_doc) }}",
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
			function validateInput() {
				// get the input value and the current username from the page
				var inputVal = document.getElementById('validasi').value;
				var currentUsername = '{{ Auth::user()->username }}';

				// check if the input is not empty and matches the current username
				if (inputVal !== '' && inputVal === currentUsername) {
					return true; // allow form submission
				} else {
					alert('Isi kolom dengan username Anda!.');
					return false; // prevent form submission
				}
			}
		</script>
@endsection
