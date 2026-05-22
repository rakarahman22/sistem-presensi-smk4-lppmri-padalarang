<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Presensi Digital SMK 4 LPPM RI Padalarang">
    <title>Sistem Presensi | SMK 4 LPPM RI Padalarang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --blue-900: #0B2545;
            --blue-800: #153B6E;
            --blue-700: #1A4F8A;
            --blue-600: #2563A8;
            --blue-500: #3B82C4;
            --blue-100: #DBEAFE;
            --blue-50:  #EFF6FF;
            --gold-500: #D4A017;
            --gold-400: #E8B420;
            --gold-100: #FEF9E7;
            --white:    #FFFFFF;
            --gray-50:  #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-400: #94A3B8;
            --gray-600: #475569;
            --gray-800: #1E293B;
            --green:    #16A34A;
            --green-bg: #DCFCE7;
            --red-bg:   #FEE2E2;
            --red:      #DC2626;
            --amber-bg: #FEF3C7;
            --amber:    #D97706;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--white);
            color: var(--gray-800);
        }

        /* ── NAVBAR ── */
        .navbar {
            position: sticky; top: 0; z-index: 999;
            background: rgba(11,37,69,0.97);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .nav-inner {
            max-width: 1140px; margin: 0 auto;
            padding: 0 2rem; height: 68px;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .nav-brand { display: flex; align-items: center; gap: 14px; text-decoration: none; }
        .nav-emblem {
            width: 44px; height: 44px; border-radius: 10px;
            background: var(--gold-400);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .nav-emblem svg { width: 26px; height: 26px; }
        .nav-name { font-size: 13px; font-weight: 700; color: #fff; line-height: 1.3; }
        .nav-sub  { font-size: 11px; color: rgba(255,255,255,0.5); font-weight: 400; }
        .nav-links { display: flex; align-items: center; gap: 2rem; list-style: none; }
        .nav-links a {
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,0.7); text-decoration: none;
            transition: color .2s;
        }
        .nav-links a:hover { color: #fff; }
        .btn-login-nav {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--gold-400); color: var(--blue-900);
            font-family: inherit; font-size: 13px; font-weight: 700;
            padding: 9px 20px; border-radius: 8px; border: none;
            cursor: pointer; text-decoration: none;
            transition: background .2s, transform .15s;
        }
        .btn-login-nav:hover { background: #f0c030; transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(160deg, var(--blue-900) 0%, var(--blue-700) 60%, #1d6fa8 100%);
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(circle at 80% 20%, rgba(212,160,23,0.12) 0%, transparent 50%),
                radial-gradient(circle at 10% 80%, rgba(255,255,255,0.04) 0%, transparent 40%);
        }
        .hero-grid-bg {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .hero-inner {
            position: relative; z-index: 1;
            max-width: 1140px; margin: 0 auto; padding: 6rem 2rem 5rem;
            display: grid; grid-template-columns: 1fr 420px; gap: 4rem; align-items: center;
        }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(212,160,23,0.15); border: 1px solid rgba(212,160,23,0.35);
            color: var(--gold-400); font-size: 12px; font-weight: 600;
            padding: 6px 14px; border-radius: 20px; margin-bottom: 1.5rem;
        }
        .eyebrow-dot {
            width: 7px; height: 7px; background: var(--gold-400); border-radius: 50%;
            animation: blink 2.5s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 48px; font-weight: 800; color: #fff;
            line-height: 1.1; margin-bottom: 1.25rem;
        }
        .hero-title .gold { color: var(--gold-400); }

        .hero-desc {
            font-size: 16px; color: rgba(255,255,255,0.72);
            line-height: 1.8; margin-bottom: 2.5rem; max-width: 480px;
        }

        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 3rem; }

        .btn-cta-primary {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--gold-400); color: var(--blue-900);
            font-family: inherit; font-size: 15px; font-weight: 800;
            padding: 14px 30px; border-radius: 12px; border: none;
            cursor: pointer; text-decoration: none;
            transition: all .2s; box-shadow: 0 4px 20px rgba(212,160,23,0.35);
        }
        .btn-cta-primary:hover { background: #f0c030; transform: translateY(-2px); box-shadow: 0 8px 28px rgba(212,160,23,0.45); }

        .btn-cta-ghost {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.08); color: #fff;
            font-family: inherit; font-size: 15px; font-weight: 600;
            padding: 14px 28px; border-radius: 12px; border: 1.5px solid rgba(255,255,255,0.2);
            cursor: pointer; text-decoration: none;
            transition: all .2s;
        }
        .btn-cta-ghost:hover { background: rgba(255,255,255,0.14); border-color: rgba(255,255,255,0.4); }

        .hero-trust { display: flex; gap: 24px; flex-wrap: wrap; }
        .trust-item { display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.65); font-size: 13px; font-weight: 500; }
        .trust-item i { color: var(--gold-400); font-size: 17px; }

        /* ── PREVIEW CARD ── */
        .preview-card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px; padding: 1.5rem;
            backdrop-filter: blur(20px);
        }
        .pc-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 6px;
        }
        .pc-title { font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.6px; }
        .live-chip {
            display: flex; align-items: center; gap: 5px;
            background: rgba(22,163,74,0.2); color: #4ade80;
            font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
        }
        .live-dot { width: 6px; height: 6px; background: #4ade80; border-radius: 50%; animation: blink 2s infinite; }
        .pc-date { font-size: 11px; color: rgba(255,255,255,0.4); margin-bottom: 12px; font-weight: 500; }

        .att-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .att-row:last-child { border-bottom: none; }
        .att-left { display: flex; align-items: center; gap: 10px; }

        .ava {
            width: 34px; height: 34px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800; flex-shrink: 0;
        }
        .ava-b { background: rgba(59,130,196,0.3); color: #93C5FD; }
        .ava-g { background: rgba(22,163,74,0.3);  color: #86EFAC; }
        .ava-a { background: rgba(212,160,23,0.3); color: #FDE68A; }
        .ava-r { background: rgba(220,38,38,0.3);  color: #FCA5A5; }

        .att-name  { font-size: 13px; font-weight: 600; color: #fff; }
        .att-kelas { font-size: 11px; color: rgba(255,255,255,0.45); }

        .spill {
            font-size: 11px; font-weight: 700; padding: 3px 11px; border-radius: 20px;
        }
        .sp-hadir { background: rgba(22,163,74,0.2);  color: #4ade80; }
        .sp-izin  { background: rgba(212,160,23,0.2); color: #FCD34D; }
        .sp-sakit { background: rgba(59,130,196,0.2); color: #93C5FD; }
        .sp-alfa  { background: rgba(220,38,38,0.2);  color: #FCA5A5; }

        .pc-stats {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-top: 12px;
        }
        .pcs-box {
            background: rgba(255,255,255,0.07);
            border-radius: 10px; padding: 12px 8px; text-align: center;
        }
        .pcs-n { font-size: 20px; font-weight: 800; color: var(--gold-400); display: block; line-height:1; margin-bottom: 3px; }
        .pcs-l { font-size: 11px; color: rgba(255,255,255,0.5); font-weight: 500; }

        /* ── INFOBAR ── */
        .infobar { background: var(--blue-800); border-top: 1px solid rgba(255,255,255,0.06); }
        .infobar-inner {
            max-width: 1140px; margin: 0 auto; padding: 1.75rem 2rem;
            display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap;
        }
        .infobar-addr { font-size: 13px; color: rgba(255,255,255,0.65); font-weight: 500; }
        .infobar-addr strong { color: #fff; display: block; font-size: 14px; margin-bottom: 2px; }
        .infobar-stats { display: flex; align-items: center; gap: 2rem; }
        .ib-stat { text-align: center; }
        .ib-n { font-size: 26px; font-weight: 800; color: var(--gold-400); display: block; line-height:1; }
        .ib-l { font-size: 11px; color: rgba(255,255,255,0.5); font-weight: 500; }
        .ib-div { width: 1px; height: 36px; background: rgba(255,255,255,0.12); }

        /* ── FEATURES ── */
        .section { max-width: 1140px; margin: 0 auto; padding: 5rem 2rem; }
        .sec-bg { background: var(--gray-50); border-top: 1px solid var(--gray-200); border-bottom: 1px solid var(--gray-200); }

        .sec-tag {
            display: inline-block; font-size: 12px; font-weight: 700; color: var(--blue-700);
            text-transform: uppercase; letter-spacing: 1px;
            background: var(--blue-50); padding: 4px 12px; border-radius: 20px;
            border: 1px solid var(--blue-100); margin-bottom: 10px;
        }
        .sec-title {
            font-family: 'Playfair Display', serif;
            font-size: 34px; font-weight: 700; color: var(--blue-900);
            margin-bottom: 10px; line-height: 1.2;
        }
        .sec-desc { font-size: 15px; color: var(--gray-600); line-height: 1.75; max-width: 500px; }

        .feat-grid {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; margin-top: 2.5rem;
        }
        .feat-card {
            background: #fff; border: 1px solid var(--gray-200);
            border-radius: 16px; padding: 1.75rem;
            transition: box-shadow .2s, transform .2s;
        }
        .feat-card:hover { box-shadow: 0 8px 28px rgba(26,79,138,0.1); transform: translateY(-3px); }

        .fi {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 1.25rem;
        }
        .fi-blue  { background: var(--blue-50); color: var(--blue-700); }
        .fi-gold  { background: var(--gold-100); color: #92600a; }
        .fi-green { background: var(--green-bg); color: #15803d; }

        .ft { font-size: 15px; font-weight: 700; color: var(--blue-900); margin-bottom: 7px; }
        .fd { font-size: 13px; color: var(--gray-600); line-height: 1.65; }

        /* ── ROLES / PORTAL ── */
        .roles-grid {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; margin-top: 2.5rem;
        }
        .role-card {
            background: #fff; border: 1.5px solid var(--gray-200);
            border-radius: 18px; padding: 2rem;
            transition: all .25s; text-decoration: none; color: inherit;
            display: flex; flex-direction: column;
        }
        .role-card:hover {
            border-color: var(--blue-600);
            box-shadow: 0 8px 32px rgba(26,79,138,0.13);
            transform: translateY(-3px);
        }
        .ri {
            width: 60px; height: 60px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin-bottom: 1.25rem; flex-shrink: 0;
        }
        .ri-admin { background: var(--blue-50);  color: var(--blue-700); }
        .ri-guru  { background: var(--green-bg); color: #15803d; }
        .ri-siswa { background: var(--gold-100); color: #92600a; }

        .rt { font-size: 17px; font-weight: 800; color: var(--blue-900); margin-bottom: 8px; }
        .rd { font-size: 13px; color: var(--gray-600); line-height: 1.65; margin-bottom: 1.5rem; flex: 1; }

        .role-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--blue-700); color: #fff;
            font-family: inherit; font-size: 13px; font-weight: 700;
            padding: 11px 20px; border-radius: 10px; border: none;
            cursor: pointer; text-decoration: none; width: 100%; justify-content: center;
            transition: background .2s, transform .15s;
        }
        .role-btn:hover { background: var(--blue-600); transform: translateY(-1px); }
        .role-btn.gold { background: var(--gold-500); }
        .role-btn.gold:hover { background: var(--gold-400); }
        .role-btn.green { background: #15803d; }
        .role-btn.green:hover { background: #16a34a; }

        /* ── FOOTER ── */
        .footer { background: var(--blue-900); border-top: 1px solid rgba(255,255,255,0.07); }
        .footer-inner {
            max-width: 1140px; margin: 0 auto; padding: 2rem;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
        }
        .footer-copy { font-size: 13px; color: rgba(255,255,255,0.45); }
        .footer-copy strong { color: rgba(255,255,255,0.75); }
        .tech-pills { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .tp {
            background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.55); font-size: 12px; font-weight: 600;
            padding: 4px 12px; border-radius: 20px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; gap: 2.5rem; padding: 4rem 1.5rem; }
            .hero-title { font-size: 36px; }
            .feat-grid, .roles-grid { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; }
        }
        @media (max-width: 560px) {
            .feat-grid, .roles-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 30px; }
            .hero-cta { flex-direction: column; }
            .btn-cta-primary, .btn-cta-ghost { width: 100%; justify-content: center; }
            .infobar-stats { gap: 1.25rem; }
            .ib-n { font-size: 20px; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<header class="navbar">
    <div class="nav-inner">
        <a href="#" class="nav-brand">
            <div class="nav-emblem">
                <svg viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 2L3 7v6c0 5.55 4.27 10.74 10 12 5.73-1.26 10-6.45 10-12V7L13 2z" fill="#0B2545" stroke="#0B2545" stroke-width="0.5"/>
                    <path d="M8 13h10M13 8v10" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <div class="nav-name">SMK 4 LPPM RI Padalarang</div>
                <div class="nav-sub">Sistem Presensi Digital</div>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="#fitur">Fitur</a></li>
            <li><a href="#portal">Portal</a></li>
            <li><a href="#tentang">Tentang</a></li>
            <li>
                <a href="/login" class="btn-login-nav">
                    <i class="ti ti-login" aria-hidden="true"></i>
                    Masuk
                </a>
            </li>
        </ul>
    </div>
</header>

{{-- HERO --}}
<section class="hero">
    <div class="hero-grid-bg" aria-hidden="true"></div>
    <div class="hero-inner">
        <div>
            <div class="hero-eyebrow">
                <span class="eyebrow-dot" aria-hidden="true"></span>
                Semester Genap &mdash; Tahun Ajaran 2024/2025
            </div>
            <h1 class="hero-title">
                Presensi <span class="gold">Digital</span><br>
                Siswa SMK 4<br>LPPM RI Padalarang
            </h1>
            <p class="hero-desc">
                Platform absensi modern untuk memudahkan guru mencatat kehadiran, 
                wali kelas memantau rekap, dan siswa mengecek riwayat presensi 
                secara real-time dalam satu sistem terpadu.
            </p>
            <div class="hero-cta">
                <a href="/login" class="btn-cta-primary">
                    <i class="ti ti-login" aria-hidden="true"></i>
                    Masuk ke Sistem
                </a>
                <a href="#fitur" class="btn-cta-ghost">
                    <i class="ti ti-info-circle" aria-hidden="true"></i>
                    Lihat Fitur
                </a>
            </div>
            <div class="hero-trust">
                <div class="trust-item"><i class="ti ti-clock-check" aria-hidden="true"></i> Real-time</div>
                <div class="trust-item"><i class="ti ti-device-mobile" aria-hidden="true"></i> Mobile Friendly</div>
                <div class="trust-item"><i class="ti ti-shield-lock" aria-hidden="true"></i> Data Aman</div>
                <div class="trust-item"><i class="ti ti-24-hours" aria-hidden="true"></i> Akses 24 Jam</div>
            </div>
        </div>

        {{-- Preview Card --}}
        <div class="preview-card">
            <div class="pc-header">
                <span class="pc-title">Presensi Hari Ini</span>
                <span class="live-chip">
                    <span class="live-dot" aria-hidden="true"></span>
                    Live
                </span>
            </div>
            <div class="pc-date" id="liveDate">Memuat waktu...</div>

            <div class="att-row">
                <div class="att-left">
                    <div class="ava ava-b">AR</div>
                    <div>
                        <div class="att-name">Aldi Ramadhan</div>
                        <div class="att-kelas">XII RPL 1 &middot; 2024001</div>
                    </div>
                </div>
                <span class="spill sp-hadir">Hadir</span>
            </div>
            <div class="att-row">
                <div class="att-left">
                    <div class="ava ava-g">DN</div>
                    <div>
                        <div class="att-name">Dini Nurhaliza</div>
                        <div class="att-kelas">XII RPL 1 &middot; 2024002</div>
                    </div>
                </div>
                <span class="spill sp-hadir">Hadir</span>
            </div>
            <div class="att-row">
                <div class="att-left">
                    <div class="ava ava-a">FS</div>
                    <div>
                        <div class="att-name">Faisal Santoso</div>
                        <div class="att-kelas">XII TKJ 2 &middot; 2024003</div>
                    </div>
                </div>
                <span class="spill sp-izin">Izin</span>
            </div>
            <div class="att-row">
                <div class="att-left">
                    <div class="ava ava-r">RW</div>
                    <div>
                        <div class="att-name">Rini Wulandari</div>
                        <div class="att-kelas">XII AKL 1 &middot; 2024004</div>
                    </div>
                </div>
                <span class="spill sp-sakit">Sakit</span>
            </div>

            <div class="pc-stats">
                <div class="pcs-box">
                    <span class="pcs-n">94%</span>
                    <span class="pcs-l">Hadir Hari Ini</span>
                </div>
                <div class="pcs-box">
                    <span class="pcs-n">1.247</span>
                    <span class="pcs-l">Total Siswa</span>
                </div>
                <div class="pcs-box">
                    <span class="pcs-n">38</span>
                    <span class="pcs-l">Kelas Aktif</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- INFO BAR --}}
<div class="infobar" id="tentang">
    <div class="infobar-inner">
        <div class="infobar-addr">
            <strong>SMK 4 LPPM RI Padalarang</strong>
            Jl. Raya Padalarang, Kab. Bandung Barat, Jawa Barat 40553
        </div>
        <div class="infobar-stats">
            <div class="ib-stat"><span class="ib-n">12</span><span class="ib-l">Jurusan</span></div>
            <div class="ib-div" aria-hidden="true"></div>
            <div class="ib-stat"><span class="ib-n">86</span><span class="ib-l">Guru &amp; Staff</span></div>
            <div class="ib-div" aria-hidden="true"></div>
            <div class="ib-stat"><span class="ib-n">1.247</span><span class="ib-l">Siswa Aktif</span></div>
            <div class="ib-div" aria-hidden="true"></div>
            <div class="ib-stat"><span class="ib-n">38</span><span class="ib-l">Rombel</span></div>
        </div>
    </div>
</div>

{{-- FITUR --}}
<div class="sec-bg" id="fitur">
    <div class="section">
        <span class="sec-tag">Fitur Unggulan</span>
        <h2 class="sec-title">Semua yang Anda Butuhkan</h2>
        <p class="sec-desc">Dirancang khusus untuk kebutuhan sekolah kejuruan dengan fitur lengkap dan antarmuka yang mudah digunakan siapa saja.</p>

        <div class="feat-grid">
            <div class="feat-card">
                <div class="fi fi-blue"><i class="ti ti-clock-check" aria-hidden="true"></i></div>
                <div class="ft">Presensi Real-time</div>
                <div class="fd">Data kehadiran langsung tercatat saat guru memasukkan absensi per mata pelajaran, tanpa rekap manual.</div>
            </div>
            <div class="feat-card">
                <div class="fi fi-gold"><i class="ti ti-chart-bar" aria-hidden="true"></i></div>
                <div class="ft">Laporan &amp; Statistik</div>
                <div class="fd">Laporan lengkap per siswa, kelas, dan mata pelajaran. Ekspor ke PDF dan Excel dengan satu klik.</div>
            </div>
            <div class="feat-card">
                <div class="fi fi-green"><i class="ti ti-message-circle" aria-hidden="true"></i></div>
                <div class="ft">Notifikasi Otomatis</div>
                <div class="fd">Orang tua mendapat notifikasi jika siswa tidak hadir atau ketidakhadiran melampaui batas yang ditentukan.</div>
            </div>
            <div class="feat-card">
                <div class="fi fi-blue"><i class="ti ti-calendar-event" aria-hidden="true"></i></div>
                <div class="ft">Jadwal Terintegrasi</div>
                <div class="fd">Presensi terintegrasi langsung dengan jadwal pelajaran sehingga absensi per mapel tercatat otomatis.</div>
            </div>
            <div class="feat-card">
                <div class="fi fi-gold"><i class="ti ti-shield-check" aria-hidden="true"></i></div>
                <div class="ft">Keamanan Data</div>
                <div class="fd">Autentikasi berlapis dengan hak akses berbeda per peran: Admin, Guru, dan Siswa.</div>
            </div>
            <div class="feat-card">
                <div class="fi fi-green"><i class="ti ti-device-mobile" aria-hidden="true"></i></div>
                <div class="ft">Responsive &amp; Mobile</div>
                <div class="fd">Dapat diakses dari smartphone, tablet, dan komputer tanpa perlu menginstal aplikasi tambahan.</div>
            </div>
        </div>
    </div>
</div>

{{-- PORTAL / ROLES --}}
<div class="section" id="portal">
    <span class="sec-tag">Portal Masuk</span>
    <h2 class="sec-title">Masuk sesuai Peran Anda</h2>
    <p class="sec-desc">Pilih peran Anda untuk masuk ke sistem. Setiap peran memiliki hak akses dan tampilan yang berbeda.</p>

    <div class="roles-grid">
        <div class="role-card">
            <div class="ri ri-admin"><i class="ti ti-settings-2" aria-hidden="true"></i></div>
            <div class="rt">Admin</div>
            <div class="rd">Kelola data master siswa, guru, kelas, jurusan, jadwal pelajaran, dan seluruh konfigurasi sistem.</div>
            <a href="/login" class="role-btn">
                <i class="ti ti-login" aria-hidden="true"></i>
                Masuk sebagai Admin
            </a>
        </div>
        <div class="role-card">
            <div class="ri ri-guru"><i class="ti ti-chalkboard" aria-hidden="true"></i></div>
            <div class="rt">Guru / Wali Kelas</div>
            <div class="rd">Catat presensi per mata pelajaran, pantau rekap kehadiran siswa, dan kelola surat izin digital.</div>
            <a href="/login" class="role-btn green">
                <i class="ti ti-login" aria-hidden="true"></i>
                Masuk sebagai Guru
            </a>
        </div>
        <div class="role-card">
            <div class="ri ri-siswa"><i class="ti ti-user-check" aria-hidden="true"></i></div>
            <div class="rt">Siswa</div>
            <div class="rd">Lihat rekap kehadiran pribadi, ajukan surat izin secara online, dan pantau persentase kehadiran Anda.</div>
            <a href="/login" class="role-btn gold">
                <i class="ti ti-login" aria-hidden="true"></i>
                Masuk sebagai Siswa
            </a>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-copy">
            &copy; {{ date('Y') }} <strong>SMK 4 LPPM RI Padalarang</strong> &mdash; Hak cipta dilindungi
        </div>
        <div class="tech-pills">
            <span style="font-size:12px;color:rgba(255,255,255,0.35);">Dibangun dengan</span>
            <span class="tp">Laravel 12</span>
            <span class="tp">MySQL</span>
            <span class="tp">Blade</span>
        </div>
    </div>
</footer>

<script>
    // Tampilkan waktu real-time di preview card
    function updateDate() {
        const el = document.getElementById('liveDate');
        if (!el) return;
        const now = new Date();
        const d = now.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
        const t = now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
        el.textContent = d + ' \u00B7 ' + t + ' WIB';
    }
    updateDate();
    setInterval(updateDate, 60000);

    // Smooth scroll anchor
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior:'smooth' }); }
        });
    });
</script>
</body>
</html>