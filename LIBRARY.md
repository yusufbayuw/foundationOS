# FoundationOS Library

Panduan ini merangkum operasional modul Library dengan pendekatan `tenant-first` dan `organization-optional`.

## Model Scope

- Semua data Library wajib memiliki `tenant_id`.
- `organization_id` bersifat opsional.
- Record dengan `organization_id = null` dianggap `tenant-wide` dan dapat dipakai lintas organization dalam tenant yang sama.
- Record dengan `organization_id` terisi hanya berlaku untuk organization tersebut.

## Komponen Utama

- Katalog: `BookCategory`, `Book`, `BookCopy`
- Sirkulasi: `Member`, `BookReservation`, `Loan`, `Fine`
- Policy: `LibraryPolicy`

## Policy Sirkulasi

Resolusi policy berjalan dengan urutan:

1. nilai eksplisit di `Member`
2. `LibraryPolicy` level organization
3. `LibraryPolicy` level tenant
4. fallback default tenant setting / default sistem

Field penting:

- `max_books`
- `loan_period_days`
- `fine_per_day`
- `max_extensions`
- `grace_period_days`
- `reservation_pickup_days`

## Command Operasional

Recalculate denda dan status keterlambatan:

```bash
php artisan fos:library:recalc-fines
php artisan fos:library:recalc-fines --tenant=1
php artisan fos:library:recalc-fines --tenant=1 --member=10
```

Audit stok katalog vs copy dan pinjaman aktif:

```bash
php artisan fos:library:stock-audit
php artisan fos:library:stock-audit --tenant=1
```

Import legacy SLiMS per tenant:

```bash
php artisan fos:library:import-slims --tenant=1 --entity=all --dry-run
php artisan fos:library:import-slims --tenant=1 --organization=10 --entity=all --dry-run
php artisan fos:library:import-slims --tenant=1 --entity=all --skip-existing
php artisan fos:library:import-slims --tenant=1 --entity=all --since="2026-03-01 00:00:00"
```

Catatan import:

- untuk library per-organization, koneksi SLiMS dibaca dari `organization_settings`
- untuk library tenant-wide, koneksi SLiMS dibaca dari `tenant_settings`
- `--skip-existing` aman untuk incremental import berbasis mapping
- `--since` hanya aktif bila tabel SLiMS memiliki kolom tanggal yang kompatibel
- import saat ini mencakup `catalog`, `members`, dan `loans`

## OPAC Publik

URL publik katalog:

```bash
/opac/{tenant_code}
/opac/{tenant_code}/organizations/{organization_code}
```

Fitur yang tersedia:

- pencarian katalog tenant-aware
- detail buku dan ketersediaan copy
- reservasi buku oleh member setelah login
- mode organization menampilkan koleksi organization tersebut ditambah koleksi tenant-wide

## SOP Singkat

### Tenant dengan perpustakaan terpusat

- buat data library dengan `organization_id = null`
- member dari organization mana pun dalam tenant tetap bisa meminjam koleksi tenant-wide

### Tenant dengan perpustakaan per organization

- isi `organization_id` pada data policy, member, copy, loan, dan fine
- koleksi tetap dapat digabung dengan record `tenant-wide` bila dibutuhkan

## Troubleshooting

- Jika data katalog terlihat kurang lengkap di OPAC, jalankan `php artisan fos:library:stock-audit`.
- Jika status denda tidak sinkron setelah impor atau update massal, jalankan `php artisan fos:library:recalc-fines`.
- Jika import SLiMS gagal pada mode organization, periksa `organization_settings` untuk pasangan `slims_db_*`.
- Jika import SLiMS gagal pada mode tenant-wide, periksa `tenant_settings` untuk pasangan `slims_db_*`.
