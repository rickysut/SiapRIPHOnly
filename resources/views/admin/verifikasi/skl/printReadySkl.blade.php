@extends('layouts.draftSKL')
@section ('styles')

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endsection
@section('content')
{{-- @include('partials.breadcrumb') --}}
<div class="row d-flex align-items-center justify-content-center text-center mt-5 mb-5">
	<a class="btn btn-sm btn-info mr-1" href="{{route('verification.skl')}}" role="button">
		<i class="fal fa-undo text-align-center mr-1"></i> Kembali
	</a>
	<button onclick="printPage()" class="btn btn-sm btn-primary btn-sm">
		<i class="fal fa-print mr-1"></i> Cetak SKL
	</button>
</div>
<div class="container" style="background-color: white !important;">
	<div data-size="A4">
		<div class="row">
			<div class="col-sm-12 mb-5">
				<img src="{{asset('/img/kopsto.png')}}" width="100%">
			</div>
		</div>
		<div class="row mb-5 fs-xl">
			<div class="col-sm-6 d-flex">
				<div class="table-responsive">
					<table class="table table-clean table-sm align-self-end">
						<tbody>
							<tr>
								<td>Nomor</td>
								<td class="text-center" width="1%">:</td>
								<td class="fw-500">{{$skl->no_skl}}</td>
							</tr>
							<tr>
								<td>Lampiran</td>
								<td class="text-center" width="1%">:</td>
								<td> - </td>
							</tr>
							<tr>
								<td>Hal</td>
								<td class="text-center" width="1%">:</td>
								<td class="fw-500">Keterangan Telah Melaksanakan Wajib Tanam dan Wajib Produksi
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
									<strong>{{ date('d F Y', strtotime($skl->published_date)) }}</strong>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row fs-xl">
			<div class="col-sm-12">
				Kepada Yth.<br>
				Pimpinan<br>
				<strong>
					<span class="keep-print-font">{{$commitment->datauser->company_name}}</span>
				</strong><br>
				di<br>
				Tempat<br>
				<p class="justify-align-stretch mt-5">
					Berdasarkan hasil evaluasi dan validasi laporan realisasi tanam dan produksi, dengan ini kami menyatakan:
				</p>
			</div>
			<div class="col-12">
				{{-- <dl class="row">
					<dd class="col-sm-3">Nama Perusahaan</dd>
					<dt class="col-sm-9">: {{$commitment->user->data_user->company_name}}</dt>
					<dd class="col-sm-3">Nomor RIPH</dd>
					<dt class="col-sm-9">: {{$commitment->no_ijin}}</dt>
					<dd class="col-sm-3">Wajib Tanam</dd>
					<dt class="col-sm-9">
						<dl class="row">
							<dd class="col-sm-3">Komitmen</dd>
							<dt class="col-sm-9">: {{ number_format($commitment->volume_riph * 0.05 / 6, 2, '.', ',') }} ha;</dt>
							<dd class="col-sm-3">Realisasi</dd>
							<dt class="col-sm-9">: {{ number_format($total_luas, 2, '.', ',') }} ha.</dt>
							<dd class="col-sm-3">Verifikasi</dd>
							<dt class="col-sm-9">: {{number_format($pengajuan->luas_verif,2,'.',',')}} ha.</dt>
						</dl>
					</dt>
					<dd class="col-sm-3">Wajib Produksi</dd>
					<dt class="col-sm-9">
						<dl class="row">
							<dd class="col-sm-3">Komitmen</dd>
							<dt class="col-sm-9">: {{ number_format($commitment->volume_riph * 0.05, 2, '.', ',') }} ton;</dt>
							<dd class="col-sm-3">Realisasi</dd>
							<dt class="col-sm-9">: {{ number_format($total_volume, 2, '.', ',') }} ton.</dt>
							<dd class="col-sm-3">Verifikasi</dd>
							<dt class="col-sm-9">: {{number_format($pengajuan->volume_verif,2,'.',',')}} ton.</dt>
						</dl>
					</dt>
				</dl> --}}
				<div class="row pl-2">
					<table class="table table table-sm table-clean w-100">
						<tbody>
							<tr>
								<td width="25%">Nama Perusahaan</td>
								<td class="text-center" width="1%">:</td>
								<th>{{$commitment->datauser->company_name}}</th>
							</tr>
							<tr>
								<td>Nomor RIPH</td>
								<td class="text-center" width="1%">:</td>
								<th>{{$commitment->no_ijin}}</th>
							</tr>
							<tr>
								<td>Wajib Tanam</td>
								<td class="text-center" width="1%">:</td>
								<th>
									<span class="mr-1">{{ number_format($wajib_tanam, 2, '.', ',') }} hektar,</span>
									Realisasi: {{ number_format($total_luas, 2, '.', ',') }} hektar.
								</th>
							</tr>
							<tr>
								<td>Wajib Produksi</td>
								<td class="text-center" width="1%">:</td>
								<th>
									<span class="mr-1">{{ number_format($wajib_produksi, 2, '.', ',') }} ton,</span>
									Realisasi: {{ number_format($total_volume, 2, '.', ',') }} ton.
								</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-12">
				<p class="justify-align-stretch">
					Telah melaksanakan kewajiban pengembangan bawang putih di dalam negeri sebagaimana ketentuan dalam Permentan 39 tahun 2019 dan perubahannya.
				</p>
				<p class="justify-align-stretch mt-3">
					Atas perhatian dan kerjasama Saudara disampaikan terima kasih.
				</p>
			</div>
			<div class="col-12">
				<dl class="row mt-5 align-items-center">
					<dd class="col-sm-7">
						{{$QrCode}}
					</dd>
					<dd class="col-sm-5">
							<span class="mb-5" style="height: 7em">{{$pejabat->jabatan ?? ''}},</span>
							<br><br><br>
							@if($pejabat || $pejabat->sign_img)
								<img style="max-width: 7em" src="{{ asset('storage/uploads/dataadmin/'.$pejabat->sign_img) }}" alt="ttd">
							@else
								ttd.
							@endif
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
