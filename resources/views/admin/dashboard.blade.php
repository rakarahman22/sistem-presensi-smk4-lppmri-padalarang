@extends('layouts.app')

@section('title', 'Dashboard Admin - SMK 4 LPPM RI Padalarang')

@section('content')

    <div class="container-fluid px-3 px-md-4 py-4">

        {{-- ===== HEADER ===== --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Dashboard Admin</h4>
                <p class="text-muted small mb-0 mt-1">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <span class="text-muted small">
                👋 Halo, <strong class="text-dark">{{ Auth::guard('admin')->user()->nama_admin ?? 'Admin' }}</strong>
            </span>
        </div>

        {{-- ===== STAT CARDS ===== --}}
        <div class="row g-3 mb-4">

            {{-- Total Siswa --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3"
                    style="background:#eff6ff;border:1px solid #bfdbfe;">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:#1e3a8a;">{{ $totalSiswa }}</div>
                        <div class="stat-lbl" style="color:#1d4ed8;">Total Siswa</div>
                    </div>
                </div>
            </div>

            {{-- Hadir Hari Ini --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3"
                    style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:#166534;">{{ $hadirHariIni }}</div>
                        <div class="stat-lbl" style="color:#15803d;">Hadir Hari Ini</div>
                        <div class="stat-sub">{{ $pctHadir }}% dari presensi</div>
                    </div>
                </div>
            </div>

            {{-- Alpa Hari Ini --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3"
                    style="background:#fff1f2;border:1px solid #fecdd3;">
                    <div class="stat-icon" style="background:#ffe4e6;color:#e11d48;">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:#9f1239;">{{ $alpaHariIni }}</div>
                        <div class="stat-lbl" style="color:#be123c;">Alpa Hari Ini</div>
                        <div class="stat-sub">Perlu ditindaklanjuti</div>
                    </div>
                </div>
            </div>

            {{-- Izin + Sakit --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3"
                    style="background:#fffbeb;border:1px solid #fde68a;">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:#92400e;">{{ $tidakHadirHariIni }}</div>
                        <div class="stat-lbl" style="color:#b45309;">Izin & Sakit</div>
                        <div class="stat-sub">Tidak hadir beralasan</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== GRAFIK + REKAP KELAS ===== --}}
        <div class="row g-4 mb-4">

            {{-- Grafik --}}
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div
                        class="card-header bg-white border-0 px-4 pt-4 pb-0 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-bar-chart-fill text-primary me-2"></i>Rekapitulasi Presensi Minggu Ini
                            </h6>
                            <p class="text-muted small mb-0 mt-1">Senin — Jumat</p>
                        </div>
                        {{-- Legend manual --}}
                        <div class="d-flex gap-3 flex-wrap">
                            <span class="small d-flex align-items-center gap-1">
                                <span class="legend-dot" style="background:#16a34a;"></span>Hadir
                            </span>
                            <span class="small d-flex align-items-center gap-1">
                                <span class="legend-dot" style="background:#e11d48;"></span>Alpa
                            </span>
                            <span class="small d-flex align-items-center gap-1">
                                <span class="legend-dot" style="background:#2563eb;"></span>Izin
                            </span>
                            <span class="small d-flex align-items-center gap-1">
                                <span class="legend-dot" style="background:#d97706;"></span>Sakit
                            </span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-3">
                        <div style="height:300px;position:relative;">
                            <canvas id="absensiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rekap per Kelas --}}
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-building text-success me-2"></i>Rekap Per Kelas Hari Ini
                        </h6>
                        <p class="text-muted small mb-3 mt-1">Dikelompokkan berdasarkan tingkat & jurusan</p>
                        <hr class="mt-0 mb-0">
                    </div>
                    <div class="card-body px-3 py-2" style="max-height:340px;overflow-y:auto;">
                        @forelse($rekapKelas as $kls)
                            @if ($loop->first || $kls['tingkat'] !== $rekapKelas[$loop->index - 1]['tingkat'])
                                <div class="rekap-tingkat-header px-2 py-1 mt-2 mb-1">
                                    <span class="badge bg-dark-subtle text-dark fw-semibold" style="font-size:.7rem;">
                                        Tingkat {{ $kls['tingkat'] }}
                                    </span>
                                </div>
                            @endif

                            <div class="rekap-kelas-item px-2 py-2">
                                <div class="mb-1">
                                    <span class="fw-semibold small text-dark">{{ $kls['tingkat'] }}
                                        {{ $kls['nama_kelas'] }}</span>
                                    <span class="text-muted" style="font-size:.7rem;"> · {{ $kls['jurusan'] ?? '' }}</span>
                                </div>
                                {{-- Mini progress --}}
                                <div class="progress rounded-pill mb-1" style="height:5px;">
                                    @php
                                        $tot = $kls['hadir'] + $kls['alpa'] + $kls['izin_sakit'];
                                        $pH = $tot > 0 ? ($kls['hadir'] / $tot) * 100 : 0;
                                        $pA = $tot > 0 ? ($kls['alpa'] / $tot) * 100 : 0;
                                        $pI = $tot > 0 ? ($kls['izin_sakit'] / $tot) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar" style="width:{{ $pH }}%;background:#16a34a;"></div>
                                    <div class="progress-bar" style="width:{{ $pI }}%;background:#d97706;"></div>
                                    <div class="progress-bar" style="width:{{ $pA }}%;background:#e11d48;"></div>
                                </div>
                                <div class="d-flex gap-2" style="font-size:.7rem;color:#64748b;">
                                    <span><i
                                            class="bi bi-check-circle-fill text-success me-1"></i>{{ $kls['hadir'] }}</span>
                                    <span><i
                                            class="bi bi-exclamation-circle-fill text-warning me-1"></i>{{ $kls['izin_sakit'] }}</span>
                                    <span><i class="bi bi-x-circle-fill text-danger me-1"></i>{{ $kls['alpa'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">Belum ada data hari ini</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== TABEL ALPA HARI INI ===== --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div
                class="card-header bg-white border-0 px-4 pt-4 pb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-x-fill text-danger me-2"></i>Siswa Alpa Hari Ini
                    </h6>
                    <p class="text-muted small mb-0 mt-1">
                        Total: <span class="fw-semibold text-danger">{{ $alpaHariIni }} siswa</span>
                    </p>
                </div>
                {{-- Select per halaman --}}
                @if ($siswaAlpaHariIni->total() > 0)
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Tampilkan</span>
                        <select onchange="window.location=this.value"
                            class="form-select form-select-sm rounded-3 border-0 bg-light fw-semibold"
                            style="width:auto;font-size:.8rem;cursor:pointer;">
                            @foreach ([10, 20, 50, 100] as $opt)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $opt]) }}"
                                    {{ $perPage == $opt ? 'selected' : '' }}>
                                    {{ $opt }} data
                                </option>
                            @endforeach
                        </select>
                        <span class="text-muted small">per halaman</span>
                    </div>
                @endif
            </div>

            <div class="card-body p-0">
                @if ($siswaAlpaHariIni->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr style="background:#f8fafc;border-bottom:2px solid #e9ecef;">
                                    <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase" style="width:5%">#
                                    </th>
                                    <th class="py-3 text-muted fw-semibold small text-uppercase">Nama Siswa</th>
                                    <th class="py-3 text-muted fw-semibold small text-uppercase">NIS</th>
                                    <th class="py-3 text-muted fw-semibold small text-uppercase">Kelas</th>
                                    <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswaAlpaHariIni as $i => $p)
                                    <tr>
                                        <td class="ps-4 text-muted small">{{ $siswaAlpaHariIni->firstItem() + $i }}</td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="mini-avatar">
                                                    {{ strtoupper(substr($p->siswa->nama_siswa ?? '?', 0, 1)) }}
                                                </div>
                                                <span
                                                    class="fw-semibold text-dark small">{{ $p->siswa->nama_siswa ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 text-muted small">{{ $p->siswa->nis ?? '-' }}</td>
                                        <td class="py-3">
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 small">
                                                {{ $p->siswa->kelas->nama_kelas ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="pe-4 py-3 text-muted small">{{ $p->keterangan ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer pagination --}}
                    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2"
                        style="background:#fafbfc;">
                        <span class="text-muted small">
                            Menampilkan {{ $siswaAlpaHariIni->firstItem() }}–{{ $siswaAlpaHariIni->lastItem() }}
                            dari <strong>{{ $siswaAlpaHariIni->total() }}</strong> siswa alpa
                        </span>
                        {{ $siswaAlpaHariIni->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-smile text-success d-block mb-2" style="font-size:2.5rem;"></i>
                        <h6 class="fw-semibold text-dark mb-1">Tidak Ada Siswa Alpa</h6>
                        <p class="text-muted small mb-0">Semua siswa hadir hari ini 🎉</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <style>
        .stat-card {
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08) !important;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-num {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-lbl {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-top: 2px;
        }

        .stat-sub {
            font-size: .68rem;
            color: #94a3b8;
            margin-top: 1px;
        }

        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .rekap-kelas-item {
            border-bottom: 1px solid #f1f5f9;
        }

        .rekap-kelas-item:last-child {
            border-bottom: none;
        }

        .mini-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rekap-tingkat-header {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }

        .table> :not(caption)>*>* {
            border-bottom-color: #f1f5f9;
        }

        .table-hover>tbody>tr:hover>* {
            background-color: #f8fafc;
        }
    </style>

    @push('scripts')
        <script>
            const ctx = document.getElementById('absensiChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($hariLabel) !!},
                    datasets: [{
                            label: 'Hadir',
                            data: {!! json_encode($dataHadir) !!},
                            backgroundColor: '#16a34a',
                            borderRadius: 5,
                            borderSkipped: false,
                            barThickness: 18,
                        },
                        {
                            label: 'Alpa',
                            data: {!! json_encode($dataAlpa) !!},
                            backgroundColor: '#e11d48',
                            borderRadius: 5,
                            borderSkipped: false,
                            barThickness: 18,
                        },
                        {
                            label: 'Izin',
                            data: {!! json_encode($dataIzin) !!},
                            backgroundColor: '#2563eb',
                            borderRadius: 5,
                            borderSkipped: false,
                            barThickness: 18,
                        },
                        {
                            label: 'Sakit',
                            data: {!! json_encode($dataSakit) !!},
                            backgroundColor: '#d97706',
                            borderRadius: 5,
                            borderSkipped: false,
                            barThickness: 18,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: {
                                size: 12
                            },
                            bodyFont: {
                                size: 12
                            },
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                precision: 0
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
