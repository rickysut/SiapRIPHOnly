<div class="subheader d-print-none align-items-center">
	<div class="col-sm-9">
		<h1 class="subheader-title">
			<i class="subheader-icon {{ ($heading_class ?? '') }}"></i> {{  ($page_heading ?? '') }} <span class='fw-300'></span>
		</h1>
		<p>{{  ($page_desc ?? '') }}</p>
	</div>
	<div class="col-sm-3">
		<div class="form-group">
			<label class="form-label" for="provinsi"></label>
			<div class="input-group">
				<select class="form-control custom-select select2-commitment"
				name="commitmentId" id="commitmentId" required>
					<option value=""></option>
					<option value="all">Semua Tahun</option>
					@foreach($commitmentbackdates as $commitmentbackdate)
						<option value="{{$commitmentbackdate->id}}">{{$commitmentbackdate->no_ijin}}</option>
					@endforeach
				</select>
			</div>
			<div class="help-block">
			</div>
		</div>	
	</div>
</div>