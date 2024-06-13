@extends('t2024.layouts.admin')
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('pks_create')
	@php
		$npwp = str_replace(['.', '-'], '', $npwpCompany);
	@endphp
	<div class="row">
		<div class="col">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>
						Data <span class="fw-300"><i>Informasi</i></span>
					</h2>
					<div class="panel-toolbar">
						<a href="{{route('2024.user.commitment.realisasi', $commitment->id)}}" class="btn btn-info btn-xs">
							<i class="fal fa-undo mr-1"></i>Kembali
						</a>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content row d-flex">
						<div class="form-group col-md-4">
							<label for="">No. RIPH</label>
							<input disabled class="form-control form-control-sm fw-500 text-primary"
							placeholder="" aria-describedby="helpId"
							value="{{$commitment->no_ijin}}">
						</div>
						<div class="form-group col-md-4">
							<label for="">No. Perjanjian</label>
							<input disabled class="form-control form-control-sm fw-500 text-primary"
							placeholder="" aria-describedby="helpId"
							value="{{$pks->no_perjanjian}}">
						</div>
						<div class="form-group col-md-4">
							<label for="">Kelompoktani</label>
							<input disabled class="form-control form-control-sm fw-500 text-primary"
							placeholder="" aria-describedby="helpId"
							value="{{$pks->nama_poktan}}">
						</div>
					</div>
				</div>
			</div>
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>
						Daftar <span class="fw-300"><i>Realisasi Lokasi dan Pelaksana</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('t2024.partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblLokasi" class="table table-bordered table-hover table-sm table-striped w-100">
							<thead class="thead-themed">
								<th>Kode Lokasi</th>
								<th>Pelaksana</th>
								<th>Realisasi Tanam</th>
								<th>Tanggal</th>
								<th>Realisasi Panen</th>
								<th>Tanggal</th>
								<th>Tindakan</th>
							</thead>
							<tbody>

							</tbody>
							<tfoot class="thead-themed">
								<tr>
									<th class="text-right" colspan="7">TOTAL REALISASI</th>
								</tr>
								<tr>
									<th class="text-right" colspan="6">Luas Tanam: </th>
									<th class="text-right">{{$sumLuas}} ha</th>
								</tr>
								<tr>
									<th class="text-right" colspan="6">Volume Panen</th>
									<th class="text-right">{{$sumProduksi}} ton</th>
								</tr>
							</tfoot>
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

<script>
	$(document).ready(function()
	{
		var noIjin = '{{$commitment->no_ijin}}';
		var formattedNoIjin = noIjin.replace(/[\/.]/g, '');
		var poktanId = '{{$pks->poktan_id}}';
		var ijin = '{{$ijin}}';

		$('#tblLokasi').dataTable(
		{
			responsive: true,
			pageLength:10,
			lengthChange: true,
			paging: true,
			ordering: true,
			processing: true,
			serverSide: true,
			order: [[0, 'asc']],
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

			ajax: {
				url: "{{ route('2024.datafeeder.getLokasiByPks', [':noIjin', ':poktanId']) }}".replace(':noIjin', formattedNoIjin).replace(':poktanId', poktanId),
				type: "GET",
			},

			columns: [
				{
					data: 'kode_spatial',
					render: function (data, type, row) {
						return data;
					}
				},
				{
					data: 'ktp_petani',
					name: 'nama_petani',
					render: function (data, type, row) {
						return row.nama_petani + ' / ' + data;
					}
				},
				{ data: 'luas_tanam'},
				{ data: 'tgl_tanam'},
				{ data: 'volume_panen'},
				{ data: 'tgl_panen'},
				{
					data: 'kode_spatial',
					render: function(data, type, row) {
						if (data) {
							if(data){
								return `<a href="{{route('2024.user.commitment.addrealisasi', ['noIjin' => ':ijin', 'spatial' => ':spatial'])}}" title="Isi/ubah data realisasi tanam" class="btn btn-primary btn-icon btn-xs text-white" >
										<i class="fa fa-edit"></i>
									</a>

									<a href="" title="ubah data tanam" class="btn btn-warning btn-icon btn-xs text-white" >
										<i class="fa fa-dolly"></i>
									</a>
									<a href="" title="ubah data tanam" class="btn btn-info btn-icon btn-xs text-white" >
										<i class="fa fa-images"></i>
									</a>
									`.replace(':ijin', ijin).replace(':spatial', data);
							}else{
								return `<a href="" title="isi data tanam" class="btn btn-warning btn-icon btn-xs text-white" >
									<i class="fa fa-map"></i>
								</a>`;
							}
						} else {
							return ``;
						}
					}
				},
			],
			columnDefs: [
				{
					targets: [6],
					className: 'text-center'
				},
				{
					targets: [2, 4],
					className: 'text-right'
				},
				{
					orderable: false,
					targets: [2, 3, 4, 5, 6]
				},
			],
			buttons: [
				{
					extend: 'pdfHtml5',
					text: '<i class="fa fa-file-pdf"></i>',
					title: 'Daftar Realisasi Lokasi dan Pelaksana',
					titleAttr: 'Generate PDF',
					className: 'btn-outline-danger btn-sm btn-icon mr-1'
				},
				{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel"></i>',
					title: 'Daftar Realisasi Lokasi dan Pelaksana',
					titleAttr: 'Generate Excel',
					className: 'btn-outline-success btn-sm btn-icon mr-1'
				},
				{
					extend: 'print',
					text: '<i class="fa fa-print"></i>',
					title: 'Daftar Realisasi Lokasi dan Pelaksana',
					titleAttr: 'Print Table',
					className: 'btn-outline-primary btn-sm btn-icon mr-1'
				}
			]
		});
	});
</script>
@endsection

{{-- {{ route('admin.task.commitments.pksmitra', $commitment->id) }} --}}
