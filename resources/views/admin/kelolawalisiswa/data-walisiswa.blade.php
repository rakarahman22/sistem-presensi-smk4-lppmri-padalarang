@extends('layouts.app')

@section('title', 'Data Wali Siswa')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0" style="color:#14532d;">
            <i class="bi bi-people-fill me-2"></i>Data Wali Siswa
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Total <strong>{{ $walis->total() }}</strong> wali terdaftar
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button"
                class="btn btn-outline-success fw-semibold d-flex align-items-center gap-2"
                style="border-radius:10px; padding:0.6rem 1.2rem;"
                data-bs-toggle="modal" data-bs-target="#modalImportWali">
            <i class="bi bi-file-earmark-arrow-up-fill"></i> Import Excel
        </button>
        <a href="{{ route('admin.wali.create') }}"
           class="btn btn-success fw-semibold d-flex align-items-center gap-2"
           style="background:#15803d; border:none; border-radius:10px; padding:0.6rem 1.2rem;">
            <i class="bi bi-plus-circle-fill"></i> Tambah Wali Siswa
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

    <form method="GET" action="{{ route('admin.wali') }}">
        <div class="row g-2 mb-3 align-items-end">

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1 text-muted">Cari Wali Siswa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}"
                           class="form-control bg-light border-start-0"
                           placeholder="Nama, username, atau no. telp..."
                           style="border-radius:0 10px 10px 0;">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small mb-1 text-muted">Status Siswa</label>
                <select name="status_siswa" class="form-select" style="border-radius:10px;"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="punya" {{ request('status_siswa') == 'punya' ? 'selected' : '' }}>
                        Sudah punya siswa
                    </option>
                    <option value="belum" {{ request('status_siswa') == 'belum' ? 'selected' : '' }}>
                        Belum punya siswa
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
                <a href="{{ route('admin.wali') }}" class="btn btn-outline-secondary w-100"
                   style="border-radius:10px;" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="text-muted small mb-0">
            Menampilkan <strong>{{ $walis->firstItem() ?? 0 }}</strong>–<strong>{{ $walis->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $walis->total() }}</strong> wali
        </p>
        <p class="text-muted small mb-0">
            Halaman {{ $walis->currentPage() }} dari {{ $walis->lastPage() }}
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.93rem;">
            <thead class="table-light text-muted fw-semibold">
                <tr>
                    <th class="ps-3" style="width:55px;">No</th>
                    <th>Nama Wali</th>
                    <th>Username</th>
                    <th>No. Telepon</th>
                    <th class="text-center" style="width:140px;">Siswa Terdaftar</th>
                    <th class="text-center" style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($walis as $i => $wali)
                    @php $jumlahSiswa = $wali->siswa->count(); @endphp
                    <tr>
                        <td class="ps-3 text-muted fw-medium">{{ $walis->firstItem() + $i }}</td>
                        <td class="fw-semibold text-dark">{{ $wali->nama_wali }}</td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2 py-1"
                                  style="border-radius:6px;">
                                {{ $wali->username }}
                            </span>
                        </td>
                        <td class="text-muted">
                            @if($wali->no_telp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wali->no_telp) }}"
                                   target="_blank" class="text-success text-decoration-none"
                                   title="Hubungi via WhatsApp">
                                    <i class="bi bi-whatsapp me-1"></i>{{ $wali->no_telp }}
                                </a>
                            @else
                                <span class="fst-italic small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($jumlahSiswa > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    {{ $jumlahSiswa }} siswa
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"
                                      style="border-radius:6px; font-weight:600;">
                                    Belum ada
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.wali.edit', $wali->id_wali) }}"
                                   class="btn btn-sm btn-light text-primary"
                                   style="border-radius:8px;" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.wali.destroy', $wali->id_wali) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus {{ addslashes($wali->nama_wali) }}?\nData siswa yang terhubung akan terpengaruh.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-light text-danger"
                                            style="border-radius:8px;" title="Hapus"
                                            {{ $jumlahSiswa > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                            Tidak ada data wali siswa yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($walis->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $walis->links() }}
        </div>
    @endif

</div>

{{-- ════════════════════════════════════════════════ --}}
{{--  MODAL IMPORT WALI SISWA                        --}}
{{-- ════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalImportWali" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0" style="border-radius:16px; overflow:hidden;">

            {{-- Header --}}
            <div class="modal-header border-0 pb-0"
                 style="background:linear-gradient(135deg,#15803d,#166534); padding:1.4rem 1.5rem 1rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Import Data Wali Siswa
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
                    <a href="{{ route('admin.wali.template') }}"
                       class="btn btn-sm btn-success fw-semibold flex-shrink-0"
                       style="border-radius:8px; white-space:nowrap;">
                        <i class="bi bi-download me-1"></i>Template
                    </a>
                </div>

                {{-- Form upload --}}
                <form id="formImportWali"
                      action="{{ route('admin.wali.import') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <label class="form-label fw-semibold small text-muted mb-1">
                        Pilih File <span class="text-danger">*</span>
                    </label>

                    {{-- Drop zone --}}
                    <div id="dropzoneWali"
                         class="text-center p-4 mb-2"
                         style="border:2px dashed #86efac; border-radius:12px;
                                background:#fafffe; cursor:pointer; transition:all .2s;"
                         onclick="document.getElementById('fileInputWali').click()"
                         ondragover="dzOver(event,'dropzoneWali')"
                         ondragleave="dzLeave('dropzoneWali')"
                         ondrop="dzDrop(event,'dropzoneWali','fileInputWali')">
                        <i class="bi bi-cloud-arrow-up text-success d-block mb-1" style="font-size:2rem;"></i>
                        <p class="fw-semibold small mb-0">Klik untuk pilih file</p>
                        <p class="text-muted mb-0" style="font-size:0.75rem;">atau drag &amp; drop di sini</p>
                        <p class="text-muted mb-0 mt-1" style="font-size:0.72rem;">
                            .xlsx &nbsp;·&nbsp; .xls &nbsp;·&nbsp; .csv &nbsp;—&nbsp; maks. 5 MB
                        </p>
                    </div>

                    <input type="file"
                           id="fileInputWali"
                           name="file_import"
                           accept=".xlsx,.xls,.csv"
                           class="d-none"
                           onchange="previewFile('Wali', this)">

                    {{-- Preview nama file --}}
                    <div id="filePreviewWali"
                         class="d-none align-items-center gap-2 p-2 mb-2"
                         style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px;">
                        <i class="bi bi-file-earmark-check-fill text-success"></i>
                        <span id="fileNameWali" class="small fw-semibold text-success flex-grow-1"></span>
                        <button type="button" class="btn btn-sm p-0 lh-1 text-muted border-0 bg-transparent"
                                onclick="clearFile('Wali')" title="Hapus">
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
                            <li>Kolom wajib: <code>nama_wali</code>.</li>
                            <li>Username duplikat akan dilewati otomatis.</li>
                            <li>Username default = <em>nama.lowercase</em> jika kosong.</li>
                            <li>Password default = <code>wali1234</code> jika kosong.</li>
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
                        id="btnImportWali"
                        class="btn btn-success fw-semibold"
                        style="background:#15803d; border:none; border-radius:10px; min-width:140px;"
                        onclick="submitImport('Wali','formImportWali')">
                    <span id="btnTextWali">
                        <i class="bi bi-upload me-1"></i>Import Sekarang
                    </span>
                    <span id="btnLoadingWali" class="d-none">
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
        new bootstrap.Modal(document.getElementById('modalImportWali')).show();
    });
@endif
</script>
@endpush