@extends('t2024.layouts.admin')

@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<style>
	#lokasiTable_info{
		color: #fd3995;
		font-weight: 500;
	}
</style>
@endsection

@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@section('content')
	{{-- @include('partials.breadcrumb') --}}
	@include('t2024.partials.subheader')
	@can('administrator_access')
	@include('t2024.partials.sysalert')
		<div class="row" id="contentToPrint">
			<div class="col-lg-3">
				<div class="card mb-g">
					<div class="card-header">
						<div class="d-flex flex-row pt-2  border-top-0 border-left-0 border-right-0">
							<div class="d-inline-block align-middle mr-3">
								@php
									$logoPath = $commitment->datauser->logo ? Storage::disk('public')->url($commitment->datauser->logo) : asset('/img/avatars/farmer.png');
								@endphp

								<span class="profile-image rounded-circle d-block"
									style="background-image:url({{ $logoPath }}); background-size: cover;"
									id="companyLogo">
								</span>
							</div>
							<h3 class="mb-0 flex-1 text-dark fw-500">
								<span id="companytitle">{{$commitment->nama}}</span>
								<small class="m-0 l-h-n font-weight-bold"></small>
							</h3>
							<span class="">

							</span>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="form-group col-12">
								<label class="text-muted" for="no_ijin">Nomor RIPH</label>
								<div class="input-group">
									<span class="font-weight-bold" id="no_ijin" name="no_ijin">{{$commitment->no_ijin}}</span>
								</div>
							</div>

							<div class="form-group col-12">
								<label class="text-muted" for="tgl_ijin">Tanggal Ijin RIPH</label>
								<div class="input-group">
									<span class="font-weight-bold" id="tgl_ijin" name="tgl_ijin">{{$commitment->tgl_ijin}}</span>
								</div>
							</div>
							<div class="form-group col-12">
								<label class="text-muted" for="tgl_akhir">Tanggal Akhir RIPH</label>
								<div class="input-group">
									<span class="font-weight-bold" id="tgl_akhir" name="tgl_akhir">{{$commitment->tgl_akhir}}</span>
								</div>
							</div>
							<div class="form-group col-12">
								<label class="text-muted" for="created_at">Tanggal Pengajuan Verifikasi</label>
								<div class="input-group">
									<span class="font-weight-bold" id="created_at" name="created_at">{{$avt->created_at}}</span>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-12"><h5 class="font-weight-bold">Ringkasan</h5></div>
							<div class="form-group col-12">
								<label class="text-muted" for="countPks">Kemitraan</label>
								<div class="input-group">
									<span class="font-weight-bold" id="countPks" name="countPks">{{$pksCount}}</span>
								</div>
							</div>
							<div class="form-group col-12">
								<label class="text-muted" for="jml_anggota">Jumlah Anggota</label>
								<div class="input-group">
									<span class="font-weight-bold" id="jml_anggota" name="jml_anggota">{{$anggotaCount}}</span>
								</div>
							</div>
							<div class="form-group col-12">
								<label class="text-muted" for="luas_tanam">Realisasi Tanam</label>
								<div class="input-group">
									<span class="font-weight-bold" id="luas_tanam" name="luas_tanam">{{$luasTanam}}</span>
								</div>
							</div>
							<div class="form-group col-12">
								<label class="text-muted" for="jml_titik">Lokasi Tanam</label>
								<div class="input-group">
									<span class="font-weight-bold" id="jml_titik" name="jml_titik">{{$jmlLokasi}}</span>
								</div>
							</div>
							<div class="form-group col-12">
								<label class="text-muted" for="jml_titik">Wilayah Kabupaten</label>
								<div class="input-group">
									<span class="font-weight-bold" id="jml_titik" name="jml_titik">
										{{ implode(', ', $kabupaten->pluck('nama_kab')->toArray()) }}
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-9">
				<div class="panel" id="panel-2">
					<div class="panel-container">
						<div class="panel-content">
							<form action="{{route('2024.admin.pengajuan.storeAssignment', [$ijin, $avt->tcode])}}" method="post" enctype="multipart/form-data">
								@csrf
								<div class="form-group">
									<label class="form-label" for="verifikatorSelect">Verifikator yang ditugaskan</label>
									<div class="input-group">
										<select class="custom-select required" id="verifikatorSelect" name="user_id" aria-label="Example select with button addon">
											<option value="" selected></option>
											@foreach ($assignees as $verifikator)
												<option value="{{$verifikator->id}}">{{$verifikator->name}}</option>
											@endforeach
										</select>
									</div>
									<span class="help-block">Nomor SK Penugasan</span>
								</div>
								<div class="form-group">
									<label class="form-label" for="fileSk">SK Penugasan</label>
									<div class="input-group">
										<div class="custom-file">
											<input type="file" class="custom-file-input required" id="fileSk" name="fileSk" aria-describedby="fileSk" accept=".pdf" required>
											<label class="custom-file-label" for="fileSk">Pilih Berkas</label>
										</div>
									</div>
									<span class="help-block">Pindaian Salinan berkas SK Penugasan</span>
								</div>
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-label" for="no_sk">Nomor SK</label>
											<input type="text" id="no_sk" name="no_sk" class="form-control required" placeholder="Helping text" required>
											<span class="help-block">Nomor SK Penugasan</span>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-label" for="tgl_sk">Tanggal SK</label>
											<div class="input-group">
												<input class="form-control required" id="tgl_sk" type="date" name="tgl_sk" required>
												<div class="input-group-append">
													<button class="btn btn-primary waves-effect waves-themed" type="submit" id="btnSubmit">
														Simpan
													</button>
												</div>
											</div>
											<span class="help-block">
												Tanggal mulai berlaku SK Penugasan
											</span>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="panel" id="panel-3">
					<div class="panel-hdr">
						<h2>Daftar Verifikator</h2>
					</div>
					<div class="panel-container">
						<div class="panel-content">
							<table id="verifikatorTable" class="table table-sm table-bordered table-striped w-100">
								<thead>
									<tr>
										<th>Nama</th>
										<th>Nomor SK</th>
										<th>Tanggal SK</th>
										<th>Berkas SK</th>
										<th>Tindakan</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($verifikators as $verifikator)
										<tr>
											<td>{{$verifikator->user->name}}</td>
											<td>{{$verifikator->no_sk}}</td>
											<td>{{$verifikator->tgl_sk}}</td>
											<td>
												@php
													$pathNpwp = str_replace(['.', '-'], '', $commitment->npwp);
												@endphp
												{{-- {{ asset('storage/uploads/'.$pathNpwp.'/'.$commitment->periodetahun.'/'.$docs->sphproduksi) }} --}}
												<a href="{{ $verifikator->file }}" target="blank">Lihat</a>
											</td>
											<td>
												@if ($verifikator->source == 'allVerifikators')
													<form action="{{route('2024.admin.pengajuan.reAssignment', [$ijin, $avt->tcode])}}" method="post" enctype="multipart/form-data">
														@csrf
														<input type="hidden" name="user_id" aria-describedby="user_id" value="{{$verifikator->user_id}}">
														<input type="hidden" name="no_sk" aria-describedby="no_sk" value="{{$verifikator->no_sk}}">
														<input type="hidden" name="fileSk" aria-describedby="fileSk" value="{{$verifikator->file}}">
														<input type="hidden" name="tgl_sk" aria-describedby="tgl_sk" value="{{$verifikator->tgl_sk}}">
														<button class="btn btn-xs btn-primary" type="submit">Re-Asign</button>
													</form>
												@else
													<button class="btn btn-icon btn-xs btn-danger" onclick="deleteAssignment('{{ $verifikator->tcode }}')">
														<i class="fal fa-trash"></i>
													</button>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	@endcan
@endsection

@section('scripts')

<script>
	$(document).ready(function()
	{
		$("#verifikatorSelect").select2({
			placeholder: "-- pilih verifikator"
		});

		const noIjin = '{{$ijin}}';
		$('#lokasiTable').dataTable({
			responsive: true,
			lengthChange: false,
			ordering: true,
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('2024.datafeeder.getLocationSampling', [':noIjin']) }}".replace(':noIjin', noIjin),
				type: "GET",
				dataSrc: "data"
			},
			language: {
				info: "Terpilih _START_ dari _END_ lokasi",
				infoEmpty: "Tidak ada data",
				infoFiltered: "(terfilter dari _MAX_ total lokasi)"
			},
			drawCallback: function(settings) {
				var api = this.api();
				var total = api.page.info().recordsTotal;
				var displayed = api.page.info().recordsDisplay;

				// Periksa checkbox yang dicentang dari seluruh tabel
				var selected = $(api.table().body()).find('input:checkbox:checked').length;

				// Update custom info display
				$('#lokasiTable_info').html(`Terpilih ${selected} dari ${total} lokasi sampling`);
			},

			rowGroup: {
				dataSrc: 'spatial.kecamatan.nama_kecamatan' // Kolom yang digunakan untuk mengelompokkan
			},
			columns: [
				{ data: 'kode_spatial', title: 'Kode Spatial' },
				{ data: 'spatial.desa.nama_desa', title: 'Nama Desa' },
				{ data: 'spatial.kecamatan.nama_kecamatan', title: 'Nama Kecamatan' },
				{ data: 'spatial.kabupaten.nama_kab', title: 'Nama Kabupaten' },
				{
					data: 'tcode',
					title: 'Actions',
					render: function(data, type, row) {
						var isChecked = row.is_selected ? 'checked' : '';
						return `
							<div class="d-flex align-items-center">
								<a href="/details/${data}" class="btn btn-icon btn-primary btn-sm">Details</a>
								<div class="custom-control custom-checkbox ml-1">
									<input type="checkbox" class="custom-control-input" id="checkbox-${data}" ${isChecked} data-tcode="${data}">
									<label class="custom-control-label" for="checkbox-${data}">Pilih</label>
								</div>
							</div>
						`;
					}
				}
			]
		});

		$(document).on('change', '.custom-control-input', function() {
			var isChecked = $(this).is(':checked');
			var tcode = $(this).data('tcode');

			$.ajax({
				url: "{{ route('2024.admin.pengajuan.tanam.saveSelectedLocations') }}",
				type: "POST",
				data: {
					selected_location_id: tcode,
					is_selected: isChecked ? 1 : 0,
					_token: '{{ csrf_token() }}'
				},
				success: function(response) {
					console.log(response.message); // Berhasil

					var table = $('#lokasiTable').DataTable();
					var selected = $(table.table().body()).find('input:checkbox:checked').length;
					var total = table.page.info().recordsTotal;

					$('#lokasiTable_info').html(`Terpilih ${selected} dari ${total} lokasi sampling`);
				},
				error: function(xhr) {
					console.error(xhr.responseText); // Gagal
				}
			});
		});


		$('#verifikatorTable').dataTable({
			responsive: true,
			lengthChange: false,
			ordering: true,
		});
	});


	function deleteAssignment(tcode) {
		if (confirm('Are you sure you want to delete this assignment?')) {
			var csrfToken = '{{ csrf_token() }}';

			// Kirim request DELETE dengan header X-HTTP-Method-Override
			fetch('{{ route("2024.admin.pengajuan.deleteAssignment", ":tcode") }}'.replace(':tcode', tcode), {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': csrfToken,
					'X-HTTP-Method-Override': 'DELETE',
					'Content-Type': 'application/json'
				},
			})
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(data => {
				alert('Assignment successfully deleted.');
				// Refresh halaman atau perbarui data jika perlu
				location.reload();
			})
			.catch(error => {
				alert('Failed to delete assignment: ' + error.message);
			});
		}
	}
</script>
@endsection
