<aside class="page-sidebar">
	<div class="page-logo">
		<a href="/admin" class="page-logo-link press-scale-down d-flex align-items-center position-relative">
			<img src="{{ asset('img/favicon.png') }}" alt="Simethris" aria-roledescription="logo">
			<img src="{{ asset('img/logo-icon.png') }}" class="page-logo-text mr-1" alt="Simethris"
				aria-roledescription="logo" style="width:50px; height:auto;">
		</a>

	</div>

	<!-- BEGIN PRIMARY NAVIGATION -->
	<nav id="js-primary-nav" class="primary-nav" role="navigation">

		{{-- search menu --}}
		<div class="nav-filter">
			<div class="position-relative">
				<input type="text" id="nav_filter_input" placeholder="Cari menu" class="form-control" tabindex="0">
				<a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off"
					data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
					<i class="fal fa-chevron-up"></i>
				</a>
			</div>
		</div>

		{{-- picture --}}
		<div class="info-card">
			@if (!empty(Auth::user()::find(Auth::user()->id)->data_user->avatar))
				<img src="{{ asset('storage/' . Auth::user()->data_user->avatar) }}" class="profile-image rounded-circle" alt="">
			@else
				<img src="{{ asset('/img/avatars/farmer.png') }}" class="profile-image rounded-circle" alt="">
			@endif

			<div class="info-card-text">
				<a href="#" class="d-flex align-items-center text-white">
					<span class="text-truncate text-truncate-sm d-inline-block">
						{{ Auth::user()->username }}
					</span>
				</a>
				<span class="d-inline-block text-truncate text-truncate-sm">
					{{ Auth::user()::find(Auth::user()->id)->data_user->company_name ?? 'user' }}
				</span>
			</div>
			<img src="{{ asset('/img/card-backgrounds/cover-2-lg.png') }}" class="cover" alt="cover">
			<a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle"
				data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
				<i class="fal fa-angle-down"></i>
			</a>
		</div>
		<div class="container" style="background-color: rgba(0, 0, 0, 0.2)">
			<ul id="date" class="list-table m-auto pt-3 pb-3">
				<li>
					<span class="d-inline-block" style="color:white"
						data-filter-tags="date day today todate">
						<span class="nav-link-text js-get-date">Hari ini</span>
					</span>
				</li>
			</ul>
		</div>
		<ul id="js-nav-menu" class="nav-menu">
			{{-- landing / beranda --}}
			@can('landing_access')
				<li class="c-sidebar-nav-item {{ request()->is('admin') ? 'active' : '' }}">
					<a href="{{ route('admin.home') }}" class="c-sidebar-nav-link"
						data-filter-tags="home beranda landing informasi berita pesan">
						<i class="c-sidebar-nav-icon fal fa-home-alt">
						</i>
						<span class="nav-link-text">{{ trans('cruds.landing.title_lang') }}</span>
					</a>
				</li>
			@endcan

			{{-- logout --}}
			<li class="c-sidebar-nav-item">
				<a href="#" class="c-sidebar-nav-link"
					data-filter-tags="keluar log out tutup"
					onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
					<i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

					</i>
					{{ trans('global.logout') }}
				</a>
			</li>
		</ul>
	</nav>
	<!-- END PRIMARY NAVIGATION -->

</aside>
