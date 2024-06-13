@extends('layouts.draftSKL')
@section ('styles')

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endsection
@section('content')
{{-- @include('partials.breadcrumb') --}}

<form action="{{route('verification.skl.recomendation.approve', $skl->id)}}" method="post" onsubmit="return confirm('Anda setuju untuk menerbitkan Surat Keterangan Lunas untuk RIPH Nomor: {{$skl->no_ijin}}')">
	@csrf
	@method('PUT')
	<div class="d-flex align-items-center justify-content-center text-center mt-5 mb-5">
		@if (Auth::user()->roles[0]->title == 'Admin')
			<a class="btn btn-sm btn-info mr-1" href="{{route('verification.skl')}}" role="button"><i class="fal fa-undo text-align-center"></i> Kembali</a>
			<button type="button" onclick="printPage()" class="btn btn-sm btn-primary btn-sm">
				<i class="fal fa-print mr-1"></i> Cetak SKL
			</button>
		@elseif (Auth::user()->roles[0]->title == 'Pejabat')
			<a class="btn btn-sm btn-info mr-1" href="{{route('verification.skl.recomendation.show', $verifikasi->id)}}" role="button"><i class="fal fa-undo text-align-center"></i> Kembali</a>
			<button class="btn btn-sm btn-danger" type="submit">
				<i class="fas fa-upload text-align-center"></i>Terbitkan SKL
			</button>
		@endif
	</div>
</form>
<div class="container" style="background-color: white !important;">
	<div data-size="A4">
		<div class="row">
			<div class="col-sm-12 mb-5">
				<img src="{{asset('/img/kopsto.png')}}" width="100%">
			</div>
		</div>
		<div class="row mb-5 fs-xl keep-print-font">
			<div class="col-sm-6 d-flex">
				<div class="table-responsive">
					<table class="table table-clean table-sm align-self-end">
						<tbody>
							<tr>
								<td>
									<strong>Nomor</strong>
								</td>
								<td>:</td>
								<td>
									<span class="mr-1">{{$skl->no_skl}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<strong>Lampiran</strong>
								</td>
								<td>:</td>
								<td>
									-
								</td>
							</tr>
							<tr>
								<td>
									<strong>Hal</strong>
								</td>
								<td>:</td>
								<td>
									Keterangan Telah Melaksanakan Wajib Tanam dan Wajib Produksi
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-sm-6 ml-sm-auto">
				<div class="table-responsive">
					<table class="table table-sm table-clean text-right">
						<tbody>
							<tr>
								<td>
									{{-- <span class="js-get-date fw-500"></span> --}}
									<span class="fw-500">{{ \Carbon\Carbon::parse($skl->published_date)->translatedFormat('d F Y') }}
									</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row fs-xl keep-print-font">
			<div class="col-12">
				Kepada Yth.<br>
				Pimpinan<br>
				<strong>
					<span class="keep-print-font">{{$commitment->datauser->company_name}}</span>
				</strong><br>
				di<br>
				Tempat<br>
				<div class="row">
					<p class="justify-align-stretch mt-5">Berdasarkan hasil evaluasi dan validasi laporan realisasi tanam dan produksi, dengan ini kami menyatakan:</p>
				</div>
			</div>
			<div class="col-12">
				<dl class="row">
					<dd class="col-sm-3">Nama Perusahaan</dd>
					<dt class="col-sm-9">: {{$commitment->user->data_user->company_name}}</dt>
					<dd class="col-sm-3">Nomor RIPH</dd>
					<dt class="col-sm-9">: {{$commitment->no_ijin}}</dt>
					<dd class="col-sm-3">Wajib Tanam</dd>
					<dt class="col-sm-9">
						: {{ number_format($commitment->luas_wajib_tanam, 2, '.', ',') }} ha, Realisasi: {{ number_format($total_luas, 2, '.', ',') }} ha.
					</dt>
					<dd class="col-sm-3">Wajib Produksi</dd>
					<dt class="col-sm-9">
						: {{ number_format($commitment->volume_produksi, 2, '.', ',') }} ton, Realisasi: {{ number_format($total_volume, 2, '.', ',') }} ton.
					</dt>
				</dl>
				{{-- <div class="row">
					<table class="table w-100 table-sm table-bordered table-striped">
						<tbody>
							<tr>
								<td width="25%">Nama Perusahaan</td>
								<th colspan="3">{{$commitment->datauser->company_name}}</th>
							</tr>
							<tr>
								<td>Nomor RIPH</td>
								<th colspan="3">{{$commitment->no_ijin}}</th>
							</tr>
							<tr>
								<td></td>
								<td width="25%" class="text-center">Komitmen</td>
								<td width="25%" class="text-center">Realisasi</td>
								<td width="25%" class="text-center">Terverifikasi</td>
							</tr>
							<tr>
								<td>Wajib Tanam (ha)</td>
								<th class="text-right pr-5">{{ number_format($wajib_tanam, 2, '.', ',') }}</th>
								<th class="text-right pr-5">{{ number_format($total_luas, 2, '.', ',') }}</th>
								<th class="text-right pr-5">{{number_format($luas_verif, 2,'.',',')}}</th>
							</tr>
							<tr>
								<td>Wajib Produksi (ton)</td>
								<th class="text-right pr-5">{{ number_format($wajib_produksi, 2, '.', ',') }}</th>
								<th class="text-right pr-5">{{ number_format($total_volume, 2, '.', ',') }}</th>
								<th class="text-right pr-5">{{number_format($volume_verif,2,'.',',')}}</th>
							</tr>
						</tbody>
					</table>
				</div> --}}
			</div>
			<div class="col-12">
				<div class="row">
					<p class="justify-align-stretch">Telah melaksanakan kewajiban pengembangan bawang putih di dalam negeri sebagaimana ketentuan dalam Permentan 39 tahun 2019 dan perubahannya serta Permentan 46 tahun 2019.</p>
					<p class="justify-align-stretch mt-3">Atas perhatian dan kerjasama Saudara disampaikan terima kasih.</p>
				</div>
			</div>
			<div class="col-12">
				<dl class="row mt-5 align-items-center">
					<dd class="col-sm-7">
						{{$QrCode}}
					</dd>
					<dd class="col-sm-5">
							<span class="mb-5" style="height: 7em">Direktur,</span>
							<br><br><br>
								{{-- @if ($pejabat)
									@if ($pejabat->sign_img)
										<img style="max-width: 7em" src="{{ asset('storage/uploads/dataadmin/'.$pejabat->sign_img) }}" alt="ttd">
									@endif
								@endif --}}
							<br>
							<u><strong>{{$pejabat->nama ?? ''}}</strong></u><br>
							<span class="mr-1">NIP.</span>{{$pejabat->nip ??''}}
						</div>
					</dd>
				</dl>
				<div class="row">
					<div class="col-sm-12">
						<ul><u>Tembusan</u>
							<li>Direktur Jenderal Hortikultura</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row position-relative">
				<i class="position-absolute pos-right pos-bottom opacity-50 mb-n1 ml-n1" >dicetak pada: {{ now() }}</i>
			</div>
		</div>
	</div>
</div>
@endsection
<!-- @parent -->
<!-- start script for this page -->
@section('scripts')
<script>
	function printPage() {
	  // Open print dialog
	  window.print();
	}
</script>
@endsection
