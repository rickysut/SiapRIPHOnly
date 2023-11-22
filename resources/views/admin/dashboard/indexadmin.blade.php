@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
{{-- @include('partials.subheader') --}}
@can('dashboard_access')
<!-- Page Content -->
<div class="subheader">
	<h1 class="subheader-title">
		<i class="subheader-icon {{ ($heading_class ?? '') }}"></i><span class="fw-700 mr-2 ml-2">{{  ($page_heading ?? '') }}</span><span class="fw-300">Realisasi & Verifikasi</span>
	</h1>
	<div class="subheader-block d-lg-flex align-items-center  d-print-none">
		<div class="d-inline-flex flex-column justify-content-center ">
			<div class="form-group row">
				<label for="periodetahun" class="col-sm-4 col-form-label text-right">Tahun</label>
				<div class="col-sm-8">
					<input id="periodetahun" name="periode" type="text" class="form-control custom-select yearpicker" placeholder="{{$currentYear}}" aria-label="Pilih tahun" aria-describedby="basic-addon2">
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel" id="panel-title-1">
	<div class="panel-hdr">
		<h2>
			<i class="fal fa-tractor mr-2"></i>
			<span class="text-uppercase">Monitoring Realisasi</span>
		</h2>
		<div class="panel-toolbar pr-3 align-self-end">
			<ul id="demo_panel-tabs" class="nav nav-tabs border-bottom-0" role="tablist">
				<li class="nav-item" data-toggle="tooltip" data-original-title="Lihat Data Ringkasan">
					<a class="nav-link active" data-toggle="tab" href="#summaryRealisasi" role="tab">Summary</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-original-title="Lihat Tabel data Komitmen (RIPH)">
					<a class="nav-link" data-toggle="tab" href="#tableCommitment" role="tab">Detail Table</a>
				</li>
			</ul>
			<a href="javascript:void(0);" class="nl-3 mr-1 btn btn-success btn-xs btn-icon waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Tampil/Sembunyi Panel">
				<i class="fal fa-minus"></i>
			</a>
			<a href="javascript:void(0);" class="btn btn-warning btn-xs btn-icon waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Layar Penuh">
				<i class="fal fa-expand"></i>
			</a>
		</div>
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<div class="tab-content">
				<div class="tab-pane fade active show" role="tabpanel" id="summaryRealisasi">
					<div class="row">
						<div class="col-md-3">
							<div class="panel rounded overflow-hidden position-relative text-white mb-g" data-toggle="tooltip" title data-original-title="Jumlah Perusahaan Pemegang RIPH (termasuk yang belum mendapatkan Persetujuan Import) dan Jumlah RIPH yang telah masuk ke dalam Database Simethris.">
								<div class="card-body bg-primary-400">
									<div class="">
										<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
											<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
											<span id="jumlah_importir">{{$jumlah_importir}}</span>
											<small class="m-0 l-h-n">Perusahaan / <span id="company" class="mr-1">{{$company}}</span>Terdaftar</small>
										</h3>
									</div>
								</div>
								<i class="fal fa-landmark position-absolute pos-right pos-bottom opacity-25 mb-n1 mr-n1" style="font-size:4rem"></i>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel rounded overflow-hidden position-relative text-white mb-g"  data-toggle="tooltip" title data-original-title="Jumlah volume RIPH pada periode ini (termasuk yang belum mendapatkan Persetujuan Import).">
								<div class="card-body bg-danger-300">
									<div class="">
										<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
											<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
											<span id="v_pengajuan_import">{{ number_format($v_pengajuan_import, 0, ',', '.') }}</span>
											<small class="m-0 l-h-n">Volume RIPH (ton)</small>
										</h3>
									</div>
								</div>
								<i class="fal fa-balance-scale position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel rounded overflow-hidden position-relative text-white mb-g" data-toggle="tooltip" title data-original-title="Total Luas Komitmen Wajib Tanam pada periode ini (termasuk yang belum mendapatkan Persetujuan Import.).">
								<div class="card-body bg-success-500">
									<div class="">
										<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
											<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
											<span id="v_beban_tanam">{{ number_format($v_beban_tanam, 2, ',', '.') }}</span>
											<small class="m-0 l-h-n">Komitmen Wajib Tanam (ha)</small>
										</h3>
									</div>
								</div>
								<i class="fal fa-seedling position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel rounded overflow-hidden position-relative text-white mb-g" data-toggle="tooltip" title data-original-title="Volume wajib produksi pada periode ini (termasuk yang belum mendapatkan Persetujuan Import).">
								<div class="card-body bg-warning-500">
									<div class="">
										<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
											<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
											<span id="v_beban_produksi">{{ number_format($v_beban_produksi, 2, ',', '.') }}</span>
											<small class="m-0 l-h-n">Komitmen Wajib Produksi (ton)</small>
										</h3>
									</div>
								</div>
								<i class="fal fa-dolly position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="panel" id="panel-2">
								<div class="panel-hdr">
									<h2>
										<i class="subheader-icon fal fa-seedling mr-1"></i>Realisasi Wajib Tanam
									</h2>
									<div class="panel-toolbar">
										{{-- <a href="{{route('admin.dashboard.monitoringrealisasi')}}" class="btn btn-xs btn-success waves-effect waves-themed">Lihat Rincian</a> --}}
									</div>
								</div>
								<div class="panel-container show">
									<div class="panel-content">
										<!-- Row -->
										<div class="row mb-3 align-items-center">
											<div class="col-lg-5 col-sm-6 align-self-center text-center">
												<div class="c-chart-wrapper">
													<div
														id = "naschartTanam"
														class="js-easy-pie-chart color-success-300 position-relative d-inline-flex align-items-center justify-content-center"
														data-percent="{{ number_format($prosenTanam, 2, ',', '.') }}"
														data-piesize="145"
														data-linewidth="10"
														data-linecap="butt"
														data-scalelength="7"
														data-toggle="tooltip"
														title data-original-title="{{ number_format($prosenTanam, 2, ',', '.') }}%. Perbandingan Luas Total realisasi tanam yang dilaporkan pelaku usaha terhadap Total Komitmen."
														data-placement="bottom">
														<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-xl">
															<span class="fs-xxl fw-500 text-dark">
																<span name="prosenTanam" id="prosenTanam">
																	{{ number_format($prosenTanam, 2, ',', '.') }}
																</span>
																<sup>%</sup>
															</span>
														</div>
													</div>
												</div>
											</div>
											<div class="col-lg-7 col-sm-6">
												<div class="shadow-1 p-2 bg-success-600 rounded overflow-hidden position-relative text-white mb-2"  data-toggle="tooltip" title data-original-title="Luas Realisasi Tanam dilaporkan Pelaku Usaha.">
													<div class="card-body">
														<h4 class="display-5 d-block l-h-n m-0 fw-500 text-white">
															<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
															<span id="total_luastanam">{{ number_format($total_luastanam, 2, ',', '.') }}</span>
															<small class="m-0 l-h-n">Total realisasi luas tanam (ha).</small>
														</h4>
													</div>
													<i class="fal fa-seedling position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="panel" id="panel-2">
								<div class="panel-hdr">
									<h2>
										<i class="subheader-icon fal fa-dolly mr-1"></i>Realisasi Wajib Produksi
									</h2>
									<div class="panel-toolbar">
										{{-- <a href="{{route('admin.dashboard.monitoringrealisasi')}}" class="btn btn-xs btn-warning waves-effect waves-themed">Lihat Rincian</a> --}}
									</div>
								</div>
								<div class="panel-container show">
									<div class="panel-content">
										<!-- Row -->
										<div class="row mb-3 align-items-center">
											<div class="col-lg-5 col-sm-6 align-self-center text-center">
												<div class="c-chart-wrapper">
													<div
														id = "naschartProduksi"
														data-percent="{{ number_format($prosenProduksi, 2, ',', '.') }}"
														data-piesize="145"
														data-linewidth="10"
														data-linecap="butt"
														data-scalelength="7"
														data-toggle="tooltip"
														title data-original-title="{{ number_format($prosenProduksi, 2, ',', '.') }}%. Perbandingan Total Produksi yang dilaporkan pelaku usaha terhadap Total Komitmen Produksi."
														data-placement="bottom"
														class="js-easy-pie-chart color-warning-500 position-relative d-inline-flex align-items-center justify-content-center">
														<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-xl">
															<span class="fs-xxl fw-500 text-dark">
																<span name="prosenProduksi" id="prosenProduksi">
																	{{ number_format($prosenProduksi, 2, ',', '.') }}
																</span>
																<sup>%</sup>
															</span>
														</div>
													</div>
												</div>
											</div>
											<div class="col-lg-7 col-sm-6">
												<div class="shadow-1 p-2 bg-warning-600 rounded overflow-hidden position-relative text-white mb-2" data-toggle="tooltip" title data-original-title="Volume Realisasi Produksi dilaporkan Pelaku Usaha.">
													<div class="card-body">
														<h4 class="display-5 d-block l-h-n m-0 fw-500 text-white">
															<span id="total_volume">{{ number_format($total_volume, 2, ',', '.') }}</span>
															<small class="m-0 l-h-n">Total realisasi produksi (ton).</small>
														</h4>
													</div>
													<i class="fal fa-dolly position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade " role="tabpanel" id="tableCommitment">
					<table class="table table-bordered table-hover table-sm table-striped w-100" id="dataMonitor">
						<thead class="thead-themed">
							<th width="25%">Nomor RIPH</th>
							<th width="25%">Perusahaan</th>
							<th class="text-left">Komitmen Wajib Tanam</th>
							<th>Luas Dilaporkan (s.d saat ini)</th>
							<th>Komitmen Wajib Produksi</th>
							<th>Volume Dilaporkan (s.d saat ini)</th>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel" id="panel-title-2">
	<div class="panel-hdr">
		<h2>
			<i class="fal fa-print-search mr-2"></i>
			<span class="text-uppercase">Monitoring Verifikasi</span>
		</h2>
		<div class="panel-toolbar">
			<a href="javascript:void(0);" class="mr-1 btn btn-success btn-xs btn-icon waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Tampil/Sembunyi Panel">
				<i class="fal fa-minus"></i>
			</a>
			<a href="javascript:void(0);" class="btn btn-warning btn-xs btn-icon waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Layar Penuh">
				<i class="fal fa-expand"></i>
			</a>
		</div>
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<div class="row">
				<div class="col-md-3">
					<div class="panel rounded overflow-hidden position-relative text-white mb-g" data-toggle="tooltip" title data-original-title="Jumlah antrian pengajuan verifikasi.">
						<div class="card-body bg-danger-300">
							<div class="">
								<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
									<span id="ajucount"></span>
									<small class="m-0 l-h-n">Pengajuan</small>
								</h3>
							</div>
						</div>
						<i class="fal fa-landmark position-absolute pos-right pos-bottom opacity-25 mb-n1 mr-n1" style="font-size:4rem"></i>
					</div>
				</div>
				<div class="col-md-3">
					<div class="panel rounded overflow-hidden position-relative text-white mb-g" data-toggle="tooltip" title data-original-title="Jumlah antrian dalam proses verifikasi.">
						<div class="card-body bg-warning-400">
							<div class="">
								<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
									<span id="proccesscount"></span>
									<small class="m-0 l-h-n">Diproses</small>
								</h3>
							</div>
						</div>
						<i class="fal fa-balance-scale position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
					</div>
				</div>
				<div class="col-md-3">
					<div class="panel rounded overflow-hidden position-relative text-white mb-g" data-toggle="tooltip" title data-original-title="Jumlah pengajuan yang telah diverifikasi dengan status SELESAI dan jumlah pengajuan yang dikembalikan kepada pelaku usaha untuk dilakukan PERBAIKAN laporan.">
						<div class="card-body bg-info-300">
							<div class="">
								<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
									<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
									<span id="verifiedcount"></span>
									<small class="m-0 l-h-n">Selesai (Perbaikan <span id="failCount"></span>)</small>
								</h3>
							</div>
						</div>
						<i class="fal fa-seedling position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
					</div>
				</div>
				<div class="col-md-3">
					<div class="panel rounded overflow-hidden position-relative text-white mb-g"  data-toggle="tooltip" title data-original-title="Jumlah RIPH yang dinyatakan LUNAS dan Terbit SKL pada periode ini.">
						<div class="card-body bg-success-500">
							<div class="">
								<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white">
									<span id="lunascount"></span>
									<small class="m-0 l-h-n">Lunas</small>
								</h3>
							</div>
						</div>
						<i class="fal fa-dolly position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="panel" id="panel-2" hidden>
						<div class="panel-hdr">
							<h2>
								<i class="subheader-icon fal fa-ballot-check mr-1"></i>Pengajuan dan Progress <span class="fw-300"><i>Verifikasi</i></span>
							</h2>
							<div class="panel-toolbar">
								@include('layouts.globaltoolbar')
							</div>
						</div>
						<div class="panel-container show">
							<div class="panel-content">
								{{-- <div class="row d-flex">
									<div class="col-md-3">
										<div class="shadow-1 p-2 bg-primary-100 rounded overflow-hidden position-relative text-white mb-2">
											<div data-toggle="tooltip" title data-original-title="Jumlah Pengajuan Verifikasi Komitmen Wajib Tanam-Produksi">
												<div class="d-flex">
													<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="ajucount">{{$ajucount ? $ajucount : 0}}</h5>
													<span>RIPH</span>
												</div>
												<span class="small">Pengajuan</span>
											</div>
											<i class="fal fa-download position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
										</div>
									</div>
									<div class="col-md-3">
										<div class="shadow-1 p-2 bg-primary-200 rounded overflow-hidden position-relative text-white mb-2">
											<div data-toggle="tooltip" title data-original-title="Jumlah RIPH yang sedang diverifikasi">
												<div class="d-flex">
													<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="proccesscount">{{$proccesscount ? $proccesscount : 0}}</h5>
													<span>RIPH</span>
												</div>
												<span class="small">Dalam Proses</span>
											</div>
											<i class="fal fa-hourglass position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
										</div>
									</div>
									<div class="col-md-3">
										<div class="shadow-1 p-2 bg-primary-300 rounded overflow-hidden position-relative text-white mb-2">
											<div data-toggle="tooltip" title data-original-title="Jumlah RIPH yang telah diverifikasi">
												<div class="d-flex">
													<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="verifiedcount">{{$verifiedcount ? $verifiedcount : 0}}</h5>
													<span>RIPH</span>
												</div>
												<span class="small">Terverifikasi</span>
											</div>
											<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
										</div>
									</div>
									<div class="col-md-3">
										<div class="shadow-1 p-2 bg-primary-500 rounded overflow-hidden position-relative text-white mb-2">
											<div data-toggle="tooltip" title data-original-title="Jumlah RIPH Lunas Komitmen Wajib Tanam-Produksi">
												<div class="d-flex justify-content-between">
													<div class="d-flex">
														<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="lunascount">{{$lunascount ? $lunascount : 0}}</h5>
														<span>RIPH</span>
													</div>
													<span class="fw-700">LUNAS</span>
												</div>

												<div class="d-flex">
													<div class="d-flex">
														<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="lunasLuas">0</h5>
														<span>ha  |  </span>
													</div>
													<div class="d-flex ml-1">
														<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="lunasVolume">0</h5>
														<span>ton</span>
													</div>
												</div>
											</div>
											<i class="fal fa-award position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
										</div>
									</div>
								</div><hr> --}}
								<table class="table table-bordered table-hover table-sm w-100" id="verifprogress">
									<thead>
										<th>Nama Perusahaan</th>
										{{-- <th>Nomor Pengajuan</th> --}}
										<th>Nomor RIPH</th>
										<th>Tanam</th>
										<th>Produksi</th>
										<th>SKL</th>
										<th>Lunas</th>
									</thead>
									<tbody>
										@foreach ($allPengajuan as $pengajuan)
											<tr>
												@php
													$statusAjutanam = $pengajuan->ajutanam->status ?? null;
													$statusAjuproduksi = $pengajuan->ajuproduksi->status ?? null;
													$statusAjuskl = $pengajuan->ajuskl->status ?? null;
													$statusCompleted = $pengajuan->completed->url ?? null;
												@endphp
												<td>{{$pengajuan->datauser->company_name}}</td>
												{{-- <td>{{$pengajuan->no_pengajuan}}</td> --}}
												<td>{{$pengajuan->no_ijin}}</td>
												<td class="text-center">
													@if ($statusAjutanam === '4')
														<span class="btn btn-xs btn-icon btn-success"><i class="fa fa-check-circle"></i></span>
													@elseif ($statusAjutanam === '5')
														<span class="btn btn-xs btn-icon btn-danger"><i class="fa fa-ban"></i></span>
													@endif
												</td>
												<td class="text-center">
													@if ($statusAjuproduksi === '4')
														<span class="btn btn-xs btn-icon btn-success"><i class="fa fa-check-circle"></i></span>
													@elseif ($statusAjuproduksi === '5')
														<span class="btn btn-xs btn-icon btn-danger"><i class="fa fa-ban"></i></span>
													@endif
												</td>
												<td class="text-center">
													@if ($statusAjuskl === '4')
														<span class="btn btn-xs btn-icon btn-success"><i class="fa fa-check-circle"></i></span>
													@elseif ($statusAjuskl === '5')
														<span class="btn btn-xs btn-icon btn-danger"><i class="fa fa-ban"></i></span>
													@endif
												</td>
												<td class="text-center">
													@if ($statusCompleted)
														<span class="btn btn-xs btn-icon btn-success"><i class="fa fa-award"></i></span>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								<hr>
								<span class="help-block mt-2">
									<label for="" class="form-label">Keterangan:</label>
									<div class="row d-flex align-items-top">
										{{-- <div class="col-md-4 col-sm-6">
											<ul>
												<li>Tahap 1: Verifikasi Realisasi Tanam</li>
												<li>Tahap 2: Verifikasi Realisasi Produksi</li>
												<li>Tahap 3: Pengajuan Ket. Lunas </li>
												<li>Lunas: Penerbitan Surat Keterangan Lunas</li>
											</ul>
										</div> --}}
										<div class="col-md-8 col-sm-6">
											<ul>
												<li class="mb-1">
													<span class="btn btn-icon btn-xs btn-success mr-1">
														<i class="fa fa-check-circle"></i>
													</span> : Pemeriksaan SELESAI dan dinyatakan SESUAI.
												</li>
												<li class="mb-1">
													<span class="btn btn-icon btn-xs btn-danger mr-1">
														<i class="fa fa-ban"></i>
													</span> : Pemeriksaan SELESAI, data dinyatakan <span class="text-danger">PERBAIKAN</span>.
												</li>
												<li>
													<span class="btn btn-icon btn-xs btn-success mr-1">
														<i class="fa fa-award"></i>
													</span> : Komitmen dinyatakan <span class="fw-700">LUNAS dan SKL diterbitkan.</span></span>.
												</li>
											</ul>
										</div>
									</div>
								</span>
							</div>
						</div>
					</div>

					<div class="panel" id="panel-2">
						<div class="panel-hdr">
							<h2>
								<i class="subheader-icon fal fa-ballot-check mr-1"></i>Pengajuan dan Progress<span class="fw-300"><i> Verifikasi</i></span>
							</h2>
							<div class="panel-toolbar">
								{{-- @include('layouts.globaltoolbar') --}}
							</div>
						</div>
						<div class="panel-container show">
							<div class="panel-content">
								<table class="table table-bordered table-hover table-sm w-100" id="tabelVerif">
									<thead class="thead-themed">
										<th width="25%">Nama Perusahaan</th>
										<th width="20%">Nomor RIPH</th>
										<th width="20%">Jenis</th>
										<th width="10%">Diajukan</th>
										<th width="10%">Diperbarui</th>
										<th width="15%">Progress</th>
									</thead>
									<tbody>
									</tbody>
								</table>
								<table hidden class="table table-bordered table-hover table-sm w-100" id="verifprogress">
									<thead>
										<th>Nama Perusahaan</th>
										<th>Nomor Pengajuan</th>
										<th>Nomor RIPH</th>
										<th>Pengajuan</th>
										<th>Tahap 1</th>
										<th>Tahap 2</th>
										<th>Tahap 3</th>
									</thead>
									<tbody>

									</tbody>
								</table>
								<span hidden class="help-block mt-2">
									<label for="" class="form-label">Keterangan:</label>
									<div class="row d-flex align-items-top">
										<div class="col-md-4 col-sm-6">
											<ul>
												<li>Tahap 1: Pemeriksaan Data</li>
												<li>Tahap 2: Pemeriksaan Lapangan</li>
												<li>Tahap 3: Rekomendasi dan Penerbitan SKL</li>
											</ul>
										</div>
										<div class="col-md-5 col-sm-6">
											<ul>
												<li>
													<span class="btn btn-icon btn-xs btn-success">
														<i class="fal fa-check-circle mr-1"></i>
													</span> : Pemeriksaan selesai dan dinyatakan sesuai.
												</li>
												<li>
													<span class="btn btn-icon btn-xs btn-danger">
														<i class="fal fa-ban mr-1"></i>
													</span> : Pemeriksaan selesai, data dinyatakan <span class="text-danger">TIDAK SESUAI</span>.
												</li>
												<li>
													<span class="btn btn-icon btn-xs btn-info">
														<i class="fal fa-file-signature mr-1"></i>
													</span> : Rekomendasi penerbitan SKL.</span>.
												</li>
												<li>
													<span class="btn btn-icon btn-xs btn-success">
														<i class="fal fa-award mr-1"></i>
													</span> : Komitmen dinyatakan <span class="fw-700">LUNAS dan SKL diterbitkan.</span></span>.
												</li>
											</ul>
										</div>
									</div>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="collapse show mb-3" id="panelB">
</div>

	<!-- End Page Content -->

@endcan
@endsection
@section('scripts')
@parent
	<script>
		$(document).ready(function() {
			//initialize datatable verifprogress


			// Create the "Status" select element and add the options
			// var selectStatus = $('<select>')
			// 	.attr('id', 'selectverifprogressStatus')
			// 	.addClass('custom-select custom-select-sm col-3 mr-2')
			// 	.on('change', function() {
			// 	var status = $(this).val();
			// 	table.column(6).search(status).draw();
			// 	});

			// $('<option>').val('').text('Semua Status').appendTo(selectStatus);
			// $('<option>').val('1').text('Sudah Terbit').appendTo(selectStatus);
			// $('<option>').val('2').text('Belum Terbit').appendTo(selectStatus);

			// Add the select elements before the first datatable button in the second table
			// $('#verifprogress_wrapper .dt-buttons').before(selectStatus);
		});
	</script>
	<script>
		$(document).ready(function() {
			var currentDate = new Date();
			var currentYear = currentDate.getFullYear(); // Mendapatkan tahun berjalan

			var url = '{{ route("admin.monitoringDataByYear", ":periodetahun") }}';
			url = url.replace(':periodetahun', currentYear); // Mengganti :periodetahun dengan tahun berjalan

			updateTableData1(url);
			updateTableData(url);

			$('.yearpicker').datepicker({
				format: 'yyyy',
				viewMode: 'years',
				minViewMode: 'years',
				autoclose: true
			});
			$('#verifprogress').dataTable({
				responsive: true,
				lengthChange: false,
				dom:
				"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'<'select'>>>" +
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				buttons: [
					{
						extend: 'pdfHtml5',
						text: '<i class="fa fa-file-pdf"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Generate PDF',
						className: 'btn-outline-danger btn-sm btn-icon mr-1'
					},
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-sm btn-icon mr-1'
					},
					{
						extend: 'csvHtml5',
						text: '<i class="fal fa-file-csv"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Generate CSV',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					},
					{
						extend: 'copyHtml5',
						text: '<i class="fa fa-copy"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Copy to clipboard',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					}
				]
			});

			var tableMonitor = $('#dataMonitor').DataTable({
				responsive: true,
				lengthChange: true,
				dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				buttons: [
					{
						extend: 'pdfHtml5',
						text: '<i class="fa fa-file-pdf"></i>',
						title: 'Monitoring Realisasi',
						titleAttr: 'Generate PDF',
						className: 'btn-outline-danger btn-xs btn-icon ml-5 mr-1'
					},
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						title: 'Monitoring Realisasi',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-xs btn-icon mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						title: 'Monitoring Realisasi',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					}
				],
				columnDefs: [
					{ className: 'text-right', targets: [2,3,4,5] },
				// 	{ className: 'text-center', targets: [1] },
				]
			});

			var verifTable = $('#tabelVerif').dataTable({
				responsive: true,
				lengthChange: false,
				ordering: true,
				dom:
					"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'<'select'>>>"+
					"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>"+
					"<'row'<'col-sm-12'tr>>"+
					"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				buttons: [
					{
						extend: 'pdfHtml5',
						text: '<i class="fa fa-file-pdf"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Generate PDF',
						className: 'btn-outline-danger btn-xs btn-icon mr-1'
					},
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-xs btn-icon mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						title: 'Monitoring Verifikasi',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					}
				]
			});

			$('#periodetahun').on('change', function() {
				var periodetahun = $(this).val();
				var url = '{{ route("admin.monitoringDataByYear", ":periodetahun") }}';
				url = url.replace(':periodetahun', periodetahun);
				updateTableData1(url);
				updateTableData(url);

				$.get(url, function (data) {
					$('#jumlah_importir').text(data.jumlah_importir);
					$('#v_pengajuan_import').text(formatNumber(data.v_pengajuan_import));
					$('#v_beban_tanam').text(formatNumber(data.v_beban_tanam));
					$('#v_beban_produksi').text(formatNumber(data.v_beban_produksi));
					$('#company').text(data.company);
					$('#volume_import').text(formatdecimals(data.volume_import));
					$('#total_luastanam').text(formatdecimals(data.total_luastanam));
					$('#total_volume').text(formatdecimals(data.total_volume));
					$('#prosenTanam').text(formatdecimals(data.prosenTanam));
					$('#prosenProduksi').text(formatdecimals(data.prosenProduksi));
					$('#ajucount').text(data.ajucount);
					$('#proccesscount').text(data.proccesscount);
					$('#verifiedcount').text(data.verifiedcount);
					$('#recomendationcount').text(data.recomendationcount);
					$('#lunascount').text(data.lunascount);
					$('#lunasLuas').text(formatdecimals(data.lunasLuas));
					$('#lunasVolume').text(formatdecimals(data.lunasVolume));

					var prosentanam = (data.prosenTanam);
					$('#naschartTanam').attr('data-percent', prosentanam);
					$('#naschartTanam').attr('data-original-title', prosentanam  + '% dari kewajiban');
					var $chartTanam = $('#naschartTanam');
					$chartTanam.data('easyPieChart').update(prosentanam);

					var prosenproduksi = (data.prosenProduksi);
					$('#naschartProduksi').attr('data-percent', prosenproduksi);
					$('#naschartProduksi').attr('data-original-title', prosenproduksi  + '% dari total kewajiban');
					var $chartProduksi = $('#naschartProduksi');
					$chartProduksi.data('easyPieChart').update(prosenproduksi);

					// // Build table for pengajuan
					var tableBody = $("#verifprogress tbody");
					tableBody.empty(); // Clear previous table data

					$.each(data.verifikasis, function (index, verifikasi) {
						console.log('Verifikasi:', verifikasi);
						var row = $("<tr></tr>");
						var namaPerusahaan = $("<td></td>").text(verifikasi.commitment.datauser.company_name);
						var nomorPengajuan = $("<td></td>").text(verifikasi.no_pengajuan);
						var nomorRIPH = $("<td></td>").text(verifikasi.no_ijin);

						var ajuCell = $('<td class="text-center"></td>').html(function() {
							if (verifikasi.status) {
								return '<span class="btn btn-xs btn-icon btn-info"><i class="fa fa-check-circle"></i></span>';
							}
						});

						var dataCell = $('<td class="text-center"></td>').html(function() {
							if (verifikasi.onlinestatus === '2') {
								return '<span class="btn btn-xs btn-icon btn-success"><i class="fa fa-check-circle"></i></span>';
							} else if (verifikasi.onlinestatus === '3') {
								return '<span class="btn btn-xs btn-danger"><i class="fa fa-ban"></i></span>';
							}
						});

						var lapanganCell = $('<td class="text-center"></td>').html(function() {
							if (verifikasi.status === '2' && !verifikasi.onfarmstatus) {
								return '<span class="btn btn-xs btn-icon btn-warning"><i class="fa fa-exclamation-circle"></i></span>';
							} else if (verifikasi.onfarmstatus === '4') {
								return '<span class="btn btn-xs btn-icon btn-success"><i class="fa fa-check-circle"></i></span>';
							} else if (verifikasi.onfarmstatus === '5') {
								return '<span class="btn btn-xs btn-icon btn-btn"><i class="fa fa-ban"></i></span>';
							}
						});

						var lunasCell = $('<td class="text-center"></td>').html(function() {
							if (verifikasi.status === '6') {
								return '<span class="btn btn-xs btn-icon btn-primary"><i class="fa fa-file-signature"></i></span>';
							} else if (verifikasi.status === '7') {
								return '<span class="btn btn-xs btn-icon btn-success"><i class="fa fa-award"></i></span> <span hidden>7</span>';
							}
						});

						row.append(namaPerusahaan, nomorPengajuan, nomorRIPH, ajuCell, dataCell, lapanganCell, lunasCell);
						tableBody.append(row);
					});
				});

				function formatNumber(number) {
					return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
				}

				function formatdecimals(number) {
					var parts = number.toFixed(2).toString().split(".");
					var formattedNumber = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
					if (parts.length > 1) {
						formattedNumber += "," + parts[1];
					} else {
						formattedNumber += ",00"; // Add two decimal places if there are none
					}
					return formattedNumber;
				}
			});


			function updateTableData1(url) {
				$.ajax({
					url: url, // Gunakan URL yang diperbarui
					type: 'GET',
					dataType: 'json',
					success: function(response) {
						tableMonitor.clear().draw();
						if (response.dataRealisasi.length > 0) {
							$.each(response.dataRealisasi, function(index, realisasi) { // Update response handling
								var company = realisasi.company;
								var noIjin = realisasi.no_ijin;
								var wT = realisasi.wajib_tanam + ' ha';
								var RwT = realisasi.realisasi_tanam + ' ha';
								var wP = realisasi.wajib_produksi + ' ton';
								var RwP = realisasi.realisasi_produksi + ' ton';

								tableMonitor.row.add([noIjin, company, wT,RwT, wP, RwP]).draw(false);
							});
						}
						tableMonitor.draw(); // Draw the table after adding the rows
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}

			function updateTableData(url) {
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function (data) {
						// Update the table and other elements with data received from the server
						$('#ajucount').text(data.ajucount);
						$('#proccesscount').text(data.proccesscount);
						$('#verifiedcount').text(data.verifiedcount);
						$('#failCount').text(data.failCount);
						$('#lunascount').text(data.lunascount);
						verifTable.fnClearTable();
						// Populate the table with data tanam from the server
						$.each(data.progresVT, function (index, verifikasi) {
							var company = verifikasi.commitment.datauser.company_name;
							var jenis = verifikasi.jenis;
							var no_ijin = verifikasi.no_ijin;
							var created = verifikasi.created_at;
							var updated = verifikasi.updated_at;
							var progress = verifikasi.TProgress;
							var progressHTML = ''; // Ini adalah variabel untuk menyimpan HTML yang akan dihasilkan

							if (progress === '1') {
								progressHTML = '<span class="badge btn-xs btn-warning"><i class="fa fa-exclamation-circle"></i> Baru</span>';
							} else if (progress === '2') {
								progressHTML = '<span class="badge btn-xs btn-primary"><i class="fal fa-hourglass"></i> Berkas</span>';
							} else if (progress === '3') {
								progressHTML = '<span class="badge btn-xs btn-info"><i class="fal fa-hourglass"></i> PKS</span>';
							} else if (progress === '4') {
								progressHTML = '<span class="badge btn-xs btn-success"><i class="fa fa-check"></i> Verifikasi Selesai</span>';
							} else if (progress === '5') {
								progressHTML = '<span class="badge btn-xs btn-danger"><i class="fa fa-ban"></i> Perbaikan</span>';
							}
							verifTable.fnAddData([company, no_ijin, jenis,created, updated, progressHTML]);
						});

						// Populate the table with data produksi from the server
						$.each(data.progresVP, function (index, verifikasi) {
							var company = verifikasi.commitment.datauser.company_name;
							var jenis = verifikasi.jenis;
							var no_ijin = verifikasi.no_ijin;
							var created = verifikasi.created_at;
							var updated = verifikasi.updated_at;
							var progress = verifikasi.PProgress;
							var progressHTML = ''; // Ini adalah variabel untuk menyimpan HTML yang akan dihasilkan

							if (progress === '1') {
								progressHTML = '<span class="badge btn-xs btn-warning"><i class="fa fa-exclamation-circle"></i> Baru</span>';
							} else if (progress === '2') {
								progressHTML = '<span class="badge btn-xs btn-primary"><i class="fal fa-hourglass"></i> Berkas</span>';
							} else if (progress === '3') {
								progressHTML = '<span class="badge btn-xs btn-info"><i class="fal fa-hourglass"></i> PKS</span>';
							} else if (progress === '4') {
								progressHTML = '<span class="badge btn-xs btn-success"><i class="fa fa-check"></i> Verifikasi Selesai</span>';
							} else if (progress === '5') {
								progressHTML = '<span class="badge btn-xs btn-danger"><i class="fa fa-ban"></i> Perbaikan</span>';
							}
							verifTable.fnAddData([company, no_ijin, jenis,created, updated, progressHTML]);
						});

						// Populate the table with data skl from the server
						$.each(data.progresVSkl, function (index, verifikasi) {
							var company = verifikasi.commitment.datauser.company_name;
							var jenis = verifikasi.jenis;
							var no_ijin = verifikasi.no_ijin;
							var created = verifikasi.created_at;
							var updated = verifikasi.updated_at;
							var progress = verifikasi.SklProgress;
							var progressHTML = ''; // Ini adalah variabel untuk menyimpan HTML yang akan dihasilkan

							if (progress === '1') {
								progressHTML = '<span class="badge btn-xs btn-warning"><i class="fa fa-exclamation-circle"></i> Baru</span>';
							} else if (progress === '2') {
								progressHTML = '<span class="badge btn-xs btn-primary"><i class="fal fa-hourglass"></i> Rekomendasi</span>';
							} else if (progress === '3') {
								progressHTML = '<span class="badge btn-xs btn-info"><i class="fal fa-hourglass"></i> Disetujui</span>';
							} else if (progress === '4') {
								progressHTML = '<span class="badge btn-xs btn-success"><i class="fa fa-award"></i> SKL Terbit</span>';
							} else if (progress === '5') {
								progressHTML = '<span class="badge btn-xs btn-danger"><i class="fa fa-ban"></i> Perbaikan</span>';
							}
							verifTable.fnAddData([company, no_ijin, jenis,created, updated, progressHTML]);
						});
						verifTable.fnDraw();
					},
					error: function (xhr, status, error) {
						console.log("AJAX request error: " + error);
					}
				});
			}
		});
	</script>
@endsection
