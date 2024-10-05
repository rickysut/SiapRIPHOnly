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
@php
	$pathNpwp = str_replace(['.', '-'], '', $npwp);
@endphp
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
			<div class="panel" id="panel-5">
				<div class="panel-hdr">
					<h2>
					</h2>
					<div class="panel-toolbar">
						<a href="{{route('2024.user.commitment.index')}}" class="btn btn-info btn-xs btn-w-m waves-effect waves-themed">Kembali</a>
					</div>
				</div>
				<div class="panel-container">
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
			<div class="card">
				<div class="panel-hdr">
					<h2>Dokumen Realisasi</h2>
					<div class="panel-toolbar">
						<a href="{{route('2024.user.commitment.index')}}" class="btn btn-info btn-xs btn-w-m waves-effect waves-themed">Kembali</a>
					</div>
				</div>
				<div class="card-body">
					<div class="row d-flex justify-content-between align-items-start">
						<div class="col-lg-6">
							<span class="h2 text-muted">Tahap Tanam</span>
							<div class="panel-tag fade show bg-white border-warning m-0 l-h-m-n">
								<p class="small">Berkas-berkas yang diperlukan terkait dengan Verifikasi Tanam. Lengkapi dan unggah dokumen berikut sebelum Anda mengajukan Verifikasi Tanam.</p>
							</div>
						</div>
						<div class="col-lg-6 mb-5">
							<ul class="list-group">
								<li class="list-group-item">
									<div class="form-group">
										<label class="form-label" for="logbook">Logbook <span class="text-danger">*</span></label>
										<div class="input-group">
											<input type="text" name="logbook" id="logbook" class="form-control" placeholder="autogenerate Logbook" aria-label="Logbook" aria-describedby="logbook" value="{{ $docs && optional($docs->where('kind', 'logbook')->first())->file_url ? 'Available' : '' }}" disabled readonly>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'logbook')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'logbook')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="sptjmtanam">Form SPTJM (tanam)</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="sptjmtanam" id="sptjmtanam-fr" value="{{ old('sptjmtanam', optional($docs->where('kind', 'sptjmtanam')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="sptjmtanam">
													{{ $docs && optional($docs->where('kind', 'sptjmtanam')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'sptjmtanam')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'sptjmtanam')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="rta">Form RTA</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="rta" id="rta" value="{{ old('rta', optional($docs->where('kind', 'rta')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="rta">
													{{ $docs && optional($docs->where('kind', 'rta')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'rta')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'rta')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="sphtanam">SPH-SBS Tanam</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="sphtanam" id="sphtanam" value="{{ old('sphtanam', optional($docs->where('kind', 'sphtanam')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="sphtanam">
													{{ $docs && optional($docs->where('kind', 'sphtanam')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'sphtanam')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'sphtanam')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="spvt">Pengajuan Verifikasi Tanam</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="spvt" id="spvt" value="{{ old('spvt', optional($docs->where('kind', 'spvt')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="spvt">
													{{ $docs && optional($docs->where('kind', 'spvt')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'spvt')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'spvt')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
								</li>
							</ul>
						</div>
						<div class="col-lg-6">
							<span class="h2 text-muted">Tahap Produksi</span>
							<div class="panel-tag fade show bg-white border-warning m-0 l-h-m-n">
								<p class="small">Berkas-berkas yang diperlukan terkait dengan Verifikasi Tanam. Lengkapi dan unggah dokumen berikut sebelum Anda mengajukan Verifikasi Produksi.</p>
							</div>
						</div>
						<div class="col-lg-6 mb-5">
							<ul class="list-group">
								<li class="list-group-item">
									<div class="form-group">
										<label class="form-label" for="sptjmproduksi">Form SPTJM (produksi)</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="sptjmproduksi" id="sptjmproduksi" value="{{ old('sptjmproduksi', optional($docs->where('kind', 'sptjmproduksi')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="sptjmproduksi">
													{{ $docs && optional($docs->where('kind', 'sptjmproduksi')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'sptjmproduksi')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'sptjmproduksi')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="rpo">Form RPO</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="rpo" id="rpo" value="{{ old('rpo', optional($docs->where('kind', 'rpo')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="rpo">
													{{ $docs && optional($docs->where('kind', 'rpo')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'rpo')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'rpo')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="sphproduksi">SPH-SBS Produksi</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="sphproduksi" id="sphproduksi" value="{{ old('sphproduksi', optional($docs->where('kind', 'sphproduksi')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="sphproduksi">
													{{ $docs && optional($docs->where('kind', 'sphproduksi')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'sphproduksi')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'sphproduksi')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="formLa">Form LA</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="formLa" id="formLa" value="{{ old('formLa', optional($docs->where('kind', 'formLa')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="formLa">
													{{ $docs && optional($docs->where('kind', 'formLa')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'formLa')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'formLa')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
									<div class="form-group">
										<label class="form-label" for="spvp">Pengajuan Verifikasi Produksi</label>
										<div class="input-group">
											<div class="custom-file">
												<input type="file" accept=".pdf" class="custom-file-input size-validation" name="spvp" id="spvp" value="{{ old('spvp', optional($docs->where('kind', 'spvp')->first())->file_url) }}">
												<label class="custom-file-label text-success" for="spvp">
													{{ $docs && optional($docs->where('kind', 'spvp')->first())->file_url ? 'Terlampir' : '' }}
												</label>
											</div>
										</div>
										<span class="help-block">
											@if($docs && optional($docs->where('kind', 'spvp')->first())->file_url)
												<a href="{{ optional($docs->where('kind', 'spvp')->first())->file_url }}" target="_blank">
													Lihat Dokumen diunggah.
												</a>
											@endif
										</span>
									</div>
								</li>
							</ul>
						</div>
						<div class="col-12">
							<span class="h2 text-muted">Keterangan</span>
							<p>Lengkapi dan unggah seluruh dokumen yang diperlukan sebelum Anda mengajukan Verifikasi.</p>
							<small>
								<ul class="list-group">
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">Jenis dan Ukuran Berkas</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>Setiap unggahan hanya menerima berkas berjenis Portable Document Format (pdf)</li>
												<li>Ukuran maksimum yang dapat diterima adalah 2 megabytes.</li>
											</ul>
										</div>
									</li>
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">Logbook</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>Anda tidak perlu mengunggah berkas Logbook.</li>
												<li>Logbook akan terlampir secara mandiri (otomatis) saat anda mengajukan verifikasi baik Tanam maupun Produksi.</li>
												<li>Namun demikian, pastikan Anda menyimpan SALINAN ASLI (hard copy) yang diperlukan pemeriksaan/verifikasi aktual di lapangan.</li>
											</ul>
										</div>
									</li>
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">SPTJM</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>SPTJM adalah form Surat Pertanggungjawaban Mutlak.</li>
												<li>Anda wajib mengunggah SPTJM untuk setiap tahap realisasi (Tanam/Produksi).</li>
											</ul>
										</div>
									</li>
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">Form RTA</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>form Realisasi Tanam.</li>
											</ul>
										</div>
									</li>
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">Form RPO</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>form Realisasi Produksi.</li>
											</ul>
										</div>
									</li>
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">Form SPH-SBS</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>Form SPH-SBS Tanam/Produksi dari Petugas Data Kecamatan Setempat.</li>
											</ul>
										</div>
									</li>
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">Form LA</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>Adalah form laporan akhir berisi seluruh data terkait realisasi wajib tanam-produksi.</li>
											</ul>
										</div>
									</li>
									<li class="d-flex justify-content-between align-items-start mb-3">
										<span class="fw-500 col-3">Surat Pengajuan Verifikasi</span>
										<div class="col-9">
											<ul class="text-muted">
												<li>Tanam: Unggah jika Anda ingin mengajukan Pemeriksaan/Verifikasi Tanam oleh Petugas Verifikator.</li>
												<li>Produksi: Unggah jika Anda ingin mengajukan Pemeriksaan/Verifikasi Produksi oleh Petugas Verifikator.</li>
											</ul>
										</div>
									</li>
								</ul>
							</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalPks" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog modal-dialog-right" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<span class="modal-title">
						<h4 class="fw-500">Edit Data PKS</h4>
						<span>Data Perjanjian Kerjasama dengan Mitra Kelompok Tani <span id=""></span></span>
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
									<input type="file" accept=".pdf" class="custom-file-input  size-validation" id="berkas_pks" name="berkas_pks" onchange="validateFile(this)">
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
@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent

<script>
	$(document).ready(function() {
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
					data: 'total_luas_lahan',
					render: function (data, type, row) {
						var formattedData = parseFloat(data).toLocaleString('id-ID');
						return formattedData + ' m2';
					}
				},
				{
					data: 'statusData',
					render: function (data, type, row) {
						if (data == 'Filled') {
							return `<button type="button" class="btn btn-icon btn-xs btn-success" onclick="openModal(` + row.id + `)" data-toggle="tooltip" data-original-title="Data sudah lengkap">
										<i class="fal fa-cassette-tape"></i>
									</button>
									<a href="` + daftarLokasiRoute.replace(':noIjin', formattedNoIjin).replace(':poktanId', row.kode_poktan) + `" class="btn btn-icon btn-xs btn-primary" data-toggle="tooltip" data-original-title="Lengkapi data realisasi Komitmen Wajib Tanam-produksi">
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
					// $('#tcode').text(data.tcode || '');
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
						$('#file-name-label').text('Terlampir');
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
						location.reload();
						// Lakukan reload datatable di sini
						// Ganti dengan kode yang benar untuk reload datatable
						// dataTable.ajax.reload();
						// $('#modalPks').modal('hide');
					});
				},
				error: function(xhr, status, error) {
					console.error('Gagal memperbarui data PKS:', status, error);
					Swal.fire({
						icon: 'error',
						title: 'Gagal',
						text: 'Terjadi kesalahan saat memperbarui data PKS. Silakan coba lagi!'
					}).then(() => {
						location.reload();
						// Lakukan reload datatable di sini
						// Ganti dengan kode yang benar untuk reload datatable
						// dataTable.ajax.reload();
						// $('#modalPks').modal('hide');
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
				const fileSize = file.size / 1024 / 1024;
				if (!file.type.includes('pdf')) {
					Swal.fire({
						icon: 'error',
						title: 'Berkas tidak valid',
						text: 'Silakan unggah berkas dalam format PDF.',
					});
					input.value = '';
				} else if (fileSize > 2) {
					Swal.fire({
						icon: 'error',
						title: 'Berkas terlalu besar',
						text: 'Ukuran berkas melebihi batas maksimum 2MB.',
					});
					input.value = '';
				}
			}
		}

		$('.size-validation').on('change', function() {
			var file = this.files[0];
			if (file) {
				var fileSize = file.size / 1024 / 1024; // in MB
				var fileType = file.type; // Mime type of the file
				var allowedTypes = ['application/pdf']; // Allowed MIME types

				// Check file size
				if (fileSize > 2) {
					Swal.fire({
						icon: 'error',
						title: 'Ukuran Berkas',
						text: 'Ukuran yang diijinkan tidak melebihi 2MB',
					});
					$(this).val('');
					$(this).next('.custom-file-label').text('Pilih berkas...');
				}
				// Check file type
				else if (!allowedTypes.includes(fileType)) {
					Swal.fire({
						icon: 'error',
						title: 'Tipe Berkas',
						text: 'Hanya berkas PDF yang diijinkan',
					});
					$(this).val('');
					$(this).next('.custom-file-label').text('Pilih berkas...');
				}
				else {
					$(this).next('.custom-file-label').text(file.name);
				}
			}
		});
	});
</script>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		// Ambil semua elemen input file
		var fileInputs = document.querySelectorAll('input[type="file"]');

		fileInputs.forEach(function (fileInput) {
			fileInput.addEventListener('change', function () {
				// Periksa apakah ada file yang dipilih
				if (fileInput.files.length > 0) {
					var formData = new FormData();
					formData.append(fileInput.name, fileInput.files[0]);

					fetch("{{ route('2024.user.commitment.storeUserDocs', $ijin) }}", {
						method: 'POST',
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						},
						body: formData
					})
					.then(response => response.json())
					.then(data => {
						// Tangani respons di sini
						console.log('Success:', data);
					})
					.catch(error => {
						// Tangani kesalahan di sini
						console.error('Error:', error);
					});
				}
			});
		});
	});
</script>
@endsection
