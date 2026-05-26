@extends('layouts.app')

@section('title', 'Lembar Absensi Kelas')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-md-center align-items-start flex-column flex-md-row mb-3">
            <div>
                <span class="badge bg-success mb-2" style="background-color: #15803d;">Sesi Aktif Kelas</span>
                <h3 class="fw-bold text-dark m-0">{{ $sesi->nama_mapel }}</h3>
                <p class="text-muted m-0">Kelas: <strong>{{ $sesi->kelas->tingkat }} {{ $sesi->kelas->nama_kelas }}</strong> | Mulai Jam: {{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }} WIB</p>
            </div>
            <a href="{{ route('guru.absen-mapel.index') }}" class="btn btn-light mt-3 mt-md-0 fw-medium" style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <hr>

        <form action="{{ route('guru.absen-mapel.simpan', $sesi->id_mengajar) }}" method="POST">
            @csrf
            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="60">No</th>
                            <th width="150">NIS</th>
                            <th>Nama Siswa</th>
                            <th width="350" class="text-center">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presensiSiswa as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-muted">{{ $row->siswa->nis ?? '-' }}</td>
                                <td class="fw-semibold text-dark">{{ $row->siswa->nama_siswa }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-3">
                                        <label class="btn btn-outline-success btn-sm px-3" style="border-radius: 6px;">
                                            <input type="radio" name="status[{{ $row->id_presensi_mapel }}]" value="Hadir" {{ $row->status == 'Hadir' ? 'checked' : '' }} autocomplete="off"> H
                                        </label>
                                        <label class="btn btn-outline-warning btn-sm px-3" style="border-radius: 6px;">
                                            <input type="radio" name="status[{{ $row->id_presensi_mapel }}]" value="Sakit" {{ $row->status == 'Sakit' ? 'checked' : '' }} autocomplete="off"> S
                                        </label>
                                        <label class="btn btn-outline-info btn-sm px-3" style="border-radius: 6px;">
                                            <input type="radio" name="status[{{ $row->id_presensi_mapel }}]" value="Izin" {{ $row->status == 'Izin' ? 'checked' : '' }} autocomplete="off"> I
                                        </label>
                                        <label class="btn btn-outline-danger btn-sm px-3" style="border-radius: 6px;">
                                            <input type="radio" name="status[{{ $row->id_presensi_mapel }}]" value="Alpa" {{ $row->status == 'Alpa' ? 'checked' : '' }} autocomplete="off"> A
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-2.5 fw-bold shadow-sm" style="border-radius: 10px;">
                    <i class="bi bi-cloud-check-fill me-1"></i> Simpan Hasil Absensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection