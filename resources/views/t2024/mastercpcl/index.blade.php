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
						Daftar <span class="fw-300"><i>CPCL</i></span>
					</h2>
					<div class="panel-toolbar">
						<div class="btn-group">
							<a href="{{route('2024.cpcl.anggota.create')}}" type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" data-offset="0,10" data-original-title="Tambah CPCL">
								<i class="fal fa-user-plus"></i>
								Registrasi Anggota Baru
							</a>
						</div>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblPoktan" class="table table-bordered table-hover table-sm table-striped w-100">
							<thead class="thead-themed">
								<th>Nama Petani</th>
								<th>NIK</th>
								<th>Kontak</th>
								<th>Lahan</th>
								<th>Provinsi</th>
								<th>Kabupaten</th>
								<th>Kecamatan</th>
								<th>Desa</th>
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

		$('#tblPoktan').dataTable(
		{
			responsive: true,
			lengthChange: true,
			lengthMenu: [10, 25, 50, 100],
			paging: true,
			ordering: true,
			processing: true,
			serverSide: true,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			ajax: {
				url: "{{ route('2024.datafeeder.getAllCpcl')}}",
				type: "GET",
				dataSrc: "data"
			},
			columns:[
				{data: 'nama_petani'},
				{data: 'ktp_petani'},
				{data: 'kontak'},
				{
					data: 'jumlah_spatial',
					render: function (data, type, row) {
						return `<ul class='list-group'>
							<li class='list-group-items d-flex justify-content-between align-item-center'>
								<span>Lahan</span>
								<span>`+ data +` titik</span>
							</li>
							<li class='list-group-items d-flex justify-content-between align-item-center'>
								<span>Luas</span>
								<span>`+ row.total_luas / 10000 +` ha</span>
							</li>
							</ul>
							`;
					}
				},
				{data: 'nama_provinsi'},
				{data: 'nama_kabupaten'},
				{data: 'nama_kecamatan'},
				{data: 'nama_desa'},
				{
					data: 'ktp_petani',
					render: function (data, type, row) {
						return `<a type="button" href="{{ route('2024.cpcl.anggota.show', ':nik') }}" class="btn btn-icon btn-default btn-xs"><i class="fal fa-pencil"></i></a>`
							.replace(':nik', data);
					}
				}
			],
			buttons: [
				{
					extend: 'pdfHtml5',
					text: '<i class="fa fa-file-pdf"></i>',
					title: 'Daftar Realisasi Lokasi dan Pelaksana',
					titleAttr: 'Generate PDF',
					className: 'btn-outline-danger btn-sm btn-icon mr-1',
					exportOptions:
					{
						columns: [0,1,2,3,4,5],
					}
				},
				{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel"></i>',
					title: 'Daftar Realisasi Lokasi dan Pelaksana',
					titleAttr: 'Generate Excel',
					className: 'btn-outline-success btn-sm btn-icon mr-1',
					exportOptions:
					{
						columns: [0,1,2,3,4,5]
					}
				},
				{
					extend: 'print',
					text: '<i class="fa fa-print"></i>',
					title: 'Daftar Realisasi Lokasi dan Pelaksana',
					titleAttr: 'Print Table',
					className: 'btn-outline-primary btn-sm btn-icon mr-1',
					exportOptions:
					{
						columns: [0,1,2,3,4,5]
					}
				}
			],
		});
	});
</script>
@endsection
