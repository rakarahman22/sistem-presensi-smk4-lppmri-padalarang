@extends('layouts.app')

@section('title', 'Pengaturan Lokasi Absensi - SMK 4 LPPM RI Padalarang')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-success m-0" style="color: #14532d !important;"><i class="bi bi-map-marked-alt me-2"></i>Konfigurasi Lokasi Sekolah</h3>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Form Input Parameter -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white; h-100">
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-crosshairs text-success me-1"></i> Parameter Geofencing</h5>
            <p class="text-muted small mb-4">Tentukan titik koordinat pusat sekolah dan radius jangkauan maksimal untuk presensi siswa.</p>
            
            <form action="{{ route('admin.lokasi.update') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Latitude (Garis Lintang)</label>
                    <input type="text" name="latitude_sekolah" id="lat" class="form-control" value="{{ old('latitude_sekolah', $geo->latitude_sekolah) }}" required style="border-radius: 8px;">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Longitude (Garis Bujur)</label>
                    <input type="text" name="longitude_sekolah" id="lng" class="form-control" value="{{ old('longitude_sekolah', $geo->longitude_sekolah) }}" required style="border-radius: 8px;">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small text-secondary">Radius Jangkauan Presensi (Meter)</label>
                    <input type="number" name="radius_meter" id="radius" class="form-control" value="{{ old('radius_meter', $geo->radius_meter) }}" required style="border-radius: 8px;">
                </div>

                <div class="p-3 mb-4" style="background:#f0fdf4; padding:15px; border-radius:12px; border: 1px solid #dcfce7;">
                    <h6 class="fw-bold text-success small mb-2"><i class="bi bi-shield-lock-fill me-1"></i> Keamanan Modul Presensi</h6>
                    <label class="form-check-label small text-dark d-flex align-items-center gap-2 style="cursor: pointer;">
                        <input type="checkbox" class="form-check-input" checked style="cursor: pointer;"> Deteksi Spofing / Fake GPS / Mock Location
                    </label>
                </div>

                <button type="submit" class="btn btn-success w-100" style="background-color: #15803d; border: none; border-radius: 10px; padding: 0.75rem 1.5rem; font-weight: 600;">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan Lokasi
                </button>
            </form>
        </div>
    </div>

    <!-- Peta Interaktif Preview -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 20px; background: white;">
            <div id="map-preview" style="height: 450px; border-radius: 14px; border: 1px solid #e2e8f0; z-index: 1;"></div>
            <div class="form-text small text-muted mt-2 px-1">
                <i class="bi bi-info-circle"></i> <strong>Tips:</strong> Anda bisa mengklik langsung di area peta untuk menggeser penanda pusat lokasi sekolah secara otomatis.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Memasang Pustaka Pemetaan LeafletJS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Ambil nilai koordinat mula-mula dari input form Laravel
        let currentLat = parseFloat(document.getElementById('lat').value);
        let currentLng = parseFloat(document.getElementById('lng').value);
        let currentRad = parseInt(document.getElementById('radius').value);

        // 1. Inisialisasi Kanvas Peta
        const map = L.map('map-preview').setView([currentLat, currentLng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // 2. Pasang Marker Pin dan Lingkaran Radius Geofence
        let marker = L.marker([currentLat, currentLng]).addTo(map);
        let circle = L.circle([currentLat, currentLng], {
            radius: currentRad,
            color: '#15803d',
            fillColor: '#15803d',
            fillOpacity: 0.18
        }).addTo(map);

        // 3. Fungsi Sinkronisasi Perubahan Input Form ke Tampilan Peta
        function updateMap() {
            let nLat = parseFloat(document.getElementById('lat').value) || 0;
            let nLng = parseFloat(document.getElementById('lng').value) || 0;
            let nRad = parseInt(document.getElementById('radius').value) || 0;

            marker.setLatLng([nLat, nLng]);
            circle.setLatLng([nLat, nLng]);
            circle.setRadius(nRad);
        }

        // Pantau ketikan admin pada form secara real-time
        document.getElementById('lat').addEventListener('input', updateMap);
        document.getElementById('lng').addEventListener('input', updateMap);
        document.getElementById('radius').addEventListener('input', updateMap);

        // 4. Fitur Klik Peta Langsung Ambil Koordinat (Mudah Digunakan)
        map.on('click', function (e) {
            let clickedLat = e.latlng.lat.toFixed(6);
            let clickedLng = e.latlng.lng.toFixed(6);

            // Tulis ulang ke dalam kotak input form
            document.getElementById('lat').value = clickedLat;
            document.getElementById('lng').value = clickedLng;

            // Segarkan posisi pin di peta
            updateMap();
        });
    });
</script>
@endpush