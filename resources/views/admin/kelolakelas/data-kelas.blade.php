@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-collection-fill me-2"></i>Data Kelas
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $kelases->total() }}</strong> kelas terdaftar
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button"
                class="btn btn-outline-success fw-semibold d-flex align-items-center gap-2"
                style="border-radius:10px; padding:0.6rem 1.2rem;"
                data-bs-toggle="modal" data-bs-target="#modalImportKelas">
            <i class="bi bi-file-earmark-arrow-up-fill"></i> Import Excel
        </button>
        <a href="{{ route('admin.kelas.create') }}"
           class="btn btn-success fw-semibold d-flex align-items-center gap-2"
           style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
            <i class="bi bi-plus-circle-fill"></i> Tambah Kelas
        </a>
    </div>
</div>

{{-- Alert success --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Alert import error --}}
@if(session('import_error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:10px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Import Gagal:</div>
        <p class="mb-0 small">{{ session('import_error') }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm p-4" style="border-radius:20px;">

    <form method="GET" action="{{ route('admin.kelas') }}">
        <div class="row g-2 mb-3 align-items-end">

            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold small mb-1 text-muted">Tingkat</label>
                <select name="tingkat" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($tingkat_list as $t)
                        <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small mb-1 text-muted">Jurusan</label>
                <select name="jurusan" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusan_list as $j)
                        <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>
                            {{ $j }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold small mb-1 text-muted">Wali Kelas</label>
                <select name="status_wali" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="ada" {{ request('status_wali') == 'ada' ? 'selected' : '' }}>
                        Sudah ada
                    </option>
                    <option value="belum" {{ request('status_wali') == 'belum' ? 'selected' : '' }}>
                        Belum ada
                    </option>
                </select>
            </div>

            <div class="col-4 col-md-1">
                <label class="form-label fw-semibold small mb-1 text-muted">Tampilkan</label>
                <select name="per_page" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    @foreach([10, 25, 50, 100, 500] as $n)
                        <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-4 col-md-1">
                <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
                <button type="submit" class="btn btn-primary w-100" style="border-radius:10px;">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <div class="col-4 col-md-1">
                <label class="form-label small mb-1 d-block" style="visibility:hidden;">x</label>
                <a href="{{ route('admin.kelas') }}" class="btn btn-outline-secondary w-100"
                   style="border-radius:10px;" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="text-muted small mb-0">
            Menampilkan <strong>{{ $kelases->firstItem() ?? 0 }}</strong>–<strong>{{ $kelases->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $kelases->total() }}</strong> kelas
        </p>
        <p class="text-muted small mb-0">
            Halaman {{ $kelases->currentPage() }} dari {{ $kelases->lastPage() }}
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th>Kelas</th>
                    <th>Tingkat</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th class="text-center" style="width:130px;">Jumlah Siswa</th>
                    <th class="text-center" style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelases as $i => $kelas)
                    @php $jumlahSiswa = $kelas->siswa->count(); @endphp
                    <tr>
                        <td class="ps-3 text-muted fw-medium">{{ $kelases->firstItem() + $i }}</td>
                        <td>
                            <span class="badge bg-success px-2 py-1"
                                  style="border-radius:6px; background:#15803d !important; font-weight:600;">
                                {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                            </span>
                        </td>
                        <td class="text-muted fw-medium">{{ $kelas->tingkat }}</td>
                        <td class="fw-semibold text-dark">{{ $kelas->jurusan }}</td>
                        <td>
                            @if($kelas->waliKelas)
                                <span class="text-dark fw-medium small">
                                    <i class="bi bi-person-badge text-success me-1"></i>
                                    {{ $kelas->waliKelas->nama_guru }}
                                </span>
                            @else
                                <span class="text-muted fst-italic small">
                                    <i class="bi bi-dash-circle me-1"></i>Belum ditentukan
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $jumlahSiswa > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border' }}"
                                  style="border-radius:6px; font-weight:600; min-width:60px;">
                                {{ $jumlahSiswa }} siswa
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.siswa') }}?id_kelas={{ $kelas->id_kelas }}"
                                   class="btn btn-sm btn-light text-success"
                                   style="border-radius:8px;" title="Lihat Siswa Kelas Ini">
                                    <i class="bi bi-people-fill"></i>
                                </a>
                                <a href="{{ route('admin.kelas.edit', $kelas->id_kelas) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $kelas->id_kelas) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus kelas {{ addslashes($kelas->tingkat . ' ' . $kelas->nama_kelas) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger"
                                            style="border-radius:8px;" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-folder-x display-6 d-block mb-2 text-secondary"></i>
                            Tidak ada data kelas yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kelases->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $kelases->links() }}
        </div>
    @endif

</div>

{{-- ════════════════════════════════════════════════ --}}
{{--  MODAL IMPORT KELAS                             --}}
{{-- ════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalImportKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0" style="border-radius:16px; overflow:hidden;">

            {{-- Header --}}
            <div class="modal-header border-0 pb-0"
                 style="background:linear-gradient(135deg,#15803d,#166534); padding:1.4rem 1.5rem 1rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Import Data Kelas
                    </h5>
                    <p class="text-white-50 small mb-0 mt-1">Upload file Excel atau CSV</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body px-4 py-3">

                {{-- Download template --}}
                <div class="d-flex align-items-center gap-3 p-3 mb-3"
                     style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px;">
                    <div class="flex-shrink-0 text-success" style="font-size:2rem; line-height:1;">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-semibold small text-success-emphasis">Belum punya template?</p>
                        <p class="mb-0 text-muted" style="font-size:0.8rem;">
                            Download template Excel yang sudah sesuai format sistem.
                        </p>
                    </div>
                    <a href="{{ route('admin.kelas.template') }}"
                       class="btn btn-sm btn-success fw-semibold flex-shrink-0"
                       style="border-radius:8px; white-space:nowrap;">
                        <i class="bi bi-download me-1"></i>Template
                    </a>
                </div>

                {{-- Form upload --}}
                <form id="formImportKelas"
                      action="{{ route('admin.kelas.import') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <label class="form-label fw-semibold small text-muted mb-1">
                        Pilih File <span class="text-danger">*</span>
                    </label>

                    {{-- Drop zone --}}
                    <div id="dropzoneKelas"
                         class="text-center p-4 mb-2"
                         style="border:2px dashed #86efac; border-radius:12px;
                                background:#fafffe; cursor:pointer; transition:all .2s;"
                         onclick="document.getElementById('fileInputKelas').click()"
                         ondragover="dzOver(event,'dropzoneKelas')"
                         ondragleave="dzLeave('dropzoneKelas')"
                         ondrop="dzDrop(event,'dropzoneKelas','fileInputKelas')">
                        <i class="bi bi-cloud-arrow-up text-success d-block mb-1" style="font-size:2rem;"></i>
                        <p class="fw-semibold small mb-0">Klik untuk pilih file</p>
                        <p class="text-muted mb-0" style="font-size:0.75rem;">atau drag &amp; drop di sini</p>
                        <p class="text-muted mb-0 mt-1" style="font-size:0.72rem;">
                            .xlsx &nbsp;·&nbsp; .xls &nbsp;·&nbsp; .csv &nbsp;—&nbsp; maks. 5 MB
                        </p>
                    </div>

                    <input type="file"
                           id="fileInputKelas"
                           name="file_import"
                           accept=".xlsx,.xls,.csv"
                           class="d-none"
                           onchange="previewFile('Kelas', this)">

                    {{-- Preview nama file --}}
                    <div id="filePreviewKelas"
                         class="d-none align-items-center gap-2 p-2 mb-2"
                         style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px;">
                        <i class="bi bi-file-earmark-check-fill text-success"></i>
                        <span id="fileNameKelas" class="small fw-semibold text-success flex-grow-1"></span>
                        <button type="button" class="btn btn-sm p-0 lh-1 text-muted border-0 bg-transparent"
                                onclick="clearFile('Kelas')" title="Hapus">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>

                    {{-- Info box --}}
                    <div class="rounded-3 p-3 mt-2"
                         style="background:#fffbeb; border:1px solid #fde68a;">
                        <p class="mb-1 fw-semibold small" style="color:#92400e;">
                            <i class="bi bi-info-circle me-1"></i>Ketentuan file:
                        </p>
                        <ul class="mb-0 ps-3 small" style="color:#78350f; line-height:1.8;">
                            <li>Gunakan template yang tersedia agar format sesuai.</li>
                            <li>Kolom wajib: <code>nama_kelas</code> dan <code>tingkat</code>.</li>
                            <li>Wali kelas dicocokkan by nama guru — pastikan guru sudah ada di sistem.</li>
                            <li>Kombinasi tingkat + nama kelas duplikat akan dilewati otomatis.</li>
                        </ul>
                    </div>

                </form>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
                <button type="button" class="btn btn-light fw-semibold"
                        style="border-radius:10px;" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button"
                        id="btnImportKelas"
                        class="btn btn-success fw-semibold"
                        style="background:#15803d; border:none; border-radius:10px; min-width:140px;"
                        onclick="submitImport('Kelas','formImportKelas')">
                    <span id="btnTextKelas">
                        <i class="bi bi-upload me-1"></i>Import Sekarang
                    </span>
                    <span id="btnLoadingKelas" class="d-none">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Memproses...
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }
    .table-hover > tbody > tr:hover > * { background-color: #f8fafc; }
</style>
@endpush

@push('scripts')
<script>
// ─── Drag & drop helpers ─────────────────────────────────────────────────────
function dzOver(e, id) {
    e.preventDefault();
    const el = document.getElementById(id);
    el.style.borderColor = '#15803d';
    el.style.background  = '#f0fdf4';
}
function dzLeave(id) {
    const el = document.getElementById(id);
    el.style.borderColor = '#86efac';
    el.style.background  = '#fafffe';
}
function dzDrop(e, zoneId, inputId) {
    e.preventDefault();
    dzLeave(zoneId);
    const files = e.dataTransfer.files;
    if (!files.length) return;
    const input = document.getElementById(inputId);
    const dt = new DataTransfer();
    dt.items.add(files[0]);
    input.files = dt.files;
    const suffix = inputId.replace('fileInput', '');
    previewFile(suffix, input);
}

// ─── Preview file terpilih ───────────────────────────────────────────────────
function previewFile(suffix, input) {
    if (!input.files || !input.files[0]) return;
    const file    = input.files[0];
    const maxSize = 5 * 1024 * 1024;
    const ext     = file.name.split('.').pop().toLowerCase();

    if (file.size > maxSize) {
        alert('Ukuran file melebihi 5 MB. Pilih file yang lebih kecil.');
        clearFile(suffix); return;
    }
    if (!['xlsx','xls','csv'].includes(ext)) {
        alert('Format tidak didukung. Gunakan .xlsx, .xls, atau .csv.');
        clearFile(suffix); return;
    }

    const kb = (file.size / 1024).toFixed(1);
    document.getElementById('fileName'   + suffix).textContent = file.name + ' (' + kb + ' KB)';
    document.getElementById('filePreview'+ suffix).classList.remove('d-none');
    document.getElementById('filePreview'+ suffix).classList.add('d-flex');
    document.getElementById('dropzone'   + suffix).style.display = 'none';
}

// ─── Hapus file yang dipilih ─────────────────────────────────────────────────
function clearFile(suffix) {
    document.getElementById('fileInput'  + suffix).value = '';
    document.getElementById('filePreview'+ suffix).classList.add('d-none');
    document.getElementById('filePreview'+ suffix).classList.remove('d-flex');
    document.getElementById('dropzone'   + suffix).style.display = '';
}

// ─── Submit dengan loading state ─────────────────────────────────────────────
function submitImport(suffix, formId) {
    const input = document.getElementById('fileInput' + suffix);
    if (!input || !input.files || !input.files[0]) {
        alert('Pilih file terlebih dahulu!');
        return;
    }
    document.getElementById('btnText'   + suffix).classList.add('d-none');
    document.getElementById('btnLoading'+ suffix).classList.remove('d-none');
    document.getElementById('btnImport' + suffix).disabled = true;
    document.getElementById(formId).submit();
}

// ─── Buka modal otomatis jika ada import_error ───────────────────────────────
@if(session('import_error'))
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('modalImportKelas')).show();
    });
@endif
</script>
@endpush