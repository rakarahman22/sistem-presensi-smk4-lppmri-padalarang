@extends('layouts.app')

@section('title', 'Presensi Harian - SMK 4 LPPM RI')

@section('content')
    <div class="content-fluid px-0 px-md-3">
        <div class="header mb-3">
            <h1 class="fw-bold text-dark" style="font-size: 1.8rem;">Presensi Lokasi</h1>
            <p class="text-muted small mb-0">Pastikan GPS aktif dan Anda berada di area sekolah sebelum melakukan presensi.
            </p>
        </div>

        <!-- JAM DIGITAL -->
        <div class="clock-container mb-4 shadow-sm bg-white p-3 rounded-3 text-center mx-auto d-block"
            style="max-width: 480px; border: 1px solid #e2e8f0;">
            <div id="digitalClock" class="presensi-clock">00:00:00</div>
            <div style="font-size: 0.8rem; color: #64748b;" class="mt-1">Waktu Server Indonesia (WIB)</div>
        </div>

        <!-- AREA NOTIFIKASI SUKSES / GAGAL FROM BACK-END -->
        <div class="mx-auto" style="max-width: 500px;">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm text-center py-3 mb-3 small"
                    style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
                    <i class="fas fa-check-circle me-1"></i> <strong>{{ session('success') }}</strong>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-sm text-center py-3 mb-3 small"
                    style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
                    <i class="fas fa-exclamation-triangle me-1"></i> <strong>{{ session('error') }}</strong>
                </div>
            @endif
        </div>

        <!-- KARTU UTAMA ABSENSI -->
        <div class="card bg-white border-0 shadow-sm p-4 mx-auto"
            style="border-radius: 15px; max-width: 500px; border: 1px solid #eee; position: relative;">

            <!-- Tombol Refresh Update Lokasi Manual -->
            <button class="refresh-map-btn position-absolute border bg-white" id="btn-update-lokasi" title="Update Lokasi"
                type="button">
                <i class="fas fa-sync-alt text-secondary"></i> Update
            </button>

            <!-- Peta Leaflet -->
            <div id="map" class="mb-3 presensi-map"></div>

            <!-- Box Indikator Status Radius Jarak -->
            <div id="statusBox" class="status-text status-out">
                <i class="fas fa-spinner fa-spin"></i> Menghitung Jarak...
            </div>

            <!-- Pesan info tambahan (akurasi GPS, jarak kekurangan, dll) -->
            <div id="infoBox" class="info-text d-none"></div>

            <p style="font-size: 0.85em; color: #666; margin-bottom: 20px;">
                <i class="fas fa-map-marker-alt text-danger"></i> Pin Merah: Sekolah |
                <i class="fas fa-circle text-primary"></i> Titik Biru: Anda
            </p>

            <!-- FORM ALUR ABSENSI MANDIRI (SISTEM 1 KALI KLIK) -->
            @if (!$presensiHariIni)
                <!-- JALUR 1: BELUM ABSEN HARI INI -->
                <form action="{{ route('siswa.presensi.store') }}" method="POST" id="form-action-absen">
                    @csrf
                    <input type="hidden" name="lat_siswa" id="lat_input">
                    <input type="hidden" name="long_siswa" id="long_input">
                    <input type="hidden" name="accuracy" id="accuracy_input">

                    <button type="submit" class="btn btn-disabled w-100" id="startScan" disabled>
                        <i class="fas fa-spinner fa-spin me-1"></i> Memeriksa Lokasi...
                    </button>
                </form>
            @else
                <!-- JALUR 2: SUDAH ABSEN (KUNCI DAN TAMPILKAN STATUS BERHASIL) -->
                <div class="p-4 text-center rounded-3 small shadow-inner shadow-sm border border-success-subtle"
                    style="background-color: #f0fdf4 !important;">
                    <i class="fas fa-check-circle text-success fs-2 mb-2 d-block"></i>
                    <span class="fw-bold text-success d-block mb-1" style="font-size: 1.1em;">Presensi Anda Hari Ini
                        Berhasil!</span>
                    <p class="text-muted mb-0 small">Tercatat pada jam: <strong
                            class="text-dark">{{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}
                            WIB</strong></p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Pustaka Pemetaan Geofencing LeafletJS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- 1. JAM DIGITAL ---
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                const clockEl = document.getElementById('digitalClock');
                if (clockEl) clockEl.innerText = `${h}:${m}:${s}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // --- 2. KONFIGURASI DINAMIS DARI DATABASE LARAVEL ---
            const latSekolah = parseFloat("{{ $geofence->latitude_sekolah ?? -6.849462 }}");
            const lngSekolah = parseFloat("{{ $geofence->longitude_sekolah ?? 107.486074 }}");
            const radiusLimit = parseInt("{{ $geofence->radius_meter ?? 100 }}");
            // Ambang akurasi GPS — selaraskan dengan MAX_ACCURACY_METER di server
            const maxAccuracy = 100;
            const schoolCoords = [latSekolah, lngSekolah];

            const statusBox = document.getElementById('statusBox');
            const infoBox = document.getElementById('infoBox');
            const btn = document.getElementById('startScan');

            function showInfo(message) {
                if (!infoBox) return;
                if (!message) {
                    infoBox.classList.add('d-none');
                    infoBox.innerHTML = '';
                    return;
                }
                infoBox.classList.remove('d-none');
                infoBox.innerHTML = message;
            }

            function setButtonState(enabled, text, iconClass, variant) {
                if (!btn) return;
                btn.disabled = !enabled;
                btn.innerHTML = `<i class="${iconClass} me-1"></i> ${text}`;
                btn.className = 'btn w-100 ' + variant;
            }

            if (document.getElementById('map')) {
                // Inisialisasi Peta Leaflet
                const map = L.map('map', {
                    zoomControl: false
                }).setView(schoolCoords, 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                // Pasang Pin Merah Sekolah
                const redIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
                L.marker(schoolCoords, {
                    icon: redIcon
                }).addTo(map).bindPopup("Titik Presensi Sekolah");

                // Gambar Batas Jangkauan Geofence Sekolah
                L.circle(schoolCoords, {
                    color: '#15803d',
                    weight: 1,
                    fillColor: '#15803d',
                    fillOpacity: 0.12,
                    radius: radiusLimit
                }).addTo(map);

                // Inisialisasi Dot Penanda HP Siswa (Titik Biru)
                let userCircle = L.circleMarker([0, 0], {
                    radius: 7,
                    color: 'white',
                    fillColor: '#1e40af',
                    fillOpacity: 1,
                    weight: 2
                }).addTo(map);

                // Lingkaran akurasi GPS siswa (radius = accuracy meter)
                let accuracyCircle = L.circle([0, 0], {
                    radius: 0,
                    color: '#1e40af',
                    weight: 1,
                    fillColor: '#1e40af',
                    fillOpacity: 0.08
                }).addTo(map);

                // Rumus Matematika Haversine Sisi Browser Client
                function getDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(
                        lat2 * Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                }

                // Pembaruan Antarmuka (UI) Berdasarkan Perubahan Jarak & Akurasi GPS
                function updateUI(lat, lng, accuracy) {
                    const distance = Math.round(getDistance(lat, lng, schoolCoords[0], schoolCoords[1]));
                    userCircle.setLatLng([lat, lng]);
                    accuracyCircle.setLatLng([lat, lng]);
                    accuracyCircle.setRadius(accuracy || 0);

                    // Set value ke input hidden sebelum dikirim ke Controller
                    const latInput = document.getElementById('lat_input');
                    const lngInput = document.getElementById('long_input');
                    const accInput = document.getElementById('accuracy_input');
                    if (latInput) latInput.value = lat;
                    if (lngInput) lngInput.value = lng;
                    if (accInput) accInput.value = (accuracy !== undefined && accuracy !== null) ? accuracy : '';

                    // Selaraskan dengan ACCURACY_WARN_METER & ACCURACY_MAX_METER di controller
                    const ACCURACY_WARN_METER = 200;
                    const ACCURACY_MAX_METER = 1000;

                    const akurasiKurang = accuracy !== undefined && accuracy !== null && accuracy >
                        ACCURACY_WARN_METER;
                    const akurasiSangatBuruk = accuracy !== undefined && accuracy !== null && accuracy >
                        ACCURACY_MAX_METER;

                    if (distance <= radiusLimit && !akurasiSangatBuruk) {
                        statusBox.className = "status-text status-in";
                        statusBox.innerHTML =
                            `<i class="fas fa-check-circle"></i> Terdeteksi di Radius (${distance} m)`;
                        setButtonState(true, 'Kirim Kehadiran', 'fas fa-paper-plane', 'btn-active');

                        if (akurasiKurang) {
                            showInfo(
                                `Sinyal GPS agak lemah (± ${Math.round(accuracy)} m), namun posisi Anda masih terdeteksi dalam radius sekolah. Anda tetap bisa melanjutkan presensi.`
                                );
                        } else {
                            showInfo(null);
                        }
                    } else if (akurasiSangatBuruk) {
                        statusBox.className = "status-text status-warn";
                        statusBox.innerHTML =
                            `<i class="fas fa-satellite-dish"></i> Sinyal GPS Tidak Valid (± ${Math.round(accuracy)} m)`;
                        setButtonState(false, 'Presensi', 'fas fa-ban', 'btn-disabled');
                        showInfo(
                            'Lokasi tidak dapat diverifikasi. Pastikan GPS di HP aktif (bukan absen dari laptop/PC), lalu klik "Update".'
                            );
                    } else {
                        const kurang = distance - radiusLimit;
                        statusBox.className = "status-text status-out";
                        statusBox.innerHTML =
                            `<i class="fas fa-exclamation-triangle"></i> Di Luar Jangkauan (${distance} m)`;
                        setButtonState(false, 'Presensi', 'fas fa-ban', 'btn-disabled');
                        showInfo(`Anda perlu mendekat sekitar <strong>${kurang} m</strong> lagi ke area sekolah.`);
                    }
                }

                // Fungsi Inti Melacak Lokasi GPS HP Siswa + Fitur Anti-Blokir
                function getLocation() {
                    if (navigator.geolocation) {
                        statusBox.className = "status-text status-out";
                        statusBox.innerHTML =
                        `<i class="fas fa-spinner fa-spin"></i> Mencari Sinyal Satelit GPS...`;
                        showInfo(null);
                        setButtonState(false, 'Memeriksa Lokasi...', 'fas fa-spinner fa-spin', 'btn-disabled');

                        navigator.geolocation.getCurrentPosition((pos) => {
                            updateUI(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy);
                            map.panTo([pos.coords.latitude, pos.coords.longitude]);
                        }, (err) => {
                            let pesanUser = "Gagal melacak lokasi. Klik tombol Update untuk mencoba lagi.";
                            if (err.code === err.PERMISSION_DENIED) {
                                pesanUser =
                                    "Akses lokasi ditolak. Izinkan akses lokasi melalui ikon gembok/info di bilah alamat browser, lalu klik Update.";
                            } else if (err.code === err.TIMEOUT) {
                                pesanUser =
                                    "Waktu pencarian lokasi habis. Pastikan GPS & koneksi internet aktif, lalu klik Update.";
                            }
                            statusBox.className = "status-text status-out";
                            statusBox.innerHTML =
                                `<i class="fas fa-exclamation-circle"></i> Lokasi belum ditemukan`;
                            showInfo(pesanUser);
                            setButtonState(false, 'Presensi', 'fas fa-ban', 'btn-disabled');
                        }, {
                            enableHighAccuracy: true,
                            timeout: 12000,
                            maximumAge: 0
                        });
                    } else {
                        statusBox.className = "status-text status-out";
                        statusBox.innerHTML =
                        `<i class="fas fa-exclamation-circle"></i> Geolocation Tidak Didukung`;
                        showInfo(
                            "Browser Anda tidak mendukung fitur pelacakan lokasi. Gunakan browser lain (Chrome/Safari versi terbaru)."
                            );
                        setButtonState(false, 'Presensi', 'fas fa-ban', 'btn-disabled');
                    }
                }

                // Hubungkan tombol update ke fungsi GPS
                document.getElementById('btn-update-lokasi').addEventListener('click', function(e) {
                    e.preventDefault();
                    getLocation();
                });

                // Jalankan pelacakan otomatis saat halaman dimuat
                getLocation();
            }
        });
    </script>

    <style>
        /* ── Jam digital ──────────────────────────────────── */
        .presensi-clock {
            font-size: 1.6rem;
            font-weight: 600;
            color: #15803d;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }

        /* ── Peta ─────────────────────────────────────────── */
        .presensi-map {
            height: 250px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
            border: 1px solid #e2e8f0;
        }

        @media (max-width: 480px) {
            .presensi-map {
                height: 200px;
            }
        }

        /* ── Tombol update lokasi ─────────────────────────── */
        .refresh-map-btn {
            top: 35px;
            right: 35px;
            z-index: 100;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
            transition: background-color .15s ease;
        }

        .refresh-map-btn:hover {
            background-color: #f0fdf4 !important;
        }

        /* ── Status & info box ────────────────────────────── */
        .status-text {
            font-weight: 600;
            font-size: 0.95em;
            margin: 15px 0 8px;
            padding: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .status-in {
            color: #15803d;
            background-color: #f0fdf4;
            border: 1px solid #86efac;
        }

        .status-out {
            color: #dc2626;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }

        .status-warn {
            color: #b45309;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
        }

        .info-text {
            font-size: 0.8rem;
            color: #6b7280;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 14px;
            text-align: center;
            line-height: 1.5;
        }

        /* ── Tombol presensi ──────────────────────────────── */
        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-active {
            background-color: #15803d;
            color: white;
            cursor: pointer;
        }

        .btn-active:hover {
            background-color: #166534;
        }

        .btn-disabled {
            background-color: #cbd5e1;
            color: #94a3b8;
            cursor: not-allowed;
        }
    </style>
@endpush
