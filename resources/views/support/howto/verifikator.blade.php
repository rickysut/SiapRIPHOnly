@extends('layouts.admin')
@section ('styles')
	<style>
		td {
			vertical-align: middle !important;
		}
	</style>
@endsection
@section('content')
	@include('partials.subheader')
	@can('verificator_task_access')
		@include('partials.sysalert')
		<div class="panel shadow" id="panel-1">
			<div class="panel-container show card-body embed-responsive embed-responsive-16by9">
				<iframe class="embed-responsive-item"
					src="{{ $asset }}" width="100%" frameborder="0">
				</iframe>
			</div>
		</div>
	@endcan
@endsection

@section('scripts')
	@parent
	<script>
	</script>
@endsection
