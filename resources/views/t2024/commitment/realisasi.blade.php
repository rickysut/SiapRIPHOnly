@extends('t2024.layouts.admin')
@section('styles')
<style>
	.swal2-container {
	z-index: 10000; /* Atur nilai z-index yang lebih tinggi dari modal */
}

</style>
@endsection
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@can('commitment_show')
@include('t2024.partials.sysalert')
	{{-- {{ dd($data_poktan) }} --}}
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#panel-5" role="tab" aria-selected="true">Realisasi Perjanjian Kerjasama/PKS</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#panel-6" role="tab" aria-selected="true">Unggah Berkas (wajib)</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade active show" id="panel-5" role="tabpanel" aria-labelledby="panel-5">
			<div class="row mb-3">
				<div class="col-12">
					<div class="panel-tag fade show bg-white border-info text-info m-0 l-h-m-n">
						<div class="d-flex align-items-center">
							<i class="fas fa-info-circle mr-1"></i>
							<div class="flex-1">
								<small><span class="mr-1 fw-500">INFORMASI!</span>Anda dapat mengisi data Realisasi Komitmen Tanam dan Produksi setelah melengkapi data Perjanjian Kerjasama.</small>
							</div>
							<a href="{{route('2024.user.commitment.index')}}" class="btn btn-info btn-xs btn-w-m waves-effect waves-themed">Kembali</a>
						</div>
					</div>
				</div>
			</div>
			<div class="panel" id="panel-5">
				<div class="panel-container show">
					<div class="panel-content">
						<table id="tblPks" class="table table-sm table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>No. Perjanjian</th>
									<th>Poktan Mitra</th>
									<th>Anggota</th>
									<th>Luas Lahan</th>
									<th>Tindakan</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="panel-6" role="tabpanel" aria-labelledby="panel-6">
			<form action="{{route('admin.task.commitment.realisasi.storeUserDocs', $commitment->id)}}" method="post" enctype="multipart/form-data" id="docsUpload">
					@csrf
				<div class="row mb-3">
					<div class="col-12">
						<div class="panel-tag fade show bg-white border-danger m-0 l-h-m-n">
							<div class="d-flex align-items-center">
								<i class="fas fa-exclamation-circle mr-1 text-danger"></i>
								<div class="flex-1 text-danger">
									<small><span class="mr-1 fw-700">PERHATIAN!</span>Seluruh Dokumen Tanam & Produksi harus diunggah sebelum <span class="fw-700 text-uppercase">Pengajuan Surat Keterangan Lunas</span> dilakukan.</small>
								</div>
								<a href="{{route('2024.user.commitment.index')}}" class="mr-1 btn btn-info btn-xs btn-w-m waves-effect waves-themed">Kembali</a>
								<!-- Button trigger modal -->
								<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modelId">
									Perlu Bantuan ?
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-deck">
					<div class="card" id="panel-6-a">
						<div class="panel-hdr">
							<h2>Dokumen Realisasi Tanam</h2>
						</div>
						<div class="card-body">
							<div class="panel-tag fade show">
								<div class="d-flex align-items-top">
									<i class="fal fa-info-circle mr-1"></i>
									<div class="flex-1">
										<small>
											Berkas-berkas yang diperlukan terkait dengan Verifikasi Tanam. Lengkapi dan unggah dokumen berikut sebelum Anda mengajukan Verifikasi Tanam. <br>
										</small>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="sptjmtanam">Form SPTJM (tanam)</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="sptjmtanam" id="sptjmtanam" value="{{ old('sptjmtanam', optional($docs)->sptjmtanam) }}">
										<label class="custom-file-label" for="sptjmtanam">{{ $docs ? ($docs->sptjmtanam ? $docs->sptjmtanam : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->sptjmtanam)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->sptjmtanam) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
											<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Surat Pertanggungjawaban Mutlak Realisasi Komitmen Wajib Tanam.  Pdf, max 2Mb</span>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="rta">Form RTA</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="rta" id="rta" value="{{ old('rta', optional($docs)->rta) }}">
										<label class="custom-file-label" for="rta">{{ $docs ? ($docs->rta ? $docs->rta : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->rta)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->rta) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Form Laporan Realisasi Tanam (Form RTA).  Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="sphtanam">SPH-SBS Tanam</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="sphtanam" id="sphtanam" value="{{ old('sphtanam', optional($docs)->sphtanam) }}">
										<label class="custom-file-label" for="sphtanam">{{ $docs ? ($docs->sphtanam ? $docs->sphtanam : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->sphtanam)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->sphtanam) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Form SPH-SBS Tanam dari Petugas Data Kecamatan Setempat. Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="logbooktanam">Logbook Tanam</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="logbooktanam" id="logbooktanam" value="{{ old('logbooktanam', optional($docs)->logbooktanam) }}">
										<label class="custom-file-label" for="logbooktanam">{{ $docs ? ($docs->logbooktanam ? $docs->logbooktanam : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->logbooktanam)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->logbooktanam) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Logbook Tanam. Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div><hr>
							<div class="panel-tag fade show">
								<div class="d-flex align-items-top">
									<i class="fal fa-info-circle mr-1"></i>
									<div class="flex-1">
										<small>
											Unggah berkas berikut jika Anda telah mengunggah seluruh berkas di atas dan ingin mengajukan verifikasi tanam. <br>
										</small>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="spvt">Pengajuan Verifikasi Tanam</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="spvt" id="spvt" value="{{ old('spvt', optional($docs)->spvt) }}">
										<label class="custom-file-label" for="spvt">{{ $docs ? ($docs->spvt ? $docs->spvt : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->spvt)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->spvt) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
											<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Surat Pengajuan Verifikasi Tanam. Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div>
						</div>
						<div class="card-footer d-flex">
							<div class="col-md-6 text-right ml-auto">
								<button class="btn btn-primary btn-sm waves-effect waves-themed" type="submit">
									<i class="fal fa-cloud-upload mr-1"></i>Unggah Berkas Tanam
								</button>
							</div>
						</div>
					</div>

					<div class="card" id="panel-6-b">
						<div class="panel-hdr">
							<h2>Dokumen Realisasi Produksi</h2>
						</div>
						<div class="card-body">
							<div class="panel-tag fade show">
								<div class="d-flex align-items-top">
									<i class="fal fa-info-circle mr-1"></i>
									<div class="flex-1">
										<small>Berkas-berkas yang diperlukan terkait dengan Verifikasi Produksi. Lengkapi dan unggah dokumen berikut sebelum Anda mengajukan Verifikasi Produksi.</small>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="sptjmproduksi">Form SPTJM (produksi)</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="sptjmproduksi" id="sptjmproduksi" value="{{ old('sptjmproduksi', optional($docs)->sptjmproduksi) }}">
										<label class="custom-file-label" for="sptjmproduksi">{{ $docs ? ($docs->sptjmproduksi ? $docs->sptjmproduksi : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->sptjmproduksi)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->sptjmproduksi) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
											<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Surat Pertanggungjawaban Mutlak Realisasi Komitmen Wajib Prouksi. Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="rpo">Form RPO</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="rpo" id="rpo" value="{{ old('rpo', optional($docs)->rpo) }}">
										<label class="custom-file-label" for="rpo">{{ $docs ? ($docs->rpo ? $docs->rpo : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->rpo)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->rpo) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Form laporan realisasi produksi (Form RPO). Pdf, max 2Mb.</small>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="sphproduksi">SPH-SBS Produksi</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="sphproduksi" id="sphproduksi" value="{{ old('sphproduksi', optional($docs)->sphproduksi) }}">
										<label class="custom-file-label" for="sphproduksi">{{ $docs ? ($docs->sphproduksi ? $docs->sphproduksi : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->sphproduksi)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->sphproduksi) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Form SPH-SBS Tanam sampai produksi dari Petugas Data Kecamatan Setempat. Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="logbookproduksi">LogBook</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="logbookproduksi" id="logbookproduksi" value="{{ old('logbookproduksi', optional($docs)->logbookproduksi) }}">
										<label class="custom-file-label" for="logbookproduksi">{{ $docs ? ($docs->logbookproduksi ? $docs->logbookproduksi : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->logbookproduksi)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->logbookproduksi) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
											<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Salinan lembar pencatatan oleh petani sejak tanam hingga produksi. Pdf, max 2Mb.</small>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="formLa">Form LA</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" class="custom-file-input" name="formLa" id="formLa" value="{{ old('formLa', optional($docs)->formLa) }}">
										<label class="custom-file-label" for="formLa">{{ $docs ? ($docs->formLa ? $docs->formLa : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->formLa)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->formLa) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
											<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Form Laporan Akhir Realisasi Komitmen Wajib Tanam-Produksi. Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div><hr>
							<div class="panel-tag fade show">
								<div class="d-flex align-items-top">
									<i class="fal fa-info-circle mr-1"></i>
									<div class="flex-1">
										<small>Unggah berkas berikut jika Anda telah mengunggah seluruh berkas di atas dan ingin mengajukan verifikasi produksi.</small>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="spvp">Pengajuan Verifikasi Produksi</label>
								<div class="col-sm-9">
									<div class="custom-file input-group">
										<input type="file" accept=".pdf" accept=".pdf" class="custom-file-input" name="spvp" id="spvp" value="{{ old('spvp', optional($docs)->spvp) }}">
										<label class="custom-file-label" for="spvp">{{ $docs ? ($docs->spvp ? $docs->spvp : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->spvp)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->spvp) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
											<span class="text-info"><i class="fa fa-info-circle mr-1"></i>Surat Pengajuan Verifikasi Produksi. Pdf, max 2Mb.</span>
										@endif
									</span>
								</div>
							</div>
						</div>
						<div class="card-footer d-flex">
							<div class="col-md-6 text-right ml-auto">
								<button class="btn btn-primary btn-sm waves-effect waves-themed" type="submit">
									<i class="fal fa-cloud-upload mr-1"></i>Unggah Berkas Produksi
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="modal fade" id="modalPks" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog modal-dialog-right" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<span class="modal-title">
						<h4 class="fw-500">Data PKS</h4>
						<span>Data Perjanjian Kerjasama dengan Mitra Kelompok Tani</span>
					</span>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row d-flex">
						<div class="col-md-12 mb-3">
							<label class="form-label">Unggah Berkas PKS (Perjanjian Kerjasama)</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<a href="#" id="file-link">
									<span class="input-group-text" id="inputGroupPrepend3">PKS</span>
									</a>
								</div>
								<div class="custom-file">
									<input type="file" accept=".pdf" class="custom-file-input" id="berkas_pks" name="berkas_pks" onchange="validateFile(this)">
									<label class="custom-file-label" for="berkas_pks" id="file-name-label">Pilih file...</label>
								</div>
							</div>
							<span class="help-block" id="file-help-block">
							Unggah hasil pindai berkas Perjanjian dalam bentuk pdf, max 2Mb.
							</span>
						</div>
						<div class="col-md-12 mb-3">
							<div class="form-group">
								<label class="form-label" for="no_perjanjian">Nomor Perjanjian</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="">123</span>
									</div>
									<input type="text" class="form-control " id="no_perjanjian" name="no_perjanjian"
										value=""
										required>
								</div>
								<div class="help-block">
									Nomor Pejanjian Kerjasama dengan Poktan Mitra.
								</div>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<div class="form-group">
								<label class="form-label">Tanggal perjanjian</label>
								<div class="input-daterange input-group" id="" name="">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
									</div>
									<input type="date" name="tgl_perjanjian_start" id="tgl_perjanjian_start"
										class="form-control " placeholder="tanggal mulai perjanjian"
										value="" required
										aria-describedby="helpId">
								</div>
								<div class="help-block">
									Pilih Tanggal perjanjian ditandatangani.
								</div>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<div class="form-group">
								<label class="form-label">Tanggal berakhir perjanjian</label>
								<div class="input-daterange input-group" id="" name="">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
									</div>
									<input type="date" name="tgl_perjanjian_end" id="tgl_perjanjian_end"
										class="form-control " placeholder="tanggal akhir perjanjian"
										value="" required
										aria-describedby="helpId">
								</div>
								<div class="help-block">
									Pilih Tanggal berakhirnya perjanjian.
								</div>
							</div>
						</div>
						<div class="col-md-12 mb-3" hidden>
							<div class="form-group">
								<label class="form-label" for="simpleinputInvalid">Luas Rencana (ha)</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="inputGroupPrepend3"><i class="fal fa-ruler"></i></span>
									</div>
									<input type="" class="form-control " name="luas_rencana" id="luas_rencana"
										value="" step="0.01" readonly>
								</div>
								<div class="help-block">
									Jumlah Luas total sesuai dokumen perjanjian.
								</div>
							</div>
						</div>
						<div class="col-md-12 mb-3">
							<div class="form-group">
								<label class="form-label" for="varietas_tanam">Varietas Tanam</label>
								<div class="input-group">
									<select class="form-control custom-select" name="varietas_tanam" id="varietas_tanam" required>
										<option value="" hidden>-- pilih varietas</option>
									</select>
								</div>
								<div class="help-block">
									Varietas ditanam sesuai dokumen perjanjian.
								</div>
							</div>
						</div>
						<div class="col-md-12 mb-3">
							<div class="form-group">
								<label class="form-label" for="periode">Periode Tanam</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id=""><i class="fal fa-calendar-week"></i></span>
									</div>
									<input type="text" name="periode_tanam" id="periode_tanam"
										class="form-control " placeholder="misal: Jan-Feb" aria-describedby="helpId"
										value="" required>
								</div>
								<div class="help-block">
									Periode tanam sesuai dokumen perjanjian.
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button class="btn btn-primary btn-sm" type="button" id="btnPksUpdate"
						@if ($disabled) disabled @endif>
						<i class="fal fa-save mr-1"></i>Simpan
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Berkas Unggahan</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row d-flex align-items-top justify-content-between">
						<div class="col-lg-6">
							<ul>Jika Anda menemui kendala dalam mengunggah berkas
								<li>Periksa Spesifikasi Berkas
									<ul>
										<li>Pastikan jenis/ekstensi berkas telah sesuai.</li>
										<li>Pastikan ukuran setiap berkas tidak melebihi 2 megabytes (Mb).</li>
									</ul>
								</li>
								<li>Cara mengunggah
									<ul>
										<li>Cobalah untuk mengunggah berkas satu persatu. Form ini hanya dapat menerima jumlah total ukuran berkas diunggah tidak lebih dari 8 megabytes (akumulasi seluruh berkas)</li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="col-lg-6">
							<ul>Keterangan Berkas
								<li>
									Logbook
									<ul>
										<li>Logbook Tanam adalah Salinan lembar pencatatan oleh petani sejak tanam.</li>
										<li>Logbook Produksi adalah Salinan lembar pencatatan oleh petani sejak tanam hingga Produksi.</li>
										<li>Untuk Logbook, Anda tidak perlu mengunggah seluruh halaman/salinan, cukup lembar rekapitulasi (tanam atau produksi). Pastikan Anda menyimpan Salinan Asli (hard copy) saat dilakukan pemeriksaan/verifikasi.</li>
									</ul>
								</li>
								<li>
									Surat Pengajuan Verifikasi
									<ul>
									<li>Tanam: Unggah jika Anda ingin mengajukan Pemeriksaan/Verifikasi Tanam oleh Petugas Verifikator.</li>
									<li>Produksi: Unggah jika Anda ingin mengajukan Pemeriksaan/Verifikasi Produksi oleh Petugas Verifikator.</li>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>
@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent

<script>
	$(document).ready(function()
	{
		$("#selectPoktan").select2({
			placeholder: "-- pilih poktan"
		});

		$('#createPksButton').on('click', function() {
            var selectedPoktanId = $('#selectPoktan').val();
            var noIjin = $(this).data('ijin');

            if (!selectedPoktanId) {
                alert('Pilih kelompok tani terlebih dahulu.');
                return;
            }

            var url = '{{ route("2024.user.commitment.pks.create", ["noIjin" => ":no_ijin", "poktanId" => ":poktanId"]) }}';

            url = url.replace(':no_ijin', noIjin);
            url = url.replace(':poktanId', selectedPoktanId);

            window.location.href = url;
        });

		var varietass = {!! json_encode($varietass) !!};
		var csrfToken = $('meta[name="csrf-token"]').attr('content');
		var noIjin = '{{$commitment->no_ijin}}';
		var formattedNoIjin = noIjin.replace(/[\/.]/g, '');
		var daftarLokasiRoute = "{{ route('2024.user.commitment.daftarLokasi', ['noIjin' => ':noIjin', 'poktanId' => ':poktanId']) }}";

		var dataTable = $('#tblPks').DataTable({
			responsive: true,
			lengthMenu: [10, 25, 50, 75, 100],
			pageLength:10,
			lengthChange: true,
			paging: true,
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('2024.datafeeder.getPksByIjin', $ijin) }}", // Rute untuk mengambil data
				type: "GET",
			},
			columns: [
				{ data: 'no_perjanjian' },
				{ data: 'nama_poktan' },
				{
					data: 'lokasi_count',
					render: function (data, type, row) {
						return data + ' org';
					}
				},
				{
					data: 'lokasi_sum_luas_lahan',
					render: function (data, type, row) {
						return parseFloat(data).toFixed(3).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' ha';
					}
				},
				{
					data: 'status',
					render: function (data, type, row) {
						if (data == 'Filled') {
							return `<button type="button" class="btn btn-icon btn-xs btn-success" onclick="openModal(` + row.id + `)" data-toggle="tooltip" data-original-title="Data sudah lengkap">
										<i class="fal fa-cassette-tape"></i>
									</button>
									<a href="` + daftarLokasiRoute.replace(':noIjin', formattedNoIjin).replace(':poktanId', row.poktan_id) + `" class="btn btn-icon btn-xs btn-primary" data-toggle="tooltip" data-original-title="Lengkapi data realisasi Komitmen Wajib Tanam-produksi">
										<i class="fal fa-seedling"></i>
									</a>`;
						} else {
							return `<button type="button" class="btn btn-icon btn-xs btn-danger" onclick="openModal(` + row.id + `)">
										<i class="fal fa-cassette-tape"></i>
									</button>`;
						}
					}
				}
			],
			columnDefs: [
				{
					targets: [2, 3],
					className: 'text-right'
				},
				{
					targets: [4],
					className: 'text-center'
				},
			],
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			buttons: [
				{
					extend: 'pdfHtml5',
					text: '<i class="fa fa-file-pdf"></i>',
					titleAttr: 'Generate PDF',
					className: 'btn-outline-danger btn-sm btn-icon mr-1',
					customize: function(doc) {
						doc.pageMargins = [10, 20, 10, 20];
						doc.styles.tableHeader = {
							fontSize: 12,
							bold: true,
							alignment: 'center'
						};
						doc.defaultStyle = {
							fontSize: 10
						};
						doc.content[1].table.widths = [
							'25%',
							'5%',
							'12%',
							'13%',
							'45%',
						];
					},
					exportOptions: {
						columns: [0, 1, 2, 3, 4]
					}
				},
				{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel"></i>',
					titleAttr: 'Generate Excel',
					className: 'btn-outline-success btn-sm btn-icon mr-1',
					exportOptions: {
						columns: [0, 1, 2, 3, 4]
					},
					customize: function(xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];

						// Mengatur lebar kolom
						$('col', sheet).each(function() {
							$(this).attr('width', 30);
						});

						// Menambahkan gaya khusus
						$('row c[r^="C"]', sheet).each(function() {
							if ($('is t', this).text() == 'Some Text') {
								$(this).attr('s', '42');
							}
						});

						// Menambahkan border ke header
						$('row:first c', sheet).attr('s', '2');
					}
				},
				{
					extend: 'print',
					text: '<i class="fa fa-print"></i>',
					titleAttr: 'Print Table',
					className: 'btn-outline-primary btn-sm btn-icon mr-1',
					exportOptions: {
						columns: [0, 1, 2, 3, 4]
					}
				}
			],
		});

		window.openModal = function(pksId) {
			$('#modalPks').modal('show');
			var id = pksId;
			$('#btnPksUpdate').attr('data-pksid', pksId);
			$.ajax({
				url: "{{ route('2024.datafeeder.getPksById', ':id') }}".replace(':id', pksId),
				type: "GET",
				success: function(data) {
					console.log(data.no_perjanjian);
					$('#no_perjanjian').val(data.no_perjanjian || '');
					$('#tgl_perjanjian_start').val(data.tgl_perjanjian_start || '');
					$('#tgl_perjanjian_end').val(data.tgl_perjanjian_end || '');
					$('#periode_tanam').val(data.periode_tanam || '');

					// Kosongkan elemen select varietas_tanam
					$('#varietas_tanam').empty();
					// Tambahkan opsi-opsi varietas dari varietass ke elemen select
					$.each(varietass, function(index, varietas) {
						$('#varietas_tanam').append('<option value="' + varietas.id + '">' + varietas.nama_varietas + '</option>');
					});
					// Set opsi yang dipilih berdasarkan data.varietas_tanam jika ada
					if (data.varietas_tanam) {
						$('#varietas_tanam').val(data.varietas_tanam).trigger('change');
					}

					if (data.berkas_pks) {
						$('#file-name-label').text(data.berkas_pks);
						$('#file-link').attr('href', data.linkBerkas);
						$('#file-help-block').html(`<a href="${data.linkBerkas}" target="_blank">Lihat berkas yang telah diunggah</a>`);
					} else {
						$('#file-name-label').text('Pilih file...');
						$('#file-link').attr('href', '#');
						$('#file-help-block').text('Unggah hasil pindai berkas Perjanjian dalam bentuk pdf, max 2Mb.');
					}
				},
				error: function(xhr, status, error) {
					console.error('Gagal mengambil data PKS:', status, error);
				}
			});

			console.log('Membuka modal untuk Pks dengan ID ' + pksId);
		}

		function saveButtonClicked(pksId) {
			var formData = new FormData(); // Buat objek FormData
			formData.append('no_perjanjian', $('#no_perjanjian').val());
			formData.append('tgl_perjanjian_start', $('#tgl_perjanjian_start').val());
			formData.append('tgl_perjanjian_end', $('#tgl_perjanjian_end').val());
			formData.append('varietas_tanam', $('#varietas_tanam').val());
			formData.append('periode_tanam', $('#periode_tanam').val());
			// Tambahkan berkas unggahan jika ada
			var berkas_pks = $('#berkas_pks')[0].files[0];
			if (berkas_pks) {
				formData.append('berkas_pks', berkas_pks);
			}

			$.ajax({
				url: "{{ route('2024.user.commitment.updatepks', ':pksId') }}".replace(':pksId', pksId),
				type: "POST",
				headers: {
					'X-CSRF-TOKEN': csrfToken, // Tambahkan token CSRF ke header
					'X-HTTP-Method-Override': 'PUT' // Override metode ke PUT
				},
				data: formData, // Gunakan objek FormData
				processData: false, // Matikan pemrosesan data
				contentType: false, // Matikan jenis konten
				success: function(response) {
					console.log("Data PKS berhasil diperbarui:", response);
					Swal.fire({
						icon: 'success',
						title: 'Sukses',
						text: 'Data PKS berhasil diperbarui!'
					}).then(() => {
						// Lakukan reload datatable di sini
						dataTable.ajax.reload(); // Ganti dengan kode yang benar untuk reload datatable
						$('#modalPks').modal('hide');
					});
				},
				error: function(xhr, status, error) {
					console.error('Gagal memperbarui data PKS:', status, error);
					Swal.fire({
						icon: 'error',
						title: 'Gagal',
						text: 'Terjadi kesalahan saat memperbarui data PKS. Silakan coba lagi!'
					});
				}
			});
		}

		$('#btnPksUpdate').on('click', function() {
			var pksId = $(this).data('pksid');

			console.log("pks id: ", pksId)
			saveButtonClicked(pksId);
		});

		function validateFile(input) {
			const file = input.files[0];
			if (file) {
				const fileSize = file.size / 1024 / 1024; // Ukuran berkas dalam MB
				if (!file.type.includes('pdf')) {
					Swal.fire({
						icon: 'error',
						title: 'Berkas tidak valid',
						text: 'Silakan unggah berkas dalam format PDF.',
					});
					input.value = ''; // Reset input file
				} else if (fileSize > 2) {
					Swal.fire({
						icon: 'error',
						title: 'Berkas terlalu besar',
						text: 'Ukuran berkas melebihi batas maksimum 2MB.',
					});
					input.value = ''; // Reset input file
				}
			}
		}
	});


</script>
@endsection
