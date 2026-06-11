@extends('layouts.app')

@section('title', $readOnly ? 'Detail Absensi Pertemuan Lalu' : 'Lembar Absensi Kelas')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">

        {{-- ── Header sesi ── --}}
        <div class="d-flex justify-content-between align-items-md-center
                    align-items-start flex-column flex-md-row mb-3">
            <div>
                {{-- Badge status --}}
                @if($readOnly)
                    <span class="badge bg-secondary mb-2">
                        <i class="bi bi-eye me-1"></i>Readonly — Pertemuan Lalu
                    </span>
                @else
                    <span class="badge mb-2" style="background-color: #15803d;">
                        <i class="bi bi-record-circle me-1"></i>Sesi Aktif Kelas
                    </span>
                @endif

                <h3 class="fw-bold text-dark m-0">{{ $sesi->nama_mapel }}</h3>
                <p class="text-muted m-0" style="font-size: 0.9rem;">
                    Kelas: <strong>{{ $sesi->kelas->tingkat }} {{ $sesi->kelas->nama_kelas }}</strong>
                    &nbsp;|&nbsp;
                    Pertemuan Ke-<strong>{{ $pertemuanKe }}</strong>
                    &nbsp;|&nbsp;
                    {{ \Carbon\Carbon::parse($sesi->tgl_mengajar)->translatedFormat('l, d F Y') }}
                    &nbsp;|&nbsp;
                    Mulai: {{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }} WIB
                </p>
            </div>

            <a href="{{ route('guru.absen-mapel.index') }}"
               class="btn btn-light mt-3 mt-md-0 fw-medium"
               style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- Banner readonly --}}
        @if($readOnly)
            <div class="alert alert-secondary d-flex align-items-center gap-2 py-2 mb-3"
                 style="font-size: 0.875rem;">
                <i class="bi bi-lock-fill text-secondary"></i>
                <span>
                    Data absensi pertemuan lalu <strong>tidak dapat diubah</strong>.
                    Anda hanya dapat melihat catatan kehadiran pada sesi ini.
                </span>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
        @endif

        <hr>

        {{-- ── Ringkasan cepat ── --}}
        @php
            $jmlHadir = $presensiSiswa->where('status', 'Hadir')->count();
            $jmlSakit = $presensiSiswa->where('status', 'Sakit')->count();
            $jmlIzin  = $presensiSiswa->where('status', 'Izin')->count();
            $jmlAlpa  = $presensiSiswa->where('status', 'Alpa')->count();
            $total    = $presensiSiswa->count();
        @endphp

        <div class="row g-2 mb-4">
            <div class="col-6 col-md-3">
                <div class="p-3 text-center rounded-3" style="background: #f0fdf4;">
                    <div class="fs-4 fw-bold text-success">{{ $jmlHadir }}</div>
                    <div class="small text-muted">Hadir</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 text-center rounded-3" style="background: #fffbeb;">
                    <div class="fs-4 fw-bold text-warning">{{ $jmlSakit }}</div>
                    <div class="small text-muted">Sakit</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 text-center rounded-3" style="background: #eff6ff;">
                    <div class="fs-4 fw-bold text-info">{{ $jmlIzin }}</div>
                    <div class="small text-muted">Izin</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 text-center rounded-3" style="background: #fef2f2;">
                    <div class="fs-4 fw-bold text-danger">{{ $jmlAlpa }}</div>
                    <div class="small text-muted">Alpa</div>
                </div>
            </div>
        </div>

        {{-- ── Tabel absensi ── --}}
        <form action="{{ route('guru.absen-mapel.simpan', $sesi->id_mengajar) }}"
              method="POST"
              id="formAbsensi">
            @csrf

            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="50">No</th>
                            <th width="130">NIS</th>
                            <th>Nama Siswa</th>
                            <th width="320" class="text-center">
                                @if(!$readOnly)
                                    Status Kehadiran
                                @else
                                    Status
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presensiSiswa as $index => $row)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="text-muted" style="font-size: 0.875rem;">
                                    {{ $row->siswa->nis ?? '-' }}
                                </td>
                                <td class="fw-semibold text-dark">
                                    {{ $row->siswa->nama_siswa }}
                                </td>
                                <td>
                                    @if($readOnly)
                                        {{-- Mode readonly: tampilkan badge status saja --}}
                                        <div class="d-flex justify-content-center">
                                            @php
                                                $badgeMap = [
                                                    'Hadir' => 'success',
                                                    'Sakit' => 'warning',
                                                    'Izin'  => 'info',
                                                    'Alpa'  => 'danger',
                                                ];
                                                $warna = $badgeMap[$row->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $warna }}-subtle text-{{ $warna }}
                                                         border border-{{ $warna }}-subtle px-3 py-2"
                                                  style="border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                                {{ $row->status }}
                                            </span>
                                        </div>
                                    @else
                                        {{-- Mode edit: radio button H/S/I/A --}}
                                        <div class="d-flex justify-content-center gap-2">
                                            @foreach(['Hadir' => ['success','H'], 'Sakit' => ['warning','S'], 'Izin' => ['info','I'], 'Alpa' => ['danger','A']] as $val => [$color, $label])
                                                <label class="btn btn-outline-{{ $color }} btn-sm px-3"
                                                       style="border-radius: 6px; min-width: 42px;">
                                                    <input type="radio"
                                                           name="status[{{ $row->id_presensi_mapel }}]"
                                                           value="{{ $val }}"
                                                           {{ $row->status === $val ? 'checked' : '' }}
                                                           autocomplete="off">
                                                    {{ $label }}
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!$readOnly)
                <div class="d-flex justify-content-between align-items-center">
                    {{-- Tombol pilih semua hadir --}}
                    <button type="button"
                            class="btn btn-outline-success btn-sm"
                            onclick="setSemuaHadir()"
                            style="border-radius: 8px;">
                        <i class="bi bi-check2-all me-1"></i>Set Semua Hadir
                    </button>

                    <button type="submit"
                            class="btn btn-primary px-5 py-2 fw-bold shadow-sm"
                            style="border-radius: 10px;">
                        <i class="bi bi-cloud-check-fill me-1"></i> Simpan Hasil Absensi
                    </button>
                </div>
            @endif
        </form>

    </div>
</div>

@if(!$readOnly)
@push('scripts')
<script>
function setSemuaHadir() {
    document.querySelectorAll('input[type="radio"][value="Hadir"]').forEach(function (radio) {
        radio.checked = true;
        // Trigger Bootstrap button-group styling
        radio.dispatchEvent(new Event('change'));
    });
}
</script>
@endpush
@endif
@endsection