@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
{{-- <script src="{{ asset('js/gmap/js.js') }}"></script> --}}
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1ea90fk4RXPswzkOJzd17W3EZx_KNB1M&libraries=drawing,geometry"></script>
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
@if (Auth::user()->roles[0]->title == 'User')
	<script src="{{ asset('js/gmap/UserDashboardMaps.js?v=1.0.1') }}"></script>
@else
	<script src="{{ asset('js/gmap/allMaps.js?v=1.0.2') }}"></script>
@endif
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
    });
</script>

@endsection
