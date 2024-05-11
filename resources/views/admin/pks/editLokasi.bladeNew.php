@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('content')
<div class="panel">
    <div class="panel-hdr">
        <h2>
            Tambah Lokasi <span class="fw-300"><i>Lokasi</i></span>
        </h2>
    </div>
    <div class="panel-container show">
        <div class="panel-content">
            <div id="mapid" style="height: 500px;"></div>
            <form action="{{route('admin.task.updateLokasiTanam', $lokasi->id)}}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <button type="submit" class="btn btn-primary mt-3">Simpan Lokasi</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var map = L.map('mapid', {
        center: [-2.548926, 118.0148634],
        zoom: 4,
        layers: [
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            })
        ]
    });

    var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3'],
        attribution: 'Map data ©2023 Google'
    }).addTo(map);

    var baseMaps = {
        "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
        "Google Satellite": googleSat
    };

    L.control.layers(baseMaps).addTo(map);

    var marker;

    function onMapClick(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = new L.Marker(e.latlng, { draggable: true }).addTo(map);
        marker.on('dragend', function(event) {
            var position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat;
            document.getElementById('longitude').value = position.lng;
        });
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    }

    map.on('click', onMapClick);
</script>
@endsection
