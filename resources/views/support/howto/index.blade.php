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
	@can('administrator_access')
		@include('partials.sysalert')
		<div class="row">
			<div class="col-12">
				<div class="panel" id="panel-1">
					<div class="panel-container show">
						<div class="panel-content">

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
	</script>
@endsection
