@extends('layouts.admin')
@section ('styles')
<style>
td {
	vertical-align: middle !important;
}
table.dataTable tr.dtrg-group.dtrg-level-1 td, table.dataTable tr.dtrg-group.dtrg-level-2 td{
	font-weight: bold !important;
	background: #f0f0f0 !important;
	font-size: 1em !important;
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
									<label class="form-label" for="company_name">Perusahaan</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="company_name" name="company_name"
											value="{{$commitment->datauser->company_name}}" disabled>
									</div>
									<span class="help-block">Nama Perusahaan yang mengajukan verifikasi.</span>
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
						<a class="nav-link" data-toggle="tab" href="#panel-3" role="tab" aria-selected="true">Kelengkapan Berkas</a>
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
									<table class="table table-striped table-bordered table-sm w-100" id="dataRiph">
										<thead class="thead-themed text-muted text-uppercase">
											<th>Data</th>
											<th>Kewajiban</th>
											<th>Realisasi</th>
											<th>Status</th>
										</thead>
										<tbody>
											<tr>
												<td>Tanam</td>
												<td class="text-right">
													{{ number_format($commitment->volume_riph * 0.05/6, 2, '.', ',') }} ha
												</td>
												<td class="text-right">
													{{number_format($total_luastanam, 2,'.',',')}} ha
												</td>
												<td>
													@if($commitment->volume_riph * 0.05/6 >= $total_luastanam)
														<span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>TIDAK TERPENUHI</span>
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
													@if($countPks < $countPoktan)
														<i class="fas fa-exclamation-circle mr-1 text-warning"></i><span class="text-warning fw-500">TIDAK SESUAI</span>
													@else
														<i class="fas fa-check mr-1 text-success"></i><span class="text-success fw-500">SESUAI</span>
													@endif
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
							<form method="post" action="{{route('verification.tanam.checkBerkas', $verifikasi->id)}}">
								@csrf
								<div class="panel-container show">
									<div class="panel-tag fade show">
										<div class="d-flex align-items-center">
											<i class="fal fa-info-circle mr-1"></i>
											<div class="flex-1">
												<small>Berikut ini adalah berkas-berkas kelengkapan yang diunggah oleh Pelaku Usaha.</small>
											</div>
										</div>
									</div>
									<div class="panel-content">
										<table class="table table-striped table-bordered table-sm w-100" id="attchCheck">
											<thead class="thead-themed text-uppercase text-muted">
												<tr>
													<th>Form</th>
													<th>Nama Berkas</th>
													<th>Tindakan</th>
													<th>Hasil Periksa</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Surat Pertanggungjawaban Mutlak</td>
													<td>{{$userDocs->sptjmtanam}}</td>
													<td>
														@if ($userDocs->sptjmtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmtanam) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="sptjmtanamcheck" id="sptjmtanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sptjmtanamcheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->sptjmtanamcheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Surat Pengajuan Verifikasi Tanam</td>
													<td>{{$userDocs->spvt}}</td>
													<td>
														@if ($userDocs->spvt)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvt) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="spvtcheck" id="spvtcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->spvtcheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->spvtcheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Form Realisasi Tanam</td>
													<td>{{$userDocs->rta}}</td>
													<td>
														@if ($userDocs->rta)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="rtacheck" id="rtacheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->rtacheck == 'sesuai' ? 'selected' : '' }}>Ada/Sesuai</option>
															<option value="perbaiki" {{ $userDocs->rtacheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada/Perbaikan</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Form SPH-SBS</td>
													<td>{{$userDocs->sphtanam}}</td>
													<td>
														@if ($userDocs->sphtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="sphtanamcheck" id="sphtanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphtanamcheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->sphtanamcheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Logbook (s.d tanam)</td>
													<td>{{$userDocs->logbooktanam}}</td>
													<td>
														@if ($userDocs->logbooktanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbooktanam) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="logbooktanamcheck" id="logbooktanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->logbooktanamcheck == 'sesuai' ? 'selected' : '' }}>Ada</option>
															<option value="perbaiki" {{ $userDocs->logbooktanamcheck == 'perbaiki' ? 'selected' : '' }}>Tidak Ada</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between align-items-center">
									<div class="help-block col-md-7">
									</div>
									<div class="col-md text-right">
										<button type="submit" class="btn btn-primary btn-sm">
											<i class="fal fa-save"></i> Simpan Hasil Pemeriksaan
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
											<small>Berikut ini adalah data Perjanjian Kerjasama. Anda dapat memeriksa dan menetapkan hasil pemeriksaan.</small>
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

							<form action="{{route('verification.tanam.checkPksSelesai', $verifikasi->id)}}" method="post">
								@csrf
								@method('PUT')
								<div class="card-footer d-flex alignt-items-center justify-content-between">
									<div>
										{{-- <p class="help-block">Dengan menekan tombol <span class="fw-700 text-primary">'Simpan Hasil Pemeriksaan'</span>, seluruh berkas Perjanjian Kerjasama (PKS) yang belum terperiksa akan dinyatakan <span class="fw-700 text-danger">'Sesuai'</span>.</p> --}}
									</div>
									<div>
										<button type="submit" class="btn btn-primary btn-sm">
											<i class="fal fa-save"></i> Simpan Hasil Pemeriksaan
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
										<thead class="thead-themed text-uppercase text-muted">
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
								<form action="{{route('verification.tanam.storeCheck', $verifikasi->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									@method('PUT')
									<div class="panel-content">
										<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
										<input type="text" name="no_pengajuan" value="{{$verifikasi->no_pengajuan}}" hidden>
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>

										<div class="form-group row">
											<label class="col-md-3 col-lg-2 col-form-label">Hasil Pemeriksaan<sup class="text-danger"> *</sup></label>
											<div class="col-md-9 col-lg-10">
												<select name="status" id="status" class="form-control custom-select" onchange="handleStatusChange()" required>
													<option value="" hidden>-- pilih status --</option>
													<option value="4" {{ old('status', $verifikasi ? $verifikasi->status : '') == '4' ? 'selected' : '' }}>Sesuai</option>
													<option value="5" {{ old('status', $verifikasi ? $verifikasi->status : '') == '5' ? 'selected' : '' }}>Perbaikan Data</option>
												</select>
												<small id="helpId" class="text-muted">Pilih hasil pemeriksaan</small>
											</div>
										</div>
										<div class="form-group row" id="ndhprtContainer" hidden>
											<label class="col-md-3 col-lg-2 col-form-label">Nota Dinas<sup class="text-danger"> *</sup></label>
											<div class="col-md-9 col-lg-10">
												<div class="custom-file input-group">
													<input type="file" accept=".pdf" class="custom-file-input" name="ndhprt" id="ndhprt" value="{{ old('ndhprt', $verifikasi ? $verifikasi->ndhprt : '') }}">
													<label class="custom-file-label" for="ndhprt">{{ old('ndhprt', $verifikasi ? $verifikasi->ndhprt : 'pilih berkas') }}</label>
												</div>
												@if ($verifikasi->ndhprt)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->ndhprt) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Nota Dinas.
													</a>
												@else
													<span class="help-block fw-500">Nota Dinas Hasil Pemeriksaan Realisasi Tanam. <span class="text-danger">(wajib)</span>. PDF, max 2Mb.</span>
												@endif
											</div>
										</div>
										<div class="form-group row" id="batanamContainer" hidden>
											<label class="col-md-3 col-lg-2 col-form-label">Berita Acara<sup class="text-danger">*</sup></label>
											<div class="col-md-9 col-lg-10">
												<div class="custom-file input-group">
													<input type="file" accept=".pdf" class="custom-file-input" name="batanam" id="batanam" value="{{ old('batanam', $verifikasi ? $verifikasi->batanam : '') }}">
													<label class="custom-file-label" for="batanam">{{ old('batanam', $verifikasi ? $verifikasi->batanam : 'pilih berkas') }}</label>
												</div>
												@if ($verifikasi->batanam)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->batanam) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Berita Acara.
													</a>
												@else
													<span class="help-block">Berita Acara Pemeriksaan Realisasi Tanam. PDF, max 2Mb.<span class="text-danger"></span></span>
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
											<label>Dengan ini kami menyatakan verifikasi tanam telah <span class="text-danger fw-500">SELESAI</span> dilaksanakan.</label>
											<div class="input-group">
												<input type="text" class="form-control" placeholder="ketik username Anda di sini" id="validasi" name="validasi"required>
												<div class="input-group-append">
													<button class="btn btn-danger" type="submit" onclick="return validateInput()" id="btnSubmit">
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

			// $('#pksCheck').DataTable({
			// 	responsive: true,
			// 	lengthChange: true,
			// 	order: [1, 'asc'],
			// 	dom:
			// 	"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
			// 	"<'row'<'col-sm-12'tr>>" +
			// 	"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			// 	buttons: [
			// 		{
			// 			extend: 'excelHtml5',
			// 			text: '<i class="fa fa-file-excel"></i>',
			// 			title: 'Daftar Perjanjian Kerjasama',
			// 			titleAttr: 'Generate Excel',
			// 			className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
			// 		},
			// 		{
			// 			extend: 'print',
			// 			text: '<i class="fa fa-print"></i>',
			// 			title: 'Daftar Perjanjian Kerjasama',
			// 			titleAttr: 'Print Table',
			// 			className: 'btn-outline-primary btn-xs btn-icon mr-1'
			// 		}
			// 	],
			// 	columnDefs: [

			// 		{ className: 'text-center', targets: [2,3,4] },
			// 	],
			// 	ajax: {
			// 		url: url,
			// 		type: 'GET',
			// 		dataType: 'json',
			// 		dataSrc: '', // Ini adalah opsi untuk mengatur sumber data dalam respons
			// 		success: function (response) {
			// 			var pksCheck = $('#pksCheck').DataTable();
			// 			pksCheck.clear().draw();
			// 			if (response.daftarPks.length > 0) {
			// 				$.each(response.daftarPks, function(index, pks) { // Update response handling
			// 					var noPks = pks.noPks;
			// 					var kelompok = pks.kelompok;
			// 					var mulaiPks = pks.mulaiPks;
			// 					var akhirPks = pks.akhirPks;
			// 					var tglPks = `
			// 						<span>${mulaiPks !== null ? mulaiPks + ' s.d ' : ''}</span>
			// 						<span>${akhirPks !== null ? akhirPks : ''}</span>
			// 					`;
			// 					var status = pks.status;
			// 					var statusClass = status ? 'btn-success' : 'btn-warning';
			// 					var pksBtn =`
			// 						<a href="${pks.pksRoute}" class="btn btn-xs btn-icon btn-primary" title="Lihat detail">
			// 							<i class="fal fa-search"></i>
			// 						</a>
			// 						`;
			// 					pksCheck.row.add([noPks, kelompok, tglPks, status, pksBtn]).draw(false);
			// 				});
			// 			}
			// 			pksCheck.draw(); // Draw the table after adding the rows
			// 		},
			// 		error: function (xhr, status, error) {
			// 			// Handle error jika diperlukan
			// 			console.error(xhr);

			// 		}
			// 	},
			// });

			// $('#tableTanam').DataTable({
			// 	responsive: true,
			// 	lengthChange: true,
			// 	dom:
			// 	"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
			// 	"<'row'<'col-sm-12'tr>>" +
			// 	"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			// 	buttons: [
			// 		{
			// 			extend: 'excelHtml5',
			// 			text: '<i class="fa fa-file-excel"></i>',
			// 			title: 'Monitoring Timeline Realisasi',
			// 			titleAttr: 'Generate Excel',
			// 			className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
			// 		},
			// 		{
			// 			extend: 'print',
			// 			text: '<i class="fa fa-print"></i>',
			// 			title: 'Monitoring Timeline Realisasi',
			// 			titleAttr: 'Print Table',
			// 			className: 'btn-outline-primary btn-xs btn-icon mr-1'
			// 		}
			// 	],
			// 	ajax: {
			// 		url: '{{ route("verification.lokasitanam", $noIjin) }}',
			// 		type: 'GET',
			// 		dataType: 'json',
			// 		dataSrc: '', // Ini adalah opsi untuk mengatur sumber data dalam respons
			// 		success: function (response) {
			// 			// Hapus data yang ada di tabel sebelum memasukkan yang baru
			// 			var tableTanam = $('#tableTanam').DataTable();
			// 			tableTanam.clear().draw();

			// 			if (response.datarealisasi.length > 0) {
			// 				$.each(response.datarealisasi, function (index, realisasi) {
			// 					var ijinStart = realisasi.mulai_ijin;
			// 					var ijinEnd = realisasi.akhir_ijin;
			// 					var kelompok = realisasi.kelompok;
			// 					var pksAwal = realisasi.mulai_perjanjian;
			// 					var pksAkhir = realisasi.akhir_perjanjian;
			// 					var petani = realisasi.anggota;
			// 					var lokasi = realisasi.lokasi;
			// 					var awalTanam = realisasi.mulai_tanam;
			// 					var akhirTanam = realisasi.akhir_tanam;
			// 					var awalPanen = realisasi.mulai_panen;
			// 					var akhirPanen = realisasi.akhir_panen;

			// 					var pksStart = (pksAwal < ijinStart || pksAwal > ijinEnd) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku">' + pksAwal + '</span>' : pksAwal;
			// 					var pksEnd = (pksAkhir < ijinStart || pksAkhir > ijinEnd) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku">' + pksAkhir + '</span>' : pksAkhir;
			// 					var tanamStart = (awalTanam < ijinStart || awalTanam > ijinEnd || awalTanam < pksAwal || awalTanam > pksAkhir) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku atau tanggal berlaku PKS">' + awalTanam + '</span>' : awalTanam;
			// 					var tanamEnd = (akhirTanam < ijinStart || akhirTanam > ijinEnd || akhirTanam < pksAwal || akhirTanam > pksAkhir) ? '<span class="text-danger" title="Mendahului/Melampaui tanggal ijin RIPH yang berlaku atau tanggal berlaku PKS">' + akhirTanam + '</span>' : akhirTanam;

			// 					tableTanam.row.add([lokasi, petani, kelompok, pksStart, pksEnd, tanamStart, tanamEnd, awalPanen, akhirPanen]).draw(false);
			// 				});
			// 			}
			// 		},
			// 		error: function (xhr, status, error) {
			// 			// Handle error jika diperlukan
			// 			console.error(xhr);
			// 		}
			// 	},
			// });


			// $('#tableLokasi').DataTable({
			// 	responsive: true,
			// 	lengthChange: true,
			// 	dom:
			// 	"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
			// 	"<'row'<'col-sm-12'tr>>" +
			// 	"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			// 	buttons: [
			// 		{
			// 			extend: 'excelHtml5',
			// 			text: '<i class="fa fa-file-excel"></i>',
			// 			title: 'Daftar Lokasi Tanam',
			// 			titleAttr: 'Generate Excel',
			// 			className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
			// 		},
			// 		{
			// 			extend: 'print',
			// 			text: '<i class="fa fa-print"></i>',
			// 			title: 'Daftar Lokasi Tanam',
			// 			titleAttr: 'Print Table',
			// 			className: 'btn-outline-primary btn-xs btn-icon mr-1'
			// 		}
			// 	],
			// 	ajax: {
			// 		url: url,
			// 		type: 'GET',
			// 		dataType: 'json',
			// 		dataSrc: '', // Ini adalah opsi untuk mengatur sumber data dalam respons
			// 		success: function (response) {
			// 			var tableLokasi = $('#tableLokasi').DataTable();
			// 			tableLokasi.clear().draw();
			// 			if (response.lokasis.length > 0) {
			// 				$.each(response.lokasis, function(index, lokasi) { // Update response handling

			// 					var luasTanam = lokasi.luas_tanam;
			// 					var formatter = new Intl.NumberFormat('en-GB', {
			// 						style: 'decimal',
			// 						minimumFractionDigits: 2,
			// 						maximumFractionDigits: 2,
			// 					});
			// 					var noDecimal = new Intl.NumberFormat('en-GB', {
			// 						style: 'decimal',
			// 						minimumFractionDigits: 0,
			// 						maximumFractionDigits: 0,
			// 					});
			// 					var LuasTanam = formatter.format(luasTanam) + ' ha';

			// 					var poktan = lokasi.poktan;
			// 					var anggota = lokasi.anggota;
			// 					var namaLokasi = lokasi.nama_lokasi;
			// 					var jmlLokasi = noDecimal.format(namaLokasi) + ' titik';
			// 					var actionBtn = `
			// 						<a href="${lokasi.show}" class="btn btn-xs btn-icon btn-primary" title="Lihat detail">
			// 							<i class="fal fa-search"></i>
			// 						</a>
			// 					`;
			// 					tableLokasi.row.add([poktan, jmlLokasi, anggota,LuasTanam, actionBtn]).draw(false);
			// 				});
			// 			}
			// 			tableLokasi.draw(); // Draw the table after adding the rows
			// 		},
			// 		error: function (xhr, status, error) {
			// 			// Handle error jika diperlukan
			// 			console.error(xhr);
			// 		}
			// 	},
			// });
		});
	</script>

	<script>
		function handleStatusChange() {
			var status = document.getElementById("status").value;
			var ndhprtInput = document.getElementById("ndhprt");
			var batanamInput = document.getElementById("batanam");
			var ndhprtContainer = document.getElementById("ndhprtContainer");
			var batanamContainer = document.getElementById("batanamContainer");

			if (status === "5") { // Jika status adalah 'Perbaikan Data' (5)
				ndhprtInput.disabled = true;
				batanamInput.disabled = true;
				ndhprtContainer.hidden = true;
				batanamContainer.hidden = true;
			} else if (status === "4") { // Jika status adalah 'Sesuai' (4)
				ndhprtContainer.hidden = false;
				batanamContainer.hidden = false;
				ndhprtInput.disabled = false;
				batanamInput.disabled = false;
			}
		}
		function validateInput() {
			// get the input value and the current username from the page
			var status = document.getElementById("status").value;
			var inputVal = document.getElementById('validasi').value;
			var currentUsername = '{{ Auth::user()->username }}';
			var status = document.getElementById("status").value;
			var ndhprtInput = document.getElementById("ndhprt").value;
			var batanamInput = document.getElementById("batanam").value;

			// check if the input is not empty and matches the current username
			if (inputVal !== '' && inputVal === currentUsername) {
				// Jika status = 4, lakukan validasi tambahan
				if (status === "4") {
					if (ndhprtInput === '' || batanamInput === '') {
						alert("Nota Dinas dan Berita Acara harus diunggah jika status adalah 'Sesuai' (4).");
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
