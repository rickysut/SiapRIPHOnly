@extends('t2024.layouts.admin')
@section('styles')
<style>
	.display-5{
		font-size: 1.8rem;
		font-weight: 300;
		line-height: 1.25;
	}
	.select2-container--default .select2-results {
		z-index: 1050; /* Pastikan dropdown muncul di atas modal */
	}

</style>
@endsection
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('spatial_data_access')
	<div class="modal fade" id="default-example-modal-sm-center" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm modal-dialog-right" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Pilih Kabupaten</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
				<div class="modal-body">
					<button type="button" id="select-all-btn" class="btn btn-sm btn-outline-primary mb-2">
						<i class="fal fa-square"></i> <span>Pilih semua</span>
					</button>
					<div class="panel shadow-0">
						<div class="panel-container">
							<div class="panel-content custom-scroll"  style="max-height: 400px; overflow-y: auto;">
								@foreach ($indexKabupaten as $locus)
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input kabupaten-checkbox" id="{{ $locus['kabupaten_id'] }}" name="kabupaten_id">
										<label class="custom-control-label" for="{{ $locus['kabupaten_id'] }}">{{ $locus['nama_kab'] }}</label>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button class="btn btn-primary" id="btnSubmit" data-dismiss="modal">Simulasikan</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="detailModalLabel">Detail Data</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- Konten akan dimuat di sini -->
					<div id="infoLahan" class="mb-5"></div>
					<div id="kemitraanAktif" class="mb-5"></div>
					<div id="historyKemitraan" class="mb-5"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalMultiUpload" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">sm</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route('2024.spatial.storesingle') }}" enctype="multipart/form-data" method="POST" id="formSubmit">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<div class="input-group bg-white shadow-inset-2">
								<div class="input-group-prepend">
									<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
										<i class="fal fa-upload"></i>
									</span>
								</div>
								<div class="custom-file">
									<input type="file" accept=".kml" id="kml_file" name="kml_url[]" placeholder="ambil berkas KML..."
										class="custom-file-input border-left-0 bg-transparent pl-0" multiple required>
									<label class="custom-file-label text-muted" for="kml_file">ambil berkas KML... </label>
								</div>
							</div>
							<span class="help-block">Unggah berkas KML <span class="text-danger">*</span></span>
						</div>
						<div hidden>
							<div class="form-group row">
								<label for="kode_spatial" class="col-sm-3 col-form-label">Kode Spatial</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="kode_spatial" name="kode_spatial" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="latitude" class="col-sm-3 col-form-label">Latitude</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="latitude" name="latitude" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="longitude" class="col-sm-3 col-form-label">Longitude</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="longitude" name="longitude" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="polygon" class="col-sm-3 col-form-label">Polygon</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="polygon" name="polygon" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="altitude" class="col-sm-3 col-form-label">Altitude (mdpl)</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="altitude" name="altitude" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="luas_lahan" class="col-sm-3 col-form-label">Luas Lahan (m2)</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="luas_lahan" name="luas_lahan" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="poktan_name" class="col-sm-3 col-form-label">Kelompok Tani</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="poktan_name" name="poktan_name" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="ktp_petani" class="col-sm-3 col-form-label">NIK Petani</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="ktp_petani" name="ktp_petani" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="nama_petani" class="col-sm-3 col-form-label">Nama Petani</label>
								<div class="col-sm-9">
									<input class="form-control" type="text" id="nama_petani" name="nama_petani" value="" readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="provinsi_nama" class="col-sm-3 col-form-label">Provinsi</label>
								<div class="col-sm-9">
									<input class="form-control" id="provinsi_id" name="provinsi_id" value="">
								</div>
							</div>
							<div class="form-group row">
								<label for="kabupaten_nama" class="col-sm-3 col-form-label">Kabupaten</label>
								<div class="col-sm-9">
									<input class="form-control" id="kabupaten_id" name="kabupaten_id" value="">
								</div>
							</div>
							<div class="form-group row">
								<label for="kecamatan_nama" class="col-sm-3 col-form-label">Kecamatan</label>
								<div class="col-sm-9">
									<input class="form-control" id="kecamatan_id" name="kecamatan_id" value="">
								</div>
							</div>
							<div class="form-group row">
								<label for="kelurahan_nama" class="col-sm-3 col-form-label">Kelurahan</label>
								<div class="col-sm-9">
									<input class="form-control" id="kelurahan_id" name="kelurahan_id" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="uploadBtn">Save</button>
					</div>
				</form>


				<div id="progressContainer" style="display: none;">
					<p>Uploading: <span id="fileName"></span></p>
					<div style="border: 1px solid #ddd; width: 100%; height: 20px;">
						<div id="progressBar" style="width: 0%; height: 100%; background-color: green;"></div>
					</div>
					<p id="progressText">0%</p>
				</div>
			</div>
		</div>
	</div>

	<section class="mb-3" id="map">
		<div class="row">
			<div class="col-12">
				<div class="panel" id="panel-1">
					<div class="panel-container">
						<div class="panel-content p-0">
							<div id="myMap" style="height:500px; width: 100%;"></div>

							<div class="row row-grid no-gutters">
								<div class="col-sm-12 col-md-4 col-xl-3">
									<div class="px-3 py-2 d-flex align-items-center m-2">
										<span class="d-inline-block ml-2 text-muted">
											<i class="fal fa-globe-asia color-info-500 mr-1"></i>
											Total Lahan Wajib Tanam
										</span>
										<div class="ml-auto d-inline-flex align-items-center">
											<div class="d-inline-flex flex-column ml-2 text-right fw-500">
												<span class="d-inline-block">
													<span id="totalLuas"></span> ha
												</span>
												<span class="d-inline-block">
													<span id="totalLahan"></span> titik
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-4 col-xl-3">
									<div class="px-3 py-2 d-flex align-items-center m-2">
										<span class="d-inline-block ml-2 text-muted">
											<i class="fal fa-lock-open-alt color-success-500 mr-1"></i>
											Lahan Tersedia
										</span>
										<div class="ml-auto d-inline-flex align-items-center">
											<div class="d-inline-flex flex-column ml-2 text-right fw-500">
												<span class="d-inline-block">
													<span id="totalLuasAktif"></span> ha
												</span>
												<span class="d-inline-block">
													<span id="totalLahanAktif"></span> titik
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-4 col-xl-3">
									<div class="px-3 py-2 d-flex align-items-center m-2">
										<span class="d-inline-block ml-2 text-muted">
											<i class="fal fa-lock color-warning-500 mr-1"></i>
											Lahan Bermitra
										</span>
										<div class="ml-auto d-inline-flex align-items-center">
											<div class="d-inline-flex flex-column ml-2 text-right fw-500">
												<span class="d-inline-block">
													<span id="totalLuasNonAktif"></span> ha
												</span>
												<span class="d-inline-block">
													<span id="totalLahanNonAktif"></span> titik
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<div class="row">
		<div class="col">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>
						Tabel <span class="fw-300"><i>Spatial</i></span>
					</h2>
					<div class="panel-toolbar">
						<div class="btn-group">
							<button type="button" class="btn btn-xs btn-primary waves-effect waves-themed">
								<i class="fal fa-plus mr-1"></i>
								Peta Baru
							</button>
							<button type="button" class="btn btn-xs btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-themed" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								<i class="fal fa-chevron-down"></i>
							</button>
							<div class="dropdown-menu" style="">
								<a href="{{route('2024.spatial.createsingle')}}" onclick="navigateBack();" class="dropdown-item" title="import berkas KML peta satu persatu">
									<i class="fal fa-plus"></i>
									Impor Peta Tunggal
								</a>
								<a href="javascript:void(0);" class="dropdown-item" data-toggle="modal" title="Import berkas KML sekaligus" data-target="#modalMultiUpload">
									<i class="fal fa-layer-plus"></i>
									Impor Peta Jamak
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblSpatial" class="table table-bordered table-hover table-sm table-striped w-100">
							<thead class="thead-themed">
								<th>Kode Lokasi</th>
								<th>Pengelola</th>
								<th>Luas</th>
								<th>Wilayah</th>
								<th>Tindakan</th>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent


{{-- peta --}}
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).ready(function() {
        var allSelected = false;
        $('#select-all-btn').click(function() {
            $('.kabupaten-checkbox').prop('checked', !allSelected);
            if (allSelected) {
                $(this).removeClass('btn-primary').addClass('btn-outline-primary');
                $(this).find('i').removeClass('fa-check-square').addClass('fa-square');
                $(this).find('span').text('Pilih semua');
            } else {
                $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                $(this).find('i').removeClass('fa-square').addClass('fa-check-square');
                $(this).find('span').text('Batalkan pilihan');
            }
            allSelected = !allSelected;
        });

        initMap();

        $('#default-example-modal-sm-center').on('shown.bs.modal', function () {
            $(".select2-placeholder-multiple").select2({
                dropdownParent: $('#default-example-modal-sm-center'),
                placeholder: "Select State",
                width: '100%'
            });
        });
    });

    let map;
    var initialCenter = { lat: -2.5489, lng: 118.0149 };
    var initialZoom = 5;
    let markersArray = [];
    let polygonsArray = [];

    function buttonModal(){
        var controlDiv = document.createElement('div');

        var button = document.createElement('button');
        button.style.backgroundColor = '#fff';
        button.style.border = 'none';
        button.style.outline = 'none';
        button.style.width = '40px';
        button.style.height = '40px';
        button.style.borderRadius = '2px';
        button.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
        button.style.cursor = 'pointer';
        button.style.marginRight = '10px';
        button.style.padding = '0';
        button.title = 'Your Location';
        controlDiv.appendChild(button);

        var icon = document.createElement('i');
        icon.className = 'fas fa-search';
        icon.style.fontSize = '18px';
        icon.style.margin = '10px';
        button.appendChild(icon);

        google.maps.event.addListener(map, 'dragend', function() {
            icon.style.color = '#000';
        });

        button.addEventListener('click', function() {
            $('#default-example-modal-sm-center').modal('show');
        });

        controlDiv.index = 2;
        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById('myMap'), {
            mapTypeId: google.maps.MapTypeId.HYBRID,
            center: initialCenter,
            zoom: initialZoom,
            mapTypeControl: false,
            streetViewControl: false,
            scaleControl: true,
            rotateControl: false,
            styles: [
                {
                    featureType: 'all',
                    elementType: 'labels'
                    // stylers: [{ visibility: 'off' }]
                }
            ]
        });

        buttonModal();
        attachCheckboxListeners();
    }

    function attachCheckboxListeners() {
        const checkboxes = document.querySelectorAll('.kabupaten-checkbox');

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const selectedKabupaten = Array.from(checkboxes)
                    .filter(chk => chk.checked)
                    .map(chk => chk.id);

                if (selectedKabupaten.length > 0) {
                    fetchSpatialData(selectedKabupaten);
                } else {
                    clearMapMarkers();
                    clearMapPolygons();
                    map.setZoom(initialZoom);
                    map.setCenter(initialCenter);
                }
            });
        });
    }

    function fetchSpatialData(kabupatenIds) {
        fetch('{{ route('2024.datafeeder.responseGetLocationInKabupaten') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kabupaten_id: kabupatenIds })
        })
        .then(response => response.json())
        .then(data => {
            plotMarkersOnMap(data);
        })
        .catch(error => console.error('Error fetching spatial data:', error));
    }

	function plotMarkersOnMap(spatials) {
		clearMapMarkers(); // Hapus marker yang ada
		clearMapPolygons(); // Hapus polygon yang ada

		const infoWindow = new google.maps.InfoWindow();

		spatials.forEach(spatial => {
			const latLng = new google.maps.LatLng(spatial.latitude, spatial.longitude);
			const markerIcon = spatial.status === 0 ? 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
			const markerColor = spatial.status === 0 ? '#00FF00' : '#FF0000'; // Warna sesuai status
			const marker = new google.maps.Marker({
				position: latLng,
				map: map,
				title: spatial.kode_spatial,
				icon: markerIcon,
				label: {
					text: spatial.kode_spatial,
					color: '#fff',
					fontSize: '12px',
					fontWeight: 'bold'
				}
			});
			markersArray.push(marker);

			marker.addListener('click', function() {
				clearMapPolygons(); // Clear polygons on marker click
				const currentZoom = map.getZoom();
				const currentCenter = map.getCenter();

				map.setZoom(18);
				map.setCenter(marker.getPosition());
			});
		});
	}

	function openDetailModal(kode_spatial) {
		const modalUrl = `{{ route('2024.datafeeder.responseGetSpatialMoreDetail', ['spatial' => '__spatial__']) }}`.replace('__spatial__', kode_spatial);

		$.getJSON(modalUrl, function(data) {
			// Info Lahan
			let infoLahanHtml = `
				<ul class="list-group">
					<li class="list-group-item d-flex justify-content-between align-items-start text-white bg-danger">
						<span class="text-uppercase">Informasi Lahan</span>
						<span class="">${data.infoLahan.status || '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kode Lahan</span>
						<span class="fw-500 col-6">${data.infoLahan.kode_spatial || '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Luas Lahan</span>
						<span class="fw-500 col-6">${data.infoLahan.luas_lahan ? data.infoLahan.luas_lahan + ' m2' : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Desa</span>
						<span class="fw-500 col-6">${data.infoLahan.desa ? data.infoLahan.desa.kelurahan_id : '-'} - ${data.infoLahan.kecamatan ? data.infoLahan.kecamatan.nama_kecamatan : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kecamatan</span>
						<span class="fw-500 col-6">${data.infoLahan.kecamatan ? data.infoLahan.kecamatan.kecamatan_id : '-'} - ${data.infoLahan.kecamatan ? data.infoLahan.kecamatan.nama_kecamatan : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kabupaten</span>
						<span class="fw-500 col-6">${data.infoLahan.kabupaten ? data.infoLahan.kabupaten.kabupaten_id : '-'} - ${data.infoLahan.kabupaten ? data.infoLahan.kabupaten.nama_kab : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Provinsi</span>
						<span class="fw-500 col-6">${data.infoLahan.provinsi ? data.infoLahan.provinsi.provinsi_id : '-'} - ${data.infoLahan.provinsi ? data.infoLahan.provinsi.nama : '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Petani/Pengelola</span>
						<span class="fw-500 col-6">${data.infoLahan.nama_petani || '-'}</span>
					</li>
					<li class="list-group-item d-flex justify-content-start align-items-start">
						<span class="text-muted col-6">Kelompok Tani</span>
						<span class="fw-500 col-6">${data.infoPoktan.nama_kelompok || '-'}</span>
					</li>
				</ul>
			`;

			$('#infoLahan').html(infoLahanHtml);

			// Kemitraan Aktif
			if (data.kemitraanAktif) {
				let kemitraanAktifHtml = `
					<ul class="list-group">
						<li class="list-group-item d-flex justify-content-between align-items-start text-white bg-info">
							<span class="text-uppercase">Kemitraan Aktif</span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-start">
							<span class="text-muted col-6">Perusahaan</span>
							<span class="fw-500 col-6">${data.kemitraanAktif.nama || '-'}</span>
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-start">
							<span class="text-muted col-6">Nomor RIPH</span>
							<span class="fw-500 col-6">${data.kemitraanAktif.no_ijin || '-'}</span>
						</li>
						<li class="list-group-item">
							<span class="text-muted">Kegiatan dan Realisasi di Lahan</span>
							<table class="table table-hover table-striped table-bordered table-sm">
								<thead class="thead-themed">
									<tr>
										<th>Kegiatan</th>
										<th>Tanggal</th>
										<th>Jumlah</th>
										<th>Catatan</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Pengolahan Lahan</td>
										<td>${data.lokasi.lahandate ? data.lokasi.lahandate : '-'}</td>
										<td></td>
										<td>${data.lokasi.lahancomment ? data.lokasi.lahancomment : '-'}</td>
									</tr>
									<tr>
										<td>Persiapan Benih</td>
										<td>${data.lokasi.benihDate ? data.lokasi.benihDate : '-'}</td>
										<td>${data.lokasi.benihsize ? data.lokasi.benihsize : '-'} kg</td>
										<td>${data.lokasi.benihComment ? data.lokasi.benihComment : '-'}</td>
									</tr>
									<tr>
										<td>Pemasangan Mulsa</td>
										<td>${data.lokasi.mulsaDate ? data.lokasi.mulsaDate : '-'}</td>
										<td>${data.lokasi.mulsaSize ? data.lokasi.mulsaSize : '-'} roll</td>
										<td>${data.lokasi.mulsaComment ? data.lokasi.mulsaComment : '-'}</td>
									</tr>
									<tr>
										<td>Penanaman</td>
										<td>${data.lokasi.tgl_tanam ? data.lokasi.tgl_tanam : '-'}</td>
										<td>${data.lokasi.luas_tanam ? data.lokasi.luas_tanam : '-'} m2</td>
										<td>${data.lokasi.tanamComment ? data.lokasi.tanamComment : '-'}</td>
									</tr>
									<tr>
										<td>Pemupukan Pertama</td>
										<td>${data.lokasi.pupuk1Date ? data.lokasi.pupuk1Date : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Organik: ${data.lokasi.organik1 ? data.lokasi.organik1 : '-'} ton</li>
												<li>NPK: ${data.lokasi.npk1 ? data.lokasi.npk1 : '-'} kg</li>
												<li>Dolomit: ${data.lokasi.dolomit1 ? data.lokasi.dolomit1 : '-'} kg</li>
												<li>ZA: ${data.lokasi.za1 ? data.lokasi.za1 : '-'} kg</li>
										</td>
										<td>${data.lokasi.pupuk1Comment ? data.lokasi.pupuk1Comment : '-'}</td>
									</tr>
									<tr>
										<td>Pemupukan Kedua</td>
										<td>${data.lokasi.pupuk2Date ? data.lokasi.pupuk2Date : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Organik: ${data.lokasi.organik2 ? data.lokasi.organik2 : '-'} ton</li>
												<li>NPK: ${data.lokasi.npk2 ? data.lokasi.npk2 : '-'} kg</li>
												<li>Dolomit: ${data.lokasi.dolomit2 ? data.lokasi.dolomit2 : '-'} kg</li>
												<li>ZA: ${data.lokasi.za2 ? data.lokasi.za2 : '-'} kg</li>
										</td>
										<td>${data.lokasi.pupuk2Comment ? data.lokasi.pupuk2Comment : '-'}</td>
									</tr>
									<tr>
										<td>Pemupukan Ketiga</td>
										<td>${data.lokasi.pupuk3Date ? data.lokasi.pupuk3Date : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Organik: ${data.lokasi.organik3 ? data.lokasi.organik3 : '-'} ton</li>
												<li>NPK: ${data.lokasi.npk3 ? data.lokasi.npk3 : '-'} kg</li>
												<li>Dolomit: ${data.lokasi.dolomit3 ? data.lokasi.dolomit3 : '-'} kg</li>
												<li>ZA: ${data.lokasi.za3 ? data.lokasi.za3 : '-'} kg</li>
											</ul>
										</td>
										<td>${data.lokasi.pupuk3Comment ? data.lokasi.pupuk3Comment : '-'}</td>
									</tr>
									<tr>
										<td>Pengendalian OPT</td>
										<td>${data.lokasi.optDate ? data.lokasi.optDate : '-'}</td>
										<td></td>
										<td>${data.lokasi.optComment ? data.lokasi.optComment : '-'}</td>
									</tr>
									<tr>
										<td>Panen/Produksi</td>
										<td>${data.lokasi.tgl_panen ? data.lokasi.tgl_panen : '-'}</td>
										<td>${data.lokasi.volume ? data.lokasi.volume : '-'} kg</td>
										<td>${data.lokasi.prodComment ? data.lokasi.prodComment : '-'}</td>
									</tr>
									<tr>
										<td>Distribusi Hasil</td>
										<td>${data.lokasi.tgl_panen ? data.lokasi.tgl_panen : '-'}</td>
										<td>
											<ul class="list-unstyled">
												<li>Untuk benih: ${data.lokasi.vol_benih ? data.lokasi.vol_benih : '-'} kg</li>
												<li>Untuk Dijual: ${data.lokasi.vol_jual ? data.lokasi.vol_jual : '-'} kg</li>
											</ul>

										</td>
										<td>${data.lokasi.distComment ? data.lokasi.distComment : '-'}</td>
									</tr>
							</table>
						</li>
					</ul>
				`;
				$('#kemitraanAktif').html(kemitraanAktifHtml);
			}

			// History Kemitraan
			if (data.historyKemitraan && data.historyKemitraan.length > 0) {
				let historyKemitraanHtml = `
					<ul class="list-group">
						<li class="list-group-item d-flex justify-content-between align-items-start text-white bg-info">
							<span class="text-uppercase">Histori Kemitraan</span>
						</li>
						<li class="list-group-item">
							<table class="table table-hover table-striped table-bordered table-sm">
								<thead class="thead-themed">
									<tr>
										<th>Perusahaan</th>
										<th>Periode</th>
										<th>No Ijin</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									${data.historyKemitraan.map(item => `
										<tr>
											<td>${item.nama || '-'}</td>
											<td>${item.periodetahun || '-'}</td>
											<td>${item.no_ijin || '-'}</td>
											<td>LUNAS</td>
										</tr>
									`).join('')}
								</tbody>
							</table>
						</li>
					</ul>
				`;
				$('#historyKemitraan').html(historyKemitraanHtml);
			}

			// Tampilkan modal
			$('#detailModal').modal('show');
		});
	}


    function fetchDetails(kode_spatial) {
        return fetch(`{{ route('2024.datafeeder.responseGetSpatialDetail') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kode_spatial: kode_spatial })
        }).then(response => response.json())
        .then(data => data.details)
        .catch(error => {
            console.error('Error:', error);
            return {};
        });
    }

    function clearMapMarkers() {
        markersArray.forEach(marker => marker.setMap(null));
        markersArray = [];
    }

    function clearMapPolygons() {
		polygonsArray.forEach(polygon => {
			if (polygon) {
				polygon.setMap(null);
			}
		});
		polygonsArray = [];
	}

</script>


@endsection

{{-- {{ route('admin.task.commitments.pksmitra', $commitment->id) }} --}}
