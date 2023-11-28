@extends('layouts.admin')
@section ('styles')
<style>
	td {
		vertical-align: middle !important;
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
	@can('online_access')
		@include('partials.sysalert')
		<div class="row" id="contentToPrint">
			@php
				$npwp = str_replace(['.', '-'], '', $commitment->npwp);
			@endphp
			<div class="col-12 d-flex flex-col align-items-center">
				<div class="">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#panel-2" role="tab" aria-selected="true">Ringkasan Pengajuan</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#panel-3" role="tab" aria-selected="true">Hasil Verifikasi Berkas</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#panel-4" role="tab" aria-selected="true">Data Kemitraan</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#panel-5" role="tab" aria-selected="true">Data Lokasi Tanam</a>
						</li>
					</ul>
				</div>
				<div class="ml-auto">
					<a href="#confirmSection" class="btn btn-primary btn-xs">Rekomendasikan</a>
				</div>
			</div>
			<div class="col-12">
				<div class="tab-content">
					<div class="tab-pane fade active show" id="panel-2" role="tabpanel" aria-labelledby="panel-2">
						<div id="panel-2" class="panel">
							<div class="panel-container">
								<div class="panel-content">
									<table class="table table-hover table-sm w-100" style="border: none; border-top:none; border-bottom:none;" id="dataSummary">
										<thead>
											<th  style="width: 32%"> </th>
											<th style="width: 1%"> </th>
											<th> </th>
										</thead>
										<tbody>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Umum
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-muted">Perusahaan</td>
												<td>:</td>
												<td class="fw-500" id="company">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Nomor Ijin (RIPH)</td>
												<td>:</td>
												<td class="fw-500" id="noIjin">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Periode</td>
												<td>:</td>
												<td class="fw-500" id="periode">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Tanggal Terbit</td>
												<td>:</td>
												<td class="fw-500" id="tgl_ijin">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Tanggal Berakhir</td>
												<td>:</td>
												<td class="fw-500" id="tgl_akhir">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Tanggal Pengajuan SKL</td>
												<td>:</td>
												<td class="fw-500" id="avsklDate">
												</td>
											</tr>
											<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Kewajiban dan Realisasi
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-muted">Luas Wajib Tanam</td>
												<td>:</td>
												<td class="fw-500" id="wajibTanam">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Realisasi Tanam</td>
												<td>:</td>
												<td class="fw-500" id="realisasiTanam">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Lokasi Tanam/Spasial</td>
												<td>:</td>
												<td class="fw-500" id="hasGeoloc">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Volume Wajib Produksi</td>
												<td>:</td>
												<td class="fw-500" id="wajibProduksi">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Realisasi Tanam</td>
												<td>:</td>
												<td class="fw-500" id="realisasiProduksi">
												</td>
											</tr>
											<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Kemitraan
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Kelompok Tani Mitra</td>
												<td>:</td>
												<td class="fw-500" id="countPoktan">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Anggota Kelompok Tani Mitra</td>
												<td>:</td>
												<td class="fw-500" id="countAnggota">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Perjanjian (PKS) diunggah</td>
												<td>:</td>
												<td class="fw-500" id="countPks">
												</td>
											</tr>
											<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Verifikasi Sebelumnya
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500">A. TAHAP TANAM</td>
												<td>:</td>
												<td class="fw-500" id=""></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Nota Dinas Verifikasi Tanam</td>
												<td>:</td>
												<td class="fw-500" id="ndhprt"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Berita Acara Pemeriksaan Tanam</td>
												<td>:</td>
												<td class="fw-500" id="batanam"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avtDate"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avtVerifAt"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Metode Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avtMetode"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Catatan Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avtNote"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Hasil Pemeriksaan (Status)</td>
												<td>:</td>
												<td class="fw-500" id="avtStatus"></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500">B. TAHAP PRODUKSI</td>
												<td>:</td>
												<td class="fw-500" id=""></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Nota Dinas Verifikasi Produksi</td>
												<td>:</td>
												<td class="fw-500" id="ndhprp"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Berita Acara Pemeriksaan Produksi</td>
												<td>:</td>
												<td class="fw-500" id="baproduksi"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avpDate"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avpVerifAt"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Metode Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avpMetode"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Catatan Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avpNote"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Hasil Pemeriksaan (Status)</td>
												<td>:</td>
												<td class="fw-500" id="avpStatus"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-3" role="tabpanel" aria-labelledby="panel-3">
						<div id="panel-3" class="panel">
							<form method="post"
								action="{{route('verification.skl.checkBerkas', $verifikasi->id)}}">
								@csrf
								<div class="panel-container">
									<div class="panel-tag fade show">
										<div class="d-flex align-items-center">
											<i class="fal fa-info-circle mr-1"></i>
											<div class="flex-1">
												<small>Berikut ini adalah berkas-berkas kelengkapan yang diunggah oleh Pelaku Usaha sebagai syarat Pengajuan Penerbitan Surat Keterangan Lunas (SKL). Berkas-berkas ini telah diperiksa (verifikasi) oleh Petugas Pemeriksa (Verifikator) pada tahap Verifikasi Komitmen Wajib Produksi.</small><br>
											</div>
										</div>
										@if (empty($verifTanam->status))
											<div class="d-flex align-items-center text-danger">
												<i class="fa fa-exclamation-circle fa-1x mr-1"></i>
												<div class="flex-1">
													<small>Pelaku usaha belum/tidak mengajukan verifikasi tanam</small><br>
												</div>
											</div>
										@endif
									</div>
									<div class="panel-content">
										<table class="table table-bordered table-hover table-striped table-sm w-100" style="vertical-align: middle" id="attCheck">
											<thead class="thead-themed">
												<th class="text-uppercase text-muted">Dokumen</th>
												<th class="text-uppercase text-muted">Hasil Periksa</th>
												<th class="text-uppercase text-muted">Tautan Berkas</th>
											</thead>
											<tbody>
												<tr>
													<td>
														@if ($userDocs->sptjmtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmtanam) }}">
																Surat Pertanggungjawaban Mutlak (tanam)
															</a>
														@else
															<span>Surat Pertanggungjawaban Mutlak (tanam)</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sptjmtanam)
															@if (empty($userDocs->sptjmtanamcheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->sptjmtanamcheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->sptjmtanamcheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																<i class="fal fa-exclamation-circle text-danger mr-1"></i>
															@endif
															{{-- <select class="form-control form-control-sm {{ $userDocs->sptjmtanamcheck ? '' : 'border-danger' }}" name="sptjmtanamcheck" id="sptjmtanamcheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->sptjmtanamcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->sptjmtanamcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sptjmtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmtanam) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->sptjmtanamcheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->sptjmproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmproduksi) }}">
																Surat Pertanggungjawaban Mutlak (produksi)
															</a>
														@else
															<span>Surat Pertanggungjawaban Mutlak (produksi)</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sptjmproduksi)
															@if (empty($userDocs->sptjmproduksicheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->sptjmproduksicheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->sptjmproduksicheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																	<i class="fal fa-exclamation-circle text-danger ml-1"></i>
															@endif
															{{-- <select class="form-control form-control-sm {{ $userDocs->sptjmproduksicheck ? '' : 'border-danger' }}" name="sptjmproduksicheck" id="sptjmproduksicheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->sptjmproduksicheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->sptjmproduksicheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sptjmproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmproduksi) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->sptjmproduksicheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->rta)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
																Form Realisasi Tanam
															</a>
														@else
															<span>Form Realisasi Tanam</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->rta)
															@if (empty($userDocs->rtacheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->rtacheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->rtacheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																	<i class="fal fa-exclamation-circle text-danger ml-1"></i>
																</span>
															@endif
															{{-- <select class="form-control form-control-sm {{ $userDocs->rtacheck ? '' : 'border-danger' }}" name="rtacheck" id="rtacheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->rtacheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->rtacheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->rta)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->rtacheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->rpo)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rpo) }}">
																Form Realisasi Produksi
															</a>
														@else
															<span>Form Realisasi Produksi</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->rpo)
															@if (empty($userDocs->rpocheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->rpocheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->rpocheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																	<i class="fal fa-exclamation-circle text-danger ml-1"></i>
																</span>
															@endif
															{{-- <select class="form-control form-control-sm {{ $userDocs->rpocheck ? '' : 'border-danger' }}" name="rpocheck" id="rpocheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->rpocheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->rpocheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->rpo)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rpo) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->rpocheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->sphtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
																SPH-SBS (Tanam)
															</a>
														@else
															<span>SPH-SBS (Tanam)</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphtanam)
															@if (empty($userDocs->sphtanamcheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->sphtanamcheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->sphtanamcheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																	<i class="fal fa-exclamation-circle text-danger ml-1"></i>
																</span>
															@endif
														{{-- <select class="form-control form-control-sm {{ $userDocs->sphtanamcheck ? '' : 'border-danger' }}" name="sphtanamcheck" id="sphtanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphtanamcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->sphtanamcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->sphtanamcheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->sphproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphproduksi) }}">
																SPH-SBS (Produksi)
															</a>
														@else
															<span>SPH-SBS (Produksi)</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphproduksi)
															@if (empty($userDocs->sphproduksicheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->sphproduksicheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->sphproduksicheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																	<i class="fal fa-exclamation-circle text-danger ml-1"></i>
																</span>
															@endif
															{{-- <select class="form-control form-control-sm {{ $userDocs->sphproduksicheck ? '' : 'border-danger' }}" name="sphproduksicheck" id="sphproduksicheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->sphproduksicheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->sphproduksicheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphproduksi) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->sphproduksicheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->logbookproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbookproduksi) }}">
																Logbook Produksi
															</a>
														@else
															<span>Logbook Produksi</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->logbookproduksi)
															@if (empty($userDocs->logbookproduksicheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->logbookproduksicheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->logbookproduksicheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																	<i class="fal fa-exclamation-circle text-danger ml-1"></i>
																</span>
															@endif
															{{-- <select class="form-control form-control-sm {{ $userDocs->logbookproduksicheck ? '' : 'border-danger' }}" name="logbookproduksicheck" id="logbookproduksicheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->logbookproduksicheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->logbookproduksicheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->logbookproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbookproduksi) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->logbookproduksicheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->formLa)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->formLa) }}">
																Laporan Akhir
															</a>
														@else
															<span>Laporan Akhir</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->formLa)
															@if (empty($userDocs->formLacheck))
																<span class="fw-500 text-danger">Belum Diperiksa</span>
															@elseif($userDocs->formLacheck == 'sesuai')
																<span class="fw-500">Sesuai</span>
																<i class="fal fa-check text-success ml-1"></i>
															@elseif($userDocs->formLacheck == 'perbaiki')
																<span class="fw-500">Perbaikan</span>
																	<i class="fal fa-exclamation-circle text-danger ml-1"></i>
																</span>
															@endif
															{{-- <select class="form-control form-control-sm {{ $userDocs->formLacheck ? '' : 'border-danger' }}" name="formLacheck" id="formLacheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->formLacheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->formLacheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select> --}}
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->formLa)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->formLa) }}">
																<i class="fal fa-file-invoice"></i>
															</a>
														@else
															<i class="fal fa-file-invoice"></i>
														@endif
														@if (empty($userDocs->formLacheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								{{-- <div class="card-footer d-flex align-items-center justify-content-between">
									<div class="ml-auto">
										<button type="submit" class="btn btn-primary btn-sm">
											<i class="fal fa-save mr-1"></i>Simpan Hasil Pemeriksaan
										</button>
									</div>
								</div> --}}
							</form>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-4" role="tabpanel" aria-labelledby="panel-4">
						<div id="panel-4" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah data Perjanjian Kerjasama. Anda dapat memeriksa ulang dan menetapkan hasil pemeriksaan.</small>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="pksCheck">
										<thead class="thead-themed">
											<tr>
												<th class="text-uppercase text-muted">Nomor Perjanjian</th>
												<th class="text-uppercase text-muted">Kelompok Tani</th>
												<th class="text-uppercase text-muted">Masa Berlaku</th>
												<th class="text-uppercase text-muted">Status</th>
												<th class="text-uppercase text-muted">Tindakan</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($pkss as $pks)
												<tr>
													<td>{{$pks->no_perjanjian}}</td>
													<td>{{$pks->masterpoktan->nama_kelompok}}</td>
													<td>
														{{$pks->tgl_perjanjian_start}} s.d
														{{$pks->tgl_perjanjian_end}}
													</td>
													<td>
														{{$pks->status}}
													</td>
													<td class="text-center">
														<a href="{{route('verification.skl.check.pks', ['noIjin' => $noIjin, 'poktan_id' => $pks->poktan_id]) }}" class="btn btn-icon @if($pks->status) btn-success @else btn-warning @endif btn-xs" data-toggle="tooltip" data-original-title="Lihat/Periksa berkas dan data.">
															<i class="fal fa-search"></i>
														</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<form action="{{route('verification.skl.checkPksSelesai', $verifikasi->id)}}" method="post" id="pksChecked">
								@csrf
								{{-- <div class="card-footer d-flex align-items-center justify-content-between">
									<div>
										<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> :Belum dilakukan pemeriksaan.
									</div>
									<div class="ml-auto">
										<button type="submit" class="btn btn-primary btn-sm" id="submitWarning">
											<i class="fal fa-save mr-1"></i>Simpan Hasil Pemeriksaan
										</button>
									</div>
								</div> --}}
							</form>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-5" role="tabpanel" aria-labelledby="panel-5">
						<div id="panel-5" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah data lokasi tanam-produksi. Anda dapat melihat dan memeriksa titik lokasi dan polygon lahan.</small>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="dataTable">
										<thead>
											<th class="text-uppercase text-muted">Kelompoktani</th>
											<th class="text-uppercase text-muted">Nama Lokasi</th>
											<th class="text-uppercase text-muted">Pengelola</th>
											<th class="text-uppercase text-muted">Luas</th>
											<th class="text-uppercase text-muted">Volume</th>
											<th class="text-uppercase text-muted">Tindakan</th>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col" id="confirmSection">
				<div class="panel" id="panel-6">
					<div class="panel-hdr">
						<h2>Panel Rekomendasi</h2>
					</div>
					<div class="panel-container show">
						<form action="{{route('verification.skl.storeCheck', $verifikasi->id)}}" method="POST" enctype="multipart/form-data">
							@csrf
							<div class="panel-content">
								<input type="hidden" name="no_ijin" value="{{$verifikasi->no_ijin}}">
								<input type="hidden" name="no_pengajuan" value="{{$verifikasi->no_pengajuan}}">
								<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
								<div class="form-group row">
									<label for="note" class="col-md-3 col-lg-2 col-form-label">Nomor SKL <sup class="text-danger"> *</sup></label>
									<div class="col-md-9 col-lg-10">
										<input type="text" class="form-control form-control-sm" name="no_skl" id="no_skl">
										<span class="help-block">Nomor Surat Keterangan Lunas.
											<span class="text-danger">(wajib)</span>
										</span>
									</div>
								</div>
								<div class="form-group row">
									<label for="note" class="col-md-3 col-lg-2 col-form-label">Tanggal diterbitkan <sup class="text-danger"> *</sup></label>
									<div class="col-md-9 col-lg-10">
										<input type="date" class="form-control form-control-sm" name="published_date" id="published_date">
										<span class="help-block">Tanggal diterbitkanya Surat Keterangan Lunas. Tanggal ini juga akan ditampilkan pada lembar SKL sebagai Tanggal diterbitkan.
											<span class="text-danger">(wajib)</span>
										</span>
									</div>
								</div>
								<div class="form-group row">
									<label for="note" class="col-md-3 col-lg-2 col-form-label">Catatan <sup class="text-danger"> *</sup></label>
									<div class="col-md-9 col-lg-10">
										<textarea name="note" id="note" rows="3" class="form-control form-control-sm" required>{{ old('note', $verifikasi ? $verifikasi->note : '') }}</textarea>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<div class="form-group">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm" placeholder="ketik username Anda di sini" id="validasi" name="validasi"required>
										<div class="input-group-append">
											<button class="btn btn-danger btn-sm" type="submit" onclick="return validateInput()" id="btnSubmit">
												<i class="fab fa-stack-overflow text-align-center mr-1"></i>Rekomendasikan
											</button>
										</div>
									</div>
								</div>
							</div>
						</form>
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

			$(function() {
				$("#pksMitra").select2({
					placeholder: "--Pilih PKS/Poktan",
					dropdownParent:'#selectPks'
				});

				$("#lokasiLahan").select2({
					placeholder: "--Pilih lokasi",
					dropdownParent:'#selectLokasi'
				});
			});

			var tableData = $('#dataTable').DataTable({
				responsive: true,

				lengthChange: true,
				dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					}
				],
				columnDefs: [

					{ className: 'text-right', targets: [3,4] },
					{ className: 'text-center', targets: [5] },
				]
			});

			$('#dataSummary').DataTable({
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
						titleAttr: 'Ekspor data ke MS. Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Cetak halaman data.',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					},
					// {
					// 	text: '<i class="fal fa-external-link"></i>',
					// 	titleAttr: 'Lihat Detail',
					// 	className: 'btn btn-icon btn-outline-info btn-xs',
					// 	action: function () {
					// 		// Replace 'to_somewhere' with your actual route and $key->id with the parameter value
					// 		window.location.href = '{{ route('verification.produksi.check', $verifikasi->id) }}';
					// 	}
					// }
				],
			});

			$.ajax({
				url: '{{ route("verification.data.summary", $verifikasi->id) }}',
				type: 'GET',
				dataType: 'json',
				success: function (data) {
					$("#company").text(data.company);
					$("#npwp").text(data.npwp);
					$("#noIjin").text(data.noIjin);
					$("#tgl_ijin").text(data.tglIjin);
					$("#tgl_akhir").text(data.tglAkhir);
					$("#avsklDate").text(data.avsklDate);
					$("#countPoktan").text(data.countPoktan + ' Kelompok');
					$("#countPks").text(data.countPks + ' berkas');
					$("#countAnggota").text(data.countAnggota + ' anggota');
					$("#avtMetode").text(data.avtMetode);
					$("#avtNote").text(data.avtNote);

					// Menetapkan teks sesuai dengan kondisi
					var options = { day: 'numeric', month: 'long', year: 'numeric' };
					$("#avsklDate").text(data.avsklDate);
					$('#avtDate').text(data.avtDate);
					$("#avtVerifAt").text(data.avtVerifAt);
					$("#avpDate").text(data.avpDate);
					$("#avpVerifAt").text(data.avpVerifAt);

					$("#avpMetode").text(data.avpMetode);
					$("#avpNote").text(data.avpNote);

					var formattedPeriode = 'Tahun ' + (data.periode);
					$("#periode").text(formattedPeriode);

					var formattedWajibTanam = (data.wajibTanam) + ' ha';
					var formattedRealisasiTanam = (data.realisasiTanam) + ' ha';
					var formattedWajibProduksi = (data.wajibProduksi) + ' ton';
					var formattedRealisasiProduksi = (data.realisasiProduksi) + ' ton';
					var formattedHasGeoLoc = (data.hasGeoloc) + ' titik';

					$("#ndhprt").html(data.ndhprt ? '<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.ndhprtLink + '">' + data.ndhprt + '</a>' : '');

					$("#batanam").html(data.batanam ? '<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.batanamLink + '">' + data.batanam + '</a>' : '');

					$("#ndhprp").html('<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.ndhprpLink + '">' + data.ndhprp + '</a>');
					$("#baproduksi").html('<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.baproduksiLink + '">' + data.baproduksi + '</a>');

					$("#wajibTanam").text(formattedWajibTanam);
					$("#realisasiTanam").text(formattedRealisasiTanam);
					$("#wajibProduksi").text(formattedWajibProduksi);
					$("#realisasiProduksi").text(formattedRealisasiProduksi);
					$("#hasGeoloc").text(formattedHasGeoLoc);

					if (typeof data.avtStatus === 'undefined' || data.avtStatus === null){
						$("#avtStatus").text('Tidak ada status').addClass("text-warning text-uppercase fw-500").append('<i class="fas fa-exclamation-circle ml-1"></i>');
					}
					else if (data.avtStatus === '1' || data.avtStatus === '2' || data.avtStatus === '3') {
						$("#avtStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					} else if (data.avtStatus === '4') {
						$("#avtStatus").text('Memenuhi Syarat').addClass("text-success text-uppercase fw-500").append('<i class="fas fa-check ml-1"></i>');
					} else {
						// Handle case when avtStatus doesn't match any of the above conditions
						$("#avtStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					}

					if (data.avpStatus === '1' || data.avpStatus === '2' || data.avpStatus === '3') {
						$("#avpStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					} else if (data.avpStatus === '4') {
						$("#avpStatus").text('Memenuhi Syarat').addClass("text-success text-uppercase fw-500").append('<i class="fas fa-check ml-1"></i>');
					} else {
						// Handle case when avpStatus doesn't match any of the above conditions
						$("#avpStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					}

					if (parseFloat(data.wajibTanam) > parseFloat(data.realisasiTanam)) {
						$("#realisasiTanam").removeClass("text-success").addClass("text-warning").append('<i class="fa fa-exclamation-circle ml-1"></i>');
					} else {
						$("#realisasiTanam").removeClass("text-warning").addClass("text-success").append('<i class="fas fa-check ml-1"></i>');
					}

					if (parseFloat(data.wajibProduksi) > parseFloat(data.realisasiProduksi)) {
						$("#realisasiProduksi").removeClass("text-success").addClass("text-danger").append('<i class="fas fa-exclamation-circle ml-1"></i>');
					} else {
						$("#realisasiProduksi").removeClass("text-danger").addClass("text-success").append('<i class="fas fa-check ml-1"></i>');
					}
				},
				error: function (xhr, status, error) {
					console.error(xhr.responseText);
				}
			});


			function updateTableData() {
				$.ajax({
					url: '{{ route("verification.lokasitanam", $noIjin) }}',
					type: 'GET',
					dataType: 'json',
					success: function(response) {

						tableData.clear().draw();
						if (response.lokasis.length > 0) {
							$.each(response.lokasis, function(index, lokasi) { // Update response handling

								var luasTanam = lokasi.luas_tanam;
								var volProduksi = lokasi.volume;
								var formatter = new Intl.NumberFormat('en-GB', {
									style: 'decimal',
									minimumFractionDigits: 2,
									maximumFractionDigits: 2,
								});
								var noDecimal = new Intl.NumberFormat('en-GB', {
									style: 'decimal',
									minimumFractionDigits: 0,
									maximumFractionDigits: 0,
								});
								var totalLuas = formatter.format(luasTanam);
								var totalProduksi = formatter.format(volProduksi);

								var id = lokasi.id;
								var npwp = lokasi.npwp;
								var noIjin = lokasi.no_ijin;
								var poktan = lokasi.poktan;
								var anggota = lokasi.anggota;
								var jmlTitik = lokasi.jumlahTitik;
								var jmlLokasi = noDecimal.format(jmlTitik) + ' titik';
								var actionBtn = `
									<a href="${lokasi.show}" class="btn btn-xs btn-icon btn-primary" title="Lihat detail">
										<i class="fal fa-search"></i>
									</a>
								`;
								tableData.row.add([poktan, jmlLokasi, anggota, totalLuas, totalProduksi, actionBtn]).draw(false);
							});
						}
						tableData.draw(); // Draw the table after adding the rows
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}
			updateTableData();
		});
	</script>

	<script>
		document.getElementById('submitWarning').addEventListener('click', function () {
			if (confirm('Setiap PKS (yang memiliki berkas lampiran) yang belum Anda periksa akan dinyatakan sebagai "SESUAI". Apakah Anda yakin ingin menyimpan data ini?')) {
				// Lanjutkan dengan submit form jika konfirmasi OK
				document.getElementById('myForm').submit();
			} else {
				// Tidak melakukan apa-apa jika pengguna membatalkan
			}
		});
	</script>

	<script>
		function validateInput1() {
			// get the input value and the current username from the page
			var inputVal = document.getElementById('validasi1').value;
			var currentUsername = '{{ Auth::user()->username }}';

			// check if the input is not empty and matches the current username
			if (inputVal !== '' && inputVal === currentUsername) {
				return true; // allow form submission
			} else {
				alert('Isi kolom Konfirmasi dengan username Anda!.');
				return false; // prevent form submission
			}
		}
	</script>
@endsection
