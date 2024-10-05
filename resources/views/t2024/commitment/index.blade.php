@extends('t2024.layouts.admin')
@section ('styles')
<style>
td {
	vertical-align: middle !important;
}
</style>
@endsection
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@can('commitment_access')
@include('t2024.partials.sysalert')
<div class="row">
	<div class="col-12">
		<div class="panel" id="panel-1">
			<div class="panel-container show">
				<div class="panel-content">
					<table id="datatable" class="table table-bordered table-hover table-striped table-sm w-100">
						<thead class="thead-themed">
							<th width="15%">No. RIPH</th>
							{{-- <th>Tahun</th>
							<th>Tgl. Terbit</th>
							<th>Vol. RIPH</th>
							<th>Kewajiban</th> --}}
							<th width="15%">Laporan Realisasi</th>
							<th width="20%">Verifikasi Tanam</th>
							<th width="20%">Verifikasi Prod</th>
							<th width="20%">Verifikasi SKL</th>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endcan
@endsection

@section('scripts')
@parent
<script>
</script>


<script>
	$(document).ready(function() {

		var table = $('#datatable').DataTable({
			responsive: true,
			pageLength:10,
			lengthChange: true,
			paging: true,
			ordering: true,
			processing: true,
			serverSide: true,
			order: [[0, 'asc']],
			ajax: {
				url: '{{ route('2024.datafeeder.getAllMyCommitment') }}',
				type: 'GET',
			},

			columnDefs:[
				{
					targets: [1,2,3,4],
					className: "text-center"
				},
				{
					targets: [1,2,3,4],
					orderable: false
				}
			],

			columns: [
				{
					data: 'ijin_full',
					render: function (data, type, row) {
						var noIjin = row.noIjin;
						var url = "{{ route('2024.user.commitment.show', ':noIjin') }}".replace(':noIjin', noIjin);
						return `
							<a href="`+ url + `" title="Lihat Data" class="" target="_blank">
								`+ data +`
							</a>
						`;
					}
				},
				{
					data: 'noIjin',
					render: function (data, type, row) {
						var noIjin = data;
						var avSklStatus = row.avSklStatus;
						var formAvSkl = "{{ route('2024.user.commitment.formavskl', ':noIjin') }}".replace(':noIjin', noIjin);
						var url = "{{ route('2024.user.commitment.realisasi', ':noIjin') }}".replace(':noIjin', noIjin);
						switch (avSklStatus) {
							case '0':
							case '1':
							case '2':
							case '3':
							case '4':
							case '5':
								return `<a href="${formAvSkl}" class="btn btn-xs btn-info" data-toggle="tooltip"
										title data-original-title="Data Pengajuan">
										<i class="fal fa-view"></i> Lihat Data
									</a>`;
							case '6':
								return `<a href="`+ url +`"
									class="btn btn-xs btn-warning" data-toggle="tooltip"
									title data-original-title="Isi Laporan Realisasi Tanam dan Produksi">
									<i class="fal fa-edit"></i> Perbaiki Data Realisasi
								</a>`;
							default:
								return `<a href="`+ url +`"
									class="btn btn-xs btn-warning" data-toggle="tooltip"
									title data-original-title="Isi Laporan Realisasi Tanam dan Produksi">
									<i class="fal fa-edit"></i> Lengkapi Realisasi
								</a>`;
						}
					}
				},
				{
					data: 'siapVerifTanam',
					render: function(data, type, row) {
						var noIjin = row.noIjin;
						var status = data;
						var avTanamStatus = row.avTanamStatus;
						var completeStatus = row.completeStatus;
						var formAvt = "{{ route('2024.user.commitment.formavt', ':noIjin') }}".replace(':noIjin', noIjin);

						if(completeStatus === "Lunas"){
							return `<span class="btn btn-icon btn-xs btn-success">
										<i class="fal fa-check"></i>
									</span>`;
						} else {
							if (status === "Belum Siap") {
								return "Belum Siap";
							} else if (status === "Siap") {
								switch (avTanamStatus) {
									case 'Tidak ada':
										return `<a href="${formAvt}" class="btn btn-icon btn-xs btn-warning">
													<i class="fal fa-upload"></i>
												</a>`;
									case '0':
										return createProgressBar("Pengajuan Verifikasi", 5, "text-warning");
									case '1':
										return createProgressBar("Pendelegasian Verifikator", 10, "text-warning");
									case '2':
										return createProgressBar("Pemeriksaan Berkas Kelengkapan", 20, "text-warning");
									case '3':
										return createProgressBar("Pemeriksaan Berkas PKS", 35, "text-warning");
									case '4':
										return createProgressBar("Pemeriksaan Kesesuaian Tanggal Tanam", 55, "text-warning");
									case '5':
										return createProgressBar("Pemeriksaan Lokasi Tanam dan Bukti-bukti", 95, "text-warning");
									case '6':
										return createProgressBar("Pemeriksaan Selesai", 100, "text-success", true, formAvt);
									case '7':
										return createProgressBar("Pemeriksaan Selesai", 100, "text-danger", true, formAvt);
									default:
										return "Status tidak diketahui";
								}
							} else {
								return "Status tidak valid";
							}
						}
					}
				},
				{
					data: 'siapVerifProduksi',
					render: function (data, type, row) {
						var noIjin = row.noIjin;
						var status = data;
						var avProdStatus = row.avProdStatus;
						var completeStatus = row.completeStatus;
						var formAvp = "{{ route('2024.user.commitment.formavp', ':noIjin') }}".replace(':noIjin', noIjin);

						if(completeStatus === "Lunas"){
							return `<span class="btn btn-icon btn-xs btn-success">
										<i class="fal fa-check"></i>
									</span>`;
						} else {
							if (status === "Belum Siap") {
								return "Belum Siap";
							} else if (status === "Siap") {
								switch (avProdStatus) {
									case 'Tidak ada':
										return `<a href="${formAvp}" class="btn btn-icon btn-xs btn-warning">
													<i class="fal fa-upload"></i>
												</a>`;
									case '0':
										return createProgressBar("Pengajuan Verifikasi", 5, "text-warning");
									case '1':
										return createProgressBar("Pendelegasian Verifikator", 10, "text-warning");
									case '2':
										return createProgressBar("Pemeriksaan Berkas Kelengkapan", 20, "text-warning");
									case '3':
										return createProgressBar("Pemeriksaan Berkas PKS", 35, "text-warning");
									case '4':
										return createProgressBar("Pemeriksaan Kesesuaian Tanggal Tanam", 55, "text-warning");
									case '5':
										return createProgressBar("Pemeriksaan Lokasi Tanam dan Bukti-bukti", 95, "text-warning");
									case '6':
										return createProgressBar("Pemeriksaan Selesai", 100, "text-success", true, formAvp);
									case '7':
										return createProgressBar("Pemeriksaan Selesai", 100, "text-danger", true, formAvp);
									default:
										return "Status tidak diketahui";
								}
							} else {
								return "Status tidak valid";
							}
						}
					}
				},
				{
					data: 'siapVerifSkl',
					render: function (data, type, row) {
						var noIjin = row.noIjin;
						var status = data;
						var avSklStatus = row.avSklStatus;
						var completeStatus = row.completeStatus;
						var formAvSkl = "{{ route('2024.user.commitment.formavskl', ':noIjin') }}".replace(':noIjin', noIjin);
						if(completeStatus === "Lunas"){
							return `<span class="btn btn-icon btn-xs btn-success">
										<i class="fal fa-check"></i>
									</span>`;
						} else {
							if (status === "Belum Siap") {
								return "Belum Siap";
							} else if (status === "Siap") {
								switch (avSklStatus) {
									case "Tidak ada":
										return `<a href="${formAvSkl}" class="btn btn-icon btn-xs btn-warning">
													<i class="fal fa-upload"></i>
												</a>`;
									case "0":
										return createProgressBar("Pengajuan Verifikasi", 20, "text-warning");
									case "1":
										return createProgressBar("Pendelegasian Verifikator", 40, "text-warning");
									case "2":
										return createProgressBar("Direkomendasikan", 60, "text-warning");
									case "3":
										return createProgressBar("Disetujui", 80, "text-warning");
									case "4":
									return createProgressBar("Pemeriksaan Selesai", 100, "text-success", true, formAvp);
									case "5":
										return createProgressBar("Ditolak", 100, "text-danger");
									case "6":
										return createProgressBar("Perbaiki Data", 0, "text-danger", true, formAvSkl);
									default:
										return '';
								}
							} else {
								return "error";
							}
						}
					}
				},
			],
			dom:
				"<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'>>" +
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
					},
				},
				{
					text: '<i class="fa fa-plus"></i>',
					className: 'btn-info btn-sm btn-icon ml-5',
					titleAttr: 'Ambil Data RIPH',
					action: function (e, dt, node, config) {
						window.location.href = '{{ route('2024.user.pull.index') }}';
					}
				},
			]
		});

		function createProgressBar(label, widthPercentage, textColorClass, showLink = false, link = '') {
			let progressBarClass;

			switch (textColorClass) {
				case "text-warning":
					progressBarClass = "bg-warning-500";
					break;
				case "text-success":
					progressBarClass = "bg-success-500";
					break;
				case "text-danger":
					progressBarClass = "bg-danger-500";
					break;
				default:
					progressBarClass = "bg-info-400";
					break;
			}

			let progressContent = `
				<div class="progress progress-sm mb-3">
					<div class="progress-bar ${progressBarClass}" role="progressbar" style="width: ${widthPercentage}%;"
						aria-valuenow="${widthPercentage}" aria-valuemin="0" aria-valuemax="100"></div>
				</div>`;

			if (showLink) {
				return `
					<div class="d-flex ${textColorClass}">
						${label}
						<span class="d-inline-block ml-auto mb-1">
							<a href="${link}" class="btn btn-icon btn-xs ${textColorClass === 'text-success' ? 'btn-success' : 'btn-danger'}">
								<i class="fal ${textColorClass === 'text-success' ? 'fa-check' : 'fa-ban'}"></i>
							</a>
						</span>
					</div>
					${progressContent}`;
			} else {
				return `
					<div class="d-flex ${textColorClass}">
						${label}
						<span class="d-inline-block ml-auto mb-1">
							<i class='fas fa-hourglass ${textColorClass}'></i>
						</span>
					</div>
					${progressContent}`;
			}
		}
	});
</script>
@endsection
