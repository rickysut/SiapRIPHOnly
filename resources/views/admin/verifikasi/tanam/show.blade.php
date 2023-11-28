@extends('layouts.admin')
@section ('styles')
<style>
	/* Remove outer border from the entire DataTable */
	.dataTables_wrapper {
		border: none;
	}

	/* Remove cell borders within the DataTable */
	table.dataTable td,
	table.dataTable th {
		border: none;
	}

	/* Remove the header border */
	table.dataTable thead th {
		border-bottom: none;
	}

	/* Remove the footer border (if applicable) */
	table.dataTable tfoot th {
		border-top: none;
	}
</style>
@endsection
@section('content')
	{{-- @include('partials.breadcrumb') --}}
	@include('partials.subheader')
	@can('online_access')
		@include('partials.sysalert')
		<div class="row" id="contentToPrint">
			@php
				$npwp = str_replace(['.', '-'], '', $commitment->npwp);
			@endphp
			<div class="col-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content">
							<table class="table table-hover table-sm w-100" style="border: none; border-top:none; border-bottom:none;" id="dataTable">
								<thead>
									<th  style="width: 32%"></th>
									<th style="width: 1%"></th>
									<th></th>
								</thead>
								<tbody>
									<tr>
										<td class="text-uppercase fw-500">
											Ringkasan Umum
										</td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-muted">Perusahaan</td>
										<td>:</td>
										<td class="fw-500">
											{{$verifikasi->commitment->datauser->company_name}}
										</td>
									</tr>
									<tr>
										<td class="text-muted">Nomor Ijin/RIPH</td>
										<td>:</td>
										<td class="fw-500">{{$verifikasi->no_ijin}}</td>
									</tr>
									<tr>
										<td class="text-muted">Tanggal Pengajuan Verifikasi</td>
										<td>:</td>
										<td class="fw-500">
											{{ \Carbon\Carbon::parse($verifikasi->created_at)->format('d F Y') }}
										</td>
									</tr>
									<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-uppercase fw-500">
											Ringkasan Kewajiban & Realisasi
										</td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-muted">Wajib Tanam</td>
										<td>:</td>
										<td class="fw-500">{{ number_format($commitment->luas_wajib_tanam, 2, '.', ',') }} ha</td>
									</tr>
									<tr>
										<td class="text-muted">Realisasi Tanam</td>
										<td>:</td>
										<td class="fw-500">
											{{number_format($total_luastanam, 2,'.',',')}} ha
											@if($commitment->luas_wajib_tanam > $total_luastanam)
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@else
												<i class="fas fa-check text-success mr-1"></i>
											@endif
										</td>
									</tr>
									<tr>
										<td class="text-muted">Lokasi Tanam (Spasial)</td>
										<td>:</td>
										<td class="fw-500">
											{{$hasGeoloc}} titik
										</td>
									</tr>
									<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-uppercase fw-500">
											Ringkasan Kemitraan
										</td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-muted">Jumlah Anggota</td>
										<td>:</td>
										<td class="fw-500">{{$countAnggota}} anggota</td>
									</tr>
									<tr>
										<td class="text-muted">Jumlah Kelompok Tani</td>
										<td>:</td>
										<td class="fw-500">{{$countPoktan}} Kelompok</td>
									</tr>
									<tr>
										<td class="text-muted">Jumlah PKS diunggah</td>
										<td>:</td>
										<td class="fw-500">{{$countPks}} Berkas</td>
									</tr>
									<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-uppercase fw-500">
											Kelengkapan Berkas
										</td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-uppercase fw-500">
											A. Tahap Tanam
										</td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-muted pl-4">Surat Pengajuan Verifikasi Tanam</td>
										<td>:</td>
										<td class="fw-500">
											@if ($userDocs->spvtcheck)
												@if ($userDocs->spvtcheck === 'sesuai')
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvt) }}">
														Ada
													</a>
													<i class="fa fa-check text-success ml-1"></i>
												@else
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvt) }}">
														Ada
													</a>
													<i class="fas fa-exclamation-circle text-danger ml-1"></i>
												@endif
											@else
												<span class="text-danger">Tidak ada berkas</span>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										</td>
									</tr>
									<tr>
										<td class="text-muted pl-4">Surat Pertanggungjawaban Mutlak (tanam)</td>
										<td>:</td>
										<td class="fw-500">
											@if ($userDocs->sptjmtanamcheck)
												@if ($userDocs->sptjmtanamcheck === 'sesuai')
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmtanam) }}">
														Ada
													</a>
													<i class="fa fa-check text-success ml-1"></i>
												@else
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjmtanam) }}">
														Ada
													</a>
													<i class="fas fa-exclamation-circle text-danger ml-1"></i>
												@endif
											@else
												<span class="text-danger">Tidak ada berkas</span>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										</td>
									</tr>
									<tr>
										<td class="text-muted pl-4">Form Realisasi Tanam</td>
										<td>:</td>
										<td class="fw-500">
											@if ($userDocs->rtacheck)
												@if ($userDocs->rtacheck === 'sesuai')
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
														Ada
													</a>
													<i class="fa fa-check text-success ml-1"></i>
												@else
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
														Ada
													</a>
													<i class="fas fa-exclamation-circle text-danger ml-1"></i>
												@endif
											@else
												<span class="text-danger">Tidak ada berkas</span>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										</td>
									</tr>
									<tr>
										<td class="text-muted pl-4">SPH-SBS (Tanam)</td>
										<td>:</td>
										<td class="fw-500">
											@if ($userDocs->sphtanamcheck)
												@if ($userDocs->sphtanamcheck === 'sesuai')
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
														Ada
													</a>
													<i class="fa fa-check text-success ml-1"></i>
												@else
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
														Ada
													</a>
													<i class="fas fa-exclamation-circle text-danger ml-1"></i>
												@endif
											@else
												<span class="text-danger">Tidak ada berkas</span>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										</td>
									</tr>
									<tr>
										<td class="text-muted pl-4">Logbook (s.d Tanam)</td>
										<td>:</td>
										<td class="fw-500">
											@if ($userDocs->logbooktanamcheck)
												@if ($userDocs->logbooktanamcheck == 'sesuai')
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbooktanam) }}">
														Ada
													</a>
													<i class="fa fa-check text-success ml-1"></i>
												@else
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbooktanam) }}">
														Ada
													</a>
													<i class="fas fa-exclamation-circle text-danger ml-1"></i>
												@endif
											@else
												<span class="text-danger">Tidak ada berkas</span>
												<i class="fas fa-exclamation-circle text-danger ml-1"></i>
											@endif
										</td>
									</tr>
									<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-uppercase fw-500">
											Ringkasan Hasil Pemeriksaan
										</td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="text-muted">Nota Dinas Verifikasi Produksi</td>
										<td>:</td>
										<td class="fw-500">
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->ndhprt) }}">
												{{$verifikasi->ndhprt}}
											</a>
										</td>
									</tr>
									<tr>
										<td class="text-muted">Berita Acara Pemeriksaan</td>
										<td>:</td>
										<td class="fw-500">
											<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->batanam) }}">
												{{$verifikasi->batanam}}
											</a>
										</td>
									</tr>
									<tr>
										<td class="text-muted">Catatan hasil pemeriksaan</td>
										<td>:</td>
										<td class="fw-500">
											{{$verifikasi->note}}
										</td>
									</tr>
									<tr>
										<td class="text-muted">Tanggal Verifikasi</td>
										<td>:</td>
										<td class="fw-500">
											{{ \Carbon\Carbon::parse($verifikasi->verif_at)->format('d F Y') }}
										</td>
									</tr>
									<tr>
										<td class="text-muted">Metode Verifikasi</td>
										<td>:</td>
										<td class="fw-500">{{$verifikasi->metode}}</td>
									</tr>
									<tr>
										<td class="text-muted">Status Verifikasi</td>
										<td>:</td>
										<td class="fw-500">
											@if ($verifikasi->status)
												@if ($verifikasi->status === '1')
													Verifikasi diajukan oleh Pelaku Usaha
												@elseif($verifikasi->status === '2' || $verifikasi->status === '3')
													Dalam proses pemeriksaan/verifikasi oleh petugas
												@elseif($verifikasi->status === '4')
													Pemeriksaan/Verifikasi telah Selesai
												@elseif($verifikasi->status === '5')
													Perbaikan Laporan
												@endif
											@else
												Tidak ada pengajuan
											@endif
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		{{-- modal view doc --}}
		<div class="modal fade" id="viewDocs" tabindex="-1" role="dialog" aria-labelledby="document" aria-hidden="true">
			<div class="modal-dialog modal-dialog-right" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">
							Berkas <span class="fw-300"><i>lampiran </i></span>
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body embed-responsive embed-responsive-16by9">
						<iframe class="embed-responsive-item" src="" width="100%"  frameborder="0"></iframe>
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
			$('#viewDocs').on('shown.bs.modal', function (e) {
				var docUrl = $(e.relatedTarget).data('doc');
				$('iframe').attr('src', docUrl);
			});

			$('#dataTable').DataTable({
				responsive: true,
				"ordering": false,
				lengthChange: false,
				pageLength: -1,
				dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-7'>>",
				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						titleAttr: 'Ekspor data ke MS. Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Cetak halaman data.',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					},
					{
						text: '<i class="fal fa-external-link"></i>',
						titleAttr: 'Lihat Detail',
						className: 'btn btn-icon btn-outline-info btn-xs mr-1',
						action: function () {
							// Replace 'to_somewhere' with your actual route and $key->id with the parameter value
							window.location.href = '{{ route('verification.tanam.check', $verifikasi->id) }}';
						}
					},
					{
						text: 'Kembali',
						titleAttr: 'Lihat Detail',
						className: 'btn btn-info btn-xs',
						action: function () {
							// Replace 'to_somewhere' with your actual route and $key->id with the parameter value
							window.location.href = '{{ route('verification.tanam') }}';
						}
					}
				],
			});
		});
	</script>
@endsection
