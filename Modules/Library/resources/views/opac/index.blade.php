<x-library::layouts.master :title="($organization->name ?? $tenant->name) . ' Library Portal'">
    <style>
        :root {
            --opac-primary: {{ $tenant->primary_color ?: '#4338ca' }};
            --opac-paper: rgba(255, 255, 255, 0.88);
            --opac-line: rgba(99, 102, 241, 0.18);
        }
        .opac-shell {
            max-width: 1240px;
            margin: 0 auto;
            padding: 2rem 1.25rem 4rem;
            color: #0f172a;
        }
        .opac-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .opac-brand {
            display: flex;
            align-items: center;
            gap: .9rem;
        }
        .opac-brand-mark {
            width: 50px;
            height: 50px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--opac-primary), #7c3aed);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            box-shadow: 0 16px 34px rgba(67, 56, 202, 0.28);
        }
        .opac-brand-logo {
            width: 50px;
            height: 50px;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid rgba(99, 102, 241, 0.14);
            background: #fff;
            box-shadow: 0 16px 34px rgba(67, 56, 202, 0.16);
        }
        .opac-brand-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
        }
        .opac-brand-subtitle {
            color: #475569;
            font-size: .92rem;
        }
        .opac-link {
            color: var(--opac-primary);
            text-decoration: none;
            font-weight: 600;
        }
        .opac-hero {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.45), transparent 24%),
                linear-gradient(135deg, rgba(224, 231, 255, 0.95) 0%, rgba(238, 242, 255, 0.95) 48%, rgba(199, 210, 254, 0.95) 100%);
            border: 1px solid rgba(129, 140, 248, 0.26);
            border-radius: 34px;
            padding: 2.25rem;
            box-shadow: 0 28px 70px rgba(49, 46, 129, 0.14);
            margin-bottom: 1.5rem;
        }
        .opac-hero::after {
            content: "";
            position: absolute;
            right: -60px;
            top: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.24);
        }
        .opac-kicker {
            margin: 0 0 .7rem;
            text-transform: uppercase;
            letter-spacing: .14em;
            font-size: .75rem;
            color: var(--opac-primary);
            font-weight: 700;
        }
        .opac-headline {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2.2rem, 5vw, 4rem);
            line-height: 1.02;
            max-width: 780px;
        }
        .opac-description {
            margin: 1rem 0 0;
            max-width: 760px;
            color: #334155;
            font-size: 1rem;
            line-height: 1.7;
        }
        .opac-search {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            margin-top: 1.2rem;
        }
        .opac-search input {
            flex: 1 1 280px;
            padding: .95rem 1rem;
            border-radius: 16px;
            border: 1px solid rgba(99, 102, 241, 0.25);
            background: rgba(255,255,255,.85);
        }
        .opac-search button {
            padding: .95rem 1.2rem;
            border: none;
            border-radius: 16px;
            background: var(--opac-primary);
            color: #fff;
            font-weight: 700;
        }
        .opac-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .9rem;
            margin-top: 1.5rem;
        }
        .opac-stat {
            background: rgba(255,255,255,.56);
            border: 1px solid rgba(255,255,255,.5);
            border-radius: 22px;
            padding: 1rem;
        }
        .opac-stat strong {
            display: block;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.7rem;
            margin-bottom: .2rem;
        }
        .opac-grid {
            display: grid;
            grid-template-columns: 1.35fr .85fr;
            gap: 1.2rem;
            align-items: start;
            margin-bottom: 1.4rem;
        }
        .opac-grid-main {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }
        .opac-side {
            display: grid;
            gap: 1rem;
        }
        .opac-panel, .opac-card, .opac-empty {
            background: var(--opac-paper);
            backdrop-filter: blur(12px);
            border: 1px solid var(--opac-line);
            border-radius: 24px;
            box-shadow: 0 20px 44px rgba(15, 23, 42, 0.08);
        }
        .opac-panel {
            padding: 1.2rem;
        }
        .opac-panel-title {
            margin: 0 0 .85rem;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
        }
        .opac-card {
            padding: 1.1rem;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .opac-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 50px rgba(30, 41, 59, 0.12);
        }
        .opac-card h3 {
            margin: 0 0 .5rem;
            font-size: 1.05rem;
            line-height: 1.35;
        }
        .opac-meta {
            color: #475569;
            font-size: .93rem;
            margin-bottom: .75rem;
        }
        .opac-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .65rem;
            border-radius: 999px;
            background: rgba(67, 56, 202, 0.10);
            color: #3730a3;
            font-size: .8rem;
            margin-bottom: .75rem;
        }
        .opac-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
        }
        .opac-filter-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
            gap: .75rem;
            margin-top: 1rem;
        }
        .opac-filter-grid select,
        .opac-filter-grid label {
            padding: .9rem 1rem;
            border-radius: 16px;
            border: 1px solid rgba(99, 102, 241, 0.25);
            background: rgba(255,255,255,.85);
            box-sizing: border-box;
        }
        .opac-filter-grid label {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: #334155;
        }
        .opac-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem .9rem;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.08);
            color: #312e81;
            font-size: .9rem;
        }
        .opac-chip-count {
            display: inline-flex;
            min-width: 1.7rem;
            justify-content: center;
            padding: .15rem .45rem;
            border-radius: 999px;
            background: rgba(255,255,255,.9);
            font-size: .8rem;
            color: #4338ca;
        }
        .opac-org-list {
            display: grid;
            gap: .75rem;
        }
        .opac-org-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: .9rem 1rem;
            border-radius: 18px;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(99, 102, 241, 0.10);
            text-decoration: none;
            color: #0f172a;
        }
        .opac-org-link small {
            color: #475569;
        }
        .opac-empty {
            padding: 1rem;
        }
        @media (max-width: 960px) {
            .opac-grid {
                grid-template-columns: 1fr;
            }
            .opac-stats {
                grid-template-columns: 1fr;
            }
            .opac-topbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .opac-filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="opac-shell">
        <div class="opac-topbar">
            <div class="opac-brand">
                @if ($brandLogoUrl)
                    <img class="opac-brand-logo" src="{{ $brandLogoUrl }}" alt="Library logo">
                @else
                    <div class="opac-brand-mark">{{ strtoupper(substr($organization->short_name ?? $organization->name ?? $tenant->code ?? $tenant->name, 0, 2)) }}</div>
                @endif
                <div>
                    <div class="opac-brand-title">{{ $organization->name ?? $tenant->name }}</div>
                    <div class="opac-brand-subtitle">{{ ! empty($organization) ? 'Organization Library Portal' : 'Tenant Library Portal' }}</div>
                </div>
            </div>
            @if (! empty($organization))
                <a class="opac-link" href="{{ route('library.opac.index', ['tenant' => $tenant->code]) }}">Lihat katalog tenant-wide</a>
            @endif
        </div>

        <section class="opac-hero">
            <p class="opac-kicker">
                {{ $tenant->name }} Library OPAC
                @if (! empty($organization))
                    • {{ $organization->name }}
                @endif
            </p>
            <h1 class="opac-headline">
                @if (! empty($organization))
                    Temukan koleksi {{ $organization->name }} dan koleksi tenant-wide.
                @else
                    Temukan buku, cek ketersediaan, dan kelola reservasi.
                @endif
            </h1>
            <p class="opac-description">
                Katalog publik tenant-aware untuk FoundationOS Library.
                @if (! empty($organization))
                    Halaman ini menampilkan koleksi khusus organization ini ditambah koleksi tenant-wide.
                @else
                    Koleksi tenant-wide tetap tampil lintas organization, sementara koleksi khusus organization tetap aman dalam tenant yang sama.
                @endif
            </p>

            <form method="GET" class="opac-search">
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari judul, ISBN, penerbit, atau klasifikasi..." />
                <button type="submit">Cari</button>
            </form>

            <form method="GET" class="opac-filter-grid">
                <input type="hidden" name="q" value="{{ $query }}">
                <select name="category">
                    <option value="">Semua kategori</option>
                    @foreach ($categoryOptions as $category)
                        <option value="{{ $category->id }}" @selected($selectedCategory === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="publisher">
                    <option value="">Semua penerbit</option>
                    @foreach ($publisherOptions as $publisherOption)
                        <option value="{{ $publisherOption }}" @selected($selectedPublisher === $publisherOption)>{{ $publisherOption }}</option>
                    @endforeach
                </select>
                <label>
                    <input type="checkbox" name="available_only" value="1" @checked($availableOnly)>
                    Hanya yang tersedia
                </label>
                <button type="submit">Terapkan Filter</button>
            </form>

            <div class="opac-stats">
                <div class="opac-stat">
                    <strong>{{ number_format($stats['titles']) }}</strong>
                    <span>Judul aktif</span>
                </div>
                <div class="opac-stat">
                    <strong>{{ number_format($stats['copies']) }}</strong>
                    <span>Total copy</span>
                </div>
                <div class="opac-stat">
                    <strong>{{ number_format($stats['available']) }}</strong>
                    <span>Copy tersedia</span>
                </div>
            </div>
        </section>

        <div class="opac-grid">
            <div class="opac-grid-main">
                @forelse ($books as $book)
                    <article class="opac-card">
                        <div class="opac-badge">{{ $book->category?->name ?? 'Tanpa Kategori' }}</div>
                        <h3>{{ $book->title }}</h3>
                        <p class="opac-meta">{{ $book->publisher ?: 'Penerbit belum diisi' }}</p>
                        <p class="opac-meta">Tersedia {{ $book->available_copies }} dari {{ $book->total_copies }} copy</p>
                        <a class="opac-link" href="{{ ! empty($organization)
                            ? route('library.opac.organization.show', ['tenant' => $tenant->code, 'organization' => $organization->code, 'book' => $book])
                            : route('library.opac.show', ['tenant' => $tenant->code, 'book' => $book]) }}">Lihat detail</a>
                    </article>
                @empty
                    <article class="opac-empty">
                        <h3>Tidak ada hasil</h3>
                        <p class="opac-meta">Coba kata kunci lain atau periksa data katalog tenant ini.</p>
                    </article>
                @endforelse
            </div>

            <aside class="opac-side">
                @if ($featuredCategories->isNotEmpty())
                    <section class="opac-panel">
                        <h3 class="opac-panel-title">Kategori Unggulan</h3>
                        <div class="opac-chip-list">
                            @foreach ($featuredCategories as $category)
                                <span class="opac-chip">
                                    {{ $category['name'] }}
                                    <span class="opac-chip-count">{{ $category['total'] }}</span>
                                </span>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if (empty($organization) && $organizations->isNotEmpty())
                    <section class="opac-panel">
                        <h3 class="opac-panel-title">Portal per Organization</h3>
                        <div class="opac-org-list">
                            @foreach ($organizations as $org)
                                <a class="opac-org-link" href="{{ route('library.opac.organization.index', ['tenant' => $tenant->code, 'organization' => $org->code]) }}">
                                    <div>
                                        <strong>{{ $org->short_name ?: $org->name }}</strong><br>
                                        <small>{{ $org->name }}</small>
                                    </div>
                                    <span class="opac-link">Buka</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="opac-panel">
                    <h3 class="opac-panel-title">Cara Menggunakan</h3>
                    <p class="opac-meta">Cari judul, buka detail buku, lalu lakukan reservasi jika akun Anda sudah terhubung ke member library pada tenant ini.</p>
                </section>
            </aside>
        </div>

        <div style="margin-top:1.5rem;">
            {{ $books->links() }}
        </div>
    </div>
</x-library::layouts.master>
