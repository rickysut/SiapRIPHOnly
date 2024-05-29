@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
{{-- @include('partials.subheader') --}}
<!-- Page Content -->
<div class="subheader">
	<h1 class="subheader-title">
		<i class="subheader-icon {{ ($heading_class ?? '') }}"></i><span class="fw-700 mr-2 ml-2">{{  ($page_heading ?? '') }}</span><span class="fw-300">Data Lokasi</span>
	</h1>
</div>
@include('partials.sysalert')
<div class="row">
	<div class="col-12">
		<div class="panel" id="panel-1">
			<div class="panel-hdr">
				<h5>Lokasi Tanam - Produksi</h5>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<div class="form-group">
						<label class="form-label" for="name-f">Pilih data</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text text-success">
									<i class="ni ni-calendar-fine fs-xl"></i>
								</span>
							</div>
							<select class="custom-select" id="year" name="year" aria-label="year">
								<option value="now" hidden>--pilih tahun</option>
								@foreach ($years as $year)
									<option value="{{ $year }}">{{ $year }}</option>
								@endforeach
							</select>
							<select class="custom-select" id="company" name="company" aria-label="company">
								<option value="" hidden>--pilih perusahaan</option>
							</select>
							<div class="input-group-append">
								<button type="button" class="btn btn-primary shadow-0 waves-effect waves-themed" id="showButton" disabled>Tampilkan</button>
							</div>
						</div>
						<span class="help-block">Pilih tahun data dan perusahaan.</span>
					</div>
				</div>

				<div class="panel-content">
					<div class="col-12">

					<table id="dataLokasi" class="table table-bordered table-hover table-striped table-sm w-100">
						<thead class="thead-themed">
							<th>Perusahaan</th>
							<th>Nomor Rekomendasi (RIPH)</th>
							<th>Nomor Perjanjian</th>
							<th>Nama Kelompok</th>
							<th>Nama Lokasi</th>
							<th>Nama Petani</th>
							<th>Latitude</th>
							<th>Longitude</th>
							<th>Polygon</th>
							<th>Luas Tanam (ha)</th>
							<th>Tanggal Tanam</th>
							<th>Volume (ton)</th>
							<th>Tanggal Produksi</th>
							<th>Status Lunas RIPH</th>
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
@endsection
@section('scripts')
@parent
	{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
	<script>
		$(document).ready(function() {
			$('#year').on('change', function() {
				$('#showButton').prop('disabled', true); // Disable button when year changes

				var year = $(this).val();
				var url = "{{ route('admin.getCompaniesByYear', ':year') }}";
				url = url.replace(':year', year);

				$.ajax({
					url: url,
					type: 'GET',
					contentType: 'application/json',
					dataType: 'json',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					success: function(data) {
						var $companySelect = $('#company');
						$companySelect.empty();
						$companySelect.append('<option value="" hidden>--pilih perusahaan</option>');
						$.each(data, function(index, company) {
							$companySelect.append('<option value="' + company.no_ijin + '">' + company.company_name + '</option>');
						});

						$('#showButton').prop('disabled', false);
					}
				});
			});

			$('#company').on('change', function() {
				var selectedCompany = $(this).val();
				if (selectedCompany !== '') {
					$('#showButton').prop('disabled', false);
				} else {
					$('#showButton').prop('disabled', true);
				}
			});

			var companyName = '';
			var exportNoIjin = '';

			$('#showButton').on('click', function() {
				$('#dataLokasi').DataTable().clear().draw();
				var companyInput = $('#company').val();

				if (companyInput !== '') {
					companyInput = companyInput.replace(/[\/\.]/g, '');

					var url = "{{ route('admin.getLocationByIjin', ':companyInput') }}";
					url = url.replace(':companyInput', encodeURIComponent(companyInput));

					// Ambil data dari URL menggunakan AJAX
					$.ajax({
						url: url,
						method: 'GET',
						success: function(response) {
							// Hapus data yang ada di tabel
							$('#dataLokasi').DataTable().clear().draw();

							// Tambahkan data realisasi ke dalam tabel
							$.each(response, function(index, realisasi) {
								if (index === 0) {
									companyName = realisasi.commitment_nama;
									noIjin = realisasi.no_ijin
									exportNoIjin = noIjin.replace(/[\/\.]/g, '');
								}

								$('#dataLokasi').DataTable().row.add([
									realisasi.commitment_nama,
									realisasi.no_ijin,
									realisasi.no_pks,
									realisasi.nama_kelompok,
									realisasi.nama_lokasi,
									realisasi.nama_petani,
									realisasi.latitude,
									realisasi.longitude,
									realisasi.polygon,
									realisasi.luas_lahan,
									realisasi.mulai_tanam,
									realisasi.volume,
									realisasi.mulai_panen,
									realisasi.status,
								]).draw();
							});
						},
						error: function(xhr, status, error) {
							console.error(error);
						}
					});
				}
			});

			// $('#showButton').on('click', function() {
			// 	$('#dataLokasi').DataTable().clear().draw();
			// 	var noIjin = $('#company').val();
			// 	console.log('raw no ijin: ', noIjin);

			// 	if (noIjin !== '') {
			// 		noIjin = noIjin.replace(/[\/\.]/g, '');
			// 		console.log('formatted: ', noIjin);

			// 		var url = "{{ route('admin.getLocationByIjin', ':noIjin') }}";
			// 		url = url.replace(':noIjin', encodeURIComponent(noIjin));

			// 		// Ambil data dari URL menggunakan AJAX
			// 		$.ajax({
			// 			url: url,
			// 			method: 'GET',
			// 			success: function(response) {
			// 				companyName = response.datauser.company_name;
			// 				noIjinA = response.no_ijin;
			// 				noIjinB = noIjinA.replace(/[\/\.]/g, '');
			// 				$('#dataLokasi').DataTable().clear().draw();

			// 				$.each(response.datarealisasi, function(index, realisasi) {
			// 					$('#dataLokasi').DataTable().row.add([
			// 						response.datauser.company_name,
			// 						response.no_ijin,
			// 						realisasi.pks.no_perjanjian,
			// 						realisasi.masterkelompok.nama_kelompok,
			// 						realisasi.nama_lokasi,
			// 						realisasi.latitude,
			// 						realisasi.longitude,
			// 						realisasi.polygon,
			// 						realisasi.luas_lahan,
			// 						realisasi.mulai_tanam,
			// 						realisasi.mulai_panen,
			// 						realisasi.volume,
			// 						response.completed.status,
			// 					]).draw();
			// 				});
			// 			},
			// 			error: function(xhr, status, error) {
			// 				console.error(error);
			// 				// Handle error jika terjadi
			// 			}
			// 		});
			// 	}
			// });

			$(document).ready(function() {
				$('#dataLokasi').DataTable({
					responsive: true,
					lengthChange: false,
					dom:
						"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'<'select'>>>" + // Move the select element to the left of the datatable buttons
						"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
					columnDefs: [
						{
							targets: [0,1,2,3,4,8], // Kolom ke-5 (index dimulai dari 0), yaitu kolom 'polygon'
							visible: false, // Sembunyikan kolom pada tampilan DataTable
							exportable: true // Tetap tampilkan kolom saat ekspor
						}
					],
					buttons: [
						{
							extend: 'csvHtml5',
							text: '<i class="fal fa-file-csv"></i>',
							title: function() {
								return companyName + "_" + exportNoIjin;
							},
							titleAttr: 'Generate CSV',
							className: 'btn-success btn-sm btn-icon mr-1'
						},
					]
				});
			});
		});
	</script>
@endsection
