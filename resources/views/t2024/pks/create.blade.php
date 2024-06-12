@extends('t2024.layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@can('commitment_edit')
@include('t2024.partials.sysalert')
	{{-- {{ dd($data_poktan) }} --}}
	<div class="row">
		<div class="col-lg-12">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>
						Data <span class="fw-300"><i>Basic</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('t2024.partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<form action="{{route('2024.user.commitment.pks.storePks')}}" method="post" enctype="multipart/form-data">
						@csrf
						<div class="panel-content">
							<div class="row d-flex">
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<label class="form-label" for="no_perjanjian">Nomor RIPH</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="">123</span>
											</div>
											<input readonly type="text" class="form-control " id="no_ijin" name="no_ijin"
												value="{{$ijin}}">
										</div>
										<div class="help-block">
											Nomor RIPH.
										</div>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<label class="form-label" for="no_perjanjian">Kelompok Tani</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id=""><i class="fal fa-users"></i></span>
											</div>
											<input readonly type="text" class="form-control " id="nama_kelompok" name="nama_kelompok" value="{{$poktan->nama_kelompok}}">
											<input type="hidden" class="form-control " id="poktan_id" name="poktan_id" value="{{$poktanId}}">
										</div>
										<div class="help-block">
											Nomor RIPH.
										</div>
									</div>
								</div>
								<div class="col-md-6 mb-3">
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
								<div class="col-md-6 mb-3">
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
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<label class="form-label" for="varietas_tanam">Varietas Tanam</label>
										<div class="input-group">
											<select class="form-control custom-select" name="varietas_tanam" id="varietas_tanam" required>
												<option value="" hidden>-- pilih varietas</option>
												@foreach ($varietass as $varietas)
													<option value="{{$varietas->id}}">{{$varietas->nama_varietas}}</option>
												@endforeach
											</select>
										</div>
										<div class="help-block">
											Varietas ditanam sesuai dokumen perjanjian.
										</div>
									</div>
								</div>
								<div class="col-md-6 mb-3">
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
						<div class="panel-content d-flex">
							<div class="ml-auto">
								<div></div>
								<button class="btn btn-sm btn-primary" type="submit">Simpan</button>
							</div>
						</div>
					</form>
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

    });
</script>

@endsection
