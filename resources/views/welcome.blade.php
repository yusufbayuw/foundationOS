<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FoundationOS') }} — Platform Operasional Institusi Pendidikan</title>
    <meta name="description" content="FoundationOS merupakan platform modular untuk operasional sekolah, kampus, dan lembaga pendidikan. Kelola akademik, keuangan, SDM, serta proses institusi dalam satu sistem yang terintegrasi.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --primary-200: #c7d2fe;
            --primary-300: #a5b4fc;
            --primary-400: #818cf8;
            --primary-500: #6366f1;
            --primary-600: #4f46e5;
            --primary-700: #4338ca;
            --primary-800: #3730a3;
            --primary-900: #312e81;

            --gray-50: #fafafa;
            --gray-100: #f4f4f5;
            --gray-200: #e4e4e7;
            --gray-300: #d4d4d8;
            --gray-400: #a1a1aa;
            --gray-500: #71717a;
            --gray-600: #52525b;
            --gray-700: #3f3f46;
            --gray-800: #27272a;
            --gray-900: #18181b;
            --gray-950: #09090b;

            --bg: #ffffff;
            --bg-alt: var(--gray-50);
            --text: var(--gray-900);
            --text-muted: var(--gray-500);
            --text-heading: var(--gray-950);
            --border: var(--gray-200);
            --card-bg: #ffffff;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --card-shadow-hover: 0 10px 25px rgba(0,0,0,0.08), 0 4px 10px rgba(0,0,0,0.04);
            --gradient-start: var(--primary-500);
            --gradient-end: #312e81;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: var(--gray-950);
                --bg-alt: var(--gray-900);
                --text: var(--gray-300);
                --text-muted: var(--gray-500);
                --text-heading: #ffffff;
                --border: var(--gray-800);
                --card-bg: var(--gray-900);
                --card-shadow: 0 1px 3px rgba(0,0,0,0.3), 0 1px 2px rgba(0,0,0,0.2);
                --card-shadow-hover: 0 10px 25px rgba(0,0,0,0.4), 0 4px 10px rgba(0,0,0,0.2);
            }
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ===== NAV ===== */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: color-mix(in srgb, var(--bg) 80%, transparent);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.875rem 1.5rem;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 0.625rem;
            font-weight: 700; font-size: 1.25rem; color: var(--text-heading);
            text-decoration: none;
        }
        .nav-brand svg { flex-shrink: 0; }
        .nav-links { display: flex; align-items: center; gap: 0.5rem; }
        .nav-link {
            display: inline-flex; align-items: center;
            padding: 0.5rem 1rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 500;
            color: var(--text-muted); text-decoration: none;
            transition: all 0.15s ease;
        }
        .nav-link:hover { color: var(--text-heading); background: var(--bg-alt); }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1.25rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 600;
            color: #fff; text-decoration: none;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            box-shadow: 0 1px 2px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.15);
            transition: all 0.2s ease;
            border: none; cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79,70,229,0.35), inset 0 1px 0 rgba(255,255,255,0.15);
        }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1.25rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 600;
            color: var(--text-heading); text-decoration: none;
            background: var(--card-bg);
            border: 1px solid var(--border);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-secondary:hover {
            border-color: var(--primary-400);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-400) 18%, transparent);
        }

        /* ===== HERO ===== */
        .hero {
            padding: 8rem 1.5rem 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 900px; height: 900px;
            background: radial-gradient(circle, color-mix(in srgb, var(--primary-400) 12%, transparent) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-content { max-width: 800px; margin: 0 auto; position: relative; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.375rem 1rem; border-radius: 99px;
            font-size: 0.8125rem; font-weight: 500;
            color: var(--primary-700);
            background: var(--primary-50);
            border: 1px solid var(--primary-200);
            margin-bottom: 1.5rem;
        }
        @media (prefers-color-scheme: dark) {
            .hero-badge {
                color: var(--primary-300);
                background: color-mix(in srgb, var(--primary-900) 40%, transparent);
                border-color: color-mix(in srgb, var(--primary-700) 40%, transparent);
            }
        }
        .hero-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--primary-500);
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }
        .hero h1 {
            font-size: clamp(2.25rem, 5vw, 3.75rem);
            font-weight: 800; line-height: 1.1;
            color: var(--text-heading);
            margin-bottom: 1.25rem;
            letter-spacing: -0.025em;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.125rem; color: var(--text-muted);
            max-width: 600px; margin: 0 auto 2rem;
            line-height: 1.7;
        }
        .hero-actions {
            display: flex; gap: 0.75rem;
            justify-content: center; flex-wrap: wrap;
        }
        .hero-visual {
            max-width: 1000px; margin: 3rem auto 0;
            position: relative; border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
        }
        .hero-visual-mockup {
            background: var(--card-bg);
            padding: 1rem;
            min-height: 400px;
            display: flex; flex-direction: column;
        }
        .mockup-topbar {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: var(--bg-alt);
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-dot-red { background: #ef4444; }
        .mockup-dot-yellow { background: #eab308; }
        .mockup-dot-green { background: #22c55e; }
        .mockup-url {
            flex: 1; margin-left: 0.75rem;
            padding: 0.375rem 0.75rem;
            background: var(--card-bg);
            border-radius: 0.375rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .mockup-body { display: flex; flex: 1; gap: 0.75rem; }
        .mockup-sidebar {
            width: 200px; flex-shrink: 0;
            background: var(--bg-alt);
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .mockup-sidebar-item {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 0.625rem; border-radius: 0.375rem;
            font-size: 0.75rem; color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        .mockup-sidebar-item.active {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: #fff;
        }
        .mockup-sidebar-icon {
            width: 16px; height: 16px;
            border-radius: 3px;
            background: currentColor;
            opacity: 0.3;
        }
        .mockup-sidebar-item.active .mockup-sidebar-icon { opacity: 0.5; }
        .mockup-main {
            flex: 1;
            background: var(--bg-alt);
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .mockup-card-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .mockup-stat-card {
            background: var(--card-bg);
            border-radius: 0.375rem;
            padding: 0.75rem;
            border: 1px solid var(--border);
        }
        .mockup-stat-label { font-size: 0.625rem; color: var(--text-muted); margin-bottom: 0.25rem; }
        .mockup-stat-value { font-size: 1.125rem; font-weight: 700; color: var(--text-heading); }
        .mockup-table {
            background: var(--card-bg);
            border-radius: 0.375rem;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .mockup-table-header {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
            padding: 0.5rem 0.75rem;
            background: var(--bg-alt);
            border-bottom: 1px solid var(--border);
        }
        .mockup-th { font-size: 0.625rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .mockup-table-row {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid var(--border);
        }
        .mockup-table-row:last-child { border-bottom: none; }
        .mockup-td { font-size: 0.6875rem; color: var(--text); }
        .mockup-badge {
            display: inline-block;
            padding: 0.125rem 0.5rem;
            border-radius: 99px;
            font-size: 0.5625rem;
            font-weight: 600;
        }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        @media (prefers-color-scheme: dark) {
            .badge-green { background: #052e16; color: #86efac; }
            .badge-amber { background: #451a03; color: #fcd34d; }
            .badge-blue { background: #172554; color: #93c5fd; }
        }

        @media (max-width: 768px) {
            .mockup-sidebar { display: none; }
            .mockup-card-grid { grid-template-columns: 1fr; }
            .mockup-table-header, .mockup-table-row { grid-template-columns: 1fr 1fr; }
        }

        /* ===== SECTIONS GENERIC ===== */
        .section {
            padding: 5rem 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-alt { background: var(--bg-alt); }
        .section-alt .section { padding-left: 1.5rem; padding-right: 1.5rem; }
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 3rem;
        }
        .section-label {
            display: inline-block;
            font-size: 0.8125rem; font-weight: 600;
            color: var(--primary-600);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }
        @media (prefers-color-scheme: dark) {
            .section-label { color: var(--primary-400); }
        }
        .section-header h2 {
            font-size: clamp(1.75rem, 3.5vw, 2.5rem);
            font-weight: 800; line-height: 1.15;
            color: var(--text-heading);
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
        }
        .section-header p {
            font-size: 1.0625rem; color: var(--text-muted);
            line-height: 1.7;
        }

        /* ===== MODULES GRID ===== */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.25rem;
        }
        .module-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .module-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .module-card:hover {
            border-color: var(--primary-300);
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }
        .module-card:hover::before { opacity: 1; }
        .module-icon {
            width: 40px; height: 40px;
            border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            background: var(--primary-50);
            color: var(--primary-600);
        }
        @media (prefers-color-scheme: dark) {
            .module-icon {
                background: color-mix(in srgb, var(--primary-900) 40%, transparent);
                color: var(--primary-400);
            }
        }
        .module-card h3 {
            font-size: 1rem; font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 0.5rem;
        }
        .module-card p {
            font-size: 0.875rem; color: var(--text-muted);
            line-height: 1.6;
        }

        /* ===== FEATURES ===== */
        .features-list {
            display: grid; gap: 4rem;
        }
        .feature-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }
        .feature-row.reverse { direction: rtl; }
        .feature-row.reverse > * { direction: ltr; }
        .feature-content { }
        .feature-content h3 {
            font-size: 1.5rem; font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 0.75rem;
            letter-spacing: -0.01em;
        }
        .feature-content p {
            font-size: 0.9375rem; color: var(--text-muted);
            line-height: 1.7; margin-bottom: 1.25rem;
        }
        .feature-checks { list-style: none; padding: 0; }
        .feature-checks li {
            display: flex; align-items: flex-start; gap: 0.625rem;
            font-size: 0.875rem; color: var(--text);
            margin-bottom: 0.625rem;
        }
        .feature-check-icon {
            width: 20px; height: 20px; flex-shrink: 0;
            border-radius: 50%;
            background: var(--primary-100);
            color: var(--primary-600);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.6875rem; font-weight: 700;
            margin-top: 1px;
        }
        @media (prefers-color-scheme: dark) {
            .feature-check-icon {
                background: color-mix(in srgb, var(--primary-800) 50%, transparent);
                color: var(--primary-400);
            }
        }
        .feature-visual {
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 2rem;
            display: flex; align-items: center; justify-content: center;
            min-height: 280px;
        }
        .feature-visual-content { text-align: center; }
        .feature-visual-icon {
            font-size: 3rem; margin-bottom: 1rem;
            opacity: 0.8;
        }
        .feature-visual-label {
            font-size: 0.8125rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .feature-row, .feature-row.reverse { grid-template-columns: 1fr; }
            .feature-row.reverse { direction: ltr; }
        }

        /* ===== PRICING ===== */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .pricing-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 2rem;
            position: relative;
            transition: all 0.25s ease;
        }
        .pricing-card.featured {
            border-color: var(--primary-400);
            box-shadow: 0 0 0 1px var(--primary-400), 0 10px 25px rgba(79,70,229,0.12);
        }
        .pricing-card.featured::before {
            content: 'Paling Populer';
            position: absolute; top: -0.75rem; left: 50%; transform: translateX(-50%);
            padding: 0.25rem 1rem; border-radius: 99px;
            font-size: 0.75rem; font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }
        .pricing-name {
            font-size: 1.125rem; font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 0.5rem;
        }
        .pricing-desc {
            font-size: 0.875rem; color: var(--text-muted);
            margin-bottom: 1.5rem; line-height: 1.5;
        }
        .pricing-price {
            font-size: 2.5rem; font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 0.25rem;
            line-height: 1;
        }
        .pricing-price span {
            font-size: 0.875rem; font-weight: 500;
            color: var(--text-muted);
        }
        .pricing-period {
            font-size: 0.8125rem; color: var(--text-muted);
            margin-bottom: 1.5rem;
        }
        .pricing-features {
            list-style: none; padding: 0;
            margin-bottom: 1.5rem;
        }
        .pricing-features li {
            display: flex; align-items: flex-start; gap: 0.5rem;
            font-size: 0.875rem; color: var(--text);
            padding: 0.375rem 0;
        }
        .pricing-features li::before {
            content: '✓';
            color: var(--primary-500);
            font-weight: 700;
            flex-shrink: 0;
        }
        .pricing-card .btn-primary,
        .pricing-card .btn-secondary {
            width: 100%;
            justify-content: center;
            padding: 0.625rem 1.25rem;
        }

        /* ===== STATS ===== */
        .stats-section {
            background: linear-gradient(135deg, var(--gray-900), var(--gray-950));
            color: #fff;
            padding: 4rem 1.5rem;
        }
        @media (prefers-color-scheme: dark) {
            .stats-section {
                background: linear-gradient(135deg, var(--gray-800), var(--gray-900));
            }
        }
        .stats-grid {
            max-width: 1000px; margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem; font-weight: 800;
            background: linear-gradient(135deg, var(--primary-300), var(--primary-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.25rem;
        }
        .stat-label {
            font-size: 0.875rem;
            color: var(--gray-400);
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* ===== TESTIMONIAL ===== */
        .testimonial-card {
            max-width: 700px; margin: 0 auto;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 2.5rem;
            text-align: center;
            position: relative;
        }
        .testimonial-quote {
            font-size: 1.125rem; color: var(--text);
            line-height: 1.7; font-style: italic;
            margin-bottom: 1.5rem;
        }
        .testimonial-quote::before { content: '"'; font-size: 3rem; color: var(--primary-400); line-height: 0; vertical-align: -0.75rem; margin-right: 0.25rem; }
        .testimonial-quote::after { content: '"'; font-size: 3rem; color: var(--primary-400); line-height: 0; vertical-align: -0.75rem; margin-left: 0.25rem; }
        .testimonial-author { font-size: 0.9375rem; font-weight: 700; color: var(--text-heading); }
        .testimonial-role { font-size: 0.8125rem; color: var(--text-muted); }

        /* ===== CTA ===== */
        .cta-section {
            padding: 5rem 1.5rem;
            text-align: center;
        }
        .cta-box {
            max-width: 700px; margin: 0 auto;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-900));
            border-radius: 1rem;
            padding: 3.5rem 2rem;
            position: relative;
            overflow: hidden;
        }
        .cta-box::before {
            content: '';
            position: absolute; top: -50%; right: -20%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-box h2 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800; color: #fff;
            margin-bottom: 0.75rem;
            position: relative;
        }
        .cta-box p {
            font-size: 1rem;
            color: rgba(255,255,255,0.85);
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto; margin-right: auto;
            position: relative;
        }
        .btn-white {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.625rem 1.75rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 700;
            color: var(--primary-700); text-decoration: none;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
            border: none; cursor: pointer;
            position: relative;
        }
        .btn-white:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* ===== FOOTER ===== */
        .footer {
            border-top: 1px solid var(--border);
            padding: 2.5rem 1.5rem;
        }
        .footer-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
        }
        .footer-copy {
            font-size: 0.8125rem; color: var(--text-muted);
        }
        .footer-links {
            display: flex; gap: 1.5rem;
        }
        .footer-links a {
            font-size: 0.8125rem; color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s;
        }
        .footer-links a:hover { color: var(--text-heading); }

        /* ===== ANIMATIONS ===== */
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fade-up 0.6s ease forwards;
        }
        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
        .delay-4 { animation-delay: 0.4s; opacity: 0; }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gray-400); }
        @media (prefers-color-scheme: dark) {
            ::-webkit-scrollbar-thumb { background: var(--gray-700); }
            ::-webkit-scrollbar-thumb:hover { background: var(--gray-600); }
        }
    </style>
</head>
<body>

    <!-- NAV -->
    <nav class="nav" id="navbar">
        <div class="nav-inner">
            <a href="/" class="nav-brand">
                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="url(#brand-grad)"/>
                    <path d="M8 12h16v2H8zm0 4h12v2H8zm0 4h14v2H8z" fill="#fff" opacity="0.9"/>
                    <defs><linearGradient id="brand-grad" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#312e81"/></linearGradient></defs>
                </svg>
                {{ config('app.name', 'FoundationOS') }}
            </a>
            <div class="nav-links">
                @auth
                    <a href="{{ url('/admin') }}" class="btn-primary">Dashboard</a>
                @else
                    <a href="#modules" class="nav-link">Modul</a>
                    <a href="#features" class="nav-link">Fitur</a>
                    <a href="#pricing" class="nav-link">Harga</a>
                    <a href="{{ url('/admin/login') }}" class="nav-link">Masuk</a>
                    <a href="{{ url('/admin/login') }}" class="btn-primary">Masuk ke Platform</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero">
        <div class="hero-content">
            <div class="hero-badge animate-fade-up">
                <span class="hero-badge-dot"></span>
                Platform Operasional Pendidikan Terintegrasi
            </div>
            <h1 class="animate-fade-up delay-1">
                Satukan Operasional Institusi dalam <span class="gradient-text">Satu Platform</span>
            </h1>
            <p class="animate-fade-up delay-2">
                FoundationOS membantu sekolah, kampus, dan lembaga pendidikan mengelola proses akademik, keuangan, SDM, serta layanan operasional melalui arsitektur modular yang siap bertumbuh bersama institusi Anda.
            </p>
            <div class="hero-actions animate-fade-up delay-3">
                <a href="{{ url('/admin/login') }}" class="btn-primary" style="padding: 0.75rem 2rem; font-size: 0.9375rem;">
                    Masuk ke Platform
                </a>
                <a href="#modules" class="btn-secondary" style="padding: 0.75rem 2rem; font-size: 0.9375rem;">
                    Tinjau Kapabilitas
                </a>
            </div>
        </div>

        <!-- Dashboard Mockup -->
        <div class="hero-visual animate-fade-up delay-4">
            <div class="hero-visual-mockup">
                <div class="mockup-topbar">
                    <span class="mockup-dot mockup-dot-red"></span>
                    <span class="mockup-dot mockup-dot-yellow"></span>
                    <span class="mockup-dot mockup-dot-green"></span>
                    <span class="mockup-url">{{ config('app.url', 'https://foundationos.test') }}/admin</span>
                </div>
                <div class="mockup-body">
                    <div class="mockup-sidebar">
                        <div class="mockup-sidebar-item active"><span class="mockup-sidebar-icon"></span> Dashboard</div>
                        <div class="mockup-sidebar-item"><span class="mockup-sidebar-icon"></span> Siswa</div>
                        <div class="mockup-sidebar-item"><span class="mockup-sidebar-icon"></span> Pendidik & Tenaga Kependidikan</div>
                        <div class="mockup-sidebar-item"><span class="mockup-sidebar-icon"></span> Keuangan</div>
                        <div class="mockup-sidebar-item"><span class="mockup-sidebar-icon"></span> Kurikulum</div>
                        <div class="mockup-sidebar-item"><span class="mockup-sidebar-icon"></span> Perpustakaan</div>
                        <div class="mockup-sidebar-item"><span class="mockup-sidebar-icon"></span> Penerimaan</div>
                        <div class="mockup-sidebar-item"><span class="mockup-sidebar-icon"></span> Pengadaan</div>
                    </div>
                    <div class="mockup-main">
                        <div class="mockup-card-grid">
                            <div class="mockup-stat-card">
                                <div class="mockup-stat-label">Total Siswa</div>
                                <div class="mockup-stat-value">1,247</div>
                            </div>
                            <div class="mockup-stat-card">
                                <div class="mockup-stat-label">Guru Aktif</div>
                                <div class="mockup-stat-value">86</div>
                            </div>
                            <div class="mockup-stat-card">
                                <div class="mockup-stat-label">Penerimaan Bulan Berjalan</div>
                                <div class="mockup-stat-value">Rp 432jt</div>
                            </div>
                        </div>
                        <div class="mockup-table">
                            <div class="mockup-table-header">
                                <span class="mockup-th">Nama</span>
                                <span class="mockup-th">Kelas</span>
                                <span class="mockup-th">Status</span>
                                <span class="mockup-th">Pembayaran</span>
                            </div>
                            <div class="mockup-table-row">
                                <span class="mockup-td">Ahmad Fauzi</span>
                                <span class="mockup-td">XII IPA 1</span>
                                <span class="mockup-td"><span class="mockup-badge badge-green">Aktif</span></span>
                                <span class="mockup-td"><span class="mockup-badge badge-green">Lunas</span></span>
                            </div>
                            <div class="mockup-table-row">
                                <span class="mockup-td">Siti Nurhaliza</span>
                                <span class="mockup-td">XI IPS 2</span>
                                <span class="mockup-td"><span class="mockup-badge badge-green">Aktif</span></span>
                                <span class="mockup-td"><span class="mockup-badge badge-amber">Cicilan</span></span>
                            </div>
                            <div class="mockup-table-row">
                                <span class="mockup-td">Budi Santoso</span>
                                <span class="mockup-td">X IPA 3</span>
                                <span class="mockup-td"><span class="mockup-badge badge-blue">Baru</span></span>
                                <span class="mockup-td"><span class="mockup-badge badge-amber">Pending</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <div class="stats-section">
        <div class="stats-grid">
            <div>
                <div class="stat-number">10+</div>
                <div class="stat-label">Modul Terintegrasi</div>
            </div>
            <div>
                <div class="stat-number">∞</div>
                <div class="stat-label">Arsitektur Multi-Tenant</div>
            </div>
            <div>
                <div class="stat-number">100%</div>
                <div class="stat-label">Modular dan Fleksibel</div>
            </div>
            <div>
                <div class="stat-number">24/7</div>
                <div class="stat-label">Akses Layanan</div>
            </div>
        </div>
    </div>

    <!-- MODULES -->
    <section class="section" id="modules">
        <div class="section-header">
            <span class="section-label">Modul</span>
            <h2>Kapabilitas Inti dalam Satu Ekosistem</h2>
            <p>Aktifkan modul sesuai prioritas institusi Anda. Setiap modul dirancang untuk berjalan mandiri, sekaligus terhubung secara konsisten dalam satu fondasi data.</p>
        </div>
        <div class="modules-grid">
            <div class="module-card">
                <div class="module-icon">🎓</div>
                <h3>Akademik Sekolah</h3>
                <p>Mengelola peserta didik, tenaga pendidik, kelas, kurikulum, jadwal, penilaian, pembinaan, dan capaian akademik dalam satu alur kerja terpadu.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">🏛️</div>
                <h3>Akademik Kampus</h3>
                <p>Mendukung pengelolaan fakultas, program studi, dosen, mahasiswa, penawaran mata kuliah, KRS, hasil studi, tesis, hingga integrasi Feeder DIKTI.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">📋</div>
                <h3>Penerimaan Peserta Didik</h3>
                <p>Mengelola periode penerimaan, pendaftaran daring, penjadwalan seleksi, hasil evaluasi, dan proses registrasi secara terstruktur.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">💰</div>
                <h3>Keuangan</h3>
                <p>Mencakup bagan akun, komponen biaya pendidikan, tagihan, pembayaran, jurnal akuntansi, serta pengendalian anggaran institusi.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">👥</div>
                <h3>Kepegawaian</h3>
                <p>Mengelola data pegawai, struktur jabatan, kontrak kerja, absensi, cuti, penggajian, dan evaluasi kinerja secara terpusat.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">📚</div>
                <h3>Perpustakaan</h3>
                <p>Menyediakan katalog dan eksemplar koleksi, manajemen anggota, sirkulasi pinjam-kembali, serta perhitungan denda otomatis.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">📦</div>
                <h3>Pengadaan</h3>
                <p>Mengatur vendor, kebutuhan pengadaan, permintaan penawaran, purchase order, penerimaan barang, dan pencatatan tagihan pemasok.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">📊</div>
                <h3>Monitoring</h3>
                <p>Menyediakan audit trail untuk setiap perubahan data serta pengelolaan dokumen pendukung yang digunakan lintas fungsi.</p>
            </div>
            <div class="module-card">
                <div class="module-icon">🌐</div>
                <h3>Referensi Global</h3>
                <p>Menyediakan data referensi wilayah, zona waktu, dan master data bersama agar seluruh modul bekerja dengan standar yang seragam.</p>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <div class="section-alt">
        <section class="section" id="features">
            <div class="section-header">
                <span class="section-label">Keunggulan</span>
                <h2>Dirancang untuk Skalabilitas dan Tata Kelola</h2>
                <p>Fondasi teknologi modern untuk mendukung pertumbuhan, kontrol, dan konsistensi proses di seluruh unit institusi.</p>
            </div>
            <div class="features-list">
                <div class="feature-row">
                    <div class="feature-content">
                        <h3>Multi-Tenancy Bawaan</h3>
                        <p>Satu basis aplikasi dapat melayani banyak institusi sekaligus dengan pemisahan data yang ketat dan tata kelola akses yang jelas.</p>
                        <ul class="feature-checks">
                            <li><span class="feature-check-icon">✓</span> Shared database dengan tenant-scoped query</li>
                            <li><span class="feature-check-icon">✓</span> Isolasi data per tenant dan organisasi</li>
                            <li><span class="feature-check-icon">✓</span> Satu pengguna dapat mengakses beberapa tenant</li>
                            <li><span class="feature-check-icon">✓</span> Role dan permission sesuai konteks tenant</li>
                        </ul>
                    </div>
                    <div class="feature-visual">
                        <div class="feature-visual-content">
                            <div class="feature-visual-icon">🏢</div>
                            <div class="feature-visual-label">Multi-Tenant Architecture</div>
                        </div>
                    </div>
                </div>
                <div class="feature-row reverse">
                    <div class="feature-content">
                        <h3>Admin Panel Premium</h3>
                        <p>Dibangun di atas Filament untuk menghadirkan admin panel yang responsif, efisien, dan nyaman digunakan oleh tim operasional.</p>
                        <ul class="feature-checks">
                            <li><span class="feature-check-icon">✓</span> Dashboard interaktif dengan widget dan grafik</li>
                            <li><span class="feature-check-icon">✓</span> CRUD otomatis melalui form builder</li>
                            <li><span class="feature-check-icon">✓</span> Tabel data dengan filter, sortir, dan bulk action</li>
                            <li><span class="feature-check-icon">✓</span> Notifikasi real-time dan action modal</li>
                        </ul>
                    </div>
                    <div class="feature-visual">
                        <div class="feature-visual-content">
                            <div class="feature-visual-icon">⚡</div>
                            <div class="feature-visual-label">Powered by Filament v5</div>
                        </div>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-content">
                        <h3>Keamanan & Kontrol Akses</h3>
                        <p>Kontrol akses granular terintegrasi dengan batas tenant sehingga setiap aksi dapat dikelola secara presisi dan dapat diaudit.</p>
                        <ul class="feature-checks">
                            <li><span class="feature-check-icon">✓</span> Filament Shield untuk pengelolaan permission</li>
                            <li><span class="feature-check-icon">✓</span> Super Admin, Admin, dan role kustom</li>
                            <li><span class="feature-check-icon">✓</span> Policy otomatis pada setiap resource</li>
                            <li><span class="feature-check-icon">✓</span> Audit trail pada setiap perubahan data</li>
                        </ul>
                    </div>
                    <div class="feature-visual">
                        <div class="feature-visual-content">
                            <div class="feature-visual-icon">🔐</div>
                            <div class="feature-visual-label">Enterprise-Grade Security</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- PRICING -->
    <section class="section" id="pricing">
        <div class="section-header">
            <span class="section-label">Harga</span>
            <h2>Pilih Paket Sesuai Tahap Pertumbuhan Institusi</h2>
            <p>Mulai dari tahap implementasi awal hingga kebutuhan operasional skala penuh, tanpa migrasi platform.</p>
        </div>
        <div class="pricing-grid">
            <div class="pricing-card">
                <div class="pricing-name">Starter</div>
                <div class="pricing-desc">Untuk institusi yang memulai standardisasi proses digital secara bertahap.</div>
                <div class="pricing-price">Gratis</div>
                <div class="pricing-period">Untuk 1 tenant tanpa batas waktu</div>
                <ul class="pricing-features">
                    <li>Hingga 100 siswa/mahasiswa</li>
                    <li>3 modul inti</li>
                    <li>1 pengguna admin</li>
                    <li>Dukungan komunitas</li>
                </ul>
                <a href="{{ url('/admin/login') }}" class="btn-secondary">Akses Paket Starter</a>
            </div>
            <div class="pricing-card featured">
                <div class="pricing-name">Professional</div>
                <div class="pricing-desc">Untuk sekolah dan kampus yang memerlukan cakupan modul lengkap dan dukungan operasional prioritas.</div>
                <div class="pricing-price">Rp 2.5jt <span>/ bulan</span></div>
                <div class="pricing-period">Per tenant, ditagihkan tahunan</div>
                <ul class="pricing-features">
                    <li>Siswa/mahasiswa tanpa batas</li>
                    <li>Seluruh modul aktif</li>
                    <li>Pengguna admin tanpa batas</li>
                    <li>Mendukung multi-organisasi</li>
                    <li>Dukungan prioritas</li>
                    <li>Integrasi Feeder DIKTI</li>
                </ul>
                <a href="{{ url('/admin/login') }}" class="btn-primary">Pilih Paket Professional</a>
            </div>
        </div>
    </section>

    <!-- TESTIMONIAL -->
    <div class="section-alt">
        <section class="section">
            <div class="section-header">
                <span class="section-label">Testimoni</span>
                <h2>Dipercaya oleh Pimpinan Institusi Pendidikan</h2>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-quote">
                    FoundationOS membantu kami menata ulang proses operasional sekolah secara lebih tertib. Administrasi peserta didik, keuangan, dan pengelolaan data kini berjalan dalam satu platform yang konsisten dan mudah diawasi.
                </p>
                <div class="testimonial-author">Dr. Rina Handayani, M.Pd.</div>
                <div class="testimonial-role">Kepala Sekolah — SMA Unggulan Nusantara</div>
            </div>
        </section>
    </div>

    <!-- CTA -->
    <section class="cta-section">
        <div class="cta-box">
            <h2>Siap Memodernisasi Operasional Institusi Anda?</h2>
            <p>Akses FoundationOS untuk menstandarkan proses, meningkatkan visibilitas data, dan memperkuat tata kelola institusi Anda.</p>
            <a href="{{ url('/admin/login') }}" class="btn-white">Masuk ke Platform</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-copy">
                © {{ date('Y') }} {{ config('app.name', 'FoundationOS') }}. Dibangun dengan Laravel &amp; Filament untuk operasional pendidikan modern.
            </div>
            <div class="footer-links">
                <a href="#modules">Modul</a>
                <a href="#features">Fitur</a>
                <a href="#pricing">Harga</a>
                <a href="{{ url('/admin/login') }}">Login</a>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Navbar background on scroll
        const nav = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                nav.style.borderBottomColor = 'var(--border)';
            } else {
                nav.style.borderBottomColor = 'transparent';
            }
        });
    </script>
</body>
</html>
