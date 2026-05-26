@extends('layouts.app')

@section('title', 'Mulai Absen Sesi Mapel')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="fw-bold text-success m-0"><i class="bi bi-journal-plus me-2"></i>Buka Absen Kelas</h5>
                    <span id="badgePertemuan" class="badge bg-secondary px-2.5 py-1.5 opacity-0 transition-all" style="border-radius: 6px; font-size: 0.8rem;">
                        Pertemuan Ke-?
                    </span>
                </div>
                <hr>
                
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('guru.absen-mapel.buka-sesi') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Kelas Bimbingan</label>
                        <select name="id_kelas" id="selectKelas" class="form-select" required>
                            <option value="">-- Pilih Kelas Anda --</option>
                            @forelse($daftarKelas as $kelas)
                                <option value="{{ $kelas->id_kelas }}">{{ $kelas->tingkat }} {{ $kelas->nama_kelas }} ({{ $kelas->jurusan }})</option>
                            @empty
                                <option value="" disabled>⚠️ Anda belum di-plotting ke kelas mana pun oleh Admin</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Mata Pelajaran Anda</label>
                        <div class="input-group">
                            <select name="id_mapel" id="selectMapel" class="form-select" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @forelse($daftarMasterMapel as $mapel)
                                    <option value="{{ $mapel->id_mapel }}">{{ $mapel->nama_mapel }} [{{ $mapel->jurusan }}]</option>
                                @empty
                                    <option value="" disabled>⚠️ Anda belum di-plotting ke mapel mana pun oleh Admin</option>
                                @endforelse
                            </select>
                            
                            <button class="btn btn-outline-success" type="button" id="btnBukaModalManual" title="Tambah Mapel Baru">
                                <i class="bi bi-plus-circle-fill"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold py-2.5" style="background-color: #15803d; border-radius: 8px;">
                        <i class="bi bi-play-fill me-1"></i> Mulai Absen Kelas
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-clock-history me-2"></i>Sesi Mengajar Anda Hari Ini</h5>
                <hr>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Jam</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Pertemuan</th> 
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatHariIni as $riwayat)
                                <tr>
                                    <td class="fw-bold text-primary">{{ \Carbon\Carbon::parse($riwayat->jam_mulai)->format('H:i') }} WIB</td>
                                    <td><span class="badge bg-success-subtle text-success" style="font-weight: 600;">{{ $riwayat->kelas->tingkat }} {{ $riwayat->kelas->nama_kelas }}</span></td>
                                    <td class="fw-semibold text-dark">{{ $riwayat->nama_mapel }}</td>
                                    
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="border-radius: 6px; font-weight: 600;">
                                            Ke-{{ $riwayat->pertemuan_ke }}
                                        </span>
                                    </td>
                                    
                                    <td class="text-end">
                                        <a href="{{ route('guru.absen-mapel.isi', $riwayat->id_mengajar) }}" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 6px; font-weight: 500;">
                                            <i class="bi bi-pencil-square me-1"></i> Ubah Absen
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-x d-block display-6 mb-2 text-secondary"></i>
                                        Belum ada sesi mengajar yang dibuka hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahMapel" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.5); display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-success"><i class="bi bi-plus-circle me-2"></i>Tambah Master Mata Pelajaran</h6>
                <button type="button" class="btn-close" id="btnTutupModalManual1"></button>
            </div>
            <div class="modal-body">
                <div id="errorAlertAjax" class="alert alert-danger d-none py-2" style="font-size: 0.9rem;"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.9rem;">Nama Mata Pelajaran</label>
                    <input type="text" id="inputNamaMapelBaru" class="form-control" placeholder="Contoh: Pemrograman Web (PPLG)" autocomplete="off">
                </div>
                <button type="button" id="btnSimpanMapelAjax" class="btn btn-success w-100 fw-bold py-2" style="background-color: #15803d; border-radius: 8px;">
                    <span id="spinnerSimpan" class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    Simpan & Daftarkan Pilihan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectKelas = document.getElementById('selectKelas');
        const selectMapel = document.getElementById('selectMapel');
        const badgePertemuan = document.getElementById('badgePertemuan');

        const modalTambahMapel = document.getElementById('modalTambahMapel');
        const btnBukaModalManual = document.getElementById('btnBukaModalManual');
        const btnTutupModalManual1 = document.getElementById('btnTutupModalManual1');

        const btnSimpanMapelAjax = document.getElementById('btnSimpanMapelAjax');
        const inputNamaMapelBaru = document.getElementById('inputNamaMapelBaru');
        const errorAlertAjax = document.getElementById('errorAlertAjax');
        const spinnerSimpan = document.getElementById('spinnerSimpan');

        btnBukaModalManual.addEventListener('click', function() {
            modalTambahMapel.style.display = 'block';
            modalTambahMapel.classList.add('show');
            inputNamaMapelBaru.focus();
        });

        function tutupModal() {
            modalTambahMapel.style.display = 'none';
            modalTambahMapel.classList.remove('show');
            errorAlertAjax.classList.add('d-none');
            inputNamaMapelBaru.value = '';
        }

        btnTutupModalManual1.addEventListener('click', tutupModal);

        // =========================================================================
        // FIX: AJAX FILTER MAPEL GURU BERDASARKAN KELAS YANG DIPILIH
        // =========================================================================
        selectKelas.addEventListener('change', function () {
            const idKelas = this.value;

            if (!idKelas) {
                selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
                selectMapel.disabled = true;
                badgePertemuan.classList.add('opacity-0');
                return;
            }

            selectMapel.innerHTML = '<option value="">⏳ Memuat Mata Pelajaran Anda...</option>';
            selectMapel.disabled = true;

            // Memanggil rute API milik guru dengan mengirim parameter ID Kelas
            fetch(`{{ route('guru.absen-mapel.get-mapel') }}?id_kelas=${idKelas}`)
                .then(response => response.json())
                .then(data => {
                    selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
                    
                    if (data.length === 0) {
                        selectMapel.innerHTML = '<option value="" disabled>⚠️ Tidak ada mapel yang di-plot untuk Anda di kelas ini</option>';
                        badgePertemuan.classList.add('opacity-0');
                        return;
                    }

                    // Render otomatis pilihan mapel resmi hasil plotting admin
                    data.forEach(mapel => {
                        const option = document.createElement('option');
                        option.value = mapel.id_mapel;
                        option.text = `${mapel.nama_mapel} [${mapel.jurusan}]`;
                        selectMapel.add(option);
                    });

                    selectMapel.disabled = false;
                    hitungPertemuan(); 
                })
                .catch(error => {
                    console.error('Error:', error);
                    selectMapel.innerHTML = '<option value="">❌ Gagal memuat mata pelajaran</option>';
                });
        });

        function hitungPertemuan() {
            const idKelas = selectKelas.value;
            const namaMapel = selectMapel.options[selectMapel.selectedIndex]?.text || '';

            if (idKelas && selectMapel.value) {
                const namaMapelBersih = namaMapel.split(' [')[0];

                fetch(`{{ route('guru.absen-mapel.cek-pertemuan') }}?id_kelas=${idKelas}&nama_mapel=${encodeURIComponent(namaMapelBersih)}`)
                    .then(response => response.json())
                    .then(data => {
                        badgePertemuan.textContent = `Pertemuan Ke-${data.pertemuan_ke}`;
                        badgePertemuan.classList.remove('bg-secondary', 'opacity-0');
                        badgePertemuan.classList.add('bg-primary');
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                badgePertemuan.classList.add('opacity-0');
                badgePertemuan.textContent = 'Pertemuan Ke-?';
            }
        }

        selectMapel.addEventListener('change', hitungPertemuan);

        // SIMPAN MAPEL BARU VIA AJAX
        btnSimpanMapelAjax.addEventListener('click', function () {
            const namaMapelValue = inputNamaMapelBaru.value.trim();

            if (!namaMapelValue) {
                errorAlertAjax.textContent = 'Nama mata pelajaran tidak boleh kosong!';
                errorAlertAjax.classList.remove('d-none');
                return;
            }

            btnSimpanMapelAjax.disabled = true;
            spinnerSimpan.classList.remove('d-none');
            errorAlertAjax.classList.add('d-none');

            fetch(`{{ route('guru.absen-mapel.tambah-mapel-ajax') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nama_mapel: namaMapelValue })
            })
            .then(response => response.json())
            .then(data => {
                btnSimpanMapelAjax.disabled = false;
                spinnerSimpan.classList.add('d-none');

                if (data.errors || data.message) {
                    errorAlertAjax.textContent = data.message || 'Gagal menyimpan mata pelajaran.';
                    errorAlertAjax.classList.remove('d-none');
                } else if (data.success) {
                    const optionBaru = document.createElement('option');
                    optionBaru.value = data.id_mapel;
                    optionBaru.text = data.nama_mapel;
                    optionBaru.selected = true;

                    selectMapel.add(optionBaru);
                    selectMapel.disabled = false;
                    tutupModal();
                    hitungPertemuan();
                }
            })
            .catch(error => {
                btnSimpanMapelAjax.disabled = false;
                spinnerSimpan.classList.add('d-none');
                errorAlertAjax.textContent = 'Terjadi kesalahan sistem atau mata pelajaran sudah ada.';
                errorAlertAjax.classList.remove('d-none');
            });
        });
    });
</script>
@endsection