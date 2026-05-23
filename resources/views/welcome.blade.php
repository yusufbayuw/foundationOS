<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FoundationOS') }} | Integrated Education Foundation OS</title>
    <meta name="description" content="FoundationOS adalah Integrated Education Foundation OS untuk yayasan pendidikan multi-unit: tenant SaaS, admin panel, platform console, parent portal, workflow designer, akademik, finance, procurement, Moodle, audit, dan education QA.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&family=fraunces:600,700&display=swap" rel="stylesheet" />

    @php
        $appName = config('app.name', 'FoundationOS');
        $adminUrl = url('/admin');
        $loginUrl = url('/admin/login');
        $registerUrl = url('/admin/register');
        $platformUrl = url('/platform');
        $parentUrl = url('/parent');
    @endphp

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --indigo-50: #eef2ff;
            --indigo-100: #e0e7ff;
            --indigo-200: #c7d2fe;
            --indigo-300: #a5b4fc;
            --indigo-400: #818cf8;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --indigo-700: #4338ca;
            --indigo-800: #3730a3;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-900: #0f172a;
            --amber-400: #fbbf24;
            --emerald-500: #10b981;
            --rose-500: #f43f5e;
            --surface: rgba(255, 255, 255, 0.82);
            --surface-strong: rgba(255, 255, 255, 0.92);
            --border: rgba(99, 102, 241, 0.12);
            --shadow-soft: 0 20px 60px rgba(15, 23, 42, 0.08);
            --shadow-card: 0 18px 45px rgba(79, 70, 229, 0.12);
            --text: var(--slate-900);
            --text-soft: var(--slate-500);
            --max: 1200px;
        }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.18), transparent 32%),
                radial-gradient(circle at top right, rgba(15, 23, 42, 0.1), transparent 28%),
                linear-gradient(180deg, #f9fbff 0%, #f4f7ff 38%, #ffffff 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        a { color: inherit; }

        .shell {
            position: relative;
            isolation: isolate;
        }

        .shell::before,
        .shell::after {
            content: "";
            position: fixed;
            inset: auto;
            z-index: -1;
            border-radius: 999px;
            filter: blur(30px);
            opacity: 0.75;
            pointer-events: none;
        }

        .shell::before {
            top: 7rem;
            left: -7rem;
            width: 22rem;
            height: 22rem;
            background: rgba(99, 102, 241, 0.16);
        }

        .shell::after {
            top: 18rem;
            right: -8rem;
            width: 24rem;
            height: 24rem;
            background: rgba(251, 191, 36, 0.11);
        }

        .container {
            width: min(calc(100% - 2rem), var(--max));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(16px);
            background: rgba(249, 251, 255, 0.72);
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .topbar-inner {
            width: min(calc(100% - 2rem), var(--max));
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 0;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.9rem;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .brand-mark {
            width: 2.8rem;
            height: 2.8rem;
            display: grid;
            place-items: center;
            border-radius: 1rem;
            color: white;
            font-size: 1rem;
            background:
                linear-gradient(145deg, var(--indigo-500), var(--indigo-700));
            box-shadow: 0 14px 26px rgba(79, 70, 229, 0.34);
        }

        .brand-copy {
            display: grid;
            gap: 0.1rem;
        }

        .brand-title {
            font-size: 1rem;
            color: var(--slate-900);
        }

        .brand-subtitle {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-soft);
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .nav a {
            text-decoration: none;
            color: var(--slate-700);
            font-size: 0.93rem;
            font-weight: 700;
            padding: 0.75rem 0.95rem;
            border-radius: 999px;
            transition: 0.2s ease;
        }

        .nav a:hover {
            background: rgba(99, 102, 241, 0.08);
            color: var(--indigo-700);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn,
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.95rem;
            padding: 0.9rem 1.3rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn {
            color: white;
            background: linear-gradient(145deg, var(--indigo-500), var(--indigo-700));
            box-shadow: 0 18px 30px rgba(79, 70, 229, 0.28);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 34px rgba(79, 70, 229, 0.35);
        }

        .btn-ghost {
            color: var(--slate-900);
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: var(--shadow-soft);
        }

        .btn-ghost:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.96);
        }

        .hero {
            padding: 4.5rem 0 2rem;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 2rem;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.1);
            color: var(--indigo-700);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 1.4rem;
        }

        .eyebrow-dot {
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 999px;
            background: linear-gradient(145deg, var(--emerald-500), var(--indigo-500));
            box-shadow: 0 0 0 0.4rem rgba(99, 102, 241, 0.12);
        }

        .hero h1 {
            margin: 0;
            max-width: 11ch;
            font-family: 'Fraunces', serif;
            font-size: clamp(3rem, 6vw, 5.2rem);
            line-height: 0.96;
            letter-spacing: -0.05em;
        }

        .hero h1 .accent {
            color: var(--indigo-700);
        }

        .hero-lead {
            margin: 1.5rem 0 0;
            max-width: 42rem;
            font-size: 1.16rem;
            line-height: 1.9;
            color: var(--slate-700);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            margin-top: 2rem;
        }

        .hero-proof {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .proof {
            padding: 1rem 1.1rem;
            border-radius: 1.25rem;
            background: var(--surface-strong);
            border: 1px solid rgba(148, 163, 184, 0.12);
            box-shadow: var(--shadow-soft);
        }

        .proof strong {
            display: block;
            font-size: 1.35rem;
            line-height: 1;
            color: var(--slate-900);
            margin-bottom: 0.45rem;
        }

        .proof span {
            font-size: 0.92rem;
            color: var(--text-soft);
        }

        .hero-panel {
            position: relative;
            padding: 1.1rem;
            border-radius: 2rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.72));
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: var(--shadow-card);
        }

        .hero-panel::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.24), rgba(251, 191, 36, 0.15));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
                    mask-composite: exclude;
            pointer-events: none;
        }

        .command-bridge {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.95rem 1.1rem;
            border-radius: 1.2rem;
            background: #111827;
            color: rgba(255,255,255,0.88);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .command-bridge span:last-child {
            color: #93c5fd;
            font-weight: 800;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 1rem;
        }

        .panel-stack {
            display: grid;
            gap: 1rem;
        }

        .card {
            background: white;
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 1.5rem;
            padding: 1.15rem;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .card h3,
        .card h4 {
            margin: 0;
        }

        .card h3 {
            font-size: 1rem;
            color: var(--slate-900);
        }

        .card p {
            margin: 0.55rem 0 0;
            color: var(--text-soft);
            font-size: 0.94rem;
            line-height: 1.7;
        }

        .mini-list {
            display: grid;
            gap: 0.7rem;
            margin-top: 1rem;
        }

        .mini-item {
            display: grid;
            gap: 0.18rem;
            padding: 0.85rem 0.95rem;
            border-radius: 1rem;
            background: var(--slate-50);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mini-item strong {
            font-size: 0.9rem;
            color: var(--slate-900);
        }

        .mini-item span {
            font-size: 0.85rem;
            color: var(--text-soft);
        }

        .signal {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.48rem 0.8rem;
            border-radius: 999px;
            font-size: 0.79rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .signal-emerald {
            color: #047857;
            background: rgba(16, 185, 129, 0.12);
        }

        .signal-amber {
            color: #b45309;
            background: rgba(251, 191, 36, 0.16);
        }

        .signal-indigo {
            color: var(--indigo-700);
            background: rgba(99, 102, 241, 0.1);
        }

        .section {
            padding: 2.5rem 0;
        }

        .section-head {
            display: grid;
            gap: 0.8rem;
            max-width: 44rem;
            margin-bottom: 2rem;
        }

        .section-kicker {
            color: var(--indigo-700);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .section h2 {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: clamp(2rem, 4vw, 3.15rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .section p {
            margin: 0;
            font-size: 1.02rem;
            color: var(--slate-700);
            line-height: 1.85;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.2rem;
        }

        .benefit {
            padding: 1.35rem;
            border-radius: 1.6rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.84));
            border: 1px solid rgba(148, 163, 184, 0.14);
            box-shadow: var(--shadow-soft);
        }

        .benefit-icon {
            width: 3rem;
            height: 3rem;
            display: grid;
            place-items: center;
            border-radius: 1rem;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            color: white;
            background: linear-gradient(145deg, var(--indigo-500), var(--indigo-700));
        }

        .benefit h3 {
            margin: 0;
            font-size: 1.05rem;
        }

        .benefit p {
            margin-top: 0.7rem;
            font-size: 0.95rem;
        }

        .journey {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 1.4rem;
            align-items: start;
        }

        .journey-intro,
        .journey-steps {
            background: var(--surface-strong);
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 1.8rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-soft);
        }

        .journey-steps {
            display: grid;
            gap: 0.95rem;
        }

        .step {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: start;
            padding: 1rem;
            border-radius: 1.2rem;
            background: white;
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .step-index {
            width: 2.5rem;
            height: 2.5rem;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--indigo-50);
            color: var(--indigo-700);
            font-weight: 800;
        }

        .step h3 {
            margin: 0;
            font-size: 1rem;
        }

        .step p {
            margin-top: 0.35rem;
            font-size: 0.92rem;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .module-card {
            position: relative;
            min-height: 12rem;
            padding: 1.2rem;
            border-radius: 1.5rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248,250,252,0.92));
            border: 1px solid rgba(148, 163, 184, 0.14);
            box-shadow: var(--shadow-soft);
        }

        .module-card strong {
            display: block;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .module-card span {
            display: block;
            color: var(--text-soft);
            font-size: 0.92rem;
            line-height: 1.75;
        }

        .narrative {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .narrative-card {
            padding: 1.3rem;
            border-radius: 1.5rem;
            background: white;
            border: 1px solid rgba(148, 163, 184, 0.14);
            box-shadow: var(--shadow-soft);
        }

        .quote {
            margin-top: 0.9rem;
            padding: 1.3rem;
            border-radius: 1.5rem;
            background: linear-gradient(145deg, rgba(67, 56, 202, 0.96), rgba(49, 46, 129, 0.95));
            color: rgba(255,255,255,0.9);
            box-shadow: var(--shadow-card);
        }

        .quote p {
            color: inherit;
            font-size: 1rem;
        }

        .quote strong {
            display: block;
            margin-top: 1rem;
            font-size: 0.88rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .cta {
            padding: 3rem 0 5rem;
        }

        .cta-panel {
            position: relative;
            overflow: hidden;
            border-radius: 2rem;
            padding: 2rem;
            background:
                radial-gradient(circle at top left, rgba(129, 140, 248, 0.22), transparent 32%),
                linear-gradient(135deg, #111827, #1e1b4b 58%, #312e81);
            color: white;
            box-shadow: 0 26px 60px rgba(49, 46, 129, 0.28);
        }

        .cta-panel::after {
            content: "";
            position: absolute;
            right: -6rem;
            bottom: -6rem;
            width: 16rem;
            height: 16rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
        }

        .cta-inner {
            position: relative;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 1.5rem;
            align-items: end;
        }

        .cta h2 {
            font-family: 'Fraunces', serif;
            font-size: clamp(2rem, 4vw, 3.3rem);
            line-height: 1.05;
            margin: 0 0 0.9rem;
            letter-spacing: -0.04em;
        }

        .cta p {
            margin: 0;
            color: rgba(255,255,255,0.76);
            font-size: 1.02rem;
            line-height: 1.85;
        }

        .cta-list {
            display: grid;
            gap: 0.8rem;
            margin-top: 1.5rem;
        }

        .cta-list span {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            color: rgba(255,255,255,0.9);
            font-weight: 700;
        }

        .cta-list i {
            width: 1.8rem;
            height: 1.8rem;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            font-style: normal;
        }

        .footer {
            padding: 0 0 2.5rem;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-top: 1.4rem;
            border-top: 1px solid rgba(148, 163, 184, 0.16);
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-links a {
            text-decoration: none;
            color: inherit;
            font-weight: 700;
        }

        @media (max-width: 1080px) {
            .hero-grid,
            .journey,
            .cta-inner {
                grid-template-columns: 1fr;
            }

            .benefits-grid,
            .modules-grid,
            .narrative,
            .hero-proof,
            .panel-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .topbar-inner,
            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav {
                display: none;
            }

            .hero {
                padding-top: 3rem;
            }

            .hero h1 {
                max-width: none;
            }

            .benefits-grid,
            .modules-grid,
            .narrative,
            .hero-proof,
            .panel-grid {
                grid-template-columns: 1fr;
            }

            .command-bridge {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="topbar-inner">
                <a href="#hero" class="brand">
                    <span class="brand-mark">FOS</span>
                    <span class="brand-copy">
                        <span class="brand-title">{{ $appName }}</span>
                        <span class="brand-subtitle">Institution Operating System</span>
                    </span>
                </a>

                <nav class="nav" aria-label="Navigasi utama">
                    <a href="#nilai">Nilai</a>
                    <a href="#fitur">Fitur</a>
                    <a href="#alur">Cara Kerja</a>
                    <a href="#modul">Modul</a>
                    <a href="#hasil">Hasil</a>
                </nav>

                <div class="nav-actions">
                    @if (auth()->check())
                        <a href="{{ $adminUrl }}" class="btn-ghost">Buka Dashboard</a>
                    @else
                        <a href="{{ $loginUrl }}" class="btn-ghost">Masuk Admin</a>
                        <a href="{{ $registerUrl }}" class="btn" style="padding: 0.45rem 1rem; font-size: 0.875rem;">Buat Tenant</a>
                    @endif
                </div>
            </div>
        </header>

        <main>
            <section class="hero" id="hero">
                <div class="container hero-grid">
                    <div>
                        <span class="eyebrow">
                            <span class="eyebrow-dot"></span>
                            Integrated Education Foundation OS
                        </span>
                        <h1>
                            Satu operating system untuk <span class="accent">yayasan, sekolah, kampus, dan orang tua</span>.
                        </h1>
                        <p class="hero-lead">
                            {{ $appName }} sekarang memusatkan tenant SaaS, Admin Panel, Platform Console, Parent Portal, Workflow Designer, akademik sekolah dan kampus, finance, procurement, HR, library, Moodle reconciliation, audit, risk, ISO, dan education QA dalam satu ruang kerja yang lebih mudah dikendalikan.
                        </p>

                        <div class="hero-actions">
                            @if (auth()->check())
                                <a href="{{ $adminUrl }}" class="btn">Masuk ke Dashboard</a>
                            @else
                                <a href="{{ $registerUrl }}" class="btn">Buat Tenant</a>
                                <a href="{{ $loginUrl }}" class="btn-ghost">Masuk Admin</a>
                            @endif
                            <a href="{{ $platformUrl }}" class="btn-ghost">Platform Console</a>
                            <a href="{{ $parentUrl }}" class="btn-ghost">Parent Portal</a>
                        </div>

                        <div class="hero-proof">
                            <div class="proof">
                                <strong>3 panel kerja</strong>
                                <span>Admin Panel untuk tenant, Platform Console untuk operator SaaS, dan Parent Portal untuk wali murid.</span>
                            </div>
                            <div class="proof">
                                <strong>800+ route aktif</strong>
                                <span>Permukaan produk mencakup web, API v1, OPAC, webhook, mobile shell, dan panel Filament.</span>
                            </div>
                            <div class="proof">
                                <strong>40+ modul</strong>
                                <span>Core, School, Campus, Finance, Procurement, Workflow, Library, GRC, dan unit usaha.</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-panel" aria-label="Gambaran manfaat produk">
                        <div class="command-bridge">
                            <span>Current product surface</span>
                            <span>Tenant-aware, modular, auditable</span>
                        </div>

                        <div class="panel-grid">
                            <div class="panel-stack">
                                <div class="card">
                                    <span class="signal signal-emerald">Operasional harian</span>
                                    <h3 style="margin-top: 0.8rem;">Admin Panel yang mengikuti konteks tenant</h3>
                                    <p>Setiap tenant bekerja dalam boundary yang jelas, dengan role, subscription guard, branding, notification, module activation, dan resource discovery dari modul aktif.</p>

                                    <div class="mini-list">
                                        <div class="mini-item">
                                            <strong>Workflow Designer</strong>
                                            <span>Tenant dapat merancang approval, form runtime, parallel gateway, SLA, delegasi, dan histori tugas.</span>
                                        </div>
                                        <div class="mini-item">
                                            <strong>Moodle reconciliation</strong>
                                            <span>Outbox, drift detection, auto-fix mode, bulk enroll, dan laporan operasi menjaga FOS sebagai source of truth.</span>
                                        </div>
                                        <div class="mini-item">
                                            <strong>Risk, audit, ISO, and education QA</strong>
                                            <span>Enterprise risk, internal audit, ISO controls, quality standards, gap analysis, dan improvement plan tersedia sebagai modul kerja.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-stack">
                                <div class="card">
                                    <span class="signal signal-indigo">Untuk pimpinan</span>
                                    <h3 style="margin-top: 0.8rem;">Executive view lintas akademik dan operasional</h3>
                                    <p>Dashboard, chart, weekly summary, finance overview, attendance recap, academic analytics, helpdesk, dan sustainability memberi sinyal yang bisa ditindaklanjuti.</p>
                                </div>

                                <div class="card">
                                    <span class="signal signal-amber">Untuk pertumbuhan</span>
                                    <h3 style="margin-top: 0.8rem;">Platform Console dan module marketplace</h3>
                                    <p>Operator platform dapat memantau tenants, users, module×tenant activation, billing, dan konfigurasi produk tanpa masuk ke data operasional tenant.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section" id="nilai">
                <div class="container">
                    <div class="section-head">
                        <span class="section-kicker">Nilai Produk</span>
                        <h2>Bukan sekadar daftar menu. Ini fondasi kerja multi-tenant untuk yayasan pendidikan yang sudah punya banyak permukaan produk.</h2>
                        <p>{{ $appName }} dirancang untuk pengguna yang setiap hari menghubungkan banyak kepentingan: admin tenant, operator platform, orang tua, manajemen yayasan, sekolah, kampus, keuangan, SDM, audit, dan layanan akademik. Fokusnya bukan memamerkan teknologi, tetapi membuat koordinasi lintas unit terasa lebih jelas dan lebih mudah dijalankan.</p>
                    </div>

                    <div class="benefits-grid">
                        <article class="benefit">
                            <div class="benefit-icon">01</div>
                            <h3>Panel kerja dipisah sesuai tanggung jawab</h3>
                            <p>Tenant mengelola operasi di Admin Panel, operator SaaS menjaga platform di Platform Console, dan wali murid masuk melalui Parent Portal yang lebih fokus.</p>
                        </article>
                        <article class="benefit">
                            <div class="benefit-icon">02</div>
                            <h3>Setiap status lebih mudah dibaca lintas level</h3>
                            <p>Apa yang masih draft, sedang direview, perlu revisi, sudah disetujui, menunggu sinkron Moodle, atau perlu tindak lanjut audit tampil lebih jelas.</p>
                        </article>
                        <article class="benefit">
                            <div class="benefit-icon">03</div>
                            <h3>Standar kerja yayasan lebih konsisten</h3>
                            <p>Workflow, approval, module activation, tenant-aware permissions, billing guard, audit trail, dan localization membantu SOP tetap rapi saat unit bertambah.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section" id="fitur">
                <div class="container">
                    <div class="section-head">
                        <span class="section-kicker">Fitur yang Menonjol</span>
                        <h2>Fitur utama sekarang mengikuti peta produk yang benar-benar sudah hidup di aplikasi.</h2>
                        <p>Setiap kemampuan utama dibangun untuk menjawab situasi kerja nyata di yayasan pendidikan: data tersebar antar unit, approval lambat, sinkronisasi Moodle drift, kontrol subscription, parent communication, audit, dan kebutuhan visibilitas pimpinan yang lebih cepat.</p>
                    </div>

                    <div class="journey">
                        <div class="journey-intro">
                            <span class="signal signal-indigo">Kenapa terasa berbeda</span>
                            <p style="margin-top: 1rem;">Alih-alih menampilkan daftar menu panjang tanpa cerita, {{ $appName }} memusatkan pengalaman pada apa yang ingin dicapai yayasan pendidikan: menjaga tenant tetap aman, sekolah dan kampus selaras, approval terlihat, orang tua terhubung, dan integrasi operasional dapat diaudit.</p>

                            <div class="quote">
                                <p>"Yang terasa premium bukan hanya tampilannya, tetapi rasa tenang saat yayasan tahu apa yang terjadi di setiap unit, apa yang menunggu keputusan, dan apa yang sudah selesai."</p>
                                <strong>Product Narrative</strong>
                            </div>
                        </div>

                        <div class="journey-steps">
                            <article class="step">
                                <div class="step-index">1</div>
                                <div>
                                    <h3>Admin Panel untuk operasi tenant</h3>
                                    <p>Tenant mengelola sekolah, kampus, finance, procurement, HR, library, DMS, helpdesk, risk, audit, dan QA dengan resource Filament yang ditemukan otomatis dari modul aktif.</p>
                                </div>
                            </article>
                            <article class="step">
                                <div class="step-index">2</div>
                                <div>
                                    <h3>Workflow Designer dan approval engine</h3>
                                    <p>Approval tidak lagi tersembunyi di chat. Setiap keputusan punya definisi, aktor, SLA, cabang paralel, form runtime, assignment, history, dan evidence.</p>
                                </div>
                            </article>
                            <article class="step">
                                <div class="step-index">3</div>
                                <div>
                                    <h3>Moodle, API, mobile, dan webhook</h3>
                                    <p>FOS tetap menjadi source of truth melalui outbox Moodle, drift reconciliation, API v1, OpenAPI, mobile shell, WhatsApp webhook, payment webhook, dan developer platform.</p>
                                </div>
                            </article>
                            <article class="step">
                                <div class="step-index">4</div>
                                <div>
                                    <h3>Parent Portal dan public services</h3>
                                    <p>Orang tua melihat anak, nilai, absensi, pengumuman, survey, dan kanal laporan; publik dapat mengakses OPAC, certificate verification, CMS page, dan inquiry.</p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section" id="alur">
                <div class="container">
                    <div class="section-head">
                        <span class="section-kicker">Cara Kerja</span>
                        <h2>Narasi produknya sederhana: satukan yayasan, sekolah, dan kampus dalam ritme kerja yang sama.</h2>
                        <p>Ketika satu yayasan memiliki banyak unit dan banyak proses, masalah utamanya biasanya bukan kekurangan menu. Masalahnya adalah kehilangan ritme kerja antar lembaga. Di situlah {{ $appName }} bekerja.</p>
                    </div>

                    <div class="narrative">
                        <article class="narrative-card">
                            <strong>Masuk</strong>
                            <p>Setiap unit bekerja dari ruang yang sama, dengan akses sesuai peran, struktur organisasi, dan konteks yayasan yang benar.</p>
                        </article>
                        <article class="narrative-card">
                            <strong>Jalankan</strong>
                            <p>Pengguna fokus pada layanan dan proses yang sedang berjalan: input data, cek status, kirim untuk approval, atau tindak lanjuti backlog lintas unit.</p>
                        </article>
                        <article class="narrative-card">
                            <strong>Pantau</strong>
                            <p>Pimpinan yayasan dan admin melihat progres, hambatan, dan hasil tanpa menunggu laporan manual dari sekolah, kampus, dan unit pendukung.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section" id="modul">
                <div class="container">
                    <div class="section-head">
                        <span class="section-kicker">Cakupan Produk</span>
                        <h2>Satu platform untuk proses yayasan yang biasanya tersebar di banyak alat, kini dipetakan sesuai modul yang ada.</h2>
                        <p>{{ $appName }} menonjol bukan karena banyaknya modul semata, tetapi karena modul-modul itu membantu yayasan membangun pengalaman kerja yang menyatu untuk sekolah, kampus, orang tua, unit usaha, dan manajemen pusat.</p>
                    </div>

                    <div class="modules-grid">
                        <article class="module-card">
                            <strong>Core Tenancy & Platform</strong>
                            <span>Tenant, organization, user, role, module marketplace, subscription, billing, branding, locale, importer, dan platform console.</span>
                        </article>
                        <article class="module-card">
                            <strong>School, Campus & Enrollment</strong>
                            <span>Data siswa dan mahasiswa, kelas, kurikulum, assessment, attendance recap, report card, academic analytics, PPDB, dan study plan.</span>
                        </article>
                        <article class="module-card">
                            <strong>Finance & Revenue</strong>
                            <span>Chart of accounts, journal entry, budget, invoice, payment, Midtrans billing, donation, sales, marketplace, property, training, and reports.</span>
                        </article>
                        <article class="module-card">
                            <strong>Workflow & Procurement</strong>
                            <span>Designer, inbox, dynamic form options, PR to RFQ to PO to GR to vendor bill, evidence, webhook subscription, and SLA escalation.</span>
                        </article>
                        <article class="module-card">
                            <strong>Library, DMS & E-Office</strong>
                            <span>OPAC publik, circulation, reservations, book stock, document archive, letters, certificate verification, and file upload audit.</span>
                        </article>
                        <article class="module-card">
                            <strong>People & Campus Operations</strong>
                            <span>Employee, payroll, leave, KPI, transport, boarding, cafeteria, clinic, counseling, event, alumni, facility, asset, and visitor kiosk.</span>
                        </article>
                        <article class="module-card">
                            <strong>GRC & Education QA</strong>
                            <span>Risk, audit, ISO, and education QA; monitoring, compliance logs, school health index, accreditation cycle, and improvement plan.</span>
                        </article>
                        <article class="module-card">
                            <strong>Integrations & Extension</strong>
                            <span>Moodle reconciliation, WhatsApp dispatch, OpenAPI, API v1, Sanctum, mobile shell, queue jobs, scheduled commands, and AI governance.</span>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section" id="hasil">
                <div class="container">
                    <div class="section-head">
                        <span class="section-kicker">Hasil yang Dirasakan</span>
                        <h2>Produk yang baik tidak hanya terlihat meyakinkan. Ia membuat yayasan merasa lebih percaya diri dalam mengelola banyak unit.</h2>
                        <p>Landing page ini memosisikan {{ $appName }} sebagai sistem operasional yayasan pendidikan yang memberi rasa tenang: lebih sedikit kebingungan antar lembaga, lebih sedikit pekerjaan ganda, dan lebih banyak visibilitas terhadap apa yang benar-benar penting.</p>
                    </div>

                    <div class="benefits-grid">
                        <article class="benefit">
                            <div class="benefit-icon">A</div>
                            <h3>Lebih sedikit pekerjaan yang tertunda diam-diam</h3>
                            <p>Karena backlog, approval, dan status utama mudah terlihat oleh sekolah, kampus, maupun manajemen yayasan.</p>
                        </article>
                        <article class="benefit">
                            <div class="benefit-icon">B</div>
                            <h3>Lebih sedikit keputusan yang kabur</h3>
                            <p>Karena setiap tindakan penting punya konteks, pencatatan, dan jalur tindak lanjut yang lebih tertata di seluruh entitas yayasan.</p>
                        </article>
                        <article class="benefit">
                            <div class="benefit-icon">C</div>
                            <h3>Lebih banyak ruang untuk fokus pada layanan pendidikan</h3>
                            <p>Karena sistem mengambil alih keruwetan administratif yang biasanya menguras perhatian tim di sekolah, kampus, dan kantor yayasan.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="cta">
                <div class="container">
                    <div class="cta-panel">
                        <div class="cta-inner">
                            <div>
                                <h2>Bangun yayasan pendidikan yang bekerja lebih rapi tanpa membuat sekolah dan kampus terasa terpecah.</h2>
                                <p>{{ $appName }} dirancang untuk yayasan yang ingin bergerak lebih tertib, lebih cepat, dan lebih percaya diri dalam mengambil keputusan sehari-hari di seluruh unitnya.</p>

                                <div class="cta-list">
                                    <span><i>1</i> Admin Panel, Platform Console, dan Parent Portal dalam satu produk</span>
                                    <span><i>2</i> Workflow, Moodle, finance, procurement, dan GRC yang bisa diaudit</span>
                                    <span><i>3</i> API, mobile shell, webhook, dan module marketplace untuk ekspansi platform</span>
                                </div>
                            </div>

                            <div class="hero-actions" style="justify-content: flex-start;">
                                @if (auth()->check())
                                    <a href="{{ $adminUrl }}" class="btn">Buka Dashboard</a>
                                @else
                                    <a href="{{ $registerUrl }}" class="btn">Buat Tenant</a>
                                    <a href="{{ $loginUrl }}" class="btn-ghost">Sudah punya akun?</a>
                                @endif
                                <a href="{{ $platformUrl }}" class="btn-ghost">Platform Console</a>
                                <a href="#hero" class="btn-ghost">Kembali ke Atas</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="container footer-inner">
                <div>
                    © {{ date('Y') }} {{ $appName }}. Sistem operasional yayasan pendidikan untuk sekolah, kampus, dan manajemen yayasan yang lebih rapi dan terkendali.
                </div>

                <div class="footer-links">
                    <a href="#fitur">Fitur</a>
                    <a href="#modul">Modul</a>
                    <a href="{{ auth()->check() ? $adminUrl : $loginUrl }}">Admin</a>
                    <a href="{{ $platformUrl }}">Platform</a>
                    <a href="{{ $parentUrl }}">Parent</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
