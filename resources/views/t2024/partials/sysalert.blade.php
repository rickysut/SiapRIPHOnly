@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<div class="d-flex flex-start w-100">
			<div class="mr-2 hidden-md-down">
				<span class="icon-stack icon-stack-lg">
					<i class="base base-7 icon-stack-3x opacity-100 color-success-500"></i>
					<i class="base base-7 icon-stack-2x opacity-100 color-success-300 fa-flip-vertical"></i>
					<i class="fas fa-check icon-stack-1x opacity-100 color-white"></i>
				</span>
			</div>
			<div class="d-flex flex-fill">
				<div class="flex-fill">
					<span class="h5">Success</span>
					<p>
						{{ $message }}
					</p>
				</div>
			</div>
		</div>
	</div>
@elseif ($message = Session::get('error'))
	<div class="alert alert-danger">
		<div class="d-flex flex-start w-100">
			<div class="mr-2 hidden-md-down">
				<span class="icon-stack icon-stack-lg">
					<i class="base base-7 icon-stack-3x opacity-100 color-error-500"></i>
					<i class="base base-7 icon-stack-2x opacity-100 color-error-300 fa-flip-vertical"></i>
					<i class="fas fa-times icon-stack-1x opacity-100 color-white"></i>
				</span>
			</div>
			<div class="d-flex flex-fill">
				<div class="flex-fill">
					<span class="h5">Error</span>
					<p>
						{{ $message }}
					</p>
				</div>
			</div>
		</div>
	</div>
@else
@endif