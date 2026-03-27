<x-library::layouts.master :title="($organization->name ?? $tenant->name) . ' Circulation Desk'">
    <style>
        :root {
            --opac-primary: {{ $tenant->primary_color ?: '#4338ca' }};
            --opac-secondary: {{ $tenant->secondary_color ?: '#0f172a' }};
        }
        .desk-shell {
            max-width: 1260px;
            margin: 0 auto;
            padding: 2rem 1.25rem 4rem;
            color: #0f172a;
        }
        .desk-hero {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 1.2rem;
            margin-bottom: 1.25rem;
        }
        .desk-panel {
            background: rgba(255,255,255,.9);
            border: 1px solid rgba(99, 102, 241, 0.14);
            border-radius: 28px;
            padding: 1.4rem;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }
        .desk-title {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 4vw, 3.5rem);
            line-height: 1.02;
        }
        .desk-kicker {
            margin: 0 0 .7rem;
            text-transform: uppercase;
            letter-spacing: .14em;
            font-size: .75rem;
            color: var(--opac-primary);
            font-weight: 700;
        }
        .desk-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
        }
        .desk-form {
            display: grid;
            gap: .85rem;
        }
        .desk-input, .desk-select, .desk-textarea {
            width: 100%;
            box-sizing: border-box;
            padding: .9rem 1rem;
            border-radius: 14px;
            border: 1px solid #c7d2fe;
            background: #fff;
        }
        .desk-button {
            padding: .9rem 1.1rem;
            border: none;
            border-radius: 14px;
            background: var(--opac-primary);
            color: #fff;
            font-weight: 700;
        }
        .desk-button-alt {
            background: #0f172a;
        }
        .desk-list {
            display: grid;
            gap: .8rem;
            margin-top: 1rem;
        }
        .desk-card {
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 20px;
            padding: 1rem;
        }
        .desk-badge {
            display: inline-flex;
            padding: .3rem .6rem;
            border-radius: 999px;
            background: rgba(67, 56, 202, 0.10);
            color: #3730a3;
            font-size: .8rem;
            margin-right: .35rem;
            margin-bottom: .35rem;
        }
        .desk-logo {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid rgba(99, 102, 241, 0.14);
            background: #fff;
        }
        .desk-brand {
            display: flex;
            align-items: center;
            gap: .9rem;
        }
        .desk-status {
            margin: 0 0 1rem;
            color: #166534;
            font-weight: 600;
        }
        .desk-toolbar {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 1rem;
        }
        .desk-inline {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .desk-toggle {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            background: rgba(238, 242, 255, 0.9);
            border: 1px solid rgba(99, 102, 241, 0.18);
            border-radius: 999px;
            padding: .65rem .95rem;
            color: #312e81;
            font-weight: 600;
            text-decoration: none;
        }
        .desk-mini-form {
            display: grid;
            gap: .6rem;
            margin-top: .9rem;
        }
        .desk-actions {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            margin-top: .85rem;
        }
        .desk-button-warn {
            background: #b45309;
        }
        .desk-button-danger {
            background: #b91c1c;
        }
        .desk-metric-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .85rem;
            margin-top: 1rem;
        }
        .desk-metric {
            background: rgba(238, 242, 255, 0.85);
            border: 1px solid rgba(99, 102, 241, 0.12);
            border-radius: 18px;
            padding: .9rem 1rem;
        }
        .desk-metric strong {
            display: block;
            font-size: 1.5rem;
            font-family: 'Space Grotesk', sans-serif;
        }
        .desk-subtle {
            color: #64748b;
            font-size: .92rem;
        }
        @media (max-width: 960px) {
            .desk-hero, .desk-grid {
                grid-template-columns: 1fr;
            }
            .desk-metric-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="desk-shell">
        @if (session('status'))
            <p class="desk-status">{{ session('status') }}</p>
        @endif

        <div class="desk-hero">
            <section class="desk-panel">
                <p class="desk-kicker">Circulation First</p>
                <h1 class="desk-title">Checkout dan return cepat untuk staf perpustakaan.</h1>
                <p style="color:#475569; line-height:1.7;">
                    Gunakan barcode scanner atau lookup manual untuk menemukan member dan copy buku, lalu proses transaksi tanpa harus masuk ke resource Filament.
                </p>
                <div style="margin-top:1rem;">
                    <span class="desk-badge">{{ $tenant->name }}</span>
                    @if (! empty($organization))
                        <span class="desk-badge">{{ $organization->name }}</span>
                    @endif
                    <span class="desk-badge">Admin Only</span>
                </div>
                <div class="desk-toolbar">
                    <a class="desk-toggle" href="{{ request()->fullUrlWithQuery(['scanner' => $scannerMode ? 0 : 1]) }}">
                        {{ $scannerMode ? 'Scanner mode aktif' : 'Aktifkan scanner mode' }}
                    </a>
                    <span style="color:#475569;">Fokus barcode dijaga tetap aktif agar meja sirkulasi bisa bekerja lebih cepat.</span>
                </div>
            </section>

            <section class="desk-panel">
                <div class="desk-brand">
                    @if ($brandLogoUrl)
                        <img class="desk-logo" src="{{ $brandLogoUrl }}" alt="Library logo">
                    @else
                        <div class="desk-logo" style="display:flex;align-items:center;justify-content:center;font-family:'Space Grotesk',sans-serif;font-weight:700;color:#4338ca;">
                            {{ strtoupper(substr($organization->short_name ?? $organization->name ?? $tenant->code ?? $tenant->name, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <strong style="display:block; font-family:'Space Grotesk',sans-serif; font-size:1.15rem;">{{ $organization->name ?? $tenant->name }}</strong>
                        <span style="color:#475569;">Circulation desk siap untuk scan barcode maupun pencarian manual.</span>
                    </div>
                </div>
                <div class="desk-metric-grid">
                    <div class="desk-metric">
                        <span class="desk-subtle">Member hasil lookup</span>
                        <strong>{{ $members->count() }}</strong>
                    </div>
                    <div class="desk-metric">
                        <span class="desk-subtle">Copy hasil scan/lookup</span>
                        <strong>{{ $copies->count() }}</strong>
                    </div>
                    <div class="desk-metric">
                        <span class="desk-subtle">Loan aktif terdeteksi</span>
                        <strong>{{ $activeLoan ? 1 : 0 }}</strong>
                    </div>
                </div>
            </section>
        </div>

        <div class="desk-grid">
            <section class="desk-panel">
                <h2 style="margin-top:0; font-family:'Space Grotesk',sans-serif;">Lookup Member</h2>
                <form method="GET" class="desk-form">
                    <input class="desk-input" type="text" name="member_q" value="{{ $memberQuery }}" placeholder="Cari nomor member, nama, email, atau status..." />
                    @if ($itemQuery !== '')
                        <input type="hidden" name="item_q" value="{{ $itemQuery }}">
                    @endif
                    <button class="desk-button" type="submit">Cari Member</button>
                </form>

                <div class="desk-list">
                    @forelse ($members as $member)
                        <article class="desk-card">
                            <strong>{{ $member->member_number }}</strong><br>
                            <span style="color:#475569;">{{ $member->user?->name ?? 'User tidak terhubung' }} • {{ $member->user?->email ?? '-' }}</span>
                            <div style="margin-top:.6rem;">
                                <span class="desk-badge">Status {{ $member->status }}</span>
                                <span class="desk-badge">Pinjaman aktif {{ $member->current_loans_count }}</span>
                            </div>
                        </article>
                    @empty
                        @if ($memberQuery !== '')
                            <article class="desk-card">Tidak ada member yang cocok dengan pencarian ini.</article>
                        @endif
                    @endforelse
                </div>
            </section>

            <section class="desk-panel">
                <h2 style="margin-top:0; font-family:'Space Grotesk',sans-serif;">Scan Barcode / Lookup Copy</h2>
                <form method="GET" class="desk-form">
                    <input class="desk-input" id="desk-item-q" type="text" name="item_q" value="{{ $itemQuery }}" placeholder="Scan barcode atau cari copy number / judul..." autocomplete="off" />
                    @if ($memberQuery !== '')
                        <input type="hidden" name="member_q" value="{{ $memberQuery }}">
                    @endif
                    @if ($scannerMode)
                        <input type="hidden" name="scanner" value="1">
                    @endif
                    <button class="desk-button" type="submit">Cari Copy</button>
                </form>

                @if ($activeLoan)
                    <article class="desk-card" style="margin-top:1rem; border-color:rgba(15,23,42,.18);">
                        <strong>Quick Return</strong>
                        <p style="color:#475569;">{{ $activeLoan->bookCopy?->copy_number }} sedang dipinjam oleh {{ $activeLoan->member?->member_number }}.</p>
                        <form method="POST" action="{{ ! empty($organization)
                            ? route('library.opac.organization.circulation.return', ['tenant' => $tenant->code, 'organization' => $organization->code])
                            : route('library.opac.circulation.return', ['tenant' => $tenant->code]) }}">
                            @csrf
                            <input type="hidden" name="loan_id" value="{{ $activeLoan->id }}">
                            <button class="desk-button desk-button-alt" type="submit">Proses Return Cepat</button>
                        </form>
                        <div class="desk-actions">
                            <form method="POST" action="{{ ! empty($organization)
                                ? route('library.opac.organization.circulation.extend', ['tenant' => $tenant->code, 'organization' => $organization->code])
                                : route('library.opac.circulation.extend', ['tenant' => $tenant->code]) }}">
                                @csrf
                                <input type="hidden" name="loan_id" value="{{ $activeLoan->id }}">
                                <button class="desk-button desk-button-warn" type="submit">Perpanjang Loan</button>
                            </form>
                        </div>
                        <form method="POST" class="desk-mini-form" action="{{ ! empty($organization)
                            ? route('library.opac.organization.circulation.issue', ['tenant' => $tenant->code, 'organization' => $organization->code])
                            : route('library.opac.circulation.issue', ['tenant' => $tenant->code]) }}">
                            @csrf
                            <input type="hidden" name="loan_id" value="{{ $activeLoan->id }}">
                            <div class="desk-inline">
                                <select class="desk-select" name="issue_type" style="max-width:220px;" required>
                                    <option value="lost">Tandai Hilang</option>
                                    <option value="damaged">Tandai Rusak</option>
                                </select>
                                <button class="desk-button desk-button-danger" type="submit">Simpan Issue</button>
                            </div>
                            <textarea class="desk-textarea" name="notes" placeholder="Catatan issue, mis. cover rusak atau copy hilang saat dipinjam." rows="3"></textarea>
                        </form>
                    </article>
                @endif

                <div class="desk-list">
                    @forelse ($copies as $copy)
                        <article class="desk-card">
                            <strong>{{ $copy->copy_number }}</strong> • {{ $copy->barcode ?: 'Tanpa barcode' }}<br>
                            <span style="color:#475569;">{{ $copy->book?->title ?? 'Judul tidak ditemukan' }}</span>
                            <div style="margin-top:.6rem;">
                                <span class="desk-badge">Status {{ $copy->status }}</span>
                                <span class="desk-badge">{{ $copy->location_shelf ?: 'Lokasi belum diisi' }}</span>
                            </div>
                        </article>
                    @empty
                        @if ($itemQuery !== '')
                            <article class="desk-card">Tidak ada copy yang cocok dengan barcode atau pencarian ini.</article>
                        @endif
                    @endforelse
                </div>
            </section>
        </div>

        <section class="desk-panel" style="margin-top:1.2rem;">
            <h2 style="margin-top:0; font-family:'Space Grotesk',sans-serif;">Quick Checkout</h2>
            <form method="POST" action="{{ ! empty($organization)
                ? route('library.opac.organization.circulation.checkout', ['tenant' => $tenant->code, 'organization' => $organization->code])
                : route('library.opac.circulation.checkout', ['tenant' => $tenant->code]) }}" class="desk-form">
                @csrf
                <select class="desk-select" name="member_id" required>
                    <option value="">Pilih member dari hasil lookup</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->member_number }} - {{ $member->user?->name }}</option>
                    @endforeach
                </select>
                <select class="desk-select" name="book_copy_id" required>
                    <option value="">Pilih copy dari hasil scan / lookup</option>
                    @foreach ($copies as $copy)
                        <option value="{{ $copy->id }}">{{ $copy->copy_number }} - {{ $copy->book?->title }}</option>
                    @endforeach
                </select>
                <textarea class="desk-textarea" name="notes" placeholder="Catatan transaksi (opsional)" rows="4"></textarea>
                <button class="desk-button" type="submit">Buat Peminjaman Cepat</button>
            </form>
        </section>

        <section class="desk-panel" style="margin-top:1.2rem;">
            <h2 style="margin-top:0; font-family:'Space Grotesk',sans-serif;">Aktivitas Terbaru</h2>
            <p class="desk-subtle">Ringkasan transaksi terakhir untuk membantu staf memastikan alur peminjaman dan pengembalian tetap terpantau.</p>
            <div class="desk-list">
                @forelse ($recentLoans as $loan)
                    <article class="desk-card">
                        <strong>{{ $loan->bookCopy?->copy_number ?? 'Copy tidak ditemukan' }}</strong>
                        <span class="desk-subtle"> • {{ $loan->bookCopy?->book?->title ?? 'Judul tidak ditemukan' }}</span>
                        <div style="margin-top:.45rem; color:#475569;">
                            Member {{ $loan->member?->member_number ?? '-' }} •
                            Status {{ $loan->status }} •
                            Jatuh tempo {{ optional($loan->due_date)->format('d M Y') ?? '-' }}
                        </div>
                        <div style="margin-top:.6rem;">
                            <span class="desk-badge">Diproses {{ optional($loan->updated_at)->diffForHumans() }}</span>
                            @if ($loan->return_date)
                                <span class="desk-badge">Kembali {{ optional($loan->return_date)->format('d M Y') }}</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <article class="desk-card">Belum ada transaksi terbaru untuk scope perpustakaan ini.</article>
                @endforelse
            </div>
        </section>
    </div>

    @if ($scannerMode)
        <script>
            (() => {
                const input = document.getElementById('desk-item-q');
                const form = input?.form;
                if (! input) {
                    return;
                }

                const focusInput = () => input.focus({ preventScroll: true });
                let submitTimer = null;

                focusInput();
                window.addEventListener('pageshow', focusInput);
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible') {
                        focusInput();
                    }
                });
                input.addEventListener('blur', () => {
                    setTimeout(focusInput, 120);
                });
                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' && form) {
                        event.preventDefault();
                        form.submit();
                    }
                });
                input.addEventListener('input', () => {
                    if (! form) {
                        return;
                    }

                    window.clearTimeout(submitTimer);
                    const value = input.value.trim();
                    if (value.length < 6) {
                        return;
                    }

                    submitTimer = window.setTimeout(() => {
                        form.submit();
                    }, 180);
                });
            })();
        </script>
    @endif
</x-library::layouts.master>
