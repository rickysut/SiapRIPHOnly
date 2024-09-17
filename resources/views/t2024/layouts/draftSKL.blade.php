<!DOCTYPE html>
<html lang="en" class="root-text-sm">
	<head>
		<meta charset="utf-8">
		<title>
			Simethris 3.0 | {{ ($page_title ?? '') }}
		</title>
		<meta name="description" content="Page Title">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
		<!-- Call App Mode on ios devices -->
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<!-- Remove Tap Highlight on Windows Phone IE -->
		<meta name="msapplication-tap-highlight" content="no">
		<!-- smartadmin base css -->
		<link rel="stylesheet" href="{{ asset('css/app.css') }}" />
		<link id="vendorsbundle" rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/vendors.bundle.css') }}">
		<link id="appbundle" rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/app.bundle.css') }}">
		<link id="mytheme" rel="stylesheet" media="screen, print" href="#">
		<link id="myskin" rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/skins/skin-master.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{asset('/css/smartadmin/page-invoice.css')}}">
		<link href="https://fonts.googleapis.com/css2?family=Tahoma&display=swap" rel="stylesheet">

		<!-- Place favicon.ico in the root directory -->

		<link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">
		<link href="{{ asset('img/favicon.png') }}" rel="icon" />
		<link href="{{ asset('img/logo-icon.png') }}" rel="apple-touch-icon" sizes="180x180" />
		<link href="{{ asset('img/logo-icon.png') }}" rel="safari-pinned-tab.svg" color="#5bbad5" />


		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/miscellaneous/reactions/reactions.css') }}">

		<!-- You can add your own stylesheet here to override any styles that comes before it
		<link rel="stylesheet" media="screen, print" href="css/your_styles.css">-->
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/datagrid/datatables/datatables.bundle.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/formplugins/dropzone/dropzone.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/formplugins/select2/select2.bundle.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/formplugins/summernote/summernote.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/miscellaneous/nestable/nestable.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/miscellaneous/reactions/reactions.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/skins/skin-master.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/statistics/c3/c3.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/statistics/chartist/chartist.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/statistics/chartjs/chartjs.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/fa-light.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/fa-regular.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/fa-solid.css') }}">
		<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/fa-brands.css') }}">


		<!-- coreui -->
		<link href="{{ asset('css/ajax/all.css') }}" rel="stylesheet" />
		<link href="{{ asset('css/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
		<link href="{{ asset('css/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" />
		<link href="{{ asset('css/datatables/select.dataTables.min.css') }}" rel="stylesheet" />

		<link href="{{ asset('css/toastr.css') }}" rel="stylesheet" />


		<meta name="csrf-token" contenst="{{ csrf_token() }}">
		<style>
			body {
				font-family: 'Tahoma', sans-serif;
			}
		</style>
		@yield('styles')
	</head>

	<body class="mod-pace-custom">  {{-- mod-skin-dark --}}
		<script src="{{ asset('js/smartadmin/pagesetting.js') }}"></script>
		<!-- begin page wrapper -->
		<div class="page-wrapper alt">
			@yield('content')
		</div>
		<form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
			{{ csrf_field() }}
		</form>
		<!-- end page wrapper -->
		<!-- begin quick menu -->
		{{-- @include('partials.navquickmenu') --}}
		<!-- end quick menu -->
		{{-- base app script --}}
		<script src="{{ asset('js/app.js') }}"></script>

		<!-- Smartadmin core -->


		<script src="{{ asset('js/vendors.bundle.js') }}"></script>

		<script src="{{ asset('js/app.bundle.js?v=1.1') }}"></script>
		<!-- Smartadmin plugin -->
		<script src="{{ asset('js/smartadmin/datagrid/datatables/datatables.bundle.js') }}"></script>
		<script src="{{ asset('js/smartadmin/datagrid/datatables/datatables.export.js') }}"></script>
		<script src="{{ asset('js/datatables/datetime.js') }}"></script>

		<script src="{{ asset('js/moment/moment.min.js') }}"></script>
		<script src="{{ asset('js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
		<script src="{{ asset('js/formplugins/dropzone/dropzone.js') }}"></script>
		<script src="{{ asset('js/formplugins/select2/select2.bundle.js') }}"></script>
		<script src="{{ asset('js/formplugins/summernote/summernote.js') }}"></script>
		<!-- Smartadmin misc -->
		<script src="{{ asset('js/smartadmin/miscellaneous/nestable/nestable.js') }}"></script>
		<!-- smartadmin statistics -->
		<script src="{{ asset('js/smartadmin/statistics/c3/c3.js') }}"></script>
		<script src="{{ asset('js/smartadmin/statistics/chartist/chartist.js') }}"></script>
		<script src="{{ asset('js/smartadmin/statistics/chartjs/chartjs.bundle.js') }}"></script>
		<script src="{{ asset('js/smartadmin/statistics/d3/d3.js') }}"></script>
		<script src="{{ asset('js/smartadmin/statistics/echart/echarts.min.js') }}"></script>
		<script src="{{ asset('js/smartadmin/statistics/easypiechart/easypiechart.bundle.js') }}"></script>
		<script src="{{ asset('js/smartadmin/statistics/sparkline/sparkline.bundle.js') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>


		<script src="{{ asset('js/toastr.js') }}"></script>


		<!-- coreui -->
		<script src="{{ asset('js/main.js?v=1.0.2') }}"></script>
		<script src="{{ asset('js/pdfmake/pdfmake.min.js') }}"></script>
		<script src="{{ asset('js/pdfmake/vfs_fonts.js') }}"></script>
		<script src="{{ asset('js/jszip/jszip.min.js') }}"></script>

		@yield('scripts')
	</body>
</html>
