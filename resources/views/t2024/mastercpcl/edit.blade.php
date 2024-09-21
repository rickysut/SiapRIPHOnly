@extends('t2024.layouts.admin')
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
{{-- @can('spatial_data_access') --}}
	<div class="row">
		<div class="col">
			<div name="panel-2" class="panel" data-title="Panel Data" data-intro="Panel ini berisi data-data" data-step="2">
				<div class="panel-hdr">
					<h2>
						Informasi Biodata <span class="fw-300"></span>
					</h2>

				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<form action="">
							<div class="form-group row">
								<div class="col-6">
									<label class="form-label" for="nama_petani">Nama Petani <span class="text-danger">*</span></label>
									<input type="text" id="nama_petani" name="nama_petani"  class="form-control @error('nama_petani') is-invalid @enderror" placeholder="nama_petani" value="{{$cpcl->nama_petani}}" required>
									<span class="help-block" id="help-nama"></span>
								</div>
								<div class="col-6">
									<label class="form-label" for="ktp_petani">NIK <span class="text-danger">*</span></label>
									<input type="text" id="ktp_petani" name="ktp_petani"  class="form-control @error('ktp_petani') is-invalid @enderror" placeholder="ktp_petani" value="{{$cpcl->ktp_petani}}" required>
									<span class="help-block" id="help-nik"></span>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-md-6">
									<label class="form-label" for="hp_petani">No. Handphone <span class="text-danger">*</span></label>
									<input type="text" id="hp_petani" name="hp_petani" class="form-control @error('hp_petani') is-invalid @enderror" placeholder="No. Handphone" value="{{$cpcl->hp_petani}}" required>
									<div class="help-block" id="help-hp"></div>
								</div>
								<div class="col-md-6">
									<label class="form-label" for="kode_poktan">Kelompok Tani <span class="text-danger">*</span></label>
									<select id="kode_poktan" name="kode_poktan" class="form-control w-100 @error('kode_poktan') is-invalid @enderror" required>
										<option value="" hidden></option>
										<option value="{{$cpcl->kode_poktan}}" selected>{{$cpcl->masterpoktan ?$cpcl->masterpoktan->nama_kelompok : ''}}</option>
										<option value=""></option>
									</select>
									<span class="help-block" id="help-poktan"></span>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6">
									<label class="form-label" for="provinsi_id">Provinsi <span class="text-danger">*</span></label>
									<select id="provinsi_id" name="provinsi_id" class="form-control w-100 @error('provinsi_id') is-invalid @enderror" required>
										<option value="" hidden></option>
										<option value="{{$cpcl->provinsi_id}}" selected>{{$cpcl->provinsi ? $cpcl->provinsi->nama : ''}}</option>
									</select>
									<span class="help-block" id="help-provinsi"></span>
								</div>
								<div class="col-md-6">
									<label class="form-label" for="kabupaten_id">Kabupaten <span class="text-danger">*</span></label>
									<select id="kabupaten_id" name="kabupaten_id" class="form-control w-100 @error('kabupaten_id') is-invalid @enderror" required>
										<option value="" hidden></option>
										<option value="{{$cpcl->kabupaten_id}}" selected>{{$cpcl->kabupaten ? $cpcl->kabupaten->nama_kab : ''}}</option>
									</select>
									<div class="help-block" id="help-kabupaten"></div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6">
									<label class="form-label" for="kecamatan_id">Kecamatan <span class="text-danger">*</span></label>
									<select id="kecamatan_id" name="kecamatan_id" class="form-control @error('kecamatan_id') is-invalid @enderror" required>
										<option value="" hidden></option>
										<option value="{{$cpcl->kecamatan_id}}" selected>{{$cpcl->kecamatan ? $cpcl->kecamatan->nama_kecamatan : ''}}</option>
									</select>
									<div class="help-block" id="help-kecamatan"></div>
								</div>
								<div class="col-md-6">
									<label class="form-label" for="kelurahan_id">Desa/Kelurahan <span class="text-danger">*</span></label>
									<select id="kelurahan_id" name="kelurahan_id" class="form-control @error('kelurahan_id') is-invalid @enderror" required>
										<option value="" hidden></option>
										<option value="{{$cpcl->kelurahan_id}}" selected>{{$cpcl->desa ? $cpcl->desa->nama_desa : ''}}</option>
									</select>
									<div class="help-block" id="help-desa"></div>
								</div>
							</div>
							<div class="form-group row d-flex align-items-end">
								<div class="col-lg-6">
								</div>
								<div class="col-lg-6 text-right">
									<button class="btn btn-primary btn-sm" type="submit">Simpan</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
{{-- @endcan --}}

@endsection

<!-- start script for this page -->
@section('scripts')
@parent
<script>
	$(document).ready(function() {
		var poktanSelect = $('#kode_poktan');
		var provinsiSelect = $('#provinsi_id');
		var kabupatenSelect = $('#kabupaten_id');
		var kecamatanSelect = $('#kecamatan_id');
		var desaSelect = $('#kelurahan_id');

		$("#provinsi_id").select2({
			placeholder: "-- pilih provinsi"
		});
		$("#kode_poktan").select2({
			placeholder: "-- pilih poktan"
		});
		$("#kabupaten_id").select2({
			placeholder: "-- pilih kabupaten"
		});
		$("#kecamatan_id").select2({
			placeholder: "-- pilih kecamatan"
		});
		$("#kelurahan_id").select2({
			placeholder: "-- pilih desa"
		});

		$.get('{{ route("2024.datafeeder.getAllPoktan") }}', function(response) {
			console.log('responses: ', response); // Log response to ensure it is being retrieved

			if (response.data && response.data.length > 0) {
				$.each(response.data, function(index, poktan) {
					var option = $('<option>', {
						value: poktan.kode_poktan,
						text: poktan.nama_kelompok
					});

					// Check if this option should be selected based on old input or current value
					if ('{{ old("kode_poktan") }}' == poktan.kode_poktan || '{{ $cpcl->kode_poktan ?? "" }}' == poktan.kode_poktan) {
						option.prop('selected', true);
					}

					// Append the option to the select element
					poktanSelect.append(option);
				});
			} else {
				console.log('No data returned or data is empty');
			}
		}).fail(function() {
			console.log('Error fetching data from server');
		});

		$.get('{{ route("wilayah.getAllProvinsi") }}', function (data) {
			$.each(data, function (key, value) {
				var option = $('<option>', {
					value: value.provinsi_id,
					text: value.nama
				});

				if ('{{ old(" ") }}' == value.provinsi_id) {
					option.attr('selected', 'selected');
				}

				provinsiSelect.append(option);
			});
		});

		provinsiSelect.change(function () {
			var selectedProvinsiId = provinsiSelect.val();

			kabupatenSelect.empty();
			kecamatanSelect.empty();
			desaSelect.empty();
			kabupatenSelect.append($('<option>', {
				value: '',
				text: '-- pilih kabupaten'
			}));
			kecamatanSelect.append($('<option>', {
				value: '',
				text: '-- pilih kecamatan'
			}));
			desaSelect.append($('<option>', {
				value: '',
				text: '-- pilih desa'
			}));

			$.get('/wilayah/getKabupatenByProvinsi/' + selectedProvinsiId, function (data) {
				$.each(data, function (key, value) {
					var option = $('<option>', {
						value: value.kabupaten_id,
						text: value.nama_kab
					});

					kabupatenSelect.append(option);
				});
			});
		});

		kabupatenSelect.change(function () {
			var selectedKabupatenId = kabupatenSelect.val();
			kecamatanSelect.empty();
			desaSelect.empty();

			kecamatanSelect.append($('<option>', {
				value: '',
				text: '-- pilih kecamatan'
			}));
			desaSelect.append($('<option>', {
				value: '',
				text: '-- pilih desa'
			}));

			$.get('/wilayah/getKecamatanByKabupaten/' + selectedKabupatenId, function (data) {
				$.each(data, function (key, value) {
					var option = $('<option>', {
						value: value.kecamatan_id,
						text: value.nama_kecamatan
					});
					kecamatanSelect.append(option);
				});
			});
		});

		kecamatanSelect.change(function () {
			var selectedKecamatanId = kecamatanSelect.val();
			desaSelect.empty();

			desaSelect.append($('<option>', {
				value: '',
				text: '-- pilih desa'
			}));

			$.get('/wilayah/getDesaByKec/' + selectedKecamatanId, function (data) {
				$.each(data, function (key, value) {
					var option = $('<option>', {
						value: value.kelurahan_id,
						text: value.nama_desa
					});
					desaSelect.append(option);
				});
			});
		});
	});
</script>
@endsection
