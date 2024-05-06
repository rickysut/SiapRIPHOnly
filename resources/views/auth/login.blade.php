@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col col-md-6 col-lg-7 hidden-sm-down">
        <h2 class="fs-xxl fw-500 mt-4 text-white">
            Simethris V.3.1
            <small class="h3 fw-300 mt-3 mb-5 text-white opacity-100">
                Bagi Pemegang RIPH yang akan mengakses aplikasi ini, Anda harus memiliki akun pada Aplikasi RIPH Online terlebih dahulu.
                <br>
                <p>Belum memiliki akun? silahkan melakukan pendaftaran <a href="https://riph.pertanian.go.id/" class="fw-700 text-white">di sini.</a></p>
            </small>
        </h2>
        {{-- <a href="#" class="fs-lg fw-500 text-white opacity-70">Learn more &gt;&gt;</a> --}}
        <div class="d-sm-flex flex-column align-items-center justify-content-center d-md-block">
            <div class="px-0 py-1 mt-5 text-white fs-nano opacity-50">
                Associate
            </div>
            <div class="d-flex flex-row opacity-70 align-items-center">
                <a href="https://riph.pertanian.go.id/" class="text-white mr-2">
                    <img src="/img/riphonline.svg" alt="simethris" aria-roledescription="logo" style="width:25px; height:auto;">
                </a>

            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-5 col-xl-4 ml-auto">
        <h1 class="text-white fw-300 mb-3">
            Login
        </h1>
        <div class="card p-4 rounded-plus bg-faded">
            <div class="d-sm-block d-md-none text-center mt-0 mb-1">
                <img src="{{ asset('img/logo-icon.png') }}" alt="simethris" aria-roledescription="logo" style="width:150px; height:auto;">
            </div>
			<button type="button" class="btn btn-sm btn-primary waves-effect waves-themed mb-2" data-toggle="modal" data-target="#login1" onclick="loginClick(1)"><i class="fal fa-plane-departure mr-1"></i>Administrator/Verifikator</button>
			<button type="button" class="btn btn-sm btn-warning waves-effect waves-themed" data-toggle="modal" data-target="#login1" onclick="loginClick(2)"><i class="fal fa-plane-departure mr-1"></i>Pelaku Usaha</button>
            @if ($errors->any())
                {{ $errors->first('roleaccess') }}
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="login1" tabindex="-1" role="dialog" style="display: none;" aria-modal="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<span id="modalTitle"></span>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body">

				<form id="js-login" novalidate="" method="POST" action="{{ route('login') }}">
					@csrf
					<input id="roleaccess" name="roleaccess" type="hidden" value=""/>
					<div class="form-group">
						<label class="form-label" for="username">Username</label>
						<div class="input-group" data-toggle="tooltip" title data-original-title="Your Username" data-title="Nama Pengguna (username)" data-intro="Type your username here" data-step="3">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<span class="fal fa-user"></span>
								</div>
							</div>

							<input id="username" name="username" type="text" class="form-control form-control-md {{ $errors->has('username') ? ' is-invalid' : '' }}" required autocomplete="{{ trans('global.login_username') }}" autofocus placeholder="{{ trans('global.login_username') }}" value="{{ old('username', null) }}" />
							@if($errors->has('username'))
							<div class="invalid-feedback">
								{{ $errors->first('username') }}
							</div>
							@endif
						</div>
					</div>
					<div class="form-group">
						<label class="form-label" for="password">Password</label>
						<div class="input-group bg-white shadow-inset-2" data-toggle="tooltip" title data-original-title="Your password" data-title="Password" data-intro="Type your password" data-step="4">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<span class="fal fa-key"></span>
								</div>
							</div>
							<input id="password" name="password" type="password" class="form-control form-control-md border-right-0 bg-transparent pr-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" required autocomplete="{{ trans('global.login_password') }}" autofocus placeholder="{{ trans('global.login_password') }}" value="" />
							@if($errors->has('password'))
							<div class="invalid-feedback">
								{{ $errors->first('password') }}
							</div>
							@endif
							<div class="input-group-append">
								<span class="input-group-text bg-transparent border-left-0">
									<i class="far fa-eye-slash text-muted" id="togglePassword"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group text-left" data-title="Ingat Saya" data-intro="Centang jika Anda ingin langsung masuk jika login berhasil" data-step="5">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="rememberme">
							<label class="custom-control-label" for="rememberme">{{ trans('global.remember_me') }}</label>
						</div>
					</div>
					<div class="row no-gutters">
						<div class="col-lg-12 pl-lg-1 my-2" data-title="Tombol masuk" data-intro="Klik tombol ini untuk mengakses aplikasi jika seluruh kolom telah terisi" data-step="6">
							<button id="js-login-btn" type="submit" class="btn btn-block btn-info btn-xm">{{ trans('global.login') }}</button>
						</div>
					</div>

					<div class="row no-gutters">
						{{-- <div class="text-center">Belum memiliki akun?</div> --}}
						<div class="col-lg-12 pl-lg-1 my-2">
							<a href="#" id="regbutton" class="btn btn-block btn-outline-danger btn-xm">Daftarkan Akun</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')

<script>
	$(document).ready(function () {
		@if ($errors->any())

			$('#login1').modal('show');
			document.querySelector('#roleaccess').value = {{ $errors->first('roleaccess') }};
		@endif

	})

	const togglePassword = document.querySelector('#togglePassword');
	const password = document.querySelector('#password');

	togglePassword.addEventListener('click', function (e) {
		// toggle the type attribute
		const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
		password.setAttribute('type', type);
		// toggle the eye slash icon\
		if (this.classList.contains('fa-eye')){
			this.classList.remove('fa-eye');
			this.classList.add('fa-eye-slash');
		} else {
			this.classList.remove('fa-eye-slash');
			this.classList.add('fa-eye');
		}

	});


	function loginClick(role_access) {
		const roleaccess = document.querySelector('#roleaccess');
		const regbut = document.querySelector('#regbutton');
		roleaccess.value = role_access;
		if (role_access==1){
			$("#regbutton").hide();
			$('#modalTitle').text('Administrator Login');
		} else if (role_access==2){
			$("#regbutton").show();
			$('#modalTitle').text('User Login');
			regbut.href = 'https://riph.pertanian.go.id/';
		} else {
			$("#regbutton").show();
			regbut.href = "{{ route('register') }}";
		}

	}
</script>

@endsection
