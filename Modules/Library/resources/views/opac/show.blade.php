<x-library::layouts.master :title="$book->title">
    <style>
        :root {
            --opac-primary: {{ $tenant->primary_color ?: '#4338ca' }};
        }
        .opac-shell {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 4rem;
            color: #172554;
        }
        .opac-layout {
            display: grid;
            grid-template-columns: 1.4fr .8fr;
            gap: 1.2rem;
            align-items: start;
        }
        .opac-panel {
            background: linear-gradient(180deg, rgba(255,255,255,.96) 0%, rgba(238,242,255,.96) 100%);
            border: 1px solid rgba(199, 210, 254, 0.9);
            border-radius: 30px;
            padding: 2rem;
            box-shadow: 0 24px 60px rgba(49, 46, 129, 0.12);
        }
        .opac-side {
            display: grid;
            gap: 1rem;
        }
        .opac-card {
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(148,163,184,.18);
            border-radius: 24px;
            padding: 1.2rem;
            box-shadow: 0 16px 40px rgba(30, 41, 59, 0.08);
        }
        .opac-card h3 {
            margin: 0 0 .5rem;
            font-family: 'Space Grotesk', sans-serif;
        }
        .opac-list {
            display: grid;
            gap: .75rem;
            margin-top: 1rem;
        }
        .opac-badge {
            display: inline-block;
            padding: .35rem .65rem;
            border-radius: 999px;
            background: #e0e7ff;
            color: #3730a3;
            font-size: .85rem;
            margin-right: .4rem;
        }
        .opac-button {
            padding: .85rem 1.2rem;
            border: none;
            border-radius: 14px;
            background: var(--opac-primary);
            color: #fff;
            font-weight: 700;
        }
        .opac-label {
            display: block;
            font-size: .92rem;
            color: #334155;
            margin-top: .8rem;
        }
        .opac-input {
            display: block;
            width: 100%;
            margin: .45rem 0 0;
            padding: .85rem 1rem;
            border-radius: 12px;
            border: 1px solid #a5b4fc;
            box-sizing: border-box;
        }
        .opac-related {
            display: grid;
            gap: .75rem;
            margin-top: 1rem;
        }
        .opac-related a {
            text-decoration: none;
            color: #0f172a;
        }
        @media (max-width: 960px) {
            .opac-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="opac-shell">
        <div style="margin-bottom:1rem;">
            <a href="{{ ! empty($organization)
                ? route('library.opac.organization.index', ['tenant' => $tenant->code, 'organization' => $organization->code])
                : route('library.opac.index', ['tenant' => $tenant->code]) }}" style="color:var(--opac-primary); text-decoration:none;">Kembali ke katalog</a>
        </div>

        <div class="opac-layout">
            <section class="opac-panel">
                <p style="margin:0 0 .5rem; text-transform:uppercase; letter-spacing:.12em; font-size:.75rem; color:var(--opac-primary);">
                    {{ $tenant->name }}
                    @if (! empty($organization))
                        • {{ $organization->name }}
                    @endif
                </p>
                <h1 style="margin:0; font-family:'Space Grotesk', sans-serif; font-size:clamp(2rem, 4vw, 3.2rem);">{{ $book->title }}</h1>
                <p style="color:#475569;">{{ $book->publisher ?: 'Penerbit belum diisi' }} {{ $book->publication_year ? '• '.$book->publication_year : '' }}</p>

                <div style="margin:1rem 0;">
                    <span class="opac-badge">ISBN {{ $book->isbn ?: '-' }}</span>
                    <span class="opac-badge">Klasifikasi {{ $book->classification_code ?: '-' }}</span>
                    <span class="opac-badge">Tersedia {{ $book->available_copies }}/{{ $book->total_copies }}</span>
                    @if ($book->category)
                        <span class="opac-badge">{{ $book->category->name }}</span>
                    @endif
                </div>

                <p style="line-height:1.8; color:#334155;">{{ $book->synopsis ?: 'Belum ada sinopsis.' }}</p>

                <h3 style="margin-top:1.6rem;">Copy & Lokasi</h3>
                <div class="opac-list">
                    @foreach ($book->copies as $copy)
                        <div>{{ $copy->copy_number }} • {{ $copy->status }} • {{ $copy->location_shelf ?: 'Lokasi belum diisi' }}</div>
                    @endforeach
                </div>
            </section>

            <aside class="opac-side">
                <section class="opac-card">
                    <h3>Reservasi</h3>
                    @auth
                        @if (session('status'))
                            <p style="color:#166534;">{{ session('status') }}</p>
                        @endif
                        @if ($members->isEmpty())
                            <p style="color:#92400e;">Akun ini belum terhubung ke member library pada tenant ini.</p>
                        @else
                            <form method="POST" action="{{ ! empty($organization)
                                ? route('library.opac.organization.reserve', ['tenant' => $tenant->code, 'organization' => $organization->code, 'book' => $book])
                                : route('library.opac.reserve', ['tenant' => $tenant->code, 'book' => $book]) }}">
                                @csrf
                                <label class="opac-label" for="member_id">Pilih member</label>
                                <select class="opac-input" id="member_id" name="member_id" required>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->member_number }}</option>
                                    @endforeach
                                </select>
                                <label class="opac-label" for="notes">Catatan</label>
                                <textarea class="opac-input" id="notes" name="notes" style="min-height:100px;"></textarea>
                                <button class="opac-button" type="submit" style="margin-top:1rem;">Buat reservasi</button>
                            </form>
                        @endif
                    @else
                        <p style="color:#475569;">Login terlebih dahulu untuk membuat reservasi buku.</p>
                    @endauth
                </section>

                @if ($relatedBooks->isNotEmpty())
                    <section class="opac-card">
                        <h3>Rekomendasi Serupa</h3>
                        <div class="opac-related">
                            @foreach ($relatedBooks as $relatedBook)
                                <a href="{{ ! empty($organization)
                                    ? route('library.opac.organization.show', ['tenant' => $tenant->code, 'organization' => $organization->code, 'book' => $relatedBook])
                                    : route('library.opac.show', ['tenant' => $tenant->code, 'book' => $relatedBook]) }}">
                                    <strong>{{ $relatedBook->title }}</strong><br>
                                    <span style="color:#475569; font-size:.92rem;">{{ $relatedBook->available_copies }}/{{ $relatedBook->total_copies }} tersedia</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</x-library::layouts.master>
