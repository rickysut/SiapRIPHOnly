@extends('layouts.admin')
@section('styles')
<style>
	a {
		text-decoration: none !important;
	}
	/* Remove outer border from the entire DataTable */
	.dataTables_wrapper {
		border: none;
	}

	/* Remove cell borders within the DataTable */
	table.dataTable td,
	table.dataTable th {
		border: none;
	}

	/* Remove the header border */
	table.dataTable thead th {
		border-bottom: none;
	}

	/* Remove the footer border (if applicable) */
	table.dataTable tfoot th {
		border-top: none;
	}
</style>
@endsection
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
@can('pengajuan_create')
@php
	$npwp = str_replace(['.', '-'], '', $commitment->npwp);
@endphp
<div class="row">
	<div class="col-12">
		<div class="text-center">
			<i class="fal fa-badge-check fa-3x subheader-icon"></i>
			<h2>Ringkasan Data</h2>
			<div class="row justify-content-center">
				<p class="lead">Ringkasan {{$page_heading}}.</p>
			</div>
		</div>

		<div id="panel-1" class="panel">
			<div class="panel-container">
				<div class="panel-content">
					<table class="table table-hover table-sm w-100" style="border: none; border-top:none; border-bottom:none;" id="dataTable">
						<thead>
							<th  style="width: 32%"></th>
							<th style="width: 1%"></th>
							<th></th>
							<th></th>
						</thead>
						<tbody>
							<tr>
								<td class="text-uppercase fw-500 h6">RINGKASAN UMUM</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Perusahaan</td>
								<td>:</td>
								<td class="fw-500">{{$company}}</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Nomor Ijin (RIPH)</td>
								<td>:</td>
								<td class="fw-500">{{$noIjin}}</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Periode RIPH</td>
								<td>:</td>
								<td class="fw-500">Tahun {{$periode}}</td>
								<td></td>
							</tr>
							<tr class="bg-primary-50" style="height: 25px; opacity: 0.2">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-uppercase fw-500 h6">RINGKASAN KEWAJIBAN DAN REALISASI</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Luas Wajib Tanam</td>
								<td>:</td>
								<td class="fw-500">{{ number_format($wajibTanam, 2, ',', '.') }} ha</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Realisasi Tanam</td>
								<td>:</td>
								<td class="fw-500">
									@if($wajibTanam > $realisasiTanam)
										<span class="text-warning">{{ number_format($realisasiTanam, 2, ',', '.') }} ha</span>
										<i class="fas fa-exclamation-circle text-warning ml-1"></i>
									@else
										{{ number_format($realisasiTanam, 2, ',', '.') }} ha
										<i class="fas fa-check text-success mr-1"></i>
									@endif
								</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Jumlah Lokasi Tanam/Spasial</td>
								<td>:</td>
								<td class="fw-500">{{ $hasGeoloc }} titik</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Volume Wajib Produksi</td>
								<td>:</td>
								<td class="fw-500">{{ number_format($wajibProduksi, 2, ',', '.') }} ton</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Realisasi Produksi</td>
								<td>:</td>
								<td class="fw-500">
									@if($wajibProduksi > $realisasiProduksi)
										<span class="text-danger">{{ number_format($realisasiProduksi, 2, ',', '.') }} ton</span>
										<i class="fas fa-exclamation-circle text-danger ml-1"></i>
									@else
										<span>{{ number_format($realisasiProduksi, 2, ',', '.') }} ton</span>
										<i class="fas fa-check text-success mr-1"></i>
									@endif
								</td>
								<td></td>
							</tr>
							<tr class="bg-primary-50" style="height: 25px; opacity: 0.2">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-uppercase fw-500 h6">RINGKASAN KEMITRAAN</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Jumlah Kelompok Tani Mitra</td>
								<td>:</td>
								<td class="fw-500">{{ $countPoktan }} kelompok</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Jumlah Anggota Kelompok</td>
								<td>:</td>
								<td class="fw-500">{{ $countAnggota }} anggota</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted">Jumlah Perjanjian (PKS) diunggah</td>
								<td>:</td>
								<td class="fw-500">{{ $countPks }} berkas</td>
								<td></td>
							</tr>
							<tr class="bg-primary-50" style="height: 25px; opacity: 0.2">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>

							<tr>
								<td class="text-uppercase fw-500">KELENGKAPAN BERKAS</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-uppercase fw-500">A. TAHAP TANAM</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Surat Pengajuan Verifikasi Tanam</td>
								<td>:</td>
								<td class="fw-500">
									@if ($userDocs->spvt)
										@if ($userDocs->spvtcheck === 'sesuai')
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvt) }}">
												Ada
											</a>
											<i class="fa fa-check text-success ml-1"></i>
										@else
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvt) }}">
												Ada
											</a>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									@else
										<span class="text-danger">Tidak ada berkas</span>
										<i class="fas fa-exclamation-circle text-danger ml-1"></i>
									@endif
								</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Surat Pertanggungjawaban Mutlak (Tanam)</td>
								<td>:</td>
								<td class="fw-500">
									@if ($userDocs->sptjmtanam)
										@if ($userDocs->sptjmtanamcheck === 'sesuai')
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmtanam) }}">
												Ada
											</a>
											<i class="fa fa-check text-success ml-1"></i>
										@else
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmtanam) }}">
												Ada
											</a>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									@else
										<span class="text-danger">Tidak ada berkas</span>
										<i class="fas fa-exclamation-circle text-danger ml-1"></i>
									@endif
								</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Form Realisasi Tanam</td>
								<td>:</td>
								<td class="fw-500">
									@if ($userDocs->rta)
										@if ($userDocs->rtacheck === 'sesuai')
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
												Ada
											</a>
											<i class="fa fa-check text-success ml-1"></i>
										@else
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
												Ada
											</a>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									@else
										<span class="text-danger">Tidak ada berkas</span>
										<i class="fas fa-exclamation-circle text-danger ml-1"></i>
									@endif
								</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">SPH-SBS (Tanam)</td>
								<td>:</td>
								<td class="fw-500">
									@if ($userDocs->sphtanam)
										@if ($userDocs->sphtanamcheck === 'sesuai')
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
												Ada
											</a>
											<i class="fa fa-check text-success ml-1"></i>
										@else
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
												Ada
											</a>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									@else
										<span class="text-danger">Tidak ada berkas</span>
										<i class="fas fa-exclamation-circle text-danger ml-1"></i>
									@endif
								</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Logbook (s.d Tanam)</td>
										<td>:</td>
										<td class="fw-500">
											@if ($userDocs->logbooktanam)
												@if ($userDocs->logbooktanamcheck === 'sesuai')
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbooktanam) }}">
														Ada
													</a>
													<i class="fa fa-check text-success ml-1"></i>
												@else
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbooktanam) }}">
														Ada
													</a>
													<i class="fas fa-exclamation-circle text-danger ml-1"></i>
												@endif
											@else
												<span class="text-danger">Tidak ada berkas</span>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										</td>
								<td></td>
							</tr>

							{{-- berkas produksi --}}
							@if(!(request()->is('admin/task/commitment/*/pengajuan/tanam/show') || request()->is('admin/task/commitment/*/formavt')))
								<tr>
									<td class="text-uppercase fw-500">B. TAHAP PRODUKSI</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Surat Pengajuan Verifikasi Produksi</td>
									<td>:</td>
									<td class="fw-500">
										@if ($userDocs->spvp)
											@if ($userDocs->spvpcheck === 'sesuai')
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvp) }}">
													Ada
												</a>
												<i class="fa fa-check text-success ml-1"></i>
											@else
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvp) }}">
													Ada
												</a>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										@else
											<span class="text-danger">Tidak ada berkas</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Surat Pertanggungjawaban Mutlak (Produksi)</td>
									<td>:</td>
									<td class="fw-500">
										@if ($userDocs->sptjmproduksi)
											@if ($userDocs->sptjmproduksicheck === 'sesuai')
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmproduksi) }}">
													Ada
												</a>
												<i class="fa fa-check text-success ml-1"></i>
											@else
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmproduksi) }}">
													Ada
												</a>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										@else
											<span class="text-danger">Tidak ada berkas</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Form Realisasi Produksi</td>
									<td>:</td>
									<td class="fw-500">
										@if ($userDocs->rpo)
											@if ($userDocs->rpocheck === 'sesuai')
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rpo) }}">
													Ada
												</a>
												<i class="fa fa-check text-success ml-1"></i>
											@else
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rpo) }}">
													Ada
												</a>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										@else
											<span class="text-danger">Tidak ada berkas</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">SPH-SBS (Produksi)</td>
									<td>:</td>
									<td class="fw-500">
										@if ($userDocs->sphproduksi)
											@if ($userDocs->sphproduksicheck === 'sesuai')
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphproduksi) }}">
													Ada
												</a>
												<i class="fa fa-check text-success ml-1"></i>
											@else
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphproduksi) }}">
													Ada
												</a>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										@else
											<span class="text-danger">Tidak ada berkas</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Logbook (s.d Produksi)</td>
									<td>:</td>
									<td class="fw-500">
										@if ($userDocs->logbookproduksi)
											@if ($userDocs->logbookproduksicheck === 'sesuai')
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbookproduksi) }}">
													Ada
												</a>
												<i class="fa fa-check text-success ml-1"></i>
											@else
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbookproduksi) }}">
													Ada
												</a>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										@else
											<span class="text-danger">Tidak ada berkas</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Laporan Akhir</td>
									<td>:</td>
									<td class="fw-500">
										@if ($userDocs->formLa)
											@if ($userDocs->formLacheck === 'sesuai')
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->formLa) }}">
													Ada
												</a>
												<i class="fa fa-check text-success ml-1"></i>
											@else
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->formLa) }}">
													Ada
												</a>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										@else
											<span class="text-danger">Tidak ada berkas</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									</td>
									<td></td>
								</tr>
							@endif

							{{-- hasil pemeriksaan --}}
							<tr class="bg-primary-50" style="height: 25px; opacity: 0.2">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-uppercase fw-500 h6">RINGKASAN HASIL</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-uppercase fw-500">A. VERIFIKASI TANAM</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Tanggal Pengajuan</td>
								<td>:</td>
								<td class="fw-500">{{ $avtDate ? \Carbon\Carbon::parse($avtDate)->format('d F Y') : '-' }}</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Tanggal Verifikasi</td>
								<td>:</td>
								<td class="fw-500">{{ $avtVerifAt ? \Carbon\Carbon::parse($avtVerifAt)->format('d F Y') : '-'}}</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Metode Verifikasi</td>
								<td>:</td>
								<td class="fw-500">{{ $avtMetode ? $avtMetode : '-' }}</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Catatan Verifikasi</td>
								<td>:</td>
								<td class="fw-500">{{ $avtNote ? $avtNote : '-'}}</td>
								<td></td>
							</tr>
							<tr>
								<td class="text-muted pl-4">Hasil Verifikasi</td>
								<td>:</td>
								<td class="fw-500">
									@if ($avtStatus)
										@if ($avtStatus === '1')
											<span class="text-danger">Verifikasi sedang diajukan</span>
											<i class="fas fa-exclamation-circle text-warning ml-1"></i>
										@elseif($avtStatus === '2' || $avtStatus === '3')
											<span class="text-danger">Dalam proses pemeriksaan/verifikasi oleh Petugas</span>
											<i class="fas fa-exclamation-circle text-warning ml-1"></i>
										@elseif($avtStatus === '4')
											<span class="text-success">Pemeriksaan/Verifikasi telah Selesai</span>
											<i class="fas fa-check text-success ml-1"></i>
										@elseif($avtStatus === '5')
											<span class="text-danger">Perbaiki Laporan</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									@else
										Belum/Tidak ada pengajuan
										<i class="fas fa-exclamation-circle text-warning ml-1"></i>
									@endif
								</td>
								<td></td>
							</tr>
							@if(!(request()->is('admin/task/commitment/*/pengajuan/tanam/show') || request()->is('admin/task/commitment/*/formavt')))
								<tr>
									<td class="text-uppercase fw-500">B. VERIFIKASI PRODUKSI</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Tanggal Pengajuan</td>
									<td>:</td>
									<td class="fw-500">{{ $avpDate ? \Carbon\Carbon::parse($avpDate)->format('d F Y') : '-'}}</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Tanggal Verifikasi</td>
									<td>:</td>
									<td class="fw-500">{{ $avpVerifAt ? \Carbon\Carbon::parse($avpVerifAt)->format('d F Y') : '-'}}</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Metode Verifikasi</td>
									<td>:</td>
									<td class="fw-500">{{ $avpMetode ? $avpMetode : '-'}}</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Catatan Verifikasi</td>
									<td>:</td>
									<td class="fw-500">{{ $avpNote ? $avpNote : '-'}}</td>
									<td></td>
								</tr>
								<tr>
									<td class="text-muted pl-4">Hasil Verifikasi</td>
									<td>:</td>
									<td class="fw-500">
										@if ($avpStatus)
											@if ($avpStatus === '1')
												<span class="text-danger">Verifikasi sedang diajukan</span>
												<i class="fas fa-exclamation-circle text-warning ml-1"></i>
											@elseif($avpStatus === '2' || $avpStatus === '3')
												<span class="text-danger">Dalam proses pemeriksaan/verifikasi oleh Petugas</span>
												<i class="fas fa-exclamation-circle text-warning ml-1"></i>
											@elseif($avpStatus === '4')
												<span class="text-success">Pemeriksaan/Verifikasi telah Selesai</span>
												<i class="fas fa-check text-success ml-1"></i>
											@elseif($avpStatus === '5')
												<span class="text-danger">Perbaiki Laporan</span>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										@else
											<span class="text-danger">Belum/Tidak ada pengajuan</span>
											<i class="fas fa-exclamation-circle text-danger ml-1"></i>
										@endif
									</td>
									<td></td>
								</tr>
								@if(request()->is('admin/task/commitment/*/pengajuan/skl/show'))
									@if ($commitment->ajuskl)
										<tr>
											<td class="text-uppercase fw-500">C. PENGAJUAN SKL</td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td class="text-muted pl-4">Tanggal Pengajuan</td>
											<td>:</td>
											<td class="fw-500">{{ $avsklDate ? \Carbon\Carbon::parse($avsklDate)->format('d F Y') : '-'}}</td>
											<td></td>
										</tr>
										<tr>
											<td class="text-muted pl-4">Tanggal Rekomendasi</td>
											<td>:</td>
											<td class="fw-500">{{ $avsklVerifAt ? \Carbon\Carbon::parse($avsklVerifAt)->format('d F Y') : '-'}}</td>
											<td></td>
										</tr>
										<tr>
											<td class="text-muted pl-4">Catatan Rekomendasi</td>
											<td>:</td>
											<td class="fw-500">{{ $avsklNote ? $avsklNote : '-'}}</td>
											<td></td>
										</tr>
										<tr>
											<td class="text-muted pl-4">Progress Pengajuan</td>
											<td>:</td>
											<td class="fw-500">
												@if ($avsklStatus)
													@if ($avsklStatus === '1')
														<span class="">Pengajuan SKL</span>
													@elseif($avsklStatus === '2')
														<span class="">Proses Rekomendasi</span>
													@elseif($avsklStatus === '3')
														<span class="">Telah Disetujui</span>
													@elseif($avsklStatus === '4')
														<span class="">Telah Diterbitkan</span>
														<i class="fas fa-check text-success ml-1"></i>
													@elseif($avsklStatus === '5')
														<span class="text-danger">Perbaiki Laporan</span>
														<i class="fas fa-exclamation-circle text-danger ml-1"></i>
													@endif
												@else
													<span class="text-danger">Belum/Tidak ada pengajuan</span>
													<i class="fas fa-exclamation-circle text-danger ml-1"></i>
												@endif
											</td>
											<td></td>
										</tr>
									@endif
								@endif
							@endif
						</tbody>
					</table>
				</div>
			</div>

			<div class="card-footer d-flex justify-content-end">
				<a href="{{ route('admin.task.commitment') }}"
					class="btn btn-xs btn-info mr-1" data-toggle="tooltip"
					title data-original-title="Kembali">
					<i class="fal fa-undo mr-1"></i>
					Kembali
				</a>
				{{-- Form pengajuan --}}
				{{-- pengajuan tanam --}}
				@if(request()->is('admin/task/commitment/*/formavt') || request()->is('admin/task/commitment/*/pengajuan/tanam/show'))
					<form action="{{route('admin.task.commitment.avt.store', $commitment->id)}}" method="post">
						@csrf
						@if(!$commitment->ajuTanam || !in_array($commitment->ajuTanam->status, ['1', '2', '3', '4']))
							<button type="submit" class="btn btn-xs btn-warning" data-toggle="tooltip"
							title data-original-title="Ajukan Verifikasi Tanam">
								<i class="fal fa-upload mr-1"></i>
								Ajukan
							</button>
						@endif
					</form>
				@endif
				{{-- pengajuan produksi --}}
				@if(request()->is('admin/task/commitment/*/formavp') || request()->is('admin/task/commitment/*/pengajuan/produksi/show'))
					<form action="{{route('admin.task.commitment.avp.store', $commitment->id)}}" method="post">
						@csrf
						@if(!$commitment->ajuProduksi || !in_array($commitment->ajuProduksi->status, ['1', '2', '3', '4']))
							<button type="submit" class="btn btn-xs btn-warning" data-toggle="tooltip"
							title data-original-title="Ajukan Verifikasi Produksi">
								<i class="fal fa-upload mr-1"></i>
								Ajukan
							</button>
						@endif
					</form>
				@endif
				{{-- pengajuan skl --}}
				@if(request()->is('admin/task/commitment/*/formavskl') || request()->is('admin/task/commitment/*/pengajuan/skl/show'))
					<form action="{{route('admin.task.commitment.avskl.store', $commitment->id)}}" method="post">
						@csrf
						@if(!$commitment->ajuskl || !in_array($commitment->ajuskl->status, ['1', '2', '3', '4']))
							<button type="submit" class="btn btn-xs btn-warning" data-toggle="tooltip"
							title data-original-title="Ajukan penerbitan SKL dan status Lunas">
								<i class="fal fa-upload mr-1"></i>
								Ajukan
							</button>
						@endif
					</form>
				@endif
			</div>
		</div>
	</div>
</div>

{{-- modal view doc --}}
<div class="modal fade" id="viewDocs" tabindex="-1" role="dialog" aria-labelledby="document" aria-hidden="true">
	<div class="modal-dialog modal-dialog-right" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					Berkas <span class="fw-300"><i>lampiran </i></span>
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body embed-responsive embed-responsive-16by9">
				<iframe class="embed-responsive-item" src="" width="100%"  frameborder="0"></iframe>
			</div>
		</div>
	</div>
</div>

@endcan
@endsection

@section('scripts')
@parent
<script>
	$(document).ready(function() {
		$('#viewDocs').on('shown.bs.modal', function (e) {
			var docUrl = $(e.relatedTarget).data('doc');
			$('iframe').attr('src', docUrl);
		});

		$('#dataTable').DataTable({
				responsive: true,
				"ordering": false,
				lengthChange: false,
				pageLength: -1,
				dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-7'>>",
				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						title: 'Ringkasan Pengajuan {{$page_heading}}',
						titleAttr: 'Ekspor data ke MS. Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						title: 'Ringkasan Pengajuan {{$page_heading}}',
						titleAttr: 'Cetak halaman data.',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					},
					// {
					// 	text: 'Ajukan',
					// 	titleAttr: 'Lihat Detail',
					// 	className: 'btn btn-outline-info btn-xs',
					// 	action: function () {
					// 		// Replace 'to_somewhere' with your actual route and $key->id with the parameter value

					// 	}
					// }
				],
			});
	});
</script>

<script>
	function validateInput() {
		// get the input value and the current username from the page
		var inputVal = document.getElementById('validasi').value;
		var currentUsername = '{{ Auth::user()->username }}';

		// check if the input is not empty and matches the current username
		if (inputVal !== '' && inputVal === currentUsername) {
			return true; // allow form submission
		} else {
			alert('Input validasi harus diisi dan bernilai sama dengan username Anda.');
			return false; // prevent form submission
		}
	}

	//back button
	function cancelBtn() {
		history.go(-1);
	}
</script>

@endsection
