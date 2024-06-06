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

			{{-- dashhboard --}}
			@can('dashboard_access')
				@if (Auth::user()->roles[0]->title == 'User')
					<li class="{{ request()->is('admin/dashboard*') ? 'active open' : '' }} ">
						<a href="#" title="Dashboard" data-filter-tags="dashboard pemantauan kinerja">
							<i class="fal fa-analytics"></i>
							<span class="nav-link-text">{{ trans('cruds.dashboard.title_lang') }}</span>
						</a>
						<ul>
							<li class="c-sidebar-nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
								<a href="{{ route('admin.dashboard') }}" title="Dashboard Data Monitor"
									data-filter-tags="dashboard data monitor kinerja">
									<i class="fa-fw fal fa-database c-sidebar-nav-icon"></i>
									<span class="nav-link-text">{{ trans('cruds.dashboardUser.title_lang') }}</span>
								</a>
							</li>
							<li class="c-sidebar-nav-item {{ request()->is('admin/dashboard/map') ? 'active' : '' }}">
								<a href="{{ route('admin.dashboard.map') }}" title="Peta Wajib Tanam"
									data-filter-tags="dashboard peta pemetaan wajib tanam">
									<i class="fa-fw fal fa-map c-sidebar-nav-icon"></i>
									<span class="nav-link-text">Peta Wajib Tanam</span>
								</a>
							</li>
						</ul>
					</li>
				@elseif (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Pejabat')
					<li class="{{ request()->is('admin/dashboard*') ? 'active open' : '' }} ">
						<a href="#" title="Dashboard" data-filter-tags="dashboard pemantauan kinerja">
							<i class="fal fa-analytics"></i>
							<span class="nav-link-text">{{ trans('cruds.dashboard.title_lang') }}</span>
						</a>
						<ul>
							<li class="c-sidebar-nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
								<a href="{{ route('admin.dashboard') }}" class="c-sidebar-nav-link"
									data-filter-tags="{{ strtolower(trans('cruds.dashboardAdmin.title_lang')) }}">
									<i
										class="fa-fw fal fa-stamp c-sidebar-nav-icon"></i>{{ trans('cruds.dashboardAdmin.title_lang') }}
								</a>
							</li>
							<li class="c-sidebar-nav-item {{ request()->is('admin/dashboard/map') ? 'active' : '' }}">
								<a href="{{ route('admin.dashboard.map') }}" title="Dashboard Pemetaan"
									data-filter-tags="dashboard pemetaan">
									<i class="fa-fw fal fa-map c-sidebar-nav-icon"></i><span class="nav-link-text">Pemetaan</span>
								</a>
							</li>
						</ul>
					</li>
				@elseif (Auth::user()->roles[0]->title == 'Verifikator')
					<li class="{{ request()->is('admin/dashboard*') ? 'active open' : '' }} ">
						<a href="#" title="Dashboard" data-filter-tags="dashboard pemantauan kinerja">
							<i class="fal fa-analytics"></i>
							<span class="nav-link-text">{{ trans('cruds.dashboard.title_lang') }}</span>
						</a>
						<ul>
							<li class="c-sidebar-nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
								<a href="{{ route('admin.dashboard') }}" class="c-sidebar-nav-link"
									data-filter-tags="{{ strtolower(trans('cruds.dashboardVerifikator.title_lang')) }}">
									<i
										class="fa-fw fal fa-stamp c-sidebar-nav-icon"></i>Monitoring {{ trans('cruds.dashboardVerifikator.title_lang') }}
								</a>
							</li>
							<li class="c-sidebar-nav-item {{ request()->is('admin/dashboard/map') ? 'active' : '' }}">
								<a href="{{ route('admin.dashboard.map') }}" title="Dashboard Pemetaan"
									data-filter-tags="dashboard pemetaan">
									<i class="fa-fw fal fa-map c-sidebar-nav-icon"></i><span class="nav-link-text">Pemetaan</span>
								</a>
							</li>
						</ul>
					</li>
				@endif
			@endcan

			{{-- user_task_access --}}
			@can('user_task_access')
				<li class="nav-title">Pelaporan Realisasi</li>
				@can('pull_access')
					<li class="c-sidebar-nav-item {{ request()->is('admin/task/pull') ? 'active' : '' }}">
						<a href="{{ route('admin.task.pull') }}"
							data-filter-tags="sinkronisasi sync tarik data siap riph">
							<i class="fa-fw fal fa-sync-alt c-sidebar-nav-icon">
							</i>
							{{ trans('cruds.pullSync.title_lang') }}
						</a>
					</li>
				@endcan
				@can('commitment_access')
					@if (Auth::user()->roles[0]->title == 'User')
						<li class="c-sidebar-nav-item {{ request()->is('admin/task/commitment') ||
						request()->is('admin/task/pks*') ? 'active' : '' }}">
							<a href="{{ route('admin.task.commitment') }}"
								data-filter-tags="daftar komitmen riph index">
								<i class="fa-fw fal fa-ballot c-sidebar-nav-icon"></i>
								{{ trans('cruds.commitment.title_lang') }}
							</a>
						</li>
					@endif
				@endcan
				{{-- pengajuan verifikasi --}}
				@can('pengajuan_access')
					{{-- <li class="c-sidebar-nav-item {{request()->is('admin/task/pengajuan*') ? 'active' : '' }}">
						@if (Auth::user()->roles[0]->title == 'User')
						<a href="{{ route('admin.task.pengajuan.index') }}" title="Pengajuan verifikasi"
							data-filter-tags="daftar pengajuan verifikasi data online onfarm">
							<i class="fa-fw fal fa-upload c-sidebar-nav-icon"></i>
							<span class="nav-link-text">
								Daftar Pengajuan Verifikasi
							</span>
						</a>
						@else

						@endif
					</li> --}}
				@endcan
				{{-- Skl terbit --}}

				@can('permohonan_access')
					<li class="c-sidebar-nav-item {{ request()->is('skl/arsip') ? 'active' : '' }}">
						<a href="{{route('admin.task.skl.arsip')}}"
							data-filter-tags="daftar skl terbit">
							<i class="fal fa-file-certificate c-sidebar-nav-icon"></i>
							<span class="nav-link-text text-wrap">
								Daftar SKL Terbits
							</span>
							@php
								$newSkl = new \App\Models\SklReads();
								$newSklCount = $newSkl->getNewSklCount();
							@endphp
							@if ($newSklCount > 0)
								<span class="dl-ref bg-danger-500 hidden-nav-function-minify hidden-nav-function-top">{{ $newSklCount }}</span>
							@endif
						</a>
					</li>
				@endcan
				@can('draft')
					<li class="{{ request()->is('admin/task/masterpenangkar')
						|| request()->is('admin/task/kelompoktani')
						|| request()->is('admin/task/masterpoktan')
						|| request()->is('admin/task/kelompoktani/*') ? 'active open' : '' }}">
						<a href="#" title="Kelompok tani"
							data-filter-tags="data master kelompoktani poktan penangkar pks">
							<i class="fa-fw fal fa-users c-sidebar-nav-icon"></i>
							<span class="nav-link-text">Master Penangkar dan Saprodi</span>
						</a>
						<ul>
							@can('poktan_access')
								@if (Auth::user()->roles[0]->title == 'user_v2')
									{{-- for later use only --}}
								@else
								@endif
								<li class="c-sidebar-nav-item {{ request()->is('admin/task/penangkar')
									|| request()->is('admin/task/penangkar/*') ? 'active' : '' }}">
									<a href="{{route('admin.task.penangkar')}}" title="Daftar Penangkar Benih Bawang Putih Berlabel"
										data-filter-tags="daftar master penangkar benih">
										<i class="fa-fw fal fa-users c-sidebar-nav-icon"></i>
										Master Penangkar
									</a>
								</li>
								<li class="c-sidebar-nav-item {{ request()->is('admin/task/saprodi') ? 'active' : '' }}">
									<a href="{{route('admin.task.saprodi.index')}}" title="Daftar Bantuan Saprodi">
										<i class="fa-fw fal fa-gifts c-sidebar-nav-icon"></i>
										Daftar Saprodi
									</a>
								</li>
							@endcan
						</ul>
					</li>
				@endcan
			@endcan

			{{-- verificator task --}}
			@can('verificator_task_access')
				<li class="nav-title" data-i18n="nav.administation">PENGAJUAN VERIFIKASI</li>
				@can('online_access')
					<li class="c-sidebar-nav-item {{ request()->is('verification/tanam')
						|| request()->is('verification/tanam*') ? 'active' : '' }}">
						<a href="{{ route('verification.tanam') }}"
							data-filter-tags="verifikasi tanam">
							<i class="fal fa-seedling c-sidebar-nav-icon"></i>
							<span class="nav-link-text">
								Verifikasi Tanam
							</span>
							@php
								$pengajuan = new \App\Models\AjuVerifTanam();
								$unverified = $pengajuan->NewRequest();
								$proceed = $pengajuan->proceedVerif();
							@endphp
							<span class="">
								{{-- untuk 2024 --}}
								@if ($unverified > 0 || $proceed > 0)
									<span class="dl-ref {{ $unverified > 0 ? 'bg-danger-500' : 'bg-warning-500' }} hidden-nav-function-minify hidden-nav-function-top">
										{{ $unverified }}/{{ $proceed }}
									</span>
								@endif
								{{-- @if ($unverified > 0)
									<span class="dl-ref bg-danger-500 hidden-nav-function-minify hidden-nav-function-top">{{ $unverified }}</span>
								@endif
								@if ($proceed > 0)
									<span class="dl-ref bg-warning-500 hidden-nav-function-minify hidden-nav-function-top">{{ $proceed }}</span>
								@endif --}}
							</span>
						</a>
					</li>
				@endcan
				@can('onfarm_access')
					<li class="c-sidebar-nav-item {{ request()->is('verification/produksi')
						|| request()->is('verification/produksi*') ? 'active' : '' }}">
						<a href="{{ route('verification.produksi') }}"
							data-filter-tags="verifikasi produksi">
							<i class="fal fa-dolly c-sidebar-nav-icon"></i>
							<span class="nav-link-text">Verifikasi Produksi</span>
							@php
								$pengajuan = new \App\Models\AjuVerifProduksi();
								$unverified = $pengajuan->NewRequest();
								$proceed = $pengajuan->proceedVerif();
							@endphp
							{{-- untuk 2024 --}}
							@if ($unverified > 0 || $proceed > 0)
								<span class="dl-ref {{ $unverified > 0 ? 'bg-danger-500' : 'bg-warning-500' }} hidden-nav-function-minify hidden-nav-function-top">
									{{ $unverified }}/{{ $proceed }}
								</span>
							@endif
							{{-- @if ($unverified > 0)
								<span class="dl-ref bg-danger-500 hidden-nav-function-minify hidden-nav-function-top">{{ $unverified }}</span>
							@endif
							@if ($proceed > 0)
								<span class="dl-ref bg-warning-500 hidden-nav-function-minify hidden-nav-function-top">{{ $proceed }}</span>
							@endif --}}
						</a>
					</li>
				@endcan
				@can('administrator_access')
					<li class="c-sidebar-nav-item {{ request()->is('verification/skl')
						|| request()->is('verification/skl*') ? 'active' : '' }}">
						<a href="{{ route('verification.skl') }}"
							data-filter-tags="verifikasi produksi">
							<i class="fal fa-award c-sidebar-nav-icon"></i>
							<span class="nav-link-text">Pengajuan SKL</span>
							@php
								$pengajuan = new \App\Models\AjuVerifSkl();
								$unverified = $pengajuan->NewRequest();
								$proceed = $pengajuan->proceedVerif();
							@endphp
							@if ($unverified > 0 || $proceed > 0)
							<span class="dl-ref {{ $unverified > 0 ? 'bg-danger-500' : 'bg-warning-500' }} hidden-nav-function-minify hidden-nav-function-top">
								{{ $unverified }}/{{ $proceed }}
							</span>
							@endif
							{{-- @if ($unverified > 0)
								<span class="dl-ref bg-danger-500 hidden-nav-function-minify hidden-nav-function-top">{{ $unverified }}</span>
							@endif
							@if ($proceed > 0)
								<span class="dl-ref bg-warning-500 hidden-nav-function-minify hidden-nav-function-top">{{ $proceed }}</span>
							@endif --}}
						</a>
					</li>
					{{-- <li class="c-sidebar-nav-item {{ request()->is('skl/recomended/list') ? 'active' : '' }}">
						<a href="{{ route('skl.recomended.list') }}"
							data-filter-tags="daftar rekomendasi skl terbit">
							<i class="fal fa-file-certificate c-sidebar-nav-icon"></i>
							<span class="nav-link-text text-wrap">Rekomendasi & SKL</span>
						@php
							$pengajuan = new \App\Models\Skl();
							$newApproved = $pengajuan->newApprovedCount();
						@endphp

						@if ($newApproved > 0)
							<span class="dl-ref bg-danger-500 hidden-nav-function-minify hidden-nav-function-top">{{ $newApproved }}</span>
						@endif
						</a>
					</li> --}}
					<li class="c-sidebar-nav-item {{ request()->is('skl/arsip') ? 'active' : '' }}">
						<a href="{{ route('skl.arsip') }}"
							data-filter-tags="daftar skl terbit">
							<i class="fal fa-file-certificate c-sidebar-nav-icon"></i>
							<span class="nav-link-text text-wrap">Daftar SKL Terbit</span>
							{{-- @php
								$newSkl = new \App\Models\SklReads();
								$newSklCount = $newSkl->getNewSklCount();
							@endphp
							@if ($newSklCount > 0)
								<span class="dl-ref bg-danger-500 hidden-nav-function-minify hidden-nav-function-top">{{ $newSklCount }}</span>
							@endif --}}
						</a>
					</li>
				@endcan
			@endcan
			{{-- direktur task --}}
			@if (Auth::user()->roles[0]->title == 'Pejabat')
				<li class="nav-title" data-i18n="nav.administation">Menu</li>
				<li class="c-sidebar-nav-item {{ request()->is('verification/skl/recomendation*') ? 'active' : '' }}">
					<a href="{{ route('verification.skl.recomendations') }}"
						data-filter-tags="daftar rekomendasi penerbitan skl"
						title="Daftar Rekomendasi Penerbitan SKL">
						<i class="fa-fw fal fa-file-signature c-sidebar-nav-icon"></i>
						<span class="nav-link-text">
							Permohonan SKL
						</span>
						@php
							$skl = new \App\Models\Skl();
							$newRecomendation = $skl->NewRecomendation();
						@endphp

						@if ($newRecomendation > 0)
							<span class="dl-ref bg-danger-500 hidden-nav-function-minify hidden-nav-function-top">{{ $newRecomendation }}</span>
						@endif
					</a>
				</li>
				<li class="c-sidebar-nav-item {{ request()->is('skl/arsip') ? 'active' : '' }}">
					<a href="{{ route('skl.arsip') }}"
						data-filter-tags="verifikasi selesai">
						<i class="fal fa-file-certificate c-sidebar-nav-icon"></i>
						<span class="nav-link-text text-wrap">
							SKL Diterbitkan
						</span>
					</a>
				</li>
			@endif

			{{-- pengelolaan berkas permohonan_access --}}
			@can('permohonan_access')
				<li class="nav-title">Pengelolaan Berkas</li>
				@can('template_access')
					<li class="c-sidebar-nav-item {{ request()->is('admin/template')
						|| request()->is('admin/template/*') ? 'active' : '' }}">
						<a href="{{ route('admin.template.index') }}" title="Master Template"
							data-filter-tags="daftar berkas file template">
							<i class="fa-fw fab fa-stack-overflow c-sidebar-nav-icon"></i>
							<span class="nav-link-text">{{ trans('cruds.template.title_lang') }}</span>
						</a>
					</li>
				@endcan
			@endcan

			{{-- Feed & Messages feedmsg_access --}}
			@can('feedmsg_access')
				<li class="nav-title">BERITA & PESAN</li>
				@can('feeds_access')
					{{-- <li class="{{ request()->is('admin/posts*')
						|| request()->is('admin/categories*') ? 'active open' : '' }}">
						<a href="#" title="Artikel/Berita"
							data-filter-tags="artikel berita informasi">
							<i class="fa-fw fal fa-rss c-sidebar-nav-icon"></i>
							<span class="nav-link-text">Artikel/Berita</span>
						</a>
						<ul>
							@can('feeds_access')
							<li class="c-sidebar-nav-item {{ request()->is('admin/categories')
								|| request()->is('admin/categories/*') ? 'active' : '' }}">
								<a href="{{ route('admin.categories.index') }}" title="Categories"
									data-filter-tags="categories kategori">
									<i class="fa-fw fal fa-rss c-sidebar-nav-icon"></i>
									Categories
								</a>
							</li>
							<li class="c-sidebar-nav-item {{ request()->is('admin/posts')
								|| request()->is('admin/posts/*') ? 'active' : '' }}">
								<a href="{{ route('admin.posts.index') }}" title="Posts"
									data-filter-tags="post artikel berita">
									<i class="fa-fw fal fa-rss c-sidebar-nav-icon"></i>
									Articles
								</a>
							</li>
							@endcan
						</ul>
					</li> --}}
				@endcan
				@can('messenger_access')
					@php($unread = \App\Models\QaTopic::unreadCount())
					<li class="c-sidebar-nav-item {{ request()->is('admin/messenger')
						|| request()->is('admin/messenger/*') ? 'active' : '' }}">
						<a href="{{ route('admin.messenger.index') }}"
							data-filter-tags="kirim pesan perpesanan send message messenger">
							<i class="c-sidebar-nav-icon fal fa-envelope"></i>
							<span class="nav-link-text">{{ trans('global.messages') }}</span>
							@if ($unread > 0)
								<span
									class="dl-ref bg-primary-500 hidden-nav-function-minify hidden-nav-function-top">{{ $unread }}
									pesan</span>
							@endif
						</a>
					</li>
				@endcan
			@endcan
			{{-- end feed --}}

			{{-- administrator access --}}
			@if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Pejabat')
			@can('administrator_access')
				<li class="nav-title" data-i18n="nav.administation">ADMINISTRATOR</li>
				{{-- user Management --}}
				@can('user_management_access')
					<li class="{{ request()->is('admin/permissions*')
						|| request()->is('admin/roles*') || request()->is('admin/users*')
						|| request()->is('admin/audit-logs*') ? 'active open' : '' }} ">
						<a href="#" title="User Management"
							data-filter-tags="setting permission user">
							<i class="fal fal fa-users"></i>
							<span class="nav-link-text">{{ trans('cruds.userManagement.title_lang') }}</span>
						</a>
						<ul>
							@can('permission_access')
								<li class="c-sidebar-nav-item {{ request()->is('admin/permissions')
									|| request()->is('admin/permissions/*') ? 'active' : '' }}">
									<a href="{{ route('admin.permissions.index') }}" title="Permission"
										data-filter-tags="setting daftar permission user">
										<i class="fa-fw fal fa-unlock-alt c-sidebar-nav-icon"></i>
										<span class="nav-link-text">{{ trans('cruds.permission.title_lang') }}</span>
									</a>
								</li>
							@endcan
							@can('role_access')
								<li class="c-sidebar-nav-item {{ request()->is('admin/roles')
									|| request()->is('admin/roles/*') ? 'active' : '' }}">
									<a href="{{ route('admin.roles.index') }}" title="Roles"
										data-filter-tags="setting role user">
										<i class="fa-fw fal fa-briefcase c-sidebar-nav-icon"></i>
										<span class="nav-link-text">{{ trans('cruds.role.title_lang') }}</span>
									</a>
								</li>
							@endcan
							@can('user_access')
								<li class="c-sidebar-nav-item {{ request()->is('admin/users')
									|| request()->is('admin/users/*') ? 'active' : '' }}">
									<a href="{{ route('admin.users.index') }}" title="User"
										data-filter-tags="setting user pengguna">
										<i class="fa-fw fal fa-user c-sidebar-nav-icon"></i>
										<span class="nav-link-text">{{ trans('cruds.user.title_lang') }}</span>
									</a>
								</li>
							@endcan
							@can('audit_log_access')
								<li class="c-sidebar-nav-item {{ request()->is('admin/audit-logs')
									|| request()->is('admin/audit-logs/*') ? 'active' : '' }}">
									<a href="{{ route('admin.audit-logs.index') }}" title="Audit Log"
										data-filter-tags="setting log_access audit">
										<i class="fa-fw fal fa-file-alt c-sidebar-nav-icon"></i>
										<span class="nav-link-text">{{ trans('cruds.auditLog.title_lang') }}</span>
									</a>
								</li>
							@endcan
						</ul>
					</li>
				@endcan

				{{-- Master data RIPH --}}
				@can('master_riph_access')
					<li class="c-sidebar-nav-item {{ request()->is('admin/riphAdmin') || request()->is('admin/riphAdmin/*') ? 'active' : '' }}">
						<a href="{{ route('admin.riphAdmin.index') }}"
							data-filter-tags="data benchmark riph tahunan">
							<i class="fab fa-stack-overflow c-sidebar-nav-icon"></i>{{ trans('cruds.masterriph.title_lang') }}
						</a>
					</li>
					<li class="c-sidebar-nav-item {{ request()->is('admin/riphAdmin') || request()->is('admin/riphAdmin/*') ? 'active' : '' }}">
						<a href="{{ route('admin.locationexport') }}"
							data-filter-tags="data benchmark riph tahunan">
							<i class="fab fa-stack-overflow c-sidebar-nav-icon"></i>
							Eksport Data Lokasi
						</a>
					</li>
				@endcan

				{{-- Master template --}}
				{{-- @can('template_access') --}}
					<li class="c-sidebar-nav-item {{ request()->is('admin/template') || request()->is('admin/template/*') ? 'active' : '' }}">
						<a href="{{ route('admin.template.index') }}"
							data-filter-tags="{{ strtolower(trans('cruds.mastertemplate.title_lang')) }}">
							<i class="fab fa-stack-overflow c-sidebar-nav-icon"></i>{{ trans('cruds.mastertemplate.title_lang') }}
						</a>
					</li>
				{{-- @endcan --}}

				{{-- data report --}}
				@can('data_report_access')
					<li hidden
						class="{{ request()->is('admin/datareport') || request()->is('admin/datareport/*') ? 'active open' : '' }}">
						<a href="#" title="Data Report"
							data-filter-tags="lapoan wajib tanam produksi report realisasi">
							<i class="fal fa-print c-sidebar-nav-icon"></i>
							<span class="nav-link-text">{{ trans('cruds.datareport.title_lang') }}</span>
						</a>
						<ul>
							@can('commitment_list_access')
								<li class="c-sidebar-nav-item {{ request()->is('admin/datareport/comlist') ? 'active' : '' }}">
									<a href="{{ route('admin.audit-logs.index') }}" title="Commitment List"
										data-filter-tags="laporan realisasi komitmen">
										<i class="fa-fw fal fa-file-alt c-sidebar-nav-icon"></i>
										<span class="nav-link-text">{{ trans('cruds.commitmentlist.title_lang') }}</span>
									</a>
								</li>
							@endcan
							@can('verification_report_access')
								<li
									class="c-sidebar-nav-item {{ request()->is('admin/datareport/verification') ? 'active' : '' }}">
									<a href="#" title="Audit Log"
										data-filter-tags="laporan realisasi verifikasi">
										<i class="fa-fw fal fa-file-alt c-sidebar-nav-icon"></i>
										<span class="nav-link-text">{{ trans('cruds.verificationreport.title_lang') }}</span>
									</a>
									<ul>
										@can('verif_onfarm_access')
											<li>
												<a href=""title="Onfarm"
													data-filter-tags="laporan realisasi verifikasi onfarm">
													<i class="fa-fw fal fa-file-alt c-sidebar-nav-icon"></i>
													<span class="nav-link-text">{{ trans('cruds.verifonfarm.title_lang') }}</span>
												</a>
											</li>
										@endcan
										@can('verif_online_access')
											<li>
												<a href=""title="Online"
													data-filter-tags="laporan realisasi verifikasi online">
													<i class="fa-fw fal fa-file-alt c-sidebar-nav-icon"></i>
													<span class="nav-link-text">{{ trans('cruds.verifonline.title_lang') }}</span>
												</a>
											</li>
										@endcan

									</ul>
								</li>
							@endcan
						</ul>
					</li>
				@endcan


				@can('varietas_access')
					<li hidden class="{{ request()->is('admin/daftarpejabat*') ? 'active open' : '' }} ">
						<a href="{{route('admin.pejabats')}}" title="Daftar Pejabat Penandatangan SKL"
							data-filter-tags="setting permission user">
							<i class="fal fa-user-tie"></i>
							<span class="nav-link-text">Daftar Pejabat</span>
						</a>
					</li>
					<li class="{{ request()->is('admin/varietas*') ? 'active open' : '' }} ">
						<a href="{{route('admin.varietas')}}" title="Daftar Varietas Hortikultura"
							data-filter-tags="setting permission user">
							<i class="fal fa-seedling"></i>
							<span class="nav-link-text">Daftar Varietas</span>
						</a>
					</li>
					<li class="{{ request()->is('admin/gmapapi*') ? 'active open' : '' }} ">
						<a href="{{route('admin.gmapapi.edit')}}" title="Goole Map API"
							data-filter-tags="google map api key">
							<i class="fab fa-google"></i>
							<span class="nav-link-text">Google Map API</span>
						</a>
					</li>
				@endcan
			@endcan
			@endif

			{{-- support --}}
			<li class="nav-title" data-i18n="nav.administation">DUKUNGAN</li>
			@can('administrator_access')
			<li class="c-sidebar-nav-item {{ request()->is('support/how_to/administrator') ? 'active' : '' }}">
				<a href="{{route('support.howto.administrator')}}" class="c-sidebar-nav-link"
					data-filter-tags="dukungan support panduan">
					<i class="c-sidebar-nav-icon fal fa-books">
					</i>
					<span class="nav-link-text">Panduan Adminisrator</span>
				</a>
			</li>
			@endcan
			@can('verificator_task_access')
			<li class="c-sidebar-nav-item {{ request()->is('support/how_to/verifikator') ? 'active' : '' }} ">
				<a href="{{route('support.howto.verifikator')}}" title="Panduan Penggunaan Aplikasi bagi Verifikator"
					data-filter-tags="dukungan support panduan">
					<i class="c-sidebar-nav-icon fal fa-books"></i>
					<span class="nav-link-text">Panduan Verifikator</span>
				</a>
			</li>
			@endcan
			@can('user_task_access')
			<li class="c-sidebar-nav-item {{ request()->is('support/how_to/importir') ? 'active' : '' }} ">
				<a href="{{route('support.howto.importir')}}" title="Panduan Penggunaan Aplikasi bagi Pelaku Usaha"
				data-filter-tags="dukungan support panduan">
					<i class="c-sidebar-nav-icon fal fa-books"></i>
					<span class="nav-link-text">Panduan Pelaku Usaha</span>
				</a>
			</li>
			@endcan
			@if (Auth::user()->roles[0]->title == 'Pejabat')
			<li class="c-sidebar-nav-item {{ request()->is('support/how_to/pejabat') ? 'active open' : '' }} ">
				<a href="{{route('support.howto.pejabat')}}" title="Panduan Penggunaan Aplikasi bagi Pejabat"
				data-filter-tags="dukungan support panduan">
					<i class="c-sidebar-nav-icon fal fa-books"></i>
					<span class="nav-link-text">Panduan Pejabat</span>
				</a>
			</li>
			@endif
			<li class="disabled">
				<a href="javascript:void(0);" title="Coming soon!"
				data-filter-tags="dukungan support tiket" disabled>
					<i class="fal fa-ticket"></i>
					<span class="nav-link-text">Tiket Bantuan</span>
				</a>
			</li>

			@can('spatial_data_access')
			<li class="nav-title" data-i18n="nav.administation">SPATIAL SYSTEM</li>
			@endcan

			{{-- personalisasi --}}
			<li class="nav-title" data-i18n="nav.administation">PERSONALISASI</li>
			{{-- Change Password --}}
			@if (Auth::user()->roles[0]->title !== 'User')
				@if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
					@can('profile_password_edit')
						<li
							class="c-sidebar-nav-item {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}">
							<a href="{{ route('profile.password.edit') }}"
								data-filter-tags="personalisasi ganti ubah change password ">
								<i class="fa-fw fas fa-key c-sidebar-nav-icon">
								</i>
								{{ trans('global.change_password') }}
							</a>
						</li>
					@endcan
				@endif
			@endif

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
