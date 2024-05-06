@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
<div class="row">
	<div class="col-12">
		<div class="panel" id="panel-2">
			<div class="panel-container">
				<div class="panel-content">

					@if(count($templates) > 0)
						<ul>
							@foreach($templates as $file)
								<li>{{$file->nama_berkas}}</li>
							@endforeach
						</ul>
					@else
						<p>No files found.</p>
					@endif
					<div class="mt-5">
						<form action="{{route('test.files.delete')}}" method="post">
							@csrf
							@method('DELETE')
							<button>Delete</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="panel" id="panel-view">
			<div class="panel-container">
				<div class="panel-content">
					<table>
						<thead>
							<th>
								Berkas
							</th>
							<th>

							</th>
						</thead>
						<tbody>
							@if(count($templates) > 0)
								@foreach($templates as $file)
									<tr>
										<td>
											<a href="{{ asset('storage/uploads/master/'.$file->lampiran) }}">{{$file->nama_berkas}}</a>
										</td>
										<td>

										</td>
									</tr>
								@endforeach
							@else
								<tr>no files</tr>
							@endif
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
			order: [[4, 'desc']],
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
