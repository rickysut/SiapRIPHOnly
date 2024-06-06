@extends('t2024.layouts.admin')
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
@can('spatial_data_access')

	<div class="row">
		<div class="col-12">
			<div class="panel" id="panel-peta">
				<div class="panel-hdr">
					<h2>
						Data Spasial <span class="fw-300">
							<i>Lokasi</i>
						</span>
					</h2>
					<div class="panel-toolbar">
						<span class="fw-500" id="kdLokasiTitle"></span>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">

						<div class="row d-flex justify-content-between">
							<div class="col-lg-5">
								<div class="form-group">
									<div class="input-group bg-white shadow-inset-2">
										<div class="input-group-prepend">
											<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
												<i class="fal fa-upload"></i>
											</span>
										</div>
										<div class="custom-file">
											<input type="file" accept=".kml" id="kml_file" placeholder="ambil berkas KML..."
												class="custom-file-input border-left-0 bg-transparent pl-0" >
											<label class="custom-file-label text-muted" for="inputGroupFile01">ambil berkas KML...</label>
										</div>
									</div>
									<span class="help-block">Unggah berkas KML</span>
								</div>
								<div id="myMap" style="height: 400px; width: 100%;" hidden></div>
							</div>
							<div class="col-lg-7">
								<ul class="list-group" id="exportedData">
								</ul>
								<div id="myForm" hidden>
									<form action="" enctype="multipart/form-data" method="POST" id="formSubmit">
										@csrf
										<input class="form-control" type="hidden" id="latitude" value="">
										<input class="form-control" type="hidden" id="longitude" value="">
										<input class="form-control" type="hidden" id="polygon" value="">
										<input class="form-control" type="hidden" id="kode_lokasi" value="">
										<input class="form-control" type="hidden" id="komoditas" value="">
										<div class="d-flex justify-content-between mt-3">
											<div></div>
											<div>
												<button class="btn btn-primary" id="btnSubmit">Simpan</button></div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="row" hidden>
		<div class="col-12">
			<div class="panel" id="panel-data" >
				<div class="panel-hdr">
					<h2>
						Data Lokasi
					</h2>
				</div>
				<div class="panel-container show">
					<table id="kmlTable" class="table table-sm table-bordered table-hover table-striped w-100">
						<thead>
							<tr>
								<th>Id</th>
								<th>Komoditas</th>
								<th>Poktan</th>
								<th>No</th>
								<th>Nama Petani</th>
								<th>Luas</th>
								<th>X</th>
								<th>Y</th>
								<th>Kecamatan</th>
								<th>Desa</th>
							</tr>
						</thead>
						<tbody id="kmlTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent
<script>
	let marker;
	let polygon;
	let myMap;

	function initMap() {
		myMap = new google.maps.Map(document.getElementById("myMap"), {
			center: { lat: -2.5489, lng: 118.0149 },
			zoom: 5,
			mapTypeId: google.maps.MapTypeId.HYBRID,
			fullscreenControl: true,
			mapTypeControl: false, // Menyembunyikan kontrol pilihan tipe peta
			streetViewControl: false, // Menyembunyikan kontrol Street View
			zoomControl: false, // Menyembunyikan kontrol zoom
			scaleControl: false, // Menyembunyikan kontrol skala
			rotateControl: false, // Menyembunyikan kontrol rotasi
		});
	}

	initMap();

	function kml_parser() {
		document.getElementById("myMap").removeAttribute("hidden");
		document.getElementById("myForm").removeAttribute("hidden");
		if (marker) {
			marker.setMap(null);
		}
		if (polygon) {
			polygon.setMap(null);
		}

		const kmlFile = document.getElementById("kml_file").files[0];

		const reader = new FileReader();

		reader.onload = (event) => {
			const kmlData = event.target.result;

			const parser = new DOMParser();
			const kmlXml = parser.parseFromString(kmlData, "application/xml");

			const coordinates = kmlXml
				.getElementsByTagName("coordinates")[0]
				.textContent.trim()
				.split(/\s+/);

			const latLngs = coordinates.map((coord) => {
				const [lng, lat] = coord.split(",");
				return new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
			});

			const linearRings = Array.from(kmlXml.getElementsByTagName("LinearRing"));
			const polygonPaths = linearRings.map((ring) => {
				const coordinates = ring
					.getElementsByTagName("coordinates")[0]
					.textContent.trim()
					.split(/\s+/);
				return coordinates.map((coord) => {
					const [lng, lat] = coord.split(",");
					return { lat: parseFloat(lat), lng: parseFloat(lng) };
				});
			});

			marker = new google.maps.Marker({
				position: latLngs[0],
				map: myMap,
				draggable: false,
			});

			polygon = new google.maps.Polygon({
				paths: polygonPaths,
				fillColor: "#fd3995",
				strokeColor: "#fd3995",
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillOpacity: 0.5,
				editable: false,
				draggable: false,
				map: myMap,
			});

			const bounds = new google.maps.LatLngBounds();
			latLngs.forEach((polygonPaths) => bounds.extend(polygonPaths));
			myMap.fitBounds(bounds);

			document.getElementById("latitude").value = marker.getPosition().lat();
			document.getElementById("longitude").value = marker.getPosition().lng();
			document.getElementById("polygon").value = JSON.stringify(
				polygon.getPath().getArray()
			);

			var infowindow = new google.maps.InfoWindow({
				content: "Hello World!",
			});

			displayAttributes(kmlXml);
		};

		reader.readAsText(kmlFile);
	}

	function displayAttributes(kmlXml) {
		const exportedDataList = document.getElementById("exportedData");
		exportedDataList.innerHTML = "";

		const placemarks = kmlXml.getElementsByTagName("Placemark");
		for (let i = 0; i < placemarks.length; i++) {
			const placemark = placemarks[i];
			const no = placemark.querySelector("SimpleData[name='NO']").textContent;
			const komoditas = placemark.querySelector("SimpleData[name='KOMODITAS']").textContent;
			const poktan = placemark.querySelector("SimpleData[name='POKTAN']").textContent;
			const namaPetani = placemark.querySelector("SimpleData[name='NMPETANI']").textContent;
			const luas = placemark.querySelector("SimpleData[name='LUAS']").textContent;
			const x = parseFloat(placemark.querySelector("SimpleData[name='X']").textContent);
			const y = parseFloat(placemark.querySelector("SimpleData[name='Y']").textContent);
			const kecamatan = placemark.querySelector("SimpleData[name='KECAMATAN']").textContent;
			const desa = placemark.querySelector("SimpleData[name='DESA']").textContent;

			document.getElementById("kode_lokasi").value = no;
			document.getElementById("komoditas").value = komoditas;
			document.getElementById("kdLokasiTitle").textContent = no;
			//tambahkan field lainnya untuk di export

			const listItem = `
				<li class='list-group-item d-flex justify-content-between'>
					<span>Kode Lokasi</span>
					<span id='no'> ${no} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>Komoditas</span>
					<span id='komoditas'> ${komoditas} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>Poktan</span>
					<span id='poktan'> ${poktan} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>Nama Petani</span>
					<span id='namaPetani'> ${namaPetani} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>Luas</span>
					<span id='luas'> ${luas} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>X</span>
					<span id='x'> ${x} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>Y</span>
					<span id='y'> ${y} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>Kecamatan</span>
					<span id='kecamatan'> ${kecamatan} </span>
				</li>
				<li class='list-group-item d-flex justify-content-between'>
					<span>Desa</span>
					<span id='desa'> ${desa} </span>
				</li>
			`;

			exportedDataList.innerHTML += listItem;
		}
	}

	document.getElementById('kml_file').addEventListener('change', kml_parser);
</script>

@endsection
