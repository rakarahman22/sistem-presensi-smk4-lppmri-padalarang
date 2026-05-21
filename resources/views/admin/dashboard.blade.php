@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success m-0" style="color: #14532d !important;">Dashboard Admin</h3>
        <span class="fw-medium text-secondary small">
            👋 Halo, Admin <strong class="text-dark">{{ Auth::guard('admin')->user()->nama_admin ?? 'Raka' }}</strong>
        </span>
    </div>

    <!-- Statistik Kartu -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card total-siswa p-3">
                <div class="stat-label">Total Siswa</div>
                <div class="stat-value">325</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card siswa-hadir p-3">
                <div class="stat-label">Siswa Hadir</div>
                <div class="stat-value">280</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card guru-aktif p-3">
                <div class="stat-label">Guru Aktif</div>
                <div class="stat-value">28</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card alpa-hari-ini p-3">
                <div class="stat-label">Alpa Hari Ini</div>
                <div class="stat-value">45</div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card chart-card">
                <div style="height: 380px; position: relative;">
                    <canvas id="absensiChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const ctx = document.getElementById('absensiChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                datasets: [
                    {
                        label: 'Hadir',
                        data: [280, 290, 275, 300, 295],
                        backgroundColor: '#15803d',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 45
                    },
                    {
                        label: 'Alpha',
                        data: [15, 10, 20, 8, 12],
                        backgroundColor: '#dc2626',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 45
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Inter', size: 12, weight: '500' },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 12 } }
                    },
                    y: {
                        min: 0,
                        max: 300,
                        ticks: {
                            stepSize: 50,
                            font: { family: 'Inter', size: 12 }
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    </script>
@endpush