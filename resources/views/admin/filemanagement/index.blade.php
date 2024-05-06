@extends('layouts.admin')

@section('styles')
	<style>
		.line-clamp-1 {
			display: -webkit-box;
			-webkit-line-clamp: 1;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}
	</style>
@endsection

@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
<div class="row">
	<div class="col-12">
		<div class="panel" id="panel-1">
			<div class="panel-container show">
				<div class="panel-content">
					<table id="datatable" class="table table-bordered table-hover table-striped w-100">
						<thead>
							<th width="25%">Form Template</th>
							<th width="40%">Template</th>
							<th>Diunggah</th>
							<th>Tindakan</th>
						</thead>
						<tbody>
							@foreach ($templates as $file)
							<tr>
								<td>{{$file->berkas}}</td>
								<td>
									<span class="font-weight-bold">{{$file->nama_berkas}}</span>
									<p class="line-clamp-1 small">{{$file->deskripsi}}</p>
								</td>
								<td>{{$file->created_at->format('d F Y')}}</td>
								<td class="d-flex">
										<form action="{{route('admin.template.delete', $file->id)}}" enctype="multipart/form-data">
											@csrf
											@method('DELETE')
											<a href="{{route('admin.template.download', $file->id)}}" class="btn btn-icon btn-xs btn-warning" data-toggle="tooltip" data-original-title="Unduh Berkas">
												<i class="fal fa-download"></i>
											</a>
											@can('administrator_access')
												{{-- <a href="" class="btn btn-icon btn-xs btn-warning" data-toggle="tooltip" data-original-title="Perbarui Berkas">
													<i class="fal fa-file-edit"></i>
												</a> --}}
												<button type="submit" class="btn btn-icon btn-xs btn-danger" data-toggle="tooltip" data-original-title="Hapus Berkas">
													<i class="fal fa-trash"></i>
												</button>
											@endcan
										</form>
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
@endsection

@section('scripts')
@parent
<script>
	$(document).ready(function()
	{
		$('#viewDocs').on('shown.bs.modal', function (e) {
			var docUrl = $(e.relatedTarget).data('doc');
			$('iframe').attr('src', docUrl);
		});

		// initialize datatable
		$('#datatable').dataTable(
		{
			responsive: true,
			lengthChange: false,
			order: [[2, 'desc']],

			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			buttons: [
				@can('administrator_access')
				{
					text: '<i class="fa fa-plus mr-1"></i> Tambah Templat',
					titleAttr: 'Create new template',
					className: 'btn btn-info btn-xs ml-2',
					action: function(e, dt, node, config) {
						window.location.href = '{{ route('admin.template.create') }}';
					},
				}
				@endcan
			]
		});
	});
</script>

@endsection
