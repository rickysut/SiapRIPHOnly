@extends('layouts.admin')
@section('content')
	@can('landing_access')
		@php($unreadmsg = \App\Models\QaTopic::unreadCount())
		@php($msgs = \App\Models\QaTopic::unreadMsg())

		@if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Pejabat' || Auth::user()->roles[0]->title == 'Verifikator')
			{{-- tanam --}}
			@php($cntAjuVerifTanam = \App\Models\AjuVerifTanam::newPengajuanCount())
			@php($getAjuVerifTanam = \App\Models\AjuVerifTanam::getNewPengajuan())
			{{-- produksi --}}
			@php($cntAjuVerifProduksi = \App\Models\AjuVerifProduksi::newPengajuanCount())
			@php($getAjuVerifProduksi = \App\Models\AjuVerifProduksi::getNewPengajuan())
			{{-- skl --}}
			@php($cntAjuVerifSkl = \App\Models\AjuVerifSkl::newPengajuanCount())
			@php($getAjuVerifSkl = \App\Models\AjuVerifSkl::getNewPengajuan())
			@php($cntpengajuan = $cntAjuVerifTanam + $cntAjuVerifProduksi + (Auth::user()->roles[0]->title == 'Admin' ? $cntAjuVerifSkl : 0))
			{{-- rekomendasi --}}
			@php($cntRecomendations = \App\Models\Skl::newPengajuanCount())
			@php($getRecomendations = \App\Models\Skl::getNewPengajuan())

		@else
			@php($cntAjuVerifTanam = 0)
			@php($cntAjuVerifTanam = null)
			@php($cntAjuVerifProduksi = 0)
			@php($getAjuVerifProduksi = null)
			@php($cntAjuVerifSkl = 0)
			@php($getAjuVerifSkl = null)
			@php($cntRecomendations = 0)
			@php($getRecomendations = null)
		@endif

		@if (Auth::user()->roles[0]->title == 'User' || Auth::user()->roles[0]->title == 'Pejabat' )
			@php($getNewSkl = \App\Models\Skl::getNewSkl())
			@php($cntgetNewSkl = \App\Models\SklReads::getNewSklCount())
		@endif

		<div class="row mb-5">
			<div class="col text-center">
				<h1 class="hidden-md-down">Selamat Datang di Simethris,</h1><br>
				<span class="display-4 fw-700 hidden-md-down">{{ Auth::user()->data_user->company_name ?? Auth::user()->name }}</span>
				<h2 class="display-4 hidden-sm-up">Hallo, <span class="fw-700">{{ Auth::user()->name }}</span></h2>
				<h4 class="hidden-md-down">
					<p class="text-muted">{!! $quote !!}</p>
				</h4>
			</div>
		</div>

		{{-- @if (Auth::user()->roles[0]->title == 'User') --}}
			<div class="row mb-5">
				<div class="col-12">
					<div class="alert alert-danger fade show" role="alert">
						<div class="d-flex align-items-top">
							<div class="alert-icon">
								<span class="icon-stack icon-stack-md">
									<i class="base-2 icon-stack-3x color-danger-400"></i>
									<i class="base-10 text-white icon-stack-1x"></i>
									<i class="fal fa-info-circle color-danger-800 icon-stack-2x"></i>
								</span>
							</div>
							<div class="flex-1">
								<span class="h3">PENGUMUMAN!!</span>
								<br>
									Sehubungan adanya pemeliharaan, sementara Menu tidak dapat digunakan. Mohon maaf atas ketidaknyamanannya.
								<br><br>
								Terima Kasih.
								<br><br>
								<strong>Administrator.</strong> at <span class="nav-link-text js-get-date"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		{{-- @endif --}}

		@if (Auth::user()->roles[0]->title == 'Pejabat')
			@if (!$profile || (!$profile->jabatan || !$profile->nip))
				<div class="row mb-5">
					<div class="col-md">
					<div class="alert alert-danger">
						<div class="d-flex flex-start w-100">
							<div class="mr-2 hidden-md-down">
								<span class="icon-stack icon-stack-lg">
									<i class="base base-7 icon-stack-3x opacity-100 color-error-500"></i>
									<i class="base base-7 icon-stack-2x opacity-100 color-error-300 fa-flip-vertical"></i>
									<i class="fas fa-exclamation icon-stack-1x opacity-100 color-white"></i>
								</span>
							</div>
							<div class="d-flex flex-fill">
								<div class="flex-fill">
									<span class="h5">Perhatian</span>
									<p>
										Anda belum melengkapi data Profile Pejabat. Silahkan lengkapi <a href="{{ route('admin.profile.pejabat') }}" class="fw-500 text-uppercase">di sini</a>.
									</p>
								</div>
							</div>
						</div>
					</div>

					</div>
				</div>
			@endif
		@endif
		<!-- Page Content -->
		<div class="row">

		</div>
		<!-- Page Content -->
	@endcan
@endsection
@section('scripts')
	@parent

	<script>
		$(document).ready(function() {
			function markAsRead(sklId) {
				var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Ambil token CSRF

				// Kirim permintaan Ajax ke metode controller untuk menandai SKL sebagai sudah dibaca
				$.ajax({
					type: 'POST',
					url: '{{ route('admin.sklReads') }}', // Menggunakan route yang sesuai
					data: {
						skl_id: sklId,
						_token: csrfToken // Sertakan token CSRF di sini
					},
					success: function(response) {
						// Setelah berhasil menandai, buka URL tautan
						window.location.href = event.target.getAttribute('href');
					}
				});
			}
		});
	</script>
@endsection
