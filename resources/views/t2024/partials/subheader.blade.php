<section aria-label="Page Title Section" aria-describedby="#section-title">
	<div class="subheader d-print-none">
		<h1 class="subheader-title">
			<i class="subheader-icon {{ ($heading_class ?? '') }}"></i> {{  ($page_heading ?? '') }} <span class='fw-300'></span>
		</h1>
		<p>{{  ($page_desc ?? '') }}</p>
	</div>
	<div class="sr-only" id="section-title">{{  ($page_heading ?? '') }}</div>
</section>