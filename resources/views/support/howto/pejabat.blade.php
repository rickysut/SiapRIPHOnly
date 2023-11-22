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
	@if (Auth::user()->roles[0]->title == 'Pejabat')
		@include('partials.sysalert')
		<div class="panel shadow" id="panel-1">
			<div class="panel-container show card-body embed-responsive embed-responsive-16by9">
				<iframe class="embed-responsive-item"
					src="{{ asset('docs/howto_pejabat.pdf') }}" width="100%" frameborder="0">
				</iframe>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
	@parent
	<script>
	</script>
@endsection
