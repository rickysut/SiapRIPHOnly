@extends('layouts.admin')
@section ('styles')
<style>
td {
	vertical-align: middle !important;
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
			<div class="col-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content">
							<div class="row">
								<div class="form-group col-md-4">
									<label class="form-label" for="no_pengajuan">Nomor Pengajuan</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="no_pengajuan" name="no_pengajuan"
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
										<input type="text" class="form-control form-control-sm" id="no_ijin" value="{{$verifikasi->no_ijin}}" name="no_ijin" disabled>
									</div>
									<span class="help-block">Nomor Ijin RIPH.</span>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-4">
									<label class="form-label" for="tgl_ijin">Tanggal Ijin RIPH</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-calendar-day"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="tgl_ijin" name="tgl_ijin" value="{{$verifikasi->commitment->tgl_ijin}}" disabled>
									</div>
									<span class="help-block">Tanggal mulai berlaku RIPH ini.</span>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label" for="tgl_akhir">Tanggal Akhir RIPH</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-calendar-day"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="tgl_akhir" name="tgl_akhir" value="{{$verifikasi->commitment->tgl_akhir}}" disabled>
									</div>
									<span class="help-block">Tanggal akhir/masa berlaku RIPH ini.</span>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label" for="created_at">Tanggal Pengajuan Verifikasi</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-calendar-day"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="created_at" name="created_at"
											value="{{$verifikasi->created_at}}" disabled>
									</div>
									<span class="help-block">Tanggal Pelaku Usaha mengajukan verifikasi.</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#panel-2" role="tab" aria-selected="true">Ringkasan Data Realisasi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-3" role="tab" aria-selected="true">Pemeriksaan Berkas</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-7" role="tab" aria-selected="true">Monitoring Timeline Realisasi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-4" role="tab" aria-selected="true">Perjanjian Kemitraan</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-5" role="tab" aria-selected="true">Data Lokasi Tanam</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-danger" data-toggle="tab" href="#panel-6" role="tab" aria-selected="true">Hasil Pemeriksaan</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade active show" id="panel-2" role="tabpanel" aria-labelledby="panel-2">
						<div id="panel-2" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah data ringkasan realisasi komitmen wajib tanam-produksi yang telah dilaporkan oleh Pelaku Usaha.</small>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table class="table table-striped table-bordered w-100" id="dataRiph">
										<thead>
											<th>Data</th>
											<th>Kewajiban</th>
											<th>Realisasi</th>
											<th>Status</th>
										</thead>
										<tbody>
											<tr>
												<td>Tanam</td>
												<td class="text-right">
													{{ number_format($commitment->luas_wajib_tanam, 2, '.', ',') }} ha
												</td>
												<td class="text-right">
													{{number_format($total_luastanam, 2,'.',',')}} ha
												</td>
												<td>
													@if($total_luastanam < $commitment->luas_wajib_tanam)
														<span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>TIDAK TERPENUHI</span>
													@else
													<span class="text-success"><i class="fas fa-check mr-1"></i>TERPENUHI</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>Produksi</td>
												<td class="text-right">
													{{ number_format($commitment->volume_produksi, 2, '.', ',') }} ton
												</td>
												<td class="text-right">
													{{number_format($total_volume, 2,'.',',')}} ton
												</td>
												<td>
													@if($total_volume < $commitment->volume_produksi)
														<span class="text-danger fw-500"><i class="fas fa-times-circle mr-1"></i>TIDAK TERPENUHI</span>
													@else
													<span class="text-success"><i class="fas fa-check mr-1"></i>TERPENUHI</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>Kelompok Tani dan PKS</td>
												<td class="text-right">
													{{$countPoktan}} Poktan
												</td>
												<td class="text-right">
													{{$countPks}} PKS
												</td>
												<td>
													{{-- @if($countPks < $countPoktan)
														<i class="fas fa-exclamation-circle mr-1 text-warning"></i><span class="text-warning fw-500">TIDAK SESUAI</span>
													@else
														<i class="fas fa-check mr-1 text-success"></i><span class="text-success fw-500">SESUAI</span>
													@endif --}}
												</td>
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
								action="{{route('verification.produksi.checkBerkas', $verifikasi->id)}}">
								@csrf
								<div class="panel-container">
									<div class="panel-tag fade show">
										<div class="d-flex align-items-center">
											<i class="fal fa-info-circle mr-1"></i>
											<div class="flex-1">
												<small>Anda dapat melakukan pemeriksaan terhadap berkas-berkas di bawah ini.</small><br>
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
												<th class="text-uppercase text-muted">Sifat</th>
												<th class="text-uppercase text-muted">Status</th>
											</thead>
											<tbody>
												<tr>
													<td>
														@if ($userDocs->spvp)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $userDocs->spvp ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvp) : '' }}">
																Surat Pengajuan Verifikasi Produksi
															</a>
														@else
															<span>Surat Pengajuan Verifikasi Produksi</span>
														@endif
													</td>
													<td class="text-center">
														WAJIB
														@if (empty($userDocs->spvpcheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td>
														@if ($userDocs->spvp)
															<select class="form-control form-control-sm {{ $userDocs->spvpcheck ? '' : 'border-danger' }}" name="spvpcheck" id="spvpcheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->spvpcheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
																<option value="perbaiki" {{ $userDocs->spvpcheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
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
														WAJIB
														@if (empty($userDocs->sptjmtanamcheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td>
														@if ($userDocs->sptjmtanam)
															<select class="form-control form-control-sm {{ $userDocs->sptjmtanamcheck ? '' : 'border-danger' }}" name="sptjmtanamcheck" id="sptjmtanamcheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->sptjmtanamcheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
																<option value="perbaiki" {{ $userDocs->sptjmtanamcheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
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
														WAJIB
														@if (empty($userDocs->sptjmproduksicheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td>
														@if ($userDocs->sptjmproduksi)
															<select class="form-control form-control-sm {{ $userDocs->sptjmproduksicheck ? '' : 'border-danger' }}" name="sptjmproduksicheck" id="sptjmproduksicheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->sptjmproduksicheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
																<option value="perbaiki" {{ $userDocs->sptjmproduksicheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
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
														WAJIB
														@if (empty($userDocs->rtacheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td>
														@if ($userDocs->rta)
															<select class="form-control form-control-sm {{ $userDocs->rtacheck ? '' : 'border-danger' }}" name="rtacheck" id="rtacheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->rtacheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
																<option value="perbaiki" {{ $userDocs->rtacheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada/Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
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
														WAJIB
														@if (empty($userDocs->rpocheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td>
														@if ($userDocs->rpo)
															<select class="form-control form-control-sm {{ $userDocs->rpocheck ? '' : 'border-danger' }}" name="rpocheck" id="rpocheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->rpocheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
																<option value="perbaiki" {{ $userDocs->rpocheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada/Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
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
														Pendukung
														@if (empty($userDocs->sphtanamcheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphtanam)
														<select class="form-control form-control-sm {{ $userDocs->sphtanamcheck ? '' : 'border-danger' }}" name="sphtanamcheck" id="sphtanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphtanamcheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->sphtanamcheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
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
														Pendukung
														@if (empty($userDocs->sphproduksicheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphproduksi)
														<select class="form-control form-control-sm {{ $userDocs->sphproduksicheck ? '' : 'border-danger' }}" name="sphproduksicheck" id="sphproduksicheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphproduksicheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->sphproduksicheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
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
														Pendukung
														@if (empty($userDocs->logbookproduksicheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->logbookproduksi)
														<select class="form-control form-control-sm {{ $userDocs->logbookproduksicheck ? '' : 'border-danger' }}" name="logbookproduksicheck" id="logbookproduksicheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->logbookproduksicheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->logbookproduksicheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
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
														Wajib
														@if (empty($userDocs->formLacheck))
															{{-- <sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> --}}
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->formLa)
														<select class="form-control form-control-sm {{ $userDocs->formLacheck ? '' : 'border-danger' }}" name="formLacheck" id="formLacheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->formLacheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->formLacheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="card-footer d-flex align-items-center justify-content-between">
									{{-- <div>
										<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> :Belum dilakukan pemeriksaan.
									</div> --}}
									<div class="ml-auto">
										<button type="submit" class="btn btn-primary btn-sm">
											<i class="fal fa-save mr-1"></i>Simpan Hasil Pemeriksaan
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-7" role="tabpanel" aria-labelledby="panel-7">
						<div id="panel-7" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah tabel untuk memeriksa kesesuaian tanggal. Text tanggal <span class="fw-500 text-danger">berwarna merah </span>memiliki arti Tanggal dimaksud berada di luar rentang yang seharusnya.</small>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table id="tableTanam" class="table table-bordered table-sm table-hover" style="width:100%">
										<thead>
											<tr>
												<th>Kelompok</th>
												<th>Petani</th>
												<th>Lokasi</th>
												<th>Awal PKS</th>
												<th>Akhir PKS</th>
												<th>Awal Tanam</th>
												<th>Akhir Tanam</th>
												<th>Awal Panen</th>
												<th>Akhir Panen</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
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
										</tbody>
									</table>
								</div>
							</div>
							<form action="{{route('verification.produksi.checkPksSelesai', $verifikasi->id)}}" method="post">
								@csrf
								<div class="card-footer d-flex alignt-items-center justify-content-between">
									<div>
									</div>
									<div>
										<button type="submit" class="btn btn-primary btn-sm">
											<i class="fal fa-save mr-1"></i>Simpan Hasil Pemeriksaan
										</button>
									</div>
								</div>
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
									<table class="table table-striped table-bordered table-sm w-100" id="tableLokasi">
										<thead>
											<th class="text-center">Kelompoktani</th>
											<th class="text-center">Jumlah Lokasi</th>
											<th class="text-center">Nama Petani</th>
											<th class="text-center">Luas</th>
											<th class="text-center">Tindakan</th>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-6" role="tabpanel" aria-labelledby="panel-6">
						<div class="panel" id="panel-6">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Setelah selesai memeriksa secara menyeluruh, Anda harus menetapkan hasil pemeriksaan yang dilakukan pada bagian ini.</small>
										</div>
									</div>
								</div>
								<form action="{{route('verification.produksi.storeCheck', $verifikasi->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									{{-- @method('PUT') --}}
									<div class="panel-content">
										<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
										<input type="text" name="no_pengajuan" value="{{$verifikasi->no_pengajuan}}" hidden>
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
										<div class="form-group row">
											<label class="col-md-3 col-lg-2 col-form-label">Hasil Pemeriksaan<sup class="text-danger"> *</sup></label>
											<div class="col-md-9 col-lg-10">
												<select name="status" id="status" class="form-control custom-select" onchange="handleStatusChange()"  required>
													<option value="" hidden>-- pilih status --</option>
													<option value="4" {{ old('status', $verifikasi ? $verifikasi->status : '') == '4' ? 'selected' : '' }}>Sesuai</option>
													<option value="5" {{ old('status', $verifikasi ? $verifikasi->status : '') == '5' ? 'selected' : '' }}>Perbaikan Data</option>
												</select>
												<small id="helpId" class="text-muted">Pilih hasil pemeriksaan</small>
											</div>
										</div>
										<div class="form-group row" id="ndhprpContainer" hidden>
											<label class="col-md-3 col-lg-2 col-form-label">Nota Dinas<sup class="text-danger"> *</sup></label>
											<div class="col-md-9 col-lg-10">
												<div class="custom-file input-group">
													<input type="file" accept=".pdf" class="custom-file-input" name="ndhprp" id="ndhprp" value="{{ old('ndhprp', optional($verifikasi)->ndhprp) }}">
													<label class="custom-file-label" for="ndhprp">{{ old('ndhprp', $verifikasi ? $verifikasi->ndhprp : 'Pilih berkas') }}</label>
												</div>
												@if ($verifikasi->ndhprp)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->ndhprp) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Nota Dinas.
													</a>
												@else
													<span class="help-block">Nota Dinas Hasil Pemeriksaan Realisasi Produksi. <span class="text-danger">(wajib)</span></span>
												@endif
											</div>
										</div>
										<div class="form-group row" id="baproduksiContainer" hidden>
											<label class="col-md-3 col-lg-2 col-form-label">Berita Acara<sup class="text-danger"> *</sup></label>
											<div class="col-md-9 col-lg-10">
												<div class="custom-file input-group">
													<input type="file" accept=".pdf" class="custom-file-input" name="baproduksi" id="baproduksi" value="{{ old('baproduksi', optional($verifikasi)->baproduksi) }}">
													<label class="custom-file-label" for="baproduksi">{{ old('baproduksi', $verifikasi ? $verifikasi->baproduksi : 'Pilih berkas') }}</label>
												</div>
												@if ($verifikasi->baproduksi)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->baproduksi) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Berita Acara.
													</a>
												@else
													<span class="help-block">Berita Acara Hasil Pemeriksaan Realisasi Produksi. <span class="text-danger">(wajib)</span></span>
												@endif
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-lg-2 col-form-label">Metode Pemeriksaan<sup class="text-danger"> *</sup></label>
											<div class="col-md-9 col-lg-10">
												<select name="metode" id="metode" class="form-control custom-select" required>
													<option value="" hidden>-- pilih metode --</option>
													<option value="Dokumen" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Dokumen' ? 'selected' : '' }}>Dokumen</option>
													<option value="Lapangan" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Lapangan' ? 'selected' : '' }}>Lapangan</option>
													<option value="Wawancara" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Wawancara' ? 'selected' : '' }}>Wawancara</option>
												</select>
												<small id="helpId" class="text-muted">Pilih metode pemeriksaan</small>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-lg-2">Catatan Pemeriksaan <sup class="text-danger"> *</sup></label>
											<div class="col-md-9 col-lg-10">
												<textarea name="note" id="note" rows="3" class="form-control form-control-sm">{{ old('note', $verifikasi ? $verifikasi->note : '') }}</textarea>
											</div>
										</div>
									</div>
									<div class="card-footer">
										<div class="form-group">
											<label class="">Dengan ini kami menyatakan verifikasi produksi telah <span class="text-danger fw-500">SELESAI</span> dilaksanakan.</label>
											<div class="input-group">
												<input type="text" class="form-control form-control-sm" placeholder="ketik username Anda di sini" id="validasi" name="validasi"required>
												<div class="input-group-append">
													<button class="btn btn-danger btn-sm" type="submit" onclick="return validateInput()" id="btnSubmit">
														<i class="fas fa-save text-align-center mr-1"></i>Simpan
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

			var url = '{{ route("verification.lokasitanam", $noIjin) }}';
			var pksCheck = $('#pksCheck').DataTable({
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
						title: 'Monitoring Timeline Realisasi',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						title: 'Monitoring Timeline Realisasi',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					}
				],
			});

			var tableTanam = $('#tableTanam').DataTable({
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
						title: 'Monitoring Timeline Realisasi',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						title: 'Monitoring Timeline Realisasi',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					}
				],
			});

			var tableLokasi = $('#tableLokasi').DataTable({
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
						title: 'Monitoring Timeline Realisasi',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						title: 'Monitoring Timeline Realisasi',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					}
				],
			});

			function updateTablePks(url) {
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function (response) {
						var pksCheck = $('#pksCheck').DataTable();
						pksCheck.clear().draw();
						if (response.daftarPks.length > 0) {
							$.each(response.daftarPks, function(index, pks) { // Update response handling
								var noPks = pks.noPks;
								var kelompok = pks.kelompok;
								var mulaiPks = pks.mulaiPks;
								var akhirPks = pks.akhirPks;
								var tglPks = `
									<span>${mulaiPks !== null ? mulaiPks + ' s.d ' : ''}</span>
									<span>${akhirPks !== null ? akhirPks : ''}</span>
								`;
								var status = pks.status;
								var statusClass = status ? 'btn-success' : 'btn-warning';
								var pksBtn =`
									<a href="${pks.pksRoute}" class="btn btn-xs btn-icon btn-primary" title="Lihat detail">
										<i class="fal fa-search"></i>
									</a>
									`;
								pksCheck.row.add([noPks, kelompok, tglPks, status, pksBtn]).draw(false);
							});
						}
						pksCheck.draw(); // Draw the table after adding the rows
					},
					error: function (xhr, status, error) {
						console.log("AJAX request error: " + error);
					}
				});
			}

			function updateTableTanam(url) {
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function (response) {
						// Hapus data yang ada di tabel sebelum memasukkan yang baru
						var tableTanam = $('#tableTanam').DataTable();
						tableTanam.clear().draw();

						if (response.datarealisasi.length > 0) {
							$.each(response.datarealisasi, function (index, realisasi) {
								var ijinStart = realisasi.mulai_ijin;
								var ijinEnd = realisasi.akhir_ijin;
								var kelompok = realisasi.kelompok;
								var pksAwal = realisasi.mulai_perjanjian;
								var pksAkhir = realisasi.akhir_perjanjian;
								var petani = realisasi.anggota;
								var lokasi = realisasi.lokasi;
								var awalTanam = realisasi.mulai_tanam;
								var akhirTanam = realisasi.akhir_tanam;
								var awalPanen = realisasi.mulai_panen;
								var akhirPanen = realisasi.akhir_panen;

								var pksStart = (pksAwal < ijinStart || pksAwal > ijinEnd) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku">' + pksAwal + '</span>' : pksAwal;
								var pksEnd = (pksAkhir < ijinStart || pksAkhir > ijinEnd) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku">' + pksAkhir + '</span>' : pksAkhir;
								var tanamStart = (awalTanam < ijinStart || awalTanam > ijinEnd || awalTanam < pksAwal || awalTanam > pksAkhir) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku atau tanggal berlaku PKS">' + awalTanam + '</span>' : awalTanam;
								var tanamEnd = (akhirTanam < ijinStart || akhirTanam > ijinEnd || akhirTanam < pksAwal || akhirTanam > pksAkhir) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku atau tanggal berlaku PKS">' + akhirTanam + '</span>' : akhirTanam;

								tableTanam.row.add([kelompok, petani, lokasi, pksStart, pksEnd, tanamStart, tanamEnd, awalPanen, akhirPanen]).draw(false);
							});
						}
					},
					error: function (xhr, status, error) {
						console.log("AJAX request error: " + error);
					}
				});
			}

			function updateTableLokasi(url) {
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function (response) {
						var tableLokasi = $('#tableLokasi').DataTable();
						tableLokasi.clear().draw();
						if (response.lokasis.length > 0) {
							$.each(response.lokasis, function(index, lokasi) { // Update response handling

								var luasTanam = lokasi.luas_tanam;
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
								var LuasTanam = formatter.format(luasTanam) + ' ha';

								var poktan = lokasi.poktan;
								var anggota = lokasi.anggota;
								var jmlTitik = lokasi.jumlahTitik;
								var jmlLokasi = noDecimal.format(jmlTitik) + ' titik';
								var actionBtn = `
									<a href="${lokasi.show}" class="btn btn-xs btn-icon btn-primary" title="Lihat detail">
										<i class="fal fa-search"></i>
									</a>
								`;
								tableLokasi.row.add([poktan, jmlLokasi, anggota,LuasTanam, actionBtn]).draw(false);
							});
						}
						tableLokasi.draw(); // Draw the table after adding the rows
					},
					error: function (xhr, status, error) {
						console.log("AJAX request error: " + error);
					}
				});
			}

			updateTablePks(url);
			updateTableTanam(url);
			updateTableLokasi(url);
		});
	</script>

	<script>
		function handleStatusChange() {
			var status = document.getElementById("status").value;
			var ndhprpInput = document.getElementById("ndhprp");
			var baproduksiInput = document.getElementById("baproduksi");
			var ndhprpContainer = document.getElementById("ndhprpContainer");
			var baproduksiContainer = document.getElementById("baproduksiContainer");

			if (status === "5") { // Jika status adalah 'Perbaikan Data' (5)
				ndhprpInput.disabled = true;
				baproduksiInput.disabled = true;

				ndhprpContainer.hidden = true;
				baproduksiContainer.hidden = true;
			} else if (status === "4") { // Jika status adalah 'Sesuai' (4)
				ndhprpContainer.hidden = false;
				baproduksiContainer.hidden = false;

				ndhprpInput.disabled = false;
				baproduksiInput.disabled = false;
			}
		}
		function validateInput() {
			// get the input value and the current username from the page
			var inputVal = document.getElementById('validasi').value;
			var currentUsername = '{{ Auth::user()->username }}';
			var status = document.getElementById("status").value;
			var ndhprpInput = document.getElementById("ndhprp");
			var baproduksiInput = document.getElementById("baproduksi");

			// check if the input is not empty and matches the current username
			if (inputVal !== '' && inputVal === currentUsername) {
				// Jika status = 4, lakukan validasi tambahan
				if (status === "4") {
					if (ndhprpInput === '' || baproduksiInput === '') {
						alert("Nota Dinas dan Berita Acara harus diunggah jika Anda menetapkan status 'Sesuai'.");
						return false; // Menghentikan pengiriman formulir
					}
				}
				return true; // Lanjutkan pengiriman formulir jika status adalah 'Tidak Sesuai' (5) atau kondisi lainnya
			} else {
				alert('Isi kolom Konfirmasi dengan username Anda!.');
				return false; // prevent form submission
			}
		}
	</script>
@endsection
