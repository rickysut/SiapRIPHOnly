@extends('t2024.layouts.admin')
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@section('content')
	{{-- @include('partials.breadcrumb') --}}
	@include('t2024.partials.subheader')
	@can('administrator_access')
	@include('t2024.partials.sysalert')
		<div class="row">
			<div class="col-12">
				<div class="panel" id="panel-1">
					<div class="panel-container show">
						<div class="panel-content">
							<table id="avtanamTable" class="table table-sm table-bordered table-striped w-100">
								<thead>
									<tr>
										<th hidden></th>
										<th>Tahap</th>
										<th>Pelaku Usaha</th>
										<th>No. RIPH</th>
										<th>Diajukan pada</th>
										<th>Verifikator</th>
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
		</div>
	@endcan
@endsection

@section('scripts')
	@parent
	<script>
		$(document).ready(function() {

			//initialize datatable dataPengajuan
			$('#avtanamTable').dataTable({
				responsive: true,
				lengthChange: false,
				ordering: true,
				processing: true,
				serverSide: true,
				dom:
					"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
					"<'row'<'col-sm-12'tr>>" +
					"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				ajax: {
					url: "{{ route('2024.datafeeder.getRequestVerif') }}",
					type: "GET",
					dataSrc: "data"
				},
				columns: [
					{data: 'id', visible: false, searchable: false},
					{data: 'tableSource'},
					{data: 'perusahaan'},
					{data: 'no_ijin'},
					{data: 'created_at'},
					{
						data: 'assignments',
						render: function(data, type, row) {
							if (data && data.length > 0) {
								let listItems = data.map(assignment => `<li>${assignment.user_name}</li>`).join('');
								return `<div class='row'><ul>${listItems}</ul></div>`;
							} else {
								return `<span class='badge badge-sm badge-danger'>Verifikator diperlukan</span>`;
							}
						},
						orderable: false,
						searchable: false,
					},
					{
						data: 'ijin',
						render: function (data, type, row) {
							var noIjin = data;
							var tcode = row.tcode;
							var status = row.status;
							var tableSource = row.tableSource;
							var viewStat;

							if (tableSource === 'TANAM') {
								viewStat = '{{ route("2024.admin.pengajuan.assignment", [":noIjin", ":tcode"]) }}'.replace(':noIjin', noIjin).replace(':tcode', tcode);
							} else if (tableSource === 'PRODUKSI') {
								viewStat = '{{ route("2024.admin.pengajuan.assignment", [":noIjin", ":tcode"]) }}'.replace(':noIjin', noIjin).replace(':tcode', tcode);
							} else {
								viewStat = '#';
							}

							return `
								<a href='`+ viewStat +`' data-toggle="tooltip" title="Lihat hasil" class="mr-1 btn btn-xs btn-icon btn-info">
									<i class="fal fa-file-search"></i>
								</a>
							`;
						}
					},
				],
				order: [[0, 'asc']],
				columnDefs: [
					{
						targets: [4], // Indeks untuk kolom tanggal
						render: function (data, type, row) {
							if (type === 'display' || type === 'filter') {
								return data ? moment(data).format('DD-MM-YYYY') : '-';
							}
							return data;
						}
					},
					{
						targets: [1,2,3,4,5,6],
						className: 'text-center'
					}
				],
				buttons: [
					{
						extend: 'pdfHtml5',
						text: '<i class="fa fa-file-pdf"></i>',
						titleAttr: 'Generate PDF',
						className: 'btn-outline-danger btn-sm btn-icon mr-1'
					},
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-sm btn-icon mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					}
				]
			});
		});
	</script>
@endsection
