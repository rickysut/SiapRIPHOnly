@extends('t2024.layouts.admin')
@section('content')

{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('spatial_data_access')

	<div class="row">
		<div class="col">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>
						Daftar <span class="fw-300"><i>Wilayah</i></span>
					</h2>
					<div class="panel-toolbar">
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn-primary waves-effect waves-themed" title data-toggle="tooltip" data-offset="0,10" data-original-title="Sinkronisasi data wilayah dari BPS">
								<i class="fal fa-undo mr-1"></i>
								Pembaruan Data
							</button>
							<button type="button" class="btn btn-xs btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-themed" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								<i class="fal fa-chevron-down"></i>
							</button>
							<div class="dropdown-menu" style="">
								<a class="dropdown-item btnUpdateProv" title="Penyelarasan data Provinsi dengan data BPS">
									Perbarui Data Provinsi
								</a>
								<a onclick="startProcessKab()" class="dropdown-item" title="Penyelarasan data Kabupaten dengan data BPS">
									Perbarui Data Kabupaten
								</a>
								<a onclick="startProcessKec()"  class="dropdown-item" title="Penyelarasan data Kecamatan dengan data BPS">
									Perbarui Data Kecamatan
								</a>
								<button onclick="startProcessDesa()" class="dropdown-item" title="Penyelarasan data Desa dengan data BPS">
									Perbarui Data Desa
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-header">
					<div class="panel-content">
						<h4 class="text-muted">Penyaringan Data</h4>
						<div class="row justify-content-between">
							<div class="form-group col-lg-4">
								<label class="form-label" for="provinsi_id">
									Provinsi
								</label>
								<select class="select2 form-control w-100" id="provinsi_id" name="provinsi_id">
									<option value=""></option>
								</select>
							</div>
							<div class="form-group col-lg-4">
								<label class="form-label" for="kabupaten_id">
									Kabupaten
								</label>
								<select class="select2 form-control w-100" id="kabupaten_id" name="kabupaten_id">
									<option value=""></option>
								</select>
							</div>
							<div class="form-group col-lg-4">
								<label class="form-label" for="kecamatan_id">
									Kecamatan
								</label>
								<select class="select2 form-control w-100" id="kecamatan_id" name="kecamatan_id">
									<option value=""></option>
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="panel-container show">
					<div class="panel-content" id="panelProv">
						<!-- datatable start -->
						<table id="tblWilayah" class="table table-bordered table-hover table-sm table-striped w-100">
							<thead class="thead-themed">
								<th style="width:5%">Kode Wilayah</th>
								<th style="width:25%">Nama Wilayah</th>
								<th>Tindakan</th>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
	function startProcessKab() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Pembaruan data Kabupaten secara Nasional akan berjalan LAMA dan tidak dapat dihentikan sebelum selesai!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, mulai proses!',
            preConfirm: async () => {
                // Menampilkan dialog kedua dan menyimpan referensi tombol
                const { value: result } = await Swal.fire({
                    title: 'Proses Pembaruan Data',
                    html: `
                        <p class="small">Pembaruan data kabupaten di seluruh provinsi terdaftar di data BPS.</p>
                        <div id="status">Proses: </div>
                        <div class="progress progress-lg">
                            <div id="progressBar" class="progress-bar progress-bar-striped bg-primary progress-bar-animated" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div id="progress">Progress: 0%</div>
                            <div id="waitTime">Menunggu...</div>
                        </div>
                        <button id="btnUpdateKab" class="btn btn-warning btnUpdateKab waves-effect waves-themed">Mulai Pembaruan</button>
                    `,
                    showConfirmButton: false,
                    didOpen: () => {
                        const btnUpdateKab = document.getElementById('btnUpdateKab');
                        btnUpdateKab.addEventListener('click', async () => {
                            // Nonaktifkan tombol
                            btnUpdateKab.disabled = true;
                            btnUpdateKab.textContent = 'Sedang Memproses...';
                            await updateAllKab();
                            // Mengupdate tombol setelah proses selesai
                            Swal.getContent().querySelector('#btnUpdateKab').textContent = 'Selesai';
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                });
                return result;
            },
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: 'Proses pembaruan Data Kabupaten selesai.',
                    icon: 'success'
                });
            }
        });
    }

    async function updateAllKab() {
        try {
            const response = await axios.get('/2024/spatial/master-wilayah/updateAllDesaFromBPS');
            const provinsiIds = response.data.provinsiIds;
            if (!provinsiIds.length) {
                Swal.fire('Tidak ada provinsi untuk diperbarui.', '', 'info');
                return;
            }

            let progressElement = document.getElementById('progress');
            let progressBarElement = document.getElementById('progressBar');
            let statusElement = document.getElementById('status');
            let waitTimeElement = document.getElementById('waitTime');

            for (let i = 0; i < provinsiIds.length; i++) {
                let provinsiId = provinsiIds[i];
                statusElement.innerText = `Memperbarui provinsi ID ${provinsiId}...`;

                let updateResponse = await axios.get(`/2024/spatial/master-wilayah/updateKabupatenFromBPS/${provinsiId}`);
                let updateResult = updateResponse.data;

                if (updateResult.success) {
                    statusElement.innerText = `Sukses: ${updateResult.message}`;
                } else {
                    statusElement.innerText = `Gagal: ${updateResult.message}`;
                }

                let progress = Math.round(((i + 1) / provinsiIds.length) * 100);
                progressElement.innerText = `Progress: ${progress}%`;
                progressBarElement.style.width = `${progress}%`;
                progressBarElement.setAttribute('aria-valuenow', progress);

                if (i < provinsiIds.length - 1) {
                    let waitTime = 60;
                    while (waitTime > 0) {
                        waitTimeElement.innerText = `Menunggu ${waitTime} detik sebelum melanjutkan...`;
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        waitTime--;
                    }
                    waitTimeElement.innerText = 'Menunggu selesai. Melanjutkan...';
                }
            }

            statusElement.innerText = 'Proses pembaruan selesai.';
            progressBarElement.classList.remove('bg-primary');
            progressBarElement.classList.add('bg-success');
        } catch (error) {
            console.error('Terjadi kesalahan:', error);
            Swal.fire('Terjadi kesalahan saat memperbarui data.', '', 'error');
        }
    }


    function startProcessKec() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Pembaruan data Kecamatan secara Nasional akan berjalan LEBIH LAMA dan tidak dapat dihentikan sebelum selesai!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, mulai proses!',
            preConfirm: async () => {
                // Menampilkan dialog kedua dan menyimpan referensi tombol
                const { value: result } = await Swal.fire({
                    title: 'Proses Pembaruan Data',
                    html: `
                        <p class="small">Pembaruan data Kecamatan di seluruh provinsi terdaftar di data BPS.</p>
                        <div id="status">Proses: </div>
                        <div class="progress progress-lg">
                            <div id="progressBar" class="progress-bar progress-bar-striped bg-primary progress-bar-animated" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div id="progress">Progress: 0%</div>
                            <div id="waitTime">Menunggu...</div>
                        </div>
                        <button id="btnUpdateKec" class="btn btn-warning btnUpdateKec waves-effect waves-themed">Mulai Pembaruan</button>
                    `,
                    showConfirmButton: false,
                    didOpen: () => {
                        const btnUpdateKec = document.getElementById('btnUpdateKec');
                        btnUpdateKec.addEventListener('click', async () => {
                            // Nonaktifkan tombol
                            btnUpdateKec.disabled = true;
                            btnUpdateKec.textContent = 'Sedang Memproses...';
                            await updateAllKec();
                            // Mengupdate tombol setelah proses selesai
                            Swal.getContent().querySelector('#btnUpdateKec').textContent = 'Selesai';
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                });
                return result;
            },
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: 'Proses pembaruan Data Kabupaten selesai.',
                    icon: 'success'
                });
            }
        });
    }

    async function updateAllKec() {
        try {
            const response = await axios.get('/2024/spatial/master-wilayah/updateAllDesaFromBPS');
            const provinsiIds = response.data.provinsiIds;
            if (!provinsiIds.length) {
                Swal.fire('Tidak ada provinsi untuk diperbarui.', '', 'info');
                return;
            }

            let progressElement = document.getElementById('progress');
            let progressBarElement = document.getElementById('progressBar');
            let statusElement = document.getElementById('status');
            let waitTimeElement = document.getElementById('waitTime');

            for (let i = 0; i < provinsiIds.length; i++) {
                let provinsiId = provinsiIds[i];
                statusElement.innerText = `Memperbarui Kecamatan dengan provinsi ID ${provinsiId}...`;

                let updateResponse = await axios.get(`/2024/spatial/master-wilayah/updateKecamatanFromBPS/${provinsiId}`);
                let updateResult = updateResponse.data;

                if (updateResult.success) {
                    statusElement.innerText = `Sukses: ${updateResult.message}`;
                } else {
                    statusElement.innerText = `Gagal: ${updateResult.message}`;
                }

                let progress = Math.round(((i + 1) / provinsiIds.length) * 100);
                progressElement.innerText = `Progress: ${progress}%`;
                progressBarElement.style.width = `${progress}%`;
                progressBarElement.setAttribute('aria-valuenow', progress);

                if (i < provinsiIds.length - 1) {
                    let waitTime = 60;
                    while (waitTime > 0) {
                        waitTimeElement.innerText = `Menunggu ${waitTime} detik sebelum melanjutkan...`;
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        waitTime--;
                    }
                    waitTimeElement.innerText = 'Menunggu selesai. Melanjutkan...';
                }
            }

            statusElement.innerText = 'Proses pembaruan selesai.';
            progressBarElement.classList.remove('bg-primary');
            progressBarElement.classList.add('bg-success');
        } catch (error) {
            console.error('Terjadi kesalahan:', error);
            Swal.fire('Terjadi kesalahan saat memperbarui data.', '', 'error');
        }
    }

    function startProcessDesa() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Pembaruan data Desa secara Nasional akan berjalan SANGAT LAMA dan tidak dapat dihentikan sebelum selesai!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, mulai proses!',
            preConfirm: async () => {
                // Menampilkan dialog kedua dan menyimpan referensi tombol
                const { value: result } = await Swal.fire({
                    title: 'Proses Pembaruan Data',
                    html: `
                        <p class="small">Pembaruan data desa di seluruh provinsi terdaftar di data BPS.</p>
                        <div id="status">Proses: </div>
                        <div class="progress progress-lg">
                            <div id="progressBar" class="progress-bar progress-bar-striped bg-primary progress-bar-animated" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div id="progress">Progress: 0%</div>
                            <div id="waitTime">Menunggu...</div>
                        </div>
                        <button id="btnUpdateDesa" class="btn btn-warning btnUpdateDesa waves-effect waves-themed">Mulai Pembaruan</button>
                    `,
                    showConfirmButton: false,
                    didOpen: () => {
                        const btnUpdateDesa = document.getElementById('btnUpdateDesa');
                        btnUpdateDesa.addEventListener('click', async () => {
                            // Nonaktifkan tombol
                            btnUpdateDesa.disabled = true;
                            btnUpdateDesa.textContent = 'Sedang Memproses...';
                            await updateAllDesa();
                            // Mengupdate tombol setelah proses selesai
                            Swal.getContent().querySelector('#btnUpdateDesa').textContent = 'Selesai';
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                });
                return result;
            },
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: 'Proses pembaruan Data Desa selesai.',
                    icon: 'success'
                });
            }
        });
    }

    async function updateAllDesa() {
        try {
            const response = await axios.get('/2024/spatial/master-wilayah/updateAllDesaFromBPS');
            const provinsiIds = response.data.provinsiIds;
            if (!provinsiIds.length) {
                Swal.fire('Tidak ada provinsi untuk diperbarui.', '', 'info');
                return;
            }

            let progressElement = document.getElementById('progress');
            let progressBarElement = document.getElementById('progressBar');
            let statusElement = document.getElementById('status');
            let waitTimeElement = document.getElementById('waitTime');

            for (let i = 0; i < provinsiIds.length; i++) {
                let provinsiId = provinsiIds[i];
                statusElement.innerText = `Memperbarui provinsi ID ${provinsiId}...`;

                let updateResponse = await axios.get(`/2024/spatial/master-wilayah/updateDesaFromBPS/${provinsiId}`);
                let updateResult = updateResponse.data;

                if (updateResult.success) {
                    statusElement.innerText = `Sukses: ${updateResult.message}`;
                } else {
                    statusElement.innerText = `Gagal: ${updateResult.message}`;
                }

                let progress = Math.round(((i + 1) / provinsiIds.length) * 100);
                progressElement.innerText = `Progress: ${progress}%`;
                progressBarElement.style.width = `${progress}%`;
                progressBarElement.setAttribute('aria-valuenow', progress);

                if (i < provinsiIds.length - 1) {
                    let waitTime = 60;
                    while (waitTime > 0) {
                        waitTimeElement.innerText = `Menunggu ${waitTime} detik sebelum melanjutkan...`;
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        waitTime--;
                    }
                    waitTimeElement.innerText = 'Menunggu selesai. Melanjutkan...';
                }
            }

            statusElement.innerText = 'Proses pembaruan selesai.';
            progressBarElement.classList.remove('bg-primary');
            progressBarElement.classList.add('bg-success');
        } catch (error) {
            console.error('Terjadi kesalahan:', error);
            Swal.fire('Terjadi kesalahan saat memperbarui data.', '', 'error');
        }
    }
</script>

<script>
	$(document).ready(function() {
		var provinsiSelect = $('#provinsi_id');
		var kabupatenSelect = $('#kabupaten_id');
		var kecamatanSelect = $('#kecamatan_id');

		var urlProvinsi = "{{ route('wilayah.getAllProvinsi') }}";
		var urlKabupaten = function(provinsiId) { return "{{ route('wilayah.getKabupatenByProvinsi', '') }}/" + provinsiId; };
		var urlKecamatan = function(kabupatenId) { return "{{ route('wilayah.getKecamatanByKabupaten', '') }}/" + kabupatenId; };
		var urlDesa = function(kecamatanId) { return "{{ route('wilayah.getDesaByKecamatan', '') }}/" + kecamatanId; };

		var urlUpdateProv = "{{ route('2024.spatial.updateProvinsiFromBPS', '') }}";
		var urlUpdateKab = function(provinsiId) { return "{{ route('2024.spatial.updateKabupatenFromBPS', '') }}/" + provinsiId; };
		var urlUpdateKec = function(provinsiId) { return "{{ route('2024.spatial.updateKecamatanFromBPS', '') }}/" + provinsiId; };
		var urlUpdateDesa = function(provinsiId) { return "{{ route('2024.spatial.updateDesaFromBPS', '') }}/" + provinsiId; };

		var statusBps = 'need update';

		// Inisialisasi Select2
		$("#provinsi_id, #kabupaten_id, #kecamatan_id").select2({
			placeholder: "-- pilih --"
		});

		// Fungsi untuk mengosongkan dan menambahkan placeholder ke Select2
		function resetSelect2(selectElement, placeholderText) {
			selectElement.empty();
			selectElement.append($('<option>', { value: '', text: placeholderText }));
		}

		// Fungsi untuk mengisi data dropdown
		function populateDropdown(url, selectElement, placeholderText, idField, nameField) {
			$.ajax({
				url: url,
				type: "GET",
				dataType: 'json',
				success: function(response) {
					resetSelect2(selectElement, placeholderText);
					$.each(response.data, function(index, item) {
						selectElement.append(new Option(item[nameField], item[idField]));
					});
				},
				error: function(xhr, status, error) {
					console.error('Gagal mengambil data:', status, error);
				}
			});
		}

		// Mengisi dropdown provinsi
		populateDropdown(urlProvinsi, provinsiSelect, '-- pilih provinsi', 'provinsi_id', 'nama');

		// inisiasi table
		var tblWilayah = $('#tblWilayah').DataTable({
			responsive: true,
			lengthChange: false,
			paging: false,
			ordering: true,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			ajax: {
				url: urlProvinsi,
				type: "GET",
				dataSrc: function (json) {
					// Check if status is 'Need Update'
					if (json.status === "Need Update") {
						// Add the button if status is 'Need Update'
						$('.dataTables_wrapper .dt-buttons').append(
							'<button type="button" class="btn btn-danger btn-sm mr-1" id="updateButton"><i class="fa fa-undo"></i> Provinsi perlu diperbarui</button>'
						);
					}
					// Return the data array to be used by DataTables
					return json.data;
				}
			},
			columns: [
				{data: 'provinsi_id', title: 'Kode BPS'},
				{data: 'nama', title: 'Nama BPS (PROVINSI)'},
				{
					data: 'provinsi_id',
					title: 'Tindakan',
					render: function (data, type, row) {
						let url1 = `{{ route('2024.spatial.updateKabupatenFromBPS', ':id') }}`;
						url1 = url1.replace(':id', data);
						let url2 = `{{ route('2024.spatial.updateKecamatanFromBPS', ':id') }}`;
						url2 = url2.replace(':id', data);
						let url3 = `{{ route('2024.spatial.updateDesaFromBPS', ':id') }}`;
						url3 = url3.replace(':id', data);

						return `
							<div class="btn-group">
								<button type="button" class="btn btn-xs btn-primary waves-effect waves-themed" title data-toggle="tooltip" data-offset="0,10" data-original-title="Sinkronisasi data wilayah dari BPS">
									<i class="fal fa-undo mr-1"></i>
									Pembaruan Data
								</button>
								<button type="button" class="btn btn-xs btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-themed" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="sr-only">Toggle Dropdown</span>
									<i class="fal fa-chevron-down"></i>
								</button>
								<div class="dropdown-menu" style="">
									<button onclick="updateData('${url1}')" type="button" class="dropdown-item">Update Kabupaten (di Prov. ${row.nama})</button>
									<button onclick="updateData('${url2}')" type="button" class="dropdown-item">Update Kecamatan (di Prov. ${row.nama})</button>
									<button onclick="updateData('${url3}')" type="button" class="dropdown-item">Update Desa (di Prov. ${row.nama})</button>
								</div>
							</div>
						`;
					}
				}
			],
			buttons: [

			]
		});

		// pembaruan table
		function updateTable(url, idField, nameField, level) {
			tblWilayah.clear().destroy();
			tblWilayah = $('#tblWilayah').DataTable({
				responsive: true,
				lengthChange: false,
				paging: false,
				ordering: true,
				language: {
					"processing": "Sedang memproses...",
					"lengthMenu": "Tampilkan _MENU_ entri",
					"zeroRecords": "Tidak ditemukan data yang sesuai",
					"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
					"infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
					"infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
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
				dom:
					"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
					"<'row'<'col-sm-12'tr>>" +
					"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				ajax: {
					url: url,
					type: "GET",
					dataSrc: "data"
				},
				columns: [
					{ data: idField, title: idField.replace('_', ' ').toUpperCase(), width: '15%' },
					{ data: nameField, title: 'NAMA BPS (' + idField.split('_')[0].toUpperCase() +')'},
					{ data: idField, title: 'Tindakan', width: '20%',
						render: function (data, type, row) {
							return '';
						}
					}
				],
				buttons: [
					{
						extend: 'pdfHtml5',
						text: '<i class="fa fa-file-pdf"></i>',
						title: 'Daftar Wilayah',
						titleAttr: 'Generate PDF',
						className: 'btn-outline-danger btn-sm btn-icon mr-1',
						exportOptions: {
							columns: [0, 1]
						}
					},
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						title: 'Daftar Wilayah',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-sm btn-icon mr-1',
						exportOptions: {
							columns: [0, 1]
						}
					},
					{
						text: '<i class="fa fa-undo"></i>',
						title: 'Reset',
						titleAttr: 'Reset',
						className: 'btn-outline-info btn-sm btn-icon mr-1',
						action: function (e, dt, button, config) {
							location.reload();
						}
					}
				]
			});
		}

		provinsiSelect.change(function() {
			var provinsiId = provinsiSelect.val();
			updateTable(urlKabupaten(provinsiId), 'kabupaten_id', 'nama_kab', 'kabupaten');
			resetSelect2(kabupatenSelect, '-- pilih kabupaten');
			resetSelect2(kecamatanSelect, '-- pilih kecamatan');

			if (provinsiId) {
				populateDropdown(urlKabupaten(provinsiId), kabupatenSelect, '-- pilih kabupaten', 'kabupaten_id', 'nama_kab');
			}
		});

		kabupatenSelect.change(function() {
			var kabupatenId = kabupatenSelect.val();
			console.log('id kabupaten: ', kabupatenId);
			updateTable(urlKecamatan(kabupatenId), 'kecamatan_id', 'nama_kecamatan', 'kecamatan');
			resetSelect2(kecamatanSelect, '-- pilih kecamatan');

			if (kabupatenId) {
				populateDropdown(urlKecamatan(kabupatenId), kecamatanSelect, '-- pilih kecamatan', 'kecamatan_id', 'nama_kecamatan');
			}
		});

		kecamatanSelect.change(function() {
			var kecamatanId = kecamatanSelect.val();
			updateTable(urlDesa(kecamatanId), 'kelurahan_id', 'nama_desa', 'desa');
		});

		$('#tblWilayah').on('click', '.btnUpdate', function() {
			var dataId = $(this).data('id');
			var level = $(this).data('level');
			var updateUrl;
			switch (level) {
				case 'provinsi':
					updateUrl = urlUpdateKab(dataId);
					break;
				case 'kabupaten':
					updateUrl = urlUpdateKec(dataId);
					break;
				case 'kecamatan':
					updateUrl = urlUpdateDesa(dataId);
					break;
				default:
					break;
			}

			if (updateUrl) {
				$.ajax({
					url: updateUrl,
					type: "GET",
					dataType: 'json',
					success: function(response) {
						Swal.fire({
							icon: 'success',
							title: 'Sukses!',
							text: 'Data wilayah berhasil diselaraskan!'
						});
						$('#tblWilayah').DataTable().ajax.reload();
					},
					error: function(xhr, status, error) {
						console.error('Gagal memperbarui data:', status, error);
						Swal.fire({
							icon: 'error',
							title: 'Gagal!',
							text: 'Terjadi kesalahan saat memperbarui data.'
						});
					}
				});
			}
		});

		$('.btnUpdateProv').on('click', function(){
			$.ajax({
				url: urlUpdateProv,
				type: "GET",
				dataType: 'json',
				success: function(response) {
					Swal.fire({
						icon: 'success',
						title: 'Sukses!',
						text: 'Data wilayah berhasil diselaraskan!'
					});
					$('#tblWilayah').DataTable().ajax.reload();
				},
				error: function(xhr, status, error) {
					console.error('Gagal memperbarui data:', status, error);
					Swal.fire({
						icon: 'error',
						title: 'Gagal!',
						text: 'Terjadi kesalahan saat memperbarui data.'
					});
				}
			});
		});
	});

	function updateData(url) {
		fetch(url, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
			},
		})
		.then(response => response.json())
		.then(result => {
			if (result.success) {
				// Use Swal.fire for a success message
				Swal.fire({
					icon: 'success',
					title: 'Sukses',
					text: result.message,
					confirmButtonText: 'OK'
				}).then(() => {
					// Refresh the DataTable after the alert is closed
					$('#tblWilayah').DataTable().ajax.reload();
				});
			} else {
				// Use Swal.fire for an error message
				Swal.fire({
					icon: 'error',
					title: 'Gagal',
					text: result.message,
					confirmButtonText: 'OK'
				}).then(() => {
					// Refresh the DataTable after the alert is closed
					$('#tblWilayah').DataTable().ajax.reload();
				});
			}
		})
		.catch(error => {
			// Use Swal.fire for any unexpected errors
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: error.message,
				confirmButtonText: 'OK'
			});
		});
	}
</script>
@endsection
