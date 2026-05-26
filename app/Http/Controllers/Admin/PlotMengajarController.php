<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlotMengajar;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;

class PlotMengajarController extends Controller
{
    public function index()
    {
        // Ambil semua data plotting lengkap dengan relasinya
        $daftarPlot = PlotMengajar::with(['guru', 'kelas', 'mapel'])->latest()->get();
        
        // Data untuk kebutuhan form dropdown awal
        $daftarGuru = Guru::orderBy('nama_guru', 'asc')->get();
        $daftarKelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        $daftarMapel = Mapel::orderBy('nama_mapel', 'asc')->get();

        return view('admin.kelolamapel.plot-index', compact('daftarPlot', 'daftarGuru', 'daftarKelas', 'daftarMapel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_guru'  => 'required|exists:gurus,id_guru',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id_mapel',
        ]);

        // Validasi agar tidak ada duplikasi data penugasan yang sama persis
        $cekDuplikat = PlotMengajar::where('id_guru', $request->id_guru)
                                    ->where('id_kelas', $request->id_kelas)
                                    ->where('id_mapel', $request->id_mapel)
                                    ->exists();

        if ($cekDuplikat) {
            return redirect()->back()->with('error', '❌ Guru tersebut sudah ditugaskan pada kelas dan mapel yang sama!');
        }

        PlotMengajar::create($request->all());

        return redirect()->back()->with('success', '✅ Plotting mengajar guru berhasil ditambahkan!');
    }

    public function destroy($id_plot)
    {
        $plot = PlotMengajar::findOrFail($id_plot);
        $plot->delete();

        return redirect()->back()->with('success', '🗑️ Plotting mengajar guru berhasil dihapus.');
    }

    /**
     * =========================================================================
     * FIX: API FILTER DATA MAPEL BERDASARKAN JURUSAN KELAS YANG DIPILIH
     * =========================================================================
     * Fungsi ini akan merespons request AJAX/Fetch API dari halaman Blade secara real-time
     */
    public function getMapelByKelas(Request $request)
{
    try {
        $id_kelas = $request->id_kelas;
        $kelas = \App\Models\Kelas::find($id_kelas);

        if (!$kelas) {
            return response()->json([]);
        }

        $jurusanKelas = $kelas->jurusan;

        // ✅ BENAR - gunakan where group agar orWhere tidak bocor
        $mapel = \App\Models\Mapel::where(function($query) use ($jurusanKelas) {
                        $query->where('jurusan', 'LIKE', '%' . $jurusanKelas . '%')
                              ->orWhere('jurusan', 'Umum');
                  })
                  ->orderBy('nama_mapel', 'asc')
                  ->get(['id_mapel', 'nama_mapel', 'jurusan']);

        return response()->json($mapel);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}