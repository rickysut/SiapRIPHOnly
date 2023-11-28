@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
@can('pks_create')
	<div class="panel" id="panel-info">
		<div class="panel-hdr">
			<h2>
				Data <span class="fw-300"><i>Informasi</i></span>
			</h2>
			<div class="panel-toolbar">
				<a href="{{route('admin.task.pks.anggotas', $pks->id)}}" class="btn btn-info btn-xs" data-toggle="tooltip" data-original-title="Kembali ke Daftar Anggota" >
					<i class="fal fa-undo mr-1"></i>Kembali
				</a>
			</div>
		</div>
		<div class="panel-container show">
			<div class="panel-content row d-flex">
				<div class="form-group col-md-4">
					<label for="">No. Perjanjian</label>
					<input disabled class="form-control form-control-sm fw-500 text-primary"
					placeholder="nomor PKS" aria-describedby="helpId"
					value="{{$pks->no_perjanjian}}">
				</div>
				<div class="form-group col-md-3">
					<label for="">Kelompoktani</label>
					<input disabled class="form-control form-control-sm fw-500 text-primary"
					placeholder="nama kelompok" aria-describedby="helpId"
					value="{{$pks->masterpoktan->nama_kelompok}}">
				</div>
				<div class="form-group col-md-3">
					<label for="">Anggota</label>
					<input disabled class="form-control form-control-sm fw-500 text-primary"
					placeholder="nama anggota" aria-describedby="helpId"
					value="{{$anggota->masteranggota->nama_petani}}">
				</div>
				<div class="form-group col-md-2">
					<label for="">Luas Rencana</label>
					<input disabled class="form-control form-control-sm fw-500 text-primary"
					placeholder="nama anggota" aria-describedby="helpId"
					value="{{$anggota->luas_lahan}} ha">
				</div>
			</div>
		</div>
	</div>
	<div class="panel" id="panel-list">
		<div class="panel-container show">
			<div class="panel-content">
				<table class="table table-bordered table-hover table-sm table-striped w-100" id="listLokasi">
					<thead class="thead-themed">
						<th>Lokasi Id</th>
						<th>Nama Lokasi</th>
						<th>Luas (ha)</th>
						<th>Tanggal Tanam</th>
						<th>Produksi (ton)</th>
						<th>Tanggal Panen</th>
						<th>Aksi</th>
					</thead>
					<tbody>
						@foreach ($listLokasi as $lokasi)
							<tr>
								<td>{{$lokasi->id}}</td>
								<td>{{$lokasi->nama_lokasi}}</td>
								<td class="text-right">{{$lokasi->luas_lahan}}</td>
								<td class="text-right">
									Mulai {{$lokasi->mulai_tanam}}<br>
									Akhir: {{$lokasi->akhir_tanam}}
								</td>
								<td class="text-right">{{$lokasi->volume}}</td>
								<td class="text-right">
									Mulai: {{$lokasi->mulai_panen}}<br>
									Akhir: {{$lokasi->akhir_panen}}
								</td>
								<td  class="text-center">
									<form action="{{route('admin.task.deleteLokasiTanam', $lokasi->id)}}" method="post">
										@csrf
										@method('DELETE')
										<a href="{{route('admin.task.pks.anggota.editLokasiTanam', ['pksId' => $pks->id, 'anggotaId' => $anggota->id, 'id' => $lokasi->id])}}" class="btn btn-icon btn-primary btn-xs" data-toggle="tooltip" data-original-title="Tambah/Ubah Data Spasial dan Tanam" >
											<i class="fal fa-edit"></i>
										</a>
										<a class="btn btn-icon btn-warning btn-xs showModal" href="javascript:void(0)" data-toggle="tooltip" data-lokasi="{{$lokasi->id}}" data-original-title="Realisasi Produksi" id="showModal">
											<i class="fal fa-dolly"></i>
										</a>
										<a href="{{route('admin.task.pks.anggota.fotoLokasi', ['pksId' => $pks->id, 'anggotaId' => $anggota->id, 'id' => $lokasi->id])}}" class="btn btn-icon btn-info btn-xs" data-toggle="tooltip" data-original-title="Bukti Pendukung" >
											<i class="fal fa-images"></i>
										</a>
										<button type="submit" class="btn btn-icon btn-danger btn-xs ml-2" data-toggle="tooltip" data-original-title="hapus data">
											<i class="fal fa-trash"></i>
										</button>
									</form>
								</td>
								<!-- Modal -->
								<div class="modal fade" id="modelProduksi-{{$lokasi->id}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title text-left">
													<div class="row">
														<span class="col-12 fw-500">Data Produksi</span>
														<span class="col-12 small">Laporan data realisasi produksi.</span>
													</div>
												</h4>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<form action="{{route('admin.task.storeRealisasiProduksi', $lokasi->id)}}" method="POST" enctype="multipart/form-data">
												@csrf
												@method('PUT')
												<div class="modal-body">
													<div class="form-group row">
														<label class="col-form-label col-md-3" for="mulai_panen">Tanggal Awal Panen<sup class="text-danger"> *</sup></label>
														<div class="col-md-9">
															<div class="input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
																</div>
																<input type="date" value="{{ old('mulai_panen', $lokasi->mulai_panen) }}" name="mulai_panen" id="mulai_panen" class="font-weight-bold form-control form-control-sm bg-white" />
															</div>
															<span class="help-block">Tanggal mulai panen.</span>
														</div>
													</div>
													<div class="form-group row">
														<label class="col-form-label col-md-3" for="akhir_panen">Tanggal Akhir Panen<sup class="text-danger"> *</sup></label>
														<div class="col-md-9">
															<div class="input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
																</div>
																<input type="date" value="{{ old('akhir_panen', $lokasi->akhir_panen) }}" name="akhir_panen" id="akhir_panen" class="font-weight-bold form-control form-control-sm bg-white" />
															</div>
															<span class="help-block">Tanggal akhir pelaksanaan panen.</span>
														</div>
													</div>
													<div class="form-group row">
														<label class="col-form-label col-md-3" for="volume">Volume Panen (ton)<sup class="text-danger"> *</sup></label>
														<div class="col-md-9">
															<div class="input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text"><i class="fal fa-map"></i></span>
																</div>
																<input type="number" step="0.01" value="{{ old('volume', $lokasi->volume) }}" name="volume" id="volume" class="font-weight-bold form-control form-control-sm bg-white" />
															</div>
															<span class="help-block">Total volume produksi yang diperoleh (ton).</span>
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
													<button type="submit" class="btn btn-primary btn-sm">Save</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</tr>
						@endforeach
					</tbody>
					<tfoot class="thead-themed fw-500">
						<tr>
							<td colspan="2" class="text-right">
								Total Luas:
							</td>
							<td class="text-right">
								{{$anggota->datarealisasi->sum('luas_lahan')}}
							</td>
							<td class="text-right">
								Total Luas:
							</td>
							<td class="text-right">
								{{$anggota->datarealisasi->sum('volume')}}
							</td>
							<td colspan="2"></td>
						</tr>
					</tfoot>
				</table>
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
		var tableLokasi = $('#listLokasi').DataTable({
			responsive: true,
			lengthChange: true,
			dom:
			"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			buttons: [
				{
					extend: 'excelHtml5',
					text: '<i class="fa fa-file-excel"></i>',
					title: 'Daftar Lokasi Tanam',
					titleAttr: 'Generate Excel',
					className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
				},
				{
					extend: 'print',
					text: '<i class="fa fa-print"></i>',
					title: 'Daftar Lokasi Tanam {{$anggota->masteranggota->nama_petani}} / {{$pks->masterpoktan->nama_kelompok}}',
					titleAttr: 'Print Table',
					className: 'btn-outline-primary btn-xs btn-icon mr-3'
				},

				@if($anggota->datarealisasi->sum('luas_lahan') < $anggota->luas_lahan )
				{
					text: '<i class="fal fa-plus"></i> Tambah Lokasi',
					titleAttr: 'Tambah data',
					className: 'btn btn-danger btn-xs',
						action: function () {
							//
							// Replace 'to_somewhere' with your actual route and $key->id with the parameter value
							window.location.href = '{{route('admin.task.pks.anggota.addLokasiTanam', ['pksId' => $pks->id, 'anggotaId' => $anggota->id])}}';
						}
				}
					@endif()
			],
		});

		$('.showModal').on('click', function () {
            var lokasiId = $(this).data('lokasi');
            $('#modelProduksi-' + lokasiId).modal('toggle');
        });
	});
</script>

@endsection
