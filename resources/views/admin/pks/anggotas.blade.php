@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
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
						<a href="{{route('admin.task.commitment.realisasi', $pks->commitment->id)}}" class="btn btn-info btn-xs">
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
							value="{{$pks->masterpoktan->nama_kelompok}}">
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
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblLokasi" class="table table-bordered table-hover table-sm table-striped w-100">
							<thead class="thead-themed">
								<th>Petani Pelaksana</th>
								<th>Lokasi</th>
								<th>Luas</th>
								<th>Tanggal Tanam</th>
								<th>Panen</th>
								<th>Tanggal Panen</th>
								<th>Tindakan</th>
							</thead>
							<tbody>
								@foreach ($lokasis as $lokasi)
								<tr>
									<td>
										<div class="d-flex justify-content-between px-2">
											<span>
												{{$lokasi->masteranggota->nama_petani}} -
												{{$lokasi->masteranggota->ktp_petani}} / {{$lokasi->luas_lahan}} ha
											</span>
											{{-- <span class="small">
												@php
													$firstGroup = [
														$lokasi->latitude,
														$lokasi->longitude,
														$lokasi->polygon,
														$lokasi->nama_lokasi,
														$lokasi->altitude,
													];
													$isFirstGroupIncomplete = array_reduce($firstGroup, function ($carry, $item) {
														return $carry || empty($item);
													}, false);

													$secondGroup = [
														$lokasi->tgl_tanam,
														$lokasi->luas_tanam,
														$lokasi->tanam_doc,
														$lokasi->tanam_pict,
													];
													$isSecondGroupIncomplete = array_reduce($secondGroup, function ($carry, $item) {
														return $carry || empty($item);
													}, false);

													$thirdGroup = [
														$lokasi->tgl_panen,
														$lokasi->volume,
														$lokasi->panen_doc,
														$lokasi->panen_pict,
													];
													$isThirdGroupIncomplete = array_reduce($thirdGroup, function ($carry, $item) {
														return $carry || empty($item);
													}, false);
												@endphp
												@if ($isFirstGroupIncomplete)
													<i class="fal fa-map-marker-slash text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Spasial belum lengkap!"></i>
												@else
													<i class="fal fa-map-marker-check text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Spasial belum lengkap!"></i>
												@endif
												@if ($isSecondGroupIncomplete)
													<i class="fal fa-seedling text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Tanam belum lengkap!"></i>
												@endif
												@if ($isThirdGroupIncomplete)
													<i class="fal fa-balance-scale text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Produksi belum lengkap!"></i>
												@endif
											</span> --}}
										</div>
									</td>
									<td class="text-right">{{$lokasi->datarealisasi->count('id')}} titik</td>
									<td class="text-right">{{$lokasi->datarealisasi->sum('luas_lahan')}} ha</td>
									<td class="text-center">{{$lokasi->datarealisasi->min('mulai_tanam')}}</td>
									<td class="text-right">{{$lokasi->datarealisasi->sum('volume')}} ton</td>
									<td class="text-center">{{$lokasi->datarealisasi->min('mulai_panen')}}</td>
									<td  class="text-center">
										<a href="{{route('admin.task.pks.anggota.listLokasi', ['pksId' => $pks->id, 'anggotaId' => $lokasi->id])}}"
											title="Tambah data lokasi tanam" class="btn btn-xs btn-icon btn-primary"
											data-toggle="tooltip" >
											<i class="fal fa-plus-circle"></i>
										</a>
									</td>
								</tr>
								@endforeach
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
		$('#tblLokasi').dataTable(
		{
			responsive: true,
			pageLength:10,
			lengthChange: true,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'fl><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
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
