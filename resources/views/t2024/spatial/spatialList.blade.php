@extends('t2024.layouts.admin')
@section('styles')
<style>
	.display-5{
		font-size: 1.8rem;
		font-weight: 300;
		line-height: 1.25;
	}
	.select2-container--default .select2-results {
		z-index: 1050; /* Pastikan dropdown muncul di atas modal */
	}
	.line-clamp-1 {
		display: -webkit-box;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
	.alias-box {
            width: 5rem;
            height: 5rem;
            background-color: #00000012; /* Warna latar belakang kotak */
            color: rgb(127, 127, 127); /* Warna teks */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px; /* Ukuran font */
            border-radius: 4px; /* Sudut melengkung (opsional) */
            text-align: center;
        }
</style>
@endsection
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('spatial_data_access')
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="detailModalLabel">Detail Data</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- Konten akan dimuat di sini -->
					<div id="infoLahan" class="mb-5"></div>
					<div id="kemitraanAktif" class="mb-5"></div>
					<div id="historyKemitraan" class="mb-5"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalMultiUpload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Unggah Berkas</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModal">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route('2024.spatial.storesingle') }}" enctype="multipart/form-data" method="POST" id="formSubmit">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<div class="input-group bg-white shadow-inset-2">
								<div class="input-group-prepend">
									<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
										<i class="fal fa-upload"></i>
									</span>
								</div>
								<div class="custom-file">
									<input type="file" accept=".kml" id="kml_file" name="kml_url[]" class="custom-file-input" multiple required style="display: none;">
    								<label for="kml_file" class="custom-file-label line-clamp-1">Pilih berkas KML...</label>
								</div>
							</div>
							<span class="help-block" id="fileCount" style="display: none;">Unggah berkas KML <span class="text-danger">*</span></span>
						</div>
						<div hidden>
							<div class="form-group row">
								<label for="kode_spatial" class="col-sm-3 col-form-label">Kode Spatial</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="kode_spatial" name="kode_spatial" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="latitude" class="col-sm-3 col-form-label">Latitude</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="latitude" name="latitude" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="longitude" class="col-sm-3 col-form-label">Longitude</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="longitude" name="longitude" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="polygon" class="col-sm-3 col-form-label">Polygon</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="polygon" name="polygon" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="altitude" class="col-sm-3 col-form-label">Altitude (mdpl)</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="altitude" name="altitude" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="luas_lahan" class="col-sm-3 col-form-label">Luas Lahan (m2)</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="luas_lahan" name="luas_lahan" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="poktan_name" class="col-sm-3 col-form-label">Kelompok Tani</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="poktan_name" name="poktan_name" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="ktp_petani" class="col-sm-3 col-form-label">NIK Petani</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="ktp_petani" name="ktp_petani" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="nama_petani" class="col-sm-3 col-form-label">Nama Petani</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="nama_petani" name="nama_petani" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="provinsi_nama" class="col-sm-3 col-form-label">Provinsi</label>
								<div class="col-sm-9">
									<input class="form-control" id="provinsi_id" name="provinsi_id" value="">
								</div>
							</div>
							<div class="form-group row">
								<label for="kabupaten_nama" class="col-sm-3 col-form-label">Kabupaten</label>
								<div class="col-sm-9">
									<input class="form-control" id="kabupaten_id" name="kabupaten_id" value="">
								</div>
							</div>
							<div class="form-group row">
								<label for="kecamatan_nama" class="col-sm-3 col-form-label">Kecamatan</label>
								<div class="col-sm-9">
									<input class="form-control" id="kecamatan_id" name="kecamatan_id" value="">
								</div>
							</div>
							<div class="form-group row">
								<label for="kelurahan_nama" class="col-sm-3 col-form-label">Kelurahan</label>
								<div class="col-sm-9">
									<input class="form-control" id="kelurahan_id" name="kelurahan_id" value="">
								</div>
							</div>
						</div>
						{{-- style="display: none;" --}}

						<div id="progressContainer" style="display: none;">
							<span>Mengunggah: <span id="fileName"></span></span>
							<div class="progress">
								<div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<p id="progressText">0%</p>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeBtn">Close</button>
						<button type="button" class="btn btn-primary" id="uploadBtn">Save</button>
					</div>
				</form>


			</div>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>
						Daftar <span class="fw-300"><i>Lahan</i></span>
					</h2>
					<div class="panel-toolbar">
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn-primary waves-effect waves-themed">
								<i class="fal fa-plus mr-1"></i>
								Peta Baru
							</button>
							<button type="button" class="btn btn-xs btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-themed" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								<i class="fal fa-chevron-down"></i>
							</button>
							<div class="dropdown-menu" style="">
								<a href="{{route('2024.spatial.createsingle')}}" onclick="navigateBack();" class="dropdown-item" title="import berkas KML peta satu persatu">
									<i class="fal fa-plus"></i>
									Impor Peta Tunggal
								</a>
								<a href="javascript:void(0);" class="dropdown-item" data-toggle="modal" title="Import berkas KML sekaligus" data-target="#modalMultiUpload">
									<i class="fal fa-layer-plus"></i>
									Impor Peta Jamak
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="card-header">
					<div class="panel-content">
						<h4 class="text-muted">Pencarian Data</h4>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="idKab">Kabupaten</label>
								<select name="idKab" id="idKab" class="custom-select form-control" aria-describedby="helpKab">
									<option value="" hidden>pilih kabupaten</option>
									<option value="" >Semua kabupaten</option>
									@foreach ($indexKabupaten as $kabupaten)
									<option value="{{$kabupaten['kabupaten_id']}}">{{$kabupaten['kabupaten_id']}} - {{$kabupaten['nama_kab']}}</option>
									@endforeach
								</select>
								<small id="helpKab" class="text-muted">saring data sesuai kabupaten dipilih</small>
							</div>
							<div class="form-group col-md-3">
								<label for="status_lahan">Status Lahan</label>
								<select name="status_lahan" id="status_lahan" class="custom-select form-control" aria-describedby="helpStatus">
									<option value="" hidden>pilih status</option>
									<option value="" >Semua status</option>
									<option value="1">Aktif</option>
									<option value="0">Tidak Aktif</option>
								</select>
								<small id="helpStatus" class="text-muted">saring data sesuai status lahan</small>
							</div>
							<div class="form-group col-md-3">
								<label for="status_mitra">Status Kemitraan</label>
								<select name="status_mitra" id="status_mitra" class="custom-select form-control" aria-describedby="helpMitra">
									<option value="" hidden>pilih status</option>
									<option value="" >Semua status</option>
									<option value="1">Bermitra</option>
									<option value="0">Tanpa mitra</option>
								</select>
								<small id="helpMitra" class="text-muted">saring data sesuai status kemitraan</small>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-12">
								<label class="form-label">Cari data</label>
								<div class="input-group bg-white shadow-inset-2">
									<div class="input-group-prepend">
										<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
											<i class="fal fa-search"></i>
										</span>
									</div>
									<input type="text" name="searchValue" id="searchValue" aria-describedby="searchValue" class="form-control border-left-0 bg-transparent pl-0" placeholder="kata kunci...">
									<div class="input-group-append">
										<button class="btn btn-default waves-effect waves-themed" type="button">Temukan</button>
									</div>
								</div>
								<small for="searchValue" class="text-muted">Temukan data berdasarkan Kode Lokasi, Kabupaten, Nama Pengelola/Petani</small>
							</div>
						</div>
						<div class="d-flex justify-content-between align-items-center">
							<div></div>
							<div class="ml-auto">
								<button id="printSpatial" class="btn btn-primary">
									<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
    								<span id="buttonText">Cetak Daftar</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div hidden>
				<table id="filesNotProcessedTable" class="table table-bordered">
					<thead>
						<tr>
							<th>Nama File</th>
							<th>Alasan</th>
						</tr>
					</thead>
					<tbody>
						<!-- Data akan dimasukkan di sini -->
					</tbody>
				</table>
			</div>
			<table id="tblSpatial" class="table table-sm table-light w-100 mb-5">
				<thead class="thead-themed">
					<th></th>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>
@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
@parent

{{-- data --}}
<script>
	$(document).ready(function(){

		$('#printSpatial').on('click', function() {
			// Get the values from the form inputs or elements
			const kabupatenId = $('#idKab').val();
			const statusLahan = $('#status_lahan').val();
			const statusMitra = $('#status_mitra').val();

			// Create the URL with query parameters
			const url = `{{ route('2024.spatial.renderPrintAllSpatials') }}?kabupaten_id=${kabupatenId}&status_lahan=${statusLahan}&status_mitra=${statusMitra}`;

			// Show spinner and change button text
			$('#spinner').show();
			$('#buttonText').text('Mempersiapkan Unduhan');

			// Use AJAX to request the PDF
			$.ajax({
				url: url,
				method: 'GET',
				xhrFields: {
					responseType: 'blob'  // Important for binary file handling
				},
				success: function(data, status, xhr) {
					// Create a URL for the blob and initiate download
					const blob = new Blob([data], { type: 'application/pdf' });
					const link = document.createElement('a');
					link.href = window.URL.createObjectURL(blob);
					link.download = 'daftar_spatials.pdf';  // Specify the filename
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);

					// Show success message with Swal.fire
					Swal.fire({
						icon: 'success',
						title: 'Unduhan Selesai',
						text: 'Berkas PDF berhasil dibuat dan diunduh. Periksa direktori unduhan di perangkat Anda.'
					});
				},
				error: function(xhr, status, error) {
					// Show error message with Swal.fire
					Swal.fire({
						icon: 'error',
						title: 'Gagal Mengunduh',
						text: 'Terjadi kesalahan saat mengunduh file: ' + error
					});
				},
				complete: function() {
					// Hide spinner and revert button text
					$('#spinner').hide();
					$('#buttonText').text('Cetak Daftar');
				}
			});
		});

		$('#filesNotProcessedTable').dataTable({
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row mb-5'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			buttons: [
				{
					extend: 'pdfHtml5',
					text: '<i class="fa fa-file-pdf"></i>',
					title: 'Daftar Lahan Wajib Tanam Produksi Bawang Putih',
					titleAttr: 'Generate PDF',
					className: 'btn-outline-danger btn-sm btn-icon mr-1'
				},
				{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel"></i>',
					title: 'Daftar Lahan Wajib Tanam Produksi Bawang Putih',
					titleAttr: 'Generate Excel',
					className: 'btn-outline-success btn-sm btn-icon mr-1'
				},
				{
					extend: 'print',
					text: '<i class="fa fa-print"></i>',
					title: 'Daftar Lahan Wajib Tanam Produksi Bawang Putih',
					titleAttr: 'Print Table',
					className: 'btn-outline-primary btn-sm btn-icon mr-1'
				}
			],
		});

		$('#tblSpatial').dataTable({
			responsive: true,
			lengthChange: false,
			processing: true,
			serverSide: true,
			language: {
				"processing": "Sedang memproses...",
				"lengthMenu": "Tampilkan _MENU_ entri",
				"zeroRecords": "Tidak ditemukan data yang sesuai",
				"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
				"infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
				"infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
				"search": "Cari:",
				"paginate": {
					"first": "Pertama",
					"last": "Terakhir",
					"next": "Berikutnya",
					"previous": "Sebelumnya"
				},
				"emptyTable": "Tidak ada data di dalam tabel",
				"loadingRecords": "Sedang memuat...",
				"thousands": ".",
				"decimal": ",",
				"aria": {
					"sortAscending": ": aktifkan untuk mengurutkan kolom naik",
					"sortDescending": ": aktifkan untuk mengurutkan kolom turun"
				}
			},
			ajax: {
				url: "{{ route('2024.datafeeder.getAllSpatials') }}",
				type: "GET",
				data: function(d) {
					d.kabupaten_id = $('#idKab').val();
					d.status_lahan = $('#status_lahan').val();
					d.status_mitra = $('#status_mitra').val();
					d.searchValue = $('#searchValue').val();
					console.log('intip: ', d);
				},
			},
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row mb-5'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

			columns: [
				{
					data: 'kode_spatial',
					render: function(data, type, row) {
						var kdSpatial = row.kode_spatial;
						var anggota = row.nama_anggota;
						var anggotaSuffix = '??';

						// Check if anggota is null or an empty string
						if (anggota && anggota.trim() !== '') {
							// Split the 'anggota' string into words
							var words = anggota.split(' ');

							if (words.length === 1) {
								// If there is only one word, take the first 2 characters
								anggotaSuffix = words[0].substring(0, 2);
							} else if (words.length >= 2) {
								// If there are 2 or more words, take the first character of the first two words
								var firstWord = words[0];
								var secondWord = words[1];
								anggotaSuffix = firstWord.charAt(0) + secondWord.charAt(0);
							}
						}
						var rawLuas = row.luas_lahan;
						var luas = rawLuas.toLocaleString('en-ID') + ' m2';
						var kabId = row.kabupaten_id;
						var namaKab = row.nama_kabupaten;
						var kode = kdSpatial.replace(/[^a-zA-Z0-9]/g, '');
						var statusCheck = row.status == 1 ? 'checked' : '';
						var activeCheck = row.active == 1 ? 'checked' : '';

						var url = "{{ route('2024.spatial.edit', ':kode') }}";
						var kmlFile = row.kml_url;
						var kmlPath = `{{ asset('storage') }}/${kmlFile}`;
						url = url.replace(':kode', kode);

						var listCard = `
							<div class="panel" id="panel-1">
								<div class="panel-container show">
									<div class="panel-content">
										<div class="d-flex flex-row pb-3 pt-2  border-top-0 border-left-0 border-right-0">
                                            <div class="d-inline-block align-middle mr-3">
                                                <div class="alias-box">
													<span class="display-4 fw-700">${anggotaSuffix}</span>
												</div>
                                            </div>
                                            <h5 class="mb-0 flex-1 text-dark fw-700 text-lg">
                                               	${kdSpatial}
                                                <small class="m-0 l-h-n">
                                                    <div >
														<div class="form-group mb-1 mt-2">
															<span class="uppercase">${anggota}</span>
														</div>
														<div class="form-group mb-1"><span>${luas}</span>
														</div>
														<div class="form-group mb-1"><span class="uppercase">${namaKab}</span>
														</div>
												</div>
                                                </small>
                                            </h5>
                                            <span>
                                                <div class="d-inline-flex flex-column">
													<div class="custom-control custom-checkbox">
														<input type="checkbox" class="custom-control-input form-control-sm active-switch" id="activeCheck_${kode}" ${activeCheck} data-kode="${kdSpatial}">
														<label class="custom-control-label" for="activeCheck_${kode}">Aktif</label>
													</div>
													<div class="custom-control custom-checkbox">
														<input type="checkbox" class="custom-control-input form-control-sm status-switch" id="customSwitch_${kode}" ${statusCheck} data-kode="${kdSpatial}">
														<label class="custom-control-label" for="customSwitch_${kode}"><span>Bermitra</span></label>
													</div>
												</div>
                                            </span>
                                        </div>
									</div>
									<div class="card-footer text-right">
										<a href="${url}" class="btn btn-sm btn-info waves-effect waves-themed" data-toggle="tooltip" data-offset="0,010" title data-original-title="Lihat Peta">
											<i class="fal fa-edit"></i> Lihat/Perbarui Peta
										</a>
										<a href="${kmlPath}" class="btn btn-sm btn-success waves-effect waves-themed ml-1" data-toggle="tooltip" data-offset="0,010" data-original-title="Unduh peta" download>
											<i class="fal fa-download"></i> Unduh Peta
										</a>
									</div>
								</div>
							</div>
							`;
						return listCard;
					}
				}
			],
			buttons: [
				{
					extend: 'pdfHtml5',
					text: '<i class="fa fa-file-pdf"></i>',
					title: 'Daftar Lahan Wajib Tanam Produksi Bawang Putih',
					titleAttr: 'Generate PDF',
					className: 'btn-outline-danger btn-sm btn-icon mr-1'
				},
				{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel"></i>',
					title: 'Daftar Lahan Wajib Tanam Produksi Bawang Putih',
					titleAttr: 'Generate Excel',
					className: 'btn-outline-success btn-sm btn-icon mr-1'
				},
				{
					extend: 'print',
					text: '<i class="fa fa-print"></i>',
					title: 'Daftar Lahan Wajib Tanam Produksi Bawang Putih',
					titleAttr: 'Print Table',
					className: 'btn-outline-primary btn-sm btn-icon mr-1'
				}
			],
			drawCallback: function(settings) {
				$('#tblSpatial thead').hide();
			}
		});

		$('#searchValue, #idKab, #status_lahan, #status_mitra').change(function() {
			console.log('Kabupaten ID:', $('#idKab').val());  // Logging nilai kabupaten_id
			console.log('Status Lahan:', $('#status_lahan').val());  // Logging nilai status_lahan
			console.log('Status Mitra:', $('#status_mitra').val());  // Logging nilai status_mitra
			$('#tblSpatial').DataTable().draw();
		});
	});

	$(document).on('change', '.status-switch', function() {
		var kode = $(this).data('kode');
		var status = $(this).is(':checked') ? 1 : 0;

		$.ajax({
			url: "{{ route('2024.spatial.updateStatus', ':kode') }}".replace(':kode', kode),
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				status: status
			},
			success: function(response) {
				if (response.success) {
					$('#tblSpatial').DataTable().ajax.reload();
					var action = status ? 'Bermitra' : 'Tersedia';
					var message = `Lokasi ${kode} ${action}`;
					Swal.fire({
						icon: 'success',
						title: 'Status Kemitraan',
						text: message,
						timer: 2000,
						showConfirmButton: false
					});
				} else {
					$('#tblSpatial').DataTable().ajax.reload();
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: response.message || 'Failed to update status.',
						showConfirmButton: true
					});
				}
			},
			error: function(xhr) {
				var errorMessage = 'Error updating status.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					errorMessage = xhr.responseJSON.message;
				}
				$('#tblSpatial').DataTable().ajax.reload();
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: errorMessage,
					showConfirmButton: true
				});
			}
		});
	});

	$(document).on('change', '.active-switch', function() {
		var kode = $(this).data('kode');
		var activeStatus = $(this).is(':checked') ? 1 : 0;

		console.log('Switch clicked for kode:', kode);
		console.log('New status:', activeStatus);

		$.ajax({
			url: "{{ route('2024.spatial.updateActive', ':kode') }}".replace(':kode', kode),
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				activeStatus: activeStatus
			},
			success: function(response) {
				if(response.success) {
					// If the request was successful, update the DataTable and show a success message
					$('#tblSpatial').DataTable().ajax.reload();
					var action = activeStatus ? 'Diaktifkan' : 'Diblokir';
					var message = `Lokasi ${kode} ${action}`;
					Swal.fire({
						icon: 'success',
						title: 'Aktivasi Lokasi',
						text: message,
						timer: 2000,
						showConfirmButton: false
					});
				} else {
					// If the request was not successful, check the response message
					$('#tblSpatial').DataTable().ajax.reload();
					Swal.fire({
						icon: 'error',
						title: 'Gagal Mengubah Status',
						text: response.message || 'Failed to update status.',
						timer: 3000,
						showConfirmButton: false
					});
				}
			},
			error: function(xhr) {
				// On error, display an alert with the error message
				$('#tblSpatial').DataTable().ajax.reload();
				Swal.fire({
					icon: 'error',
					title: 'Kesalahan',
					text: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error updating status.',
					timer: 3000,
					showConfirmButton: false
				});
			}
		});
	});

</script>

{{-- peta --}}
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

	document.getElementById('kml_file').addEventListener('change', function() {
		const fileCount = this.files.length;
		const fileCountElement = document.getElementById('fileCount');
		const fileLabel = document.querySelector('label[for="kml_file"]');

		if (fileCount > 0) {
			fileLabel.textContent = `${fileCount} berkas terpilih`;
			fileCountElement.textContent = `${fileCount} file dipilih`;
			fileCountElement.style.display = 'block';
		} else {
			fileLabel.textContent = 'Pilih berkas KML...';
			fileCountElement.style.display = 'none';
		}
	});
</script>
<script>
	// window.addEventListener('beforeunload', function (e) {
	// 	e.preventDefault();
	// 	e.returnValue = ''; // Ini akan menampilkan peringatan di browser
	// });

	// window.removeEventListener('beforeunload', function (e) {
	// 	e.preventDefault();
	// 	e.returnValue = '';
	// });
</script>
<script>
	//penampung data file
	var filesNotProcessed = [];

	document.getElementById('uploadBtn').addEventListener('click', function () {
		let kmlFiles = document.getElementById('kml_file').files;
		if (kmlFiles.length > 0) {
			disableElementsDuringUpload();
			processFiles(kmlFiles);
		} else {
			alert('Pilih setidaknya satu file.');
		}
	});

	function processFiles(files) {
		let index = 0;
		const totalFiles = files.length;

		// Tampilkan progress bar
		document.getElementById('progressContainer').style.display = 'block';
		document.getElementById('progressText').textContent = `Mengunggah 0 of ${totalFiles} berkas...`;

		function processNextFile() {
			if (index < totalFiles) {
				let file = files[index];
				kml_parser(file, (success) => {
					if (success) {
						uploadFile(file, () => {
							index++;
							updateProgress(index, totalFiles);
							processNextFile();
						});
					} else {
						filesNotProcessed.push(file.name);
						index++;
						updateProgress(index, totalFiles);
						processNextFile();
					}
				});
			} else {
				// Semua file telah diproses
				enableElementsAfterUpload();
				//tutup modal modalMultiUpload
				closeModalAndShowAlert();
				// Optional: Refresh halaman setelah konfirmasi
				// location.reload();
			}
		}

		processNextFile();
	}

	function updateProgress(uploadedFiles, totalFiles) {
		let percentComplete = Math.round((uploadedFiles / totalFiles) * 100);
		const progressBar = document.getElementById('progressBar');

		// Update progress bar width and aria-valuenow attribute
		progressBar.style.width = percentComplete + '%';
		progressBar.setAttribute('aria-valuenow', percentComplete);

		document.getElementById('progressText').textContent = `Mengunggah ${uploadedFiles} dari ${totalFiles} berkas (${percentComplete}%)`;
	}

	function kml_parser(kmlFile, callback) {
		const reader = new FileReader();

		reader.onload = (event) => {
			const kmlData = event.target.result;
			const parser = new DOMParser();
			const kmlXml = parser.parseFromString(kmlData, "application/xml");

			const placemark = kmlXml.getElementsByTagName("Placemark")[0];
			if (placemark) {
				// Ambil data dari KML
				const id_lahan = placemark.querySelector("SimpleData[name='ID_LAHAN']")?.textContent;
				const nikPetani = placemark.querySelector("SimpleData[name='NIK']")?.textContent;
				const petani = placemark.querySelector("SimpleData[name='PETANI']")?.textContent;
				const luas = placemark.querySelector("SimpleData[name='LUAS_LAHAN']")?.textContent;
				const x = parseFloat(placemark.querySelector("SimpleData[name='LATITUDE']")?.textContent);
				const y = parseFloat(placemark.querySelector("SimpleData[name='LONGITUDE']")?.textContent);
				const altitude = parseFloat(placemark.querySelector("SimpleData[name='ALTITUDE']")?.textContent);
				const desa_id = placemark.querySelector("SimpleData[name='ID_DESA']")?.textContent;
				const poktanName = placemark.querySelector("SimpleData[name='POKTAN']")?.textContent;

				// Track missing fields
				let missingFields = [];
				if (!id_lahan) missingFields.push('ID_LAHAN');
				if (!nikPetani) missingFields.push('NIK');
				if (!petani) missingFields.push('PETANI');
				if (!luas) missingFields.push('LUAS_LAHAN');
				if (isNaN(x)) missingFields.push('LATITUDE');
				if (isNaN(y)) missingFields.push('LONGITUDE');
				if (isNaN(altitude)) missingFields.push('ALTITUDE');
				if (!desa_id) missingFields.push('ID_DESA');
				if (!poktanName) missingFields.push('POKTAN');

				if (missingFields.length > 0) {
					// File is not processed due to missing or invalid fields
					filesNotProcessed.push(`${kmlFile.name}: Missing/Invalid fields - ${missingFields.join(', ')}`);
					callback(false); // Data tidak lengkap
					return;
				}

				const kecamatan_id = desa_id.substring(0, 7);
				const kabupaten_id = desa_id.substring(0, 4);
				const provinsi_id = desa_id.substring(0, 2);

				// Extract Polygon coordinates
				const coordinates = placemark.querySelector("Polygon > outerBoundaryIs > LinearRing > coordinates")?.textContent.trim();
				const polygonArray = coordinates?.split(' ').map(coord => {
					const [lng, lat] = coord.split(',').map(Number);
					return { lat, lng };
				}) || [];

				// Isi form dengan data yang diekstrak
				document.getElementById("kode_spatial").value = id_lahan;
				document.getElementById("ktp_petani").value = nikPetani;
				document.getElementById("nama_petani").value = petani;
				document.getElementById("luas_lahan").value = luas;
				document.getElementById("poktan_name").value = poktanName;
				document.getElementById("latitude").value = x;
				document.getElementById("longitude").value = y;
				document.getElementById("altitude").value = altitude;
				document.getElementById("kelurahan_id").value = desa_id;
				document.getElementById("kecamatan_id").value = kecamatan_id;
				document.getElementById("kabupaten_id").value = kabupaten_id;
				document.getElementById("provinsi_id").value = provinsi_id;
				document.getElementById("polygon").value = JSON.stringify(polygonArray);

				callback(true); // Data lengkap
			} else {
				// No Placemark found in KML
				filesNotProcessed.push(`${kmlFile.name}: Tidak ditemukan data Placemark dalam file KML.`);
				callback(false); // Data tidak lengkap
			}
		};

		reader.readAsText(kmlFile);
	}

	function uploadFile(kmlFile, callback) {
        let formData = new FormData();
        formData.append('kml_url', kmlFile);
        formData.append('kode_spatial', document.getElementById('kode_spatial').value);
        formData.append('latitude', document.getElementById('latitude').value);
        formData.append('longitude', document.getElementById('longitude').value);
        formData.append('polygon', document.getElementById('polygon').value);
        formData.append('altitude', document.getElementById('altitude').value);
        formData.append('luas_lahan', document.getElementById('luas_lahan').value);
        formData.append('poktan_name', document.getElementById('poktan_name').value);
        formData.append('ktp_petani', document.getElementById('ktp_petani').value);
        formData.append('nama_petani', document.getElementById('nama_petani').value);
        formData.append('provinsi_id', document.getElementById('provinsi_id').value);
        formData.append('kabupaten_id', document.getElementById('kabupaten_id').value);
        formData.append('kecamatan_id', document.getElementById('kecamatan_id').value);
        formData.append('kelurahan_id', document.getElementById('kelurahan_id').value);

        // Tampilkan progress bar
        document.getElementById('progressContainer').style.display = 'block';

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("2024.spatial.storesingle") }}', true); // Ganti dengan rute Anda
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}'); // Sertakan token CSRF

        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                let percentComplete = Math.round((event.loaded / event.total) * 100);
                document.getElementById('progressBar').style.width = percentComplete + '%';
                document.getElementById('progressText').textContent = percentComplete + '%';
            }
        };

        xhr.onload = function () {
            if (xhr.status === 200) {
                callback();
            } else {
                alert('Gagal mengunggah file: ' + kmlFile.name);
                callback();
            }
        };

        xhr.send(formData);
    }

	function displayFilesNotProcessed() {
		// Get the table and tbody elements
		const table = document.getElementById('filesNotProcessedTable');
		const tbody = table.querySelector('tbody');

		// Clear any existing rows in the tbody
		tbody.innerHTML = '';

		// Populate the table with the filesNotProcessed array
		filesNotProcessed.forEach(fileName => {
			const row = document.createElement('tr');

			const nameCell = document.createElement('td');
			nameCell.textContent = fileName;
			row.appendChild(nameCell);

			const reasonCell = document.createElement('td');
			reasonCell.textContent = 'File tidak lengkap atau gagal diproses'; // Add a reason or message
			row.appendChild(reasonCell);

			tbody.appendChild(row);
		});

		// Display the table
		table.style.display = 'table';
	}

	function closeModalAndShowAlert() {
		$('#modalMultiUpload').modal('hide');

		// Show the Swalfire alert
		showSwalAlert();
	}

	function showSwalAlert() {
		const message = filesNotProcessed.length > 0
			? `Beberapa file tidak diproses. Laporan akan disimpan dalam berkas .txt setelah Anda menekan tombol OK.`
			: `Semua file telah berhasil diproses.`;

		Swal.fire({
			title: 'Proses Selesai',
			text: message,
			icon: filesNotProcessed.length > 0 ? 'warning' : 'success',
			confirmButtonText: 'OK',
			allowOutsideClick: false,
		}).then(() => {
			if (filesNotProcessed.length > 0) {
				saveFilesNotProcessedToFile();
				location.reload();
			}
		});
	}

	function saveFilesNotProcessedToFile() {
		const blob = new Blob([filesNotProcessed.join('\n')], { type: 'text/plain' });
		const url = URL.createObjectURL(blob);

		const a = document.createElement('a');
		a.href = url;
		a.download = 'files_not_processed.txt';
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);

		// Revoke the object URL to free up memory
		URL.revokeObjectURL(url);
	}


    function disableElementsDuringUpload() {
        document.getElementById('uploadBtn').disabled = true;
        document.getElementById('closeBtn').disabled = true;
        document.getElementById('closeModal').disabled = true;

        document.getElementById('kml_file').disabled = true;
    }

    function enableElementsAfterUpload() {
        document.getElementById('uploadBtn').disabled = false;
        document.getElementById('closeBtn').disabled = false;
        document.getElementById('closeModal').disabled = false;

        document.getElementById('kml_file').disabled = false;
    }
</script>




@endsection

{{-- {{ route('admin.task.commitments.pksmitra', $commitment->id) }} --}}
