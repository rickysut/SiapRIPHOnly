@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
	{{-- @can('old_skl_access') --}}
		@include('partials.sysalert')
		<div class="row">
			<div class="col-lg-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content">
							<div class="table">
								<table id="completeds" class="table table-sm table-bordered table-hover table-striped w-100">
									<thead>
										<tr>
											<th>No SKL</th>
											@if (Auth::user()->roleaccess === 1)
												<th>Perusahaan</th>
											@endif
											<th>Periode </th>
											<th>Nomor RIPH</th>
											<th>Tanggal Terbit</th>
											<th>Tanggal diunggah</th>
											<th>Tindakan</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($completeds as $completed)
											@php
												$sklRead = \App\Models\SklReads::where('skl_id', $completed->skl->id)
													->where('user_id', Auth::id())
													->first();
											@endphp
											<tr  style="{{ !$sklRead ? 'background-color: rgba(255, 166, 0, 0.37)' : '' }}">
												<td>{{$completed->no_skl}}</td>
												@if (Auth::user()->roleaccess === 1)
													<td>{{$completed->datauser->company_name}}</td>
												@endif
												<td>{{$completed->periodetahun}}</td>
												<td>{{$completed->no_ijin}}</td>
												<td class="text-center">{{ date('d-m-Y', strtotime($completed->published_date)) }}</td>
												<td class="text-center">{{ date('d-m-Y', strtotime($completed->created_at)) }}</td>
												<td class="text-center d-flex justify-content-center">
													@if (Auth::user()->roles[0]->title == 'Admin' ||  Auth::user()->roles[0]->title == 'Verifikator')
													<a href="{{route('verification.skl.verifSklShow', $completed->skl->pengajuan_id)}}" class="btn btn-icon btn-info btn-xs mr-1" title="Lihat Hasil Verifikasi">
														<i class="fal fa-file-search"></i>
													</a>
													@endif
													@if (Auth::user()->roles[0]->title == 'User')
													<a href="{{route('admin.task.pengajuan.skl.show', $completed->commitment->id)}}" class="btn btn-xs btn-info btn-icon mr-1" data-toggle="tooltip" title data-original-title="SKL sudah Terbit. Klik untuk melihat Ringkasan Verifikasi.">
														<i class="fal fa-file-certificate"></i>
													</a>
													@endif
													<a href="{{ $completed->url }}" class="btn btn-icon btn-success btn-xs mr-1" title="Lihat SKL" onClick="markAsRead({{ $completed->skl->id }})" target="_blank">
														<i class="fal fa-file-certificate"></i>
													</a>
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
		</div>
	{{-- @endcan --}}
@endsection

@section('scripts')
@parent

<script>
	$(document).ready(function()
	{
		// initialize tblPenangkar
		$('#completeds').dataTable(
		{
			responsive: true,
			lengthChange: false,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
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
				}
			]
		});

	});

	// Fungsi untuk menandai SKL sebagai sudah dibaca
	function markAsRead(sklId) {
		var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Ambil token CSRF

		// Kirim permintaan Ajax ke metode controller untuk menandai SKL sebagai sudah dibaca
		$.ajax({
			type: 'POST',
			url: '{{ route('admin.sklReads') }}', // Menggunakan route yang sesuai
			data: {
				skl_id: sklId,
				_token: csrfToken // Sertakan token CSRF di sini
			},
			success: function(response) {
				// Setelah berhasil menandai, buka URL tautan
				window.location.href = event.target.getAttribute('href');
			}
		});
	}
</script>

@endsection

