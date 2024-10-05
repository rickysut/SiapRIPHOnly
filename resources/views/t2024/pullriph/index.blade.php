@extends('t2024.layouts.admin')
@section ('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/smartadmin/notifications/sweetalert2/sweetalert2.bundle.css') }}">
@endsection
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
{{-- @include('t2024.partials.subheader') --}}

@can('pull_access')
<div class="row">
	<div class="col-12">
		@include('t2024.partials.sysalert')
		<form  id="dataForm" action="{{ route('2024.user.pull.store') }}" method="POST" enctype="multipart/form-data">
		@csrf
			<div class="text-center">
				<i class="fa fa-sync-alt fa-3x text-primary"></i>
				<h2>Penyelarasan Data SiapRIPH</h2>
				<div class="row justify-content-center">
					<div class="col-md-8 order-md-2 mb-4">
						<div class="row">
							<div class="form-group col-lg-6 text-left">
								<label class="form-label">No. RIPH</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fal fa-file-invoice text-align-center"></i></span>
									</div>
									<input id="nomor" name="nomor" type="text" placeholder="____/PP.240/D/__/____" data-inputmask="'mask': '9999/PP.240/D/99/9999'" class="form-control"  required>
								</div>
								<footer class="blockquote-footer text-left">
									<cite title="Source Title">contoh: 0001/PP.240/D/04/2022</cite>
								</footer>
							</div>
							<div class="form-group col-lg-6 text-left">
								<label class="form-label">NPWP</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fal fa-credit-card-front text-align-center"></i></span>
									</div>
									<!-- NPWP ini diperoleh dari tabel user importir yang diisi pada saat registrasi -->
									<input id="npwp" type="text" placeholder="__.___.___._-___.___" data-inputmask="'mask': '99.999.999.9-999.999'" class="form-control" value="{{ ($npwp_company ?? '') }}" disabled>
								</div>
								<footer class="blockquote-footer text-left">
									<cite title="Source Title">ini adalah Nomor Pokok Wajib Pajak (NPWP) Anda.</cite>
								</footer>
							</div>
						</div>

						<div class="d-flex justify-content-end align-items-center">
							<div class="col-md-6">
								<a href="{{ route('2024.user.commitment.index') }}" class="btn btn-sm btn-info btn-block text-white">
									<i class="fas fa-arrow-left"></i>
									Kembali
								</a>
							</div>
							<div class="col-md-6">
								<a class="btn btn-sm btn-primary btn-block ml-1 text-white"  id="btnexec" >
									<i class="fas fa-sync"></i>
									CARI DATA
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row justify-content-center collapse" id="collapseData">
				<div class="col-md-8 order-md-2 mb-4">
					<p class="lead">Berikut adalah data yang diperoleh dari aplikasi SIAP RIPH berdasarkan informasi yang Anda berikan.</p>
					<h5 class="d-flex justify-content-between align-items-center mb-3">
						<span class="text-muted">HASIL</span>
						<span>
							Status: <span id="keterangan"  class="badge-pill">wait..</span>
						</span>
						<input type="hidden" id="h-keterangan" name="keterangan">
					</h5>

					<ul class="list-group mb-3 notification">
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Perusahaan/Lembaga </h6>
										<span id="nama" class="fw-500 position-absolute pos-top pos-right mt-1" id="nama_perusahaan">
										</span>
										<input type="hidden" id="h-nama" name="nama">
									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Pemegang Rekomendasi Import Produk Hortikultura</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>NPWP </h6>
										<span id="npwpout"  class="fw-500 position-absolute pos-top pos-right mt-1">
										</span>
										<input type="hidden" id="h-npwpout" name="npwp" >
									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Nomor Pokok Wajib Pajak</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Nomor RIPH </h6>
										<span id="no_ijin"  class="fw-500 position-absolute pos-top pos-right mt-1">
										</span>
										<input type="hidden" id="h-no_ijin" name="no_ijin" >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Persetujuan Rekomendasi Import Produk Hortikultura</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Periode/Tahun Anggaran </h6>
										<span id="periodetahun"  class="fw-500 position-absolute pos-top pos-right mt-1">

										</span>
										<input type="hidden" id="h-periodetahun"name="periodetahun" >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Tahun Terbit</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Tanggal Terbit </h6>
										<span id="tgl_ijin" class="fw-500 position-absolute pos-top pos-right mt-1">
										</span>
										<input type="hidden" id="h-tgl_ijin" name="tgl_ijin" >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Tanggal diterbitkannya Persetujuan RIPH</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Tanggal berakhir </h6>
										<span id="tgl_akhir" class="fw-500 position-absolute pos-top pos-right mt-1">

										</span>
										<input type="hidden" id="h-tgl_akhir" name="tgl_akhir" >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Tanggal berakhirnya masa berlaku RIPH</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Komoditas </h6>
										<span id="no_hs" class="fw-500 position-absolute pos-top pos-right mt-1">

										</span>
										<input type="hidden" id="h-no_hs" name="no_hs" >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Komoditas import pada RIPH</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Volume RIPH (ton)</h6>
										<span id="volume_riph"  class="fw-500 position-absolute pos-top pos-right mt-1">

										</span>
										<input type="hidden" id="h-volume_riph" name="volume_riph" >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Total Volume import yang tertera pada Persetujuan RIPH</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Volume Wajib Produksi (ton)</h6>
										<span id="volume_produksi" class="fw-500 position-absolute pos-top pos-right mt-1">

										</span>
										<input type="hidden" id="h-volume_produksi" name="volume_produksi"  >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Total kewajiban produksi yang harus dipenuhi.</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<a href="#" class="d-flex align-items-center">
								<span class="d-flex flex-column flex-1 ml-1">
									<span class="name">
										<h6>Komitmen Wajib Tanam (ha)</h6>
										<span id="luas_wajib_tanam"  class="fw-500 position-absolute pos-top pos-right mt-1">

										</span>
										<input type="hidden" id="h-luas_wajib_tanam" name="luas_wajib_tanam"  >

									</span>
									<footer class="blockquote-footer text-left">
										<cite title="Source Title">Total kewajiban luas tanam yang harus dipenuhi.</cite>
									</footer>
								</span>
							</a>
						</li>
						<li>
							<input type="" id="h-stok_mandiri" name="stok_mandiri" hidden>
							<input type="" id="h-pupuk_organik" name="pupuk_organik" hidden>
							<input type="" id="h-npk" name="npk" hidden>
							<input type="" id="h-dolomit" name="dolomit" hidden>
							<input type="" id="h-za" name="za" hidden>
							<input type="" id="h-mulsa" name="mulsa" hidden>
						</li>
					</ul>
					<hr class="mb-4">
					<span class="text-bold text-secondary">Kami menyatakan <span class="text-danger small">(wajib dicentang)</span>: </span>
					<div class="form-group ">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="correct-riph" name="correct" required>
							<label class="custom-control-label" for="correct-riph">Data tersebut di atas adalah benar data RIPH dari Perusahaan/Lembaga kami.</label>
						</div>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="responsible" name="responsible" required>
							<label class="custom-control-label" for="responsible">Bertanggungjawab sepenuhnya atas informasi dan data yang kami sampaikan.</label>
						</div>
					</div>
					<a id="submitbtn" class="btn btn-sm btn-primary text-white" role="button" type="submit">
						<span class="mr-1"><i class="fal fa-upload text-align-center"></i></span>
						<span id="buttonText">Simpan untuk pelaporan</span>
						<span id="spinner" class="spinner-border spinner-border-sm ml-2" style="display: none;"></span>
					</a>
					<hr class="mb-4">
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Page Content -->
@endcan
@endsection

@section('scripts')
@parent
<script src="{{ asset('js/jquery/jquery.validate.js') }}"></script>
<script src="{{ asset('js/jquery/additional-methods.js') }}"></script>
<script src="{{ asset('js/formplugins/inputmask/inputmask.bundle.js') }}"></script>
<script src="{{ asset('js/smartadmin/notifications/sweetalert2/sweetalert2.bundle.js') }}"></script>
<script>
	function getCookie(name) {
		if (!document.cookie) {
			return null;
		}

		const xsrfCookies = document.cookie.split(';')
			.map(c => c.trim())
			.filter(c => c.startsWith(name + '='));

		if (xsrfCookies.length === 0) {
			return null;
		}
		return decodeURIComponent(xsrfCookies[0].split('=')[1]);
	}

	$(document).ready(function()
	{

		$(":input").inputmask();
		$("#btnexec").on('click', function(){
			stnpwp = $("#npwp").val().replace(/[\.,-]+/g,'');
			stnomor = $("#nomor").val();
			var isNomorExists = false;

			$.ajax({
				url: "{{ route('2024.user.pull.checkYear') }}",
				type: 'get',
				data: { nomor: stnomor },
				success: function(response) {
					//Ketika Periode memenuhi syarat
					if (response.success) {
						//$noIjins
						$.each(<?php echo json_encode($noIjins); ?>, function(index, value) {
							if(value.no_ijin === stnomor) {
								isNomorExists = true;
								return false;
							}
						});

						if(isNomorExists) {
							var confirmMessage = confirm("Nomor tersebut sudah terdaftar. Jika Anda melanjutkan, data yang telah tersimpan akan terhapus dan digantikan dengan data yang baru. Apakah Anda ingin melanjutkan?");
							if(!confirmMessage) {
								return false;
							}
						}

						const arraysToCheck = [
							{ data: <?php echo json_encode($pengajuan); ?>, message: "dalam proses Verifikasi" },
							{ data: <?php echo json_encode($ajuskl); ?>, message: "dalam proses Pengajuan SKL" },
							{ data: <?php echo json_encode($completed); ?>, message: "telah berstatus Lunas" }
						];

						console.log(arraysToCheck);

						let isExists = false;
						let message = "";

						arraysToCheck.some(({ data, message: msg }) => {
							const exists = data.some(value => value.no_ijin === stnomor);
							if (exists) {
								isExists = true;
								message = `Nomor tersebut ${msg}. Permintaan ini tidak dapat dilanjutkan.`;
							}
							return exists;
						});

						if (isExists) {
							alert(message);
							return false;
						}

						$.ajax ({
							url: "{{ route('2024.user.pull.getriph') }}",
							type: 'get',
							data: {npwp: stnpwp, nomor: stnomor},
							success: function(response){
								$('#correct-riph').prop({checked: false});
								$('#responsible').prop({checked: false});

								var keteranganElement = $('#keterangan');
								keteranganElement.text(response.keterangan);
								keteranganElement.removeClass('badge badge-success');
								if (response.keterangan === 'SUCCESS') {
									keteranganElement.addClass('badge badge-success badge-pill');
									keteranganElement.text('DATA DITEMUKAN');
								} else {
									keteranganElement.addClass('badge badge-danger badge-pill');
									keteranganElement.text('DATA TIDAK DITEMUKAN');
								}
								$('#h-keterangan').val(response.keterangan);

								if (response.keterangan == 'SUCCESS') {
									$('#no_ijin').html(response.riph.persetujuan.no_ijin);
									$('#h-no_ijin').val(response.riph.persetujuan.no_ijin);
									$('#nama').html(response.riph.persetujuan.nama);
									$('#h-nama').val(response.riph.persetujuan.nama);
									$('#npwpout').html($("#npwp").val());
									$('#h-npwpout').val($("#npwp").val());
									$('#periodetahun').html(stnomor.substr(stnomor.length - 4));
									$('#h-periodetahun').val(stnomor.substr(stnomor.length - 4));

									var tglIjin = new Date(response.riph.persetujuan.tgl_ijin);
									var options = { day: 'numeric', month: 'long', year: 'numeric' };
									var formattedtglIjin = tglIjin.toLocaleDateString('id-ID', options);

									$('#tgl_ijin').html(formattedtglIjin);
									$('#h-tgl_ijin').val(response.riph.persetujuan.tgl_ijin);


									var tglAkhir = new Date(response.riph.persetujuan.tgl_akhir);
									var formattedtglAkhir = tglAkhir.toLocaleDateString('id-ID', options);
									$('#tgl_akhir').html(formattedtglAkhir);
									$('#h-tgl_akhir').val(response.riph.persetujuan.tgl_akhir);
									if (response.riph.komoditas.loop.length > 1)
									{
										$('#no_hs').html(response.riph.komoditas.loop[0].no_hs  + response.riph.komoditas.loop[0].nama_produk);
										$('#h-no_hs').val(response.riph.komoditas.loop[0].no_hs  + response.riph.komoditas.loop[0].nama_produk);
									} else {
										$('#no_hs').html(response.riph.komoditas.loop.no_hs  + response.riph.komoditas.loop.nama_produk);
										$('#h-no_hs').val(response.riph.komoditas.loop.no_hs  + response.riph.komoditas.loop.nama_produk);
									}

									var volumeRiph = response.riph.wajib_tanam.volume_riph;
									var formattedVolumeRiph = parseFloat(volumeRiph).toLocaleString('id-ID');
									$('#volume_riph').html(formattedVolumeRiph);
									$('#h-volume_riph').val(response.riph.wajib_tanam.volume_riph);

									var volumeWajib = response.riph.wajib_tanam.volume_produksi;
									var formattedVolumeWajib = parseFloat(volumeWajib).toLocaleString('id-ID');
									$('#volume_produksi').html(formattedVolumeWajib);
									$('#h-volume_produksi').val(response.riph.wajib_tanam.volume_produksi);

									var luasWajib = response.riph.wajib_tanam.luas_wajib_tanam;
									var formattedLuasWajib = parseFloat(luasWajib).toLocaleString('id-ID');
									$('#luas_wajib_tanam').html(formattedLuasWajib);
									$('#h-luas_wajib_tanam').val(response.riph.wajib_tanam.luas_wajib_tanam);

									$('#stok_mandiri').html(response.riph.wajib_tanam.stok_mandiri);
									$('#h-stok_mandiri').val(response.riph.wajib_tanam.stok_mandiri);
									$('#pupuk_organik').html(response.riph.wajib_tanam.kebutuhan_pupuk.pupuk_organik);
									$('#h-pupuk_organik').val(response.riph.wajib_tanam.kebutuhan_pupuk.pupuk_organik);
									$('#npk').html(response.riph.wajib_tanam.kebutuhan_pupuk.npk);
									$('#h-npk').val(response.riph.wajib_tanam.kebutuhan_pupuk.npk);
									$('#dolomit').html(response.riph.wajib_tanam.kebutuhan_pupuk.dolomit);
									$('#h-dolomit').val(response.riph.wajib_tanam.kebutuhan_pupuk.dolomit);
									$('#za').html(response.riph.wajib_tanam.kebutuhan_pupuk.za);
									$('#h-za').val(response.riph.wajib_tanam.kebutuhan_pupuk.za);
									$('#mulsa').html(response.riph.wajib_tanam.mulsa);
									$('#h-mulsa').val(response.riph.wajib_tanam.mulsa);
								} else {
									$('#no_ijin').html('');
									$('#h-no_ijin').val('');
									$('#nama').html('');
									$('#h-nama').val('');
									$('#npwpout').html('');
									$('#h-npwpout').val('');
									$('#periodetahun').html('');
									$('#h-periodetahun').val('');
									$('#tgl_akhir').html('');
									$('#h-tgl_akhir').html('');
									$('#tgl_ijin').html('');
									$('#h-tgl_ijin').val('');
									$('#no_hs').html('');
									$('#h-no_hs').val('');
									$('#volume_produksi').html('');
									$('#h-volume_riph').val('');
									$('#luas_wajib_tanam').html('');
									$('#h-luas_wajib_tanam').val('');
									$('#stok_mandiri').html('');
									$('#h-stok_mandiri').val('');
									$('#pupuk_organik').html('');
									$('#h-pupuk_organik').val('');
									$('#npk').html('');
									$('#h-npk').val('');
									$('#dolomit').html('');
									$('#h-dolomit').val('');
									$('#za').html('');
									$('#h-za').val('');
									$('#mulsa').html('');
									$('#h-mulsa').val('');
								}
							},
							complete: function(response){
								if(!$("#collapseData").hasClass('show')){
									$('#collapseData').collapse('toggle');
								}
							}
						});
					// Handle ketika periode tidak memenuhi syarat
					} else {
						Swal.fire({
							icon: 'warning',
							title: 'PERIODE RIPH',
							text: response.message || 'Periode RIPH Anda tidak dapat digunakan pada simethris versi ini'
						});
					}
				}
			});
		})

		var $validator = $("#dataForm").validate({
			rules: {
				correct: {
					required: true
				},
				responsible: {
					required: true
				}
			},
			messages:{
				correct: {
					required: ""
				},
				responsible: {
					required: ""
				}
			},
			highlight: function(element) {
				$(element).closest('.form-group').find('label[for="' + element.id + '"]').addClass('text-danger');
			},
			unhighlight: function(element) {
				$(element).closest('.form-group').find('label[for="' + element.id + '"]').removeClass('text-danger');
			}
		});

		$("#submitbtn").click(function(event) {
			var $valid = $("#dataForm").valid();
			if (!$valid) {
				$validator.focusInvalid();
				return false;
			}
			$("#dataForm").submit();
		});
	});
</script>

@endsection
