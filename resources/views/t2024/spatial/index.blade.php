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
						Data <span class="fw-300"><i>Informasi</i></span>
					</h2>
				</div>
			</div>
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>
						Daftar <span class="fw-300"><i>Realisasi Lokasi dan Pelaksana</i></span>
					</h2>
					<div class="panel-toolbar">
						<a href="{{route('2024.spatial.createsingle')}}" class="btn btn-xs btn-primary waves-effect waves-themed" data-toggle="tooltip" data-offset="0,10" data-original-title="Buat Peta Tunggal baru (Manual)">
							<i class="fal fa-map-marked-alt"></i>
							Buat/Import Peta Baru
						</a>
						<div class="btn-group" hidden>
							<button type="button" class="btn btn-xs btn-primary waves-effect waves-themed">
								<i class="fal fa-plus mr-1"></i>
								Peta Baru
							</button>
							<button type="button" class="btn btn-xs btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-themed" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								<i class="fal fa-chevron-down"></i>
							</button>
							<div class="dropdown-menu" style="">
								<a href="{{route('2024.spatial.createsingle')}}" onclick="navigateBack();" class="dropdown-item" data-toggle="tooltip" data-offset="0,10" data-original-title="Buat Peta Tunggal baru (Manual)">
									<i class="fal fa-plus"></i>
									Buat Peta Baru
								</a>
								<a href="javascript:void(0);" onclick="navigateBack();" class="dropdown-item" data-toggle="tooltip" data-offset="0,10" data-original-title="Import Peta Lokasi Tunggal (Per Lokasi)">
									<i class="fal fa-map-marked-alt"></i>
									Impor Peta Tunggal
								</a>
								<a href="javascript:void(0);" onclick="navigateBack();" class="dropdown-item" data-toggle="tooltip" data-offset="0,10" data-original-title="Import Peta Lokasi Jamak (Banyak Lokasi)">
									<i class="fal fa-layer-plus"></i>
									Impor Peta Jamak
								</a>
								{{-- <div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#">Separated link</a> --}}
							</div>
						</div>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblSpatial" class="table table-bordered table-hover table-sm table-striped w-100">
							<thead class="thead-themed">
								<th>Kode Lokasi</th>
								<th>Pengelola</th>
								<th>Luas</th>
								<th>Wilayah</th>
								<th>Tindakan</th>
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

<!-- start script for this page -->
@section('scripts')
@parent

<script>
	$(document).ready(function(){

		$('#tblSpatial').dataTable(
		{
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
				url: "{{ route('2024.datafeeder.getAllSpatials')}}",
				type: "GET",
				dataSrc: "data"
			},
			"columnDefs": [
				{ "targets": [2,3], "className": "text-right" },
				{ "targets": [4], "className": "text-center" },
			],
			columns:[
				{data: 'kode_spatial'},
				{data: 'ktp_petani',
					render: function (data, type, row) {
						return row.anggota.nama_petani;
					}
				},
				{
					data: 'luas_lahan',
					render: function(data, type, row) {
						// if (type === 'display') {
						// 	return parseFloat(data).toLocaleString('id-ID', {
						// 		minimumFractionDigits: 2,
						// 		maximumFractionDigits: 2
						// 	});
						// }
						var luasHektare = parseFloat(data) / 10000;
						return luasHektare.toLocaleString('id-ID', {
							minimumFractionDigits: 4,
							maximumFractionDigits: 4
						});
						return `
							<div class="justify-content-end">`
								+ data + `
							</div>
							`;
					}
				},
				{data: 'provinsi_id',
					render: function (data, type, row) {
						return row.provinsi.nama;
					}
				},

				{
					data: 'kode_spatial',
					render: function(data, type, row) {
						var kode = data.replace(/[^a-zA-Z0-9]/g, '');

						var url = "{{ route('2024.spatial.edit', ':kode') }}";
						url = url.replace(':kode', kode);
						var link = `
							<div class="justify-content-center">
								<a href="${url}" class="btn btn-icon btn-xs btn-default waves-effect waves-themed" data-toggle="tooltip" data-offset="0,10" data-original-title="Buat Peta Tunggal baru (Manual)">
									<i class="fal fa-edit"></i>
								</a>
							</div>
						`;

						return link;
					}
				}
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
