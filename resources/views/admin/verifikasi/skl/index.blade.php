@extends('layouts.admin')
@section('content')
	{{-- @include('partials.breadcrumb') --}}
	@include('partials.subheader')
	@can('onfarm_access')
		@include('partials.sysalert')
		{{-- <div class="alert alert-danger" role="alert">
		  <h4 class="alert-heading">PERBAIKAN</h4>
		  <p>Silahkan di coba kembali.</p>
		  <p class="mb-0"></p>
		</div> --}}
		<div class="row">
			<div class="col-12">
				<div class="panel" id="panel-1">
					<div class="panel-container show">
						<div class="panel-content">
							<table id="dataPengajuan" class="table table-sm table-bordered table-striped w-100">
								<thead>
									<tr>
										<th>Periode</th>
										{{-- <th>No. Pengajuan</th> --}}
										<th>Pelaku Usaha</th>
										<th>No. RIPH</th>
										<th>Diajukan pada</th>
										<th>Status</th>
										<th>Tindakan</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($verifikasis as $verifikasi)
										<tr>
											<td class="text-center">{{$verifikasi->commitment->periodetahun}}</td>
											<td>
												{{$verifikasi->datauser->company_name}}
											</td>
											<td>{{$verifikasi->commitment->no_ijin}}</td>
											<td class="text-center">{{ date('d F Y', strtotime($verifikasi->created_at)) }}</td>
											<td class="text-center">
												{{-- new --}}
												@if ($verifikasi->status === '1')
													<span class="icon-stack fa-2x" data-toggle="tooltip" data-original-title="Pengajuan baru">
														<i class="base-7 icon-stack-3x color-warning-300"></i>
														<i class="base-7 icon-stack-2x color-warning-800 opacity-70"></i>
														<span class="icon-stack-1x text-white opacity-90">!</span>
													</span>
													<span hidden>{{$verifikasi->status}}</span>
												@else
													<div disable class="btn-group btn-group-toggle" role="group">
														<label class="btn btn-{{ $verifikasi->status == 1 ? 'warning' : 'success' }} btn-xs" data-toggle="tooltip" data-original-title="Pelaku Usaha mengajukan SKL">1
															<i class="fa {{ $verifikasi->status == 1 ? 'fa-exclamation-circle' : 'fa-check' }}"></i>
														</label>

														<label class="btn btn-{{ in_array($verifikasi->status, [2, 3, 4, 5]) ? 'success' : 'default' }} btn-xs" data-toggle="tooltip" data-original-title="Rekomendasi Penerbitan SKL telah disampaikan kepada Pimpinan.">2
															<i class="fa {{ in_array($verifikasi->status, [2, 3, 4, 5]) ? 'fa-check' : 'fa-hourglass' }}"></i>
														</label>

														<label class="btn btn-{{ in_array($verifikasi->status, [3, 4, 5]) ? 'success' : 'default' }} btn-xs" data-toggle="tooltip" data-original-title="Pejabat/Pimpinan telah menyetujui penerbitan">3
															<i class="fa {{ in_array($verifikasi->status, [3, 4, 5]) ? 'fa-check' : 'fa-hourglass' }}"></i>
														</label>

														<label class="btn btn-{{ $verifikasi->status == 4 ? 'success' : ($verifikasi->status == 5 ? 'danger' : 'default') }} btn-xs" data-toggle="tooltip" data-original-title="SKL telah diterbitkan.">4
															<i class="fa {{ $verifikasi->status == 4 ? 'fa-check' : ($verifikasi->status == 5 ? 'fa-ban' : 'fa-hourglass') }}"></i>
														</label>
													</div>
												@endif

												{{-- old --}}
												{{-- @if ($verifikasi->status === '1')
													<span class="icon-stack fa-2x" data-toggle="tooltip" data-original-title="Pengajuan baru">
														<i class="base-7 icon-stack-3x color-warning-300"></i>
														<i class="base-7 icon-stack-2x color-warning-700 opacity-70"></i>
														<span class="icon-stack-1x text-white opacity-90">!</span>
													</span>
													<span hidden>{{$verifikasi->status}}</span>
												@elseif ($verifikasi->status === '2')
													<span class="icon-stack fa-2x" data-toggle="tooltip" data-original-title="Pemeriksaan Berkas Kelengkapan">
														<i class="base-7 icon-stack-3x color-info-400"></i>
														<i class="base-7 icon-stack-2x color-info-600 opacity-70"></i>
														<span class="icon-stack-1x text-white fw-500">{{$verifikasi->status}}</span>
													</span>
													<span hidden>{{$verifikasi->status}}</span>
												@elseif ($verifikasi->status === '3')
													<span class="icon-stack fa-2x" data-toggle="tooltip" data-original-title="Pemeriksaan PKS Selesai">
														<i class="base-7 icon-stack-3x color-info-400"></i>
														<i class="base-7 icon-stack-2x color-info-600 opacity-70"></i>
														<span class="icon-stack-1x text-white fw-500">{{$verifikasi->status}}</span>
													</span>
													<span hidden>{{$verifikasi->status}}</span>
												@elseif ($verifikasi->status === '4')
													<span class="icon-stack fa-2x" data-toggle="tooltip" data-original-title="Verifikasi Data Selesai">
														<i class="base-7 icon-stack-3x color-success-400"></i>
														<i class="base-7 icon-stack-2x color-success-600 opacity-70"></i>
														<span class="icon-stack-1x text-white fw-500">{{$verifikasi->status}}</span>
													</span>
													<span hidden>{{$verifikasi->status}}</span>
												@elseif ($verifikasi->status === '9')
													<span class="icon-stack fa-2x" data-toggle="tooltip" data-original-title="Perbaikan">
														<i class="base-7 icon-stack-3x color-danger-300"></i>
														<i class="base-7 icon-stack-2x color-danger-600 opacity-70"></i>
														<span class="icon-stack-1x text-white fw-500">
															<i class="fa fa-exclamation"></i>
														</span>
													</span>
													<span hidden>{{$verifikasi->status}}</span>
												@endif --}}
											</td>
											<td class="">
												@if(!$verifikasi->skl)
													<a href="{{route('verification.skl.check', $verifikasi->id)}}"
														title="Lihat rekomendasi" class="mr-1 btn btn-xs btn-icon btn-info">
														<i class="fal fa-file-search"></i>
													</a>
												@else
													<a href="{{route('verification.skl.verifSklShow', $verifikasi->id)}}"
														data-toggle="tooltip" data-original-title="Klik untuk melihat data rekomendasi SKL." class="mr-1 btn btn-xs btn-icon btn-info">
														<i class="fal fa-file-certificate"></i>
													</a>
													{{-- {{dd($verifikasi->status)}} --}}
													@if ($verifikasi->status == 3)
														<a href="{{route('skl.print', $verifikasi->skl->pengajuan_id)}}" class="btn btn-xs btn-icon btn-danger" data-toggle="tooltip" data-original-title="Telah Disetujui. Segera cetak SKL">
															<i class="fal fa-print"></i>
														</a>
														<button class="btn btn-xs btn-icon btn-warning" type="button" title="Unggah SKL yang telah ditandatangani Pejabat" data-toggle="modal" data-target="#modalUploadSkl{{$verifikasi->skl->id}}">
															<i class="fas fa-upload text-align-center"></i>
														</button>

														{{-- modal upload skl --}}
														<div class="modal fade" id="modalUploadSkl{{$verifikasi->skl->id}}"
															tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
															<div class="modal-dialog modal-dialog-center" role="document">
																<div class="modal-content">
																	<div class="modal-header">
																		<div>
																			<h5 class="modal-title" id="myModalLabel">
																				Unggah Berkas SKL
																				id AVSKL: {{$verifikasi->skl->pengajuan_id}}
																			</h5>
																			<small id="helpId" class="text-muted">Unggah berkas SKL yang telah ditandatangani oleh Pejabat.</small>
																		</div>
																		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																			<span aria-hidden="true">&times;</span>
																		</button>
																	</div>
																	{{-- {{route('verification.skl.sklUpload', $recomend->skl->id)}} --}}
																	<form action="{{route('skl.upload', $verifikasi->skl->id)}}" method="post" enctype="multipart/form-data">
																		@csrf
																		@method('put')
																		<div class="modal-body">
																			<div class="form-group">
																				<label class="">Unggah hasil cetak SKL</label>
																				<div class="custom-file input-group">
																					<input type="file" accept=".pdf" class="custom-file-input" name="skl_upload" id="skl_upload">
																					<label class="custom-file-label" for="">Pilih berkas...</label>
																				</div>
																				<span class="help-block">Unggah Dokumen Pendukung. Ekstensi pdf ukuran maks 2mb.</span>
																			</div>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-warning btn-sm"
																				data-dismiss="modal">
																				<i class="fal fa-times-circle text-danger fw-500"></i> Close
																			</button>
																			<button class="btn btn-primary btn-sm" type="submit">
																				<i class="fal fa-upload mr-1"></i>Unggah
																			</button>
																		</div>
																	</form>
																</div>
															</div>
														</div>
													@endif
												@endif
											</td>
										</tr>
									@endforeach
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
			$('#dataPengajuan').dataTable({
				responsive: true,
				lengthChange: false,
				dom:
				"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'<'select'>>>" +
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
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
						extend: 'csvHtml5',
						text: '<i class="fal fa-file-csv"></i>',
						titleAttr: 'Generate CSV',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					},
					{
						extend: 'copyHtml5',
						text: '<i class="fa fa-copy"></i>',
						titleAttr: 'Copy to clipboard',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					}]
				});

				// Get the unique values of the "Year" column
				var table = $('#dataPengajuan').DataTable();
				var years = table.column(0).data().unique().sort();

				// Create the "Year" select element and add the options
				var selectYear = $('<select>')
					.attr('id', 'selectdataPengajuanYear')
					.addClass('custom-select custom-select-sm col-3 mr-2')
					.on('change', function() {
					var year = $.fn.dataTable.util.escapeRegex($(this).val());
					table.column(0).search(year ? '^' + year + '-|'+year+'$' : '', true, false).draw();
					});

				$('<option>').val('').text('Semua Tahun').appendTo(selectYear);
				$.each(years, function(i, year) {
					$('<option>').val(year.substring(0, 4)).text(year.substring(0, 4)).appendTo(selectYear);
				});

				// Create the "Status" select element and add the options
				var selectStatus = $('<select>')
					.attr('id', 'selectdataPengajuanStatus')
					.addClass('custom-select custom-select-sm col-3 mr-2')
					.on('change', function() {
					var status = $(this).val();
					table.column(4).search(status).draw();
					});

				$('<option>').val('').text('Semua Tahap').appendTo(selectStatus);
				$('<option>').val('1').text('Baru').appendTo(selectStatus);
				$('<option>').val('2').text('Berkas').appendTo(selectStatus);
				$('<option>').val('3').text('PKS').appendTo(selectStatus);
				$('<option>').val('4').text('Selesai').appendTo(selectStatus);
				$('<option>').val('5').text('Perbaikan').appendTo(selectStatus);

				// Add the select elements before the first datatable button in the second table
				$('#dataPengajuan_wrapper .dt-buttons').before(selectYear, selectStatus);
			});
	</script>
@endsection
