<!-- BEGIN Page Header -->
@php($unreadmsg = \App\Models\QaTopic::unreadCount())
@php($msgs = \App\Models\QaTopic::unreadMsg())

@if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Pejabat' || Auth::user()->roles[0]->title == 'Verifikator')
	{{-- tanam --}}
	@php($cntAjuVerifTanam = \App\Models\AjuVerifTanam::newPengajuanCount())
	@php($getAjuVerifTanam = \App\Models\AjuVerifTanam::getNewPengajuan())
	{{-- produksi --}}
	@php($cntAjuVerifProduksi = \App\Models\AjuVerifProduksi::newPengajuanCount())
	@php($getAjuVerifProduksi = \App\Models\AjuVerifProduksi::getNewPengajuan())
	{{-- skl --}}
	@php($cntAjuVerifSkl = \App\Models\AjuVerifSkl::newPengajuanCount())
	@php($getAjuVerifSkl = \App\Models\AjuVerifSkl::getNewPengajuan())

	@php($cntNewSkl = \App\Models\SklReads::getNewSklCount())
	@php($cntgetNewSkl = \App\Models\SklReads::getNewSklCount())

	@php($cntpengajuan = $cntAjuVerifTanam + $cntAjuVerifProduksi + (Auth::user()->roles[0]->title == 'Admin' ? $cntAjuVerifSkl : 0))


	{{-- rekomendasi --}}
	@php($cntRecomendations = \App\Models\Skl::newPengajuanCount())
	@php($getRecomendations = \App\Models\Skl::getNewPengajuan())

@else
	@php($cntAjuVerifTanam = 0)
	@php($cntAjuVerifTanam = null)
	@php($cntAjuVerifProduksi = 0)
	@php($getAjuVerifProduksi = null)
	@php($cntAjuVerifSkl = 0)
	@php($getAjuVerifSkl = null)
	@php($cntpengajuan = 0)
	@php($cntRecomendations = 0)
	@php($getRecomendations = null)
@endif

<header class="page-header" role="banner">
	<!-- we need this logo when user switches to nav-function-top -->
	<div class="page-logo">
		<a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative"
			data-toggle="modal" data-target="#modal-shortcut">
			<img src="{{ asset('/img/logo-icon.png') }}" alt="{{ trans('panel.site_title') }} WebApp"
				aria-roledescription="logo">
			<span class="page-logo-text mr-1">{{ trans('panel.site_title') }} WebApp</span>
			<span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
			<i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
		</a>
	</div>
	<!-- DOC: nav menu layout change shortcut -->
	<div class="hidden-md-down dropdown-icon-menu position-relative">
		<a href="#" class="header-btn btn js-waves-off" data-action="toggle" data-class="nav-function-hidden"
			title="Hide Navigation">
			<i class="ni ni-menu"></i>
		</a>

	</div>
	<!-- DOC: mobile button appears during mobile width -->
	<div class="hidden-lg-up">
		<a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on">
			<i class="ni ni-menu"></i>
		</a>
	</div>
	{{-- <div class="search">
		<select class="searchable-field form-control"></select>
	</div> --}}
	<div class="ml-auto d-flex">
		<!-- activate app search icon (mobile) -->
		<div class="hidden-sm-up">
			<a href="#" class="header-icon" data-action="toggle" data-class="mobile-search-on"
				data-focus="search-field" title="Search">
				<i class="fal fa-search"></i>
			</a>
		</div>
		<!-- app settings -->
		{{-- <div class="hidden-md-down">
			<a href="#" class="header-icon" data-toggle="modal" title="Penyesuaian"
				data-target=".js-modal-settings">
				<i class="fal fa-cog"></i>
			</a>
		</div> --}}

		<div>
			<a href="#" class="header-icon" data-toggle="dropdown"
				title="{{ $unreadmsg }} pesan @if (Auth::user()->roles[0]->title == 'Admin'), {{ $cntpengajuan}} Pengajuan Baru, 0 SKL Baru diterbitkan @elseif (Auth::user()->roles[0]->title == 'Pejabat'), {{$cntRecomendations}} Rekomendasi SKL diajukan @elseif (Auth::user()->roles[0]->title == 'Verifikator'), {{$cntpengajuan}} Pengajuan Baru @endif">
				<i class="fal fa-envelope"></i>
				@if (Auth::user()->roles[0]->title == 'Admin')
				<span class="badge badge-icon">{{ $unreadmsg  +  $cntpengajuan }} </span>
				@elseif (Auth::user()->roles[0]->title == 'Pejabat')
					<span class="badge badge-icon">{{ $unreadmsg  +  $cntRecomendations}} </span>
				@elseif (Auth::user()->roles[0]->title == 'Verifikator')
					<span class="badge badge-icon">{{ $unreadmsg  +  $cntpengajuan}} </span>
				@endif
				@if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Verifikator')
					<span class="badge badge-icon">{{ $unreadmsg  +  $cntpengajuan }} </span>
				@elseif (Auth::user()->roles[0]->title == 'Pejabat')
					<span class="badge badge-icon">{{ $unreadmsg  +  $cntRecomendations}} </span>
				@endif
			</a>
			<div class="dropdown-menu dropdown-menu-animated dropdown-xl">
				<div
					class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center rounded-top mb-2">
					<h4 class="m-0 text-center color-white">
						<small class="mb-0 opacity-80">{{ $unreadmsg }} Pesan baru</small>
						@if (Auth::user()->roles[0]->title == 'Admin')
							<small class="mb-0 opacity-80">{{ $cntpengajuan }} Pengajuan baru</small>
						@elseif (Auth::user()->roles[0]->title == 'Pejabat')
							<small class="mb-0 opacity-80">{{ $cntRecomendations }} Rekomendasi Baru</small>
						@elseif (Auth::user()->roles[0]->title == 'Verifikator')
							<small class="mb-0 opacity-80">{{ $cntpengajuan }} Pengajuan baru</small>
						@endif
					</h4>
				</div>
				<ul class="nav nav-tabs nav-tabs-clean" role="tablist">
					<li class="nav-item">
						<a class="nav-link px-4 fs-md js-waves-on fw-500" data-toggle="tab" href="#tab-messages"
							data-i18n="drpdwn.messages">Pesan</a>
					</li>
					@if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Verifikator')
						<li class="nav-item">
							<a class="nav-link px-4 fs-md js-waves-on fw-500" data-toggle="tab" href="#tab-feeds"
								data-i18n="drpdwn.feeds">Pengajuan</a>
						</li>
						@if (Auth::user()->roles[0]->title == 'Admin')
							<li class="nav-item">
								<a class="nav-link px-4 fs-md js-waves-on fw-500" data-toggle="tab" href="#tab-new-skl" data-i18n="drpdwn.feeds">SKL Baru</a>
							</li>
						@endif
					@elseif (Auth::user()->roles[0]->title == 'Pejabat')
						<li class="nav-item">
							<a class="nav-link px-4 fs-md js-waves-on fw-500" data-toggle="tab" href="#tab-feeds"
								data-i18n="drpdwn.feeds">Rekomendasi SKL</a>
						</li>
					@endif
				</ul>
				<div class="tab-content tab-notification">
					<div class="tab-pane" id="tab-messages" role="tabpanel">
						<div class="custom-scroll h-100">
							<ul class="notification">
								@foreach ($msgs as $item)
									<li class="unread">
										<a href="{{ route('admin.messenger.showMessages', $item['id']) }}"
											class="d-flex align-items-center">
											<span class="mr-2">
												@php($user = \App\Models\User::getUserById($item['sender']))
												@if (!empty($user[0]->data_user->logo))
													<img src="{{ Storage::disk('public')->url($user[0]->data_user->logo) }}"
														class="profile-image rounded-circle" alt="">
												@else
													<img src="{{ asset('/img/favicon.png') }}"
														class="profile-image rounded-circle" alt="">
												@endif
											</span>
											<span class="d-flex flex-column flex-1 ml-1">
												<span class="name">{{ $user[0]->name }}<span
														class="badge badge-primary fw-n position-absolute pos-top pos-right mt-1">INBOX</span></span>
												<span class="msg-a fs-sm">{{ $item['subject'] }}</span>
												<span class="msg-b fs-xs">{{ $item['content'] }}</span>
												<span
													class="fs-nano text-muted mt-1">{{ $item['create_at']->diffForHumans() }}</span>
											</span>
										</a>
									</li>
								@endforeach
							</ul>
						</div>
					</div>
					@if (Auth::user()->roleaccess == '1')
						<div class="tab-pane" id="tab-feeds" role="tabpanel">
							<div class="custom-scroll h-100">
								<ul class="notification notification-1">
									@if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Verifikator')
										@foreach ($getAjuVerifTanam as $item)
											<li>
												<a href="{{ route('verification.tanam.check', [$item->id]) }}"  class="d-flex align-items-center show-child-on-hover">
													<span class="mr-2">
														@if (!empty($item->data_user->logo))
															<img src="{{ Storage::disk('public')->url($item->data_user->logo) }}"
																class="profile-image rounded-circle" alt="">
														@else
															<img src="{{ asset('/img/avatars/farmer.png') }}"
																class="profile-image rounded-circle" alt="">
														@endif
													</span>
													<span class="d-flex flex-column flex-1">
														<span class="name">{{ $item->datauser->company_name }} <span
															class="badge badge-success fw-n position-absolute pos-top pos-right mt-1">NEW</span></span>
														<span class="msg-a fs-sm">
															<span class="badge badge-success">Verifikasi Tanam</span>
														</span>
														<span class="fs-nano text-muted mt-1">{{ $item->created_at->diffForHumans() }}</span>
													</span>
												</a>
											</li>
										@endforeach
										@foreach ($getAjuVerifProduksi as $item)
											<li>
												<a href="{{ route('verification.produksi.check', [$item->id]) }}"  class="d-flex align-items-center show-child-on-hover">
													<span class="mr-2">
														@if (!empty($item->data_user->logo))
															<img src="{{ Storage::disk('public')->url($item->data_user->logo) }}"
																class="profile-image rounded-circle" alt="">
														@else
															<img src="{{ asset('/img/avatars/farmer.png') }}"
																class="profile-image rounded-circle" alt="">
														@endif
													</span>
													<span class="d-flex flex-column flex-1">
														<span class="name">{{ $item->datauser->company_name }} <span
															class="badge badge-warning fw-n position-absolute pos-top pos-right mt-1">NEW</span></span>
														<span class="msg-a fs-sm ">
															<span class="badge badge-warning">Verifikasi Produksi</span>
														</span>
														<span class="fs-nano text-muted mt-1">{{ $item->created_at->diffForHumans() }}</span>
													</span>
												</a>
											</li>
										@endforeach
									@endif
									@if (Auth::user()->roles[0]->title == 'Admin')
										@foreach ($getAjuVerifSkl as $item)
											<li>
												<a href="{{ route('verification.skl.check', [$item->id]) }}"  class="d-flex align-items-center show-child-on-hover">
													<span class="mr-2">
														@if (!empty($item->data_user->logo))
															<img src="{{ Storage::disk('public')->url($item->data_user->logo) }}"
																class="profile-image rounded-circle" alt="">
														@else
															<img src="{{ asset('/img/avatars/farmer.png') }}"
																class="profile-image rounded-circle" alt="">
														@endif
													</span>
													<span class="d-flex flex-column flex-1">
														<span class="name">{{ $item->datauser->company_name }} <span
															class="badge badge-danger fw-n position-absolute pos-top pos-right mt-1">NEW</span></span>
														<span class="msg-a fs-sm">
															<span class="badge badge-danger">Penerbitan SKL</span>
														</span>
														<span class="fs-nano text-muted mt-1">{{ $item->created_at->diffForHumans() }}</span>
													</span>
												</a>
											</li>
										@endforeach
									@endif
									@if (Auth::user()->roles[0]->title == 'Pejabat')
										@foreach ($getRecomendations as $item)
											<li>
												<a href="{{ route('verification.skl.recomendation.show', [$item->id]) }}"  class="d-flex align-items-center show-child-on-hover">
													<span class="mr-2">
														@if (!empty($item->data_user->logo))
															<img src="{{ Storage::disk('public')->url($item->data_user->logo) }}"
																class="profile-image rounded-circle" alt="">
														@else
															<img src="{{ asset('/img/avatars/farmer.png') }}"
																class="profile-image rounded-circle" alt="">
														@endif
													</span>
													<span class="d-flex flex-column flex-1">
														<span class="name">{{ $item->datauser->company_name }} <span
															class="badge badge-success fw-n position-absolute pos-top pos-right mt-1">NEW</span></span>
														<span class="msg-a fs-sm">
															<span class="badge badge-success">Direkomendasikan</span>
														</span>
														<span class="fs-nano text-muted mt-1">{{ $item->created_at->diffForHumans() }}</span>
													</span>
												</a>
											</li>
										@endforeach
									@endif
								</ul>
							</div>
						</div>
						@if (Auth::user()->roles[0]->title == 'Pejabat')
							<div class="tab-pane" id="tab-new-skl" role="tabpanel">
								<div class="custom-scroll h-100">
									<ul class="notification notification-1">
										{{-- @if($cntNewSkl > 0)
											@foreach ($getNewSkl as $item)
												<li>
													<a href="{{$item->url}}" onClick="markAsRead({{ $item->id }})" class="d-flex align-items-center show-child-on-hover">
														<span class="mr-2">
															<i class="fal fa-award fa-4x text-success"></i>
														</span>
														<span class="d-flex flex-column flex-1">
															<span class="msg-a fs-sm">
																<span class="badge badge-success">
																	SKL Diterbitkan
																	<span class="badge badge-success fw-n position-absolute pos-top pos-right mt-1">NEW</span>
																</span>
															</span>
															<span class="name">
																No: {{ $item->no_skl }}
															</span>
															<span class="fs-nano text-muted mt-1">{{ $item->published_date->format('d F Y') }} ({{ $item->published_date->diffForHumans() }})</span>
														</span>
													</a>
												</li>
											@endforeach
										@endif --}}
									</ul>
								</div>
							</div>
						@endif
					@endif
				</div>
			</div>
		</div>
		<!-- app user menu -->
		<div>
			<a href="#" data-toggle="dropdown" title="{{ Auth::user()->name }}"
				class="header-icon d-flex align-items-center justify-content-center ml-2">
				@if (!empty(Auth::user()::find(Auth::user()->id)->data_user->logo))
					<img src="{{ asset('storage/' . Auth::user()->data_user->logo) }}" class="profile-image rounded-circle" alt="">
				@else
					<img src="{{ asset('/img/favicon.png') }}" class="profile-image rounded-circle"
						alt="{{ Auth::user()->name }}">
				@endif
			</a>
			<div class="dropdown-menu dropdown-menu-animated dropdown-lg">
				<div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
					<div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
						<span class="mr-2">
							@if (!empty(Auth::user()::find(Auth::user()->id)->data_user->avatar))
								<img src="{{ asset('storage/' . Auth::user()->data_user->avatar) }}" class="profile-image rounded-circle" alt="">
							@else
								<img src="{{ asset('/img/avatars/farmer.png') }}" class="profile-image rounded-circle" alt="{{ Auth::user()->name }}">
							@endif

						</span>
						<div class="info-card-text">
							<div class="fs-lg text-truncate text-truncate-lg">{{ Auth::user()->name }}</div>
							<span class="text-truncate text-truncate-md opacity-80">{{ Auth::user()->email }}</span>
						</div>
					</div>
				</div>
				<div class="dropdown-divider m-0"></div>

				<div class="dropdown-divider m-0"></div>
				<a href="#" class="dropdown-item" data-action="app-fullscreen">
					<span data-i18n="drpdwn.fullscreen">Layar Penuh</span>
					<i class="float-right text-muted fw-n">F11</i>
				</a>
				@if (Auth::user()->roleaccess == '1')
					<a href="{{ route('admin.profile.pejabat') }}" class="dropdown-item">
						<span data-i18n="drpdwn.profile">Profile</span>
					</a>
				@else
					<a href="{{ route('admin.profile.show') }}" class="dropdown-item">
						<span data-i18n="drpdwn.profile">Profile</span>
					</a>
				@endif
				{{-- <div class="dropdown-multilevel dropdown-multilevel-left">
					<div class="dropdown-item" data-i18n="drpdwn.lang">
						Bahasa
					</div>
					<div class="dropdown-menu">
						<a href="#?lang=id" class="dropdown-item {{ app()->getLocale() == 'id' ? "active" : "" }}" data-action="lang" data-lang="id">Bahasa (ID)</a>
						<a href="#?lang=en" class="dropdown-item {{ app()->getLocale() == 'en' ? "active" : "" }}" data-action="lang" data-lang="en">English (US)</a>
					</div>
				</div> --}}
				<div class="dropdown-divider m-0"></div>
				<a class="dropdown-item fw-500 pt-3 pb-3"
					onclick="event.preventDefault(); document.getElementById('logoutform').submit();"
					href="{{ trans('global.logout') }}">
					<span data-i18n="drpdwn.page-logout">Keluar</span>
					<span class="float-right fw-n">&commat;{{ Auth::user()->name }}</span>
				</a>
			</div>
		</div>
	</div>
</header>
