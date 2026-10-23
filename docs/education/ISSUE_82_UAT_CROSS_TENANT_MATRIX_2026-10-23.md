# FoundationOS Education — UAT dan Validasi Lintas Tenant (#82)

**Tanggal baseline:** 2026-10-23  
**Lingkup:** Enrollment, School, Campus, Exam, Library, Counseling, Clinic, EducationQa, serta sinkronisasi Moodle.  
**Status pelaksanaan:** **BELUM DIJALANKAN di staging**. Dokumen ini adalah rencana uji dan format penerimaan, **bukan** bukti hasil pengujian.  
**Pemilik penerimaan:** PM FoundationOS bersama pemilik proses akademik setiap institusi.  
**Aturan rilis:** jangan merge, deploy produksi, atau menyatakan *production-ready* hanya berdasarkan CI hijau.

## Prasyarat dan data uji terisolasi

- Gunakan **staging** dengan data sintetis atau data yang dianonimkan; backup dan restore sudah diuji; SMTP/pembayaran/notifikasi diarahkan ke sandbox.
- Sediakan **Tenant A** (sekolah K-12) dan **Tenant B** (kampus), dengan pengguna berbeda; Tenant A memiliki **Unit A1** dan **Unit A2** untuk memeriksa isolasi organisasi. Jika aplikasi dapat menjalankan dua mode akademik pada tenant berbeda, uji pula pergantian konteks.
- Untuk tiap tenant sediakan administrator tenant, operator, guru/dosen, siswa/mahasiswa, wali/orang tua (jika fitur aktif), pustakawan, konselor, petugas klinik, dan akun tanpa hak. Akun global super admin diuji dengan **tenant aktif eksplisit**, bukan diasumsikan bebas dari semua pemeriksaan kepemilikan.
- Siapkan **ID nyata dari database staging** untuk dua pemohon, dua siswa, dua kelas/periode, dua mata kuliah/StudyPlan, dua ujian, dua salinan buku, dan dua rekam kesehatan. Catat referensi acak dalam lembar bukti; **jangan** merekam NIK, kata sandi, token, catatan konseling, atau diagnosis asli.
- Jalankan skenario positif dan negatif lewat UI **serta** request langsung dengan ID tenant lain (misalnya URL, payload, ekspor PDF/CSV, Livewire action). Periksa perubahan database dan audit trail sebelum/sesudah tiap operasi.
- Gunakan kombinasi PR yang sudah direview dan terintegrasi di **staging candidate**, bukan menganggap hasil workflow masing-masing PR independen menjamin tidak ada konflik integrasi.

## Matriks eksekusi

Semua baris awalnya **NOT RUN**. Status sah: `PASS`, `FAIL`, `BLOCKED`, atau `NOT RUN`. `PASS` wajib memiliki bukti ID, waktu, SHA build, akun/role yang diuji, dan verifikasi data.

| ID | Modul / golden path | Skenario positif yang wajib lulus | Uji negatif lintas tenant / organisasi | Kriteria penerimaan | Status |
| --- | --- | --- | --- | --- | --- |
| EDU-01 | Enrollment → School | Terima pemohon valid → satu Student; retry event/promote dua kali → tetap satu | Pemohon Tenant B atau tautan `converted_to_student_id` Tenant B dikirim saat konteks A; unit A2 tidak boleh mengakses data A1 tanpa izin | Tidak ada Student duplikat, pengaitan asing, atau mutasi lintas tenant; audit dapat ditelusuri | NOT RUN |
| EDU-02 | Enrollment rollback | Perubahan accepted → reverted menjaga jejak status dan relasi historis sesuai kebijakan | Reversal pemohon B dari operator A dan dokumen pemohon B via ID langsung ditolak | Riwayat pendaftaran/keputusan tidak hilang, tidak ada efek Finance tanpa persetujuan pemilik modul | NOT RUN |
| EDU-03 | School jadwal/absensi | Jadwal kelas, absensi dan pengubahan nilai untuk periode sah | Guru A1 mengubah kelas A2/B, permintaan nilai memakai student_id dari tenant lain | Semua query tulis dan baca menghormati tenant, unit, penugasan dan periode | NOT RUN |
| EDU-04 | School rapor historis | Cetak rapor periode lampau setelah siswa pindah kelas; cocokkan nilai dan absensi historis | Tautan PDF/print rapor siswa B, kelas lain, dan role tanpa hak ditolak (403/404 tanpa isi sensitif) | Data rapor akurat terhadap periode; tidak terkontaminasi kelas/periode kini | NOT RUN |
| EDU-05 | Campus KRS & nilai | KRS, hasil studi, IP dan IPK konsisten setelah nilai masuk/diubah sah | `study_plan_item_id`, `course_offering_id`, mahasiswa, dan `study_result_id` dari tenant B dikirim saat A aktif | Tidak menimpa hasil tenant lain, tidak muncul nilai atau IPK asing | NOT RUN |
| EDU-06 | Campus historis | Transkrip, mata kuliah diulang, dan perhitungan kredit pada beberapa periode cocok dengan keputusan akademik | Mahasiswa dan dosen mencoba membuka transkrip/kelas tenant lain dengan ID langsung | Rekap historis tidak berubah saat periode atau kelas aktif berubah | NOT RUN |
| EDU-07 | Exam runtime | Publish ujian sandbox → peserta sah mengerjakan → sync attempts/results → ekspor gradebook satu kali | Payload berisi participant/attempt/result UUID milik tenant atau peserta lain; replays/duplikat; nomor kelas asing | Tidak ada perubahan identitas pemilik hasil, tidak ada nilai ditukar antar peserta; retry idempoten | NOT RUN |
| EDU-08 | Exam grade bridge | Nilai ujian School/Campus tersinkron ke target yang disetujui pemilik nilai | Manipulasi metadata `study_plan_item_id` lintas tenant atau ke mahasiswa berbeda; hasil existing foreign tidak berubah | Sinkronisasi ditolak atau dilewati dan diaudit; nilai tidak tertimpa tanpa otorisasi | NOT RUN |
| EDU-09 | Library circulation | Pinjam → kembalikan → perpanjang dengan saldo/salinan akurat | Member, copy, loan, reservation dari tenant/organisasi lain; dua checkout serentak salinan yang sama | Maksimal satu peminjaman aktif per copy, quota berdasarkan transaksi nyata, mutasi asing ditolak | NOT RUN |
| EDU-10 | Counseling kerahasiaan | Konselor sah melihat catatan rahasia sesuai cakupan kasus, perubahan punya audit | Guru/petugas biasa dan akun tenant B mencoba list, detail, ekspor, restore/hapus permanen | Catatan rahasia tidak muncul pada UI/API/ekspor, termasuk direct URL; retention aman | NOT RUN |
| EDU-11 | Clinic kerahasiaan | Petugas klinik sah melihat dan mengubah rekam kesehatan pasien yang benar | Rekam tenant B/Unit A2 diakses tenant A/Unit A1, termasuk ekspor dan soft/hard delete | 403/404; tidak bocor data pasien dan tidak ada penghapusan permanen tanpa kebijakan | NOT RUN |
| EDU-12 | EducationQa | Periode survei/indikator, input bukti, review dan tindak lanjut ditandatangani pemilik mutu | Bukti/hasil survei/indikator tenant atau unit lain melalui filter/ID | Hanya dapat `PASS` setelah pemilik proses menyetujui kriteria dan alur bukti/approval nyata | BLOCKED |
| EDU-13 | Moodle sandbox | Course/enrollment/grade pull, retry, reconciliation dan outbox berhasil pada Moodle sandbox | Mapping course/user/grade Tenant B disisipkan saat A aktif; retry dengan payload sama | Nilai dan mapping tidak berpindah tenant; retry idempoten; reconcile menunjukkan 0 mismatch tidak dijelaskan | BLOCKED |
| EDU-14 | Lintas modul & regresi | Enrollment → School/Campus → Exam → report/transcript → audit; PDF dan job queue sesuai tenant | Ganti tenant aktif di tengah request/job; replay worker; ekspor dan link dokumen lintas tenant | Tidak ada kehilangan konteks tenant pada job, cache, berkas, atau notifikasi | NOT RUN |

## Kasus wajib untuk matriks akses

Ulangi skenario EDU-01 sampai EDU-14 sesuai fitur/izin aktual dengan (a) pengguna sah tenant/unit sendiri, (b) pengguna tenant lain, (c) pengguna tenant sama tetapi unit berbeda, (d) role tanpa `view`/`update`, (e) super admin global dengan tenant aktif, dan (f) queue worker tanpa atau dengan tenant context yang salah. **Jangan** menyamakan hasil *unit test* dengan bukti otorisasi Livewire/API/export pada staging.

Untuk semua penolakan, nilai yang diterima adalah 403/404 atau validasi domain 422 jika sesuai kontrak endpoint. Yang **tidak boleh** terjadi: respons 200 berisi data tenant lain; perubahan row lintas tenant; perpindahan kepemilikan `tenant_id`; atau pesan error berisi data sensitif.

## Perintah pemeriksaan otomatis sebelum UAT

Jalankan pada SHA staging candidate dengan database pengujian terisolasi:

```bash
vendor/bin/pint --test
php artisan test --compact --filter=ApplicantAcceptedPipelineTest
php artisan test --compact --filter=SchoolDocumentPdfTest
php artisan test --compact --filter=DetailedGradePullPopulatesStudyResultTest
php artisan test --compact --filter=ExamResultSyncTest
php artisan test --compact --filter=ExamGradebookExportTest
php artisan test --compact --filter=LibraryFoundationTest
php artisan test --compact --filter=CounselingConfidentialityAccessTest
php artisan test --compact --filter=ClinicHealthRecordProtectionTest
```

**Catatan:** suite dari PR #99/#100/#104/#110–#114 dan PR hasil exam terbaru hanya tersedia bersama jika perubahan telah disatukan pada kandidat integrasi. Gunakan database PostgreSQL juga, bukan SQLite saja. Hasil PHPunit tidak menggantikan tes UI/akses dokumen ataupun sinkronisasi nyata ke Moodle sandbox.

## Format bukti per skenario

```text
ID UAT:
Waktu (Asia/Jakarta):
Environment + app SHA:
Tenant aktif / unit / role (alias saja):
Kondisi awal (jumlah row dan referensi sintetis):
Langkah UI/API:
HTTP/Livewire result:
Row sebelum → sesudah (tenant_id, actor, reference; tanpa PII):
Audit trail / worker ID / correlation ID:
Tautan log atau tangkapan layar aman:
Status: PASS | FAIL | BLOCKED | NOT RUN
Penguji:
Disetujui pemilik proses:
```

## Gerbang keputusan

**Blocker P0:** kebocoran identitas/rekam medis/catatan konseling lintas tenant; overwrite nilai; duplikasi Student; nilai terasosiasi ke mahasiswa berbeda; pelanggaran hak cetak/ekspor; data hilang saat rollback. **P1:** error sinkronisasi tanpa retry/audit, race checkout, nilai historis berubah, workflow bermasalah. Semua P0 dan P1 dalam jalur wajib harus diselesaikan dan diuji ulang sebelum rekomendasi production-ready.

**Pemilik dependensi:** PM untuk urutan integrasi; pemilik Enrollment/School/Campus untuk keputusan akademik; tim SaaS untuk switching/context, subscription dan queue tenancy; Finance untuk reversal invoice; tim DevOps untuk staging/backup; pemilik Moodle untuk sandbox & kredensial; pemilik QA pendidikan untuk rubrik serta approval. Tidak ada hak melakukan perubahan atau deploy pada modul tim lain tanpa koordinasi.

**PR terkait:** #99 (promotion), #100 (rapor), #104 (GPA), #110 (Counseling), #111 (Clinic), #112 (Library), #113 (Moodle grade pull), #114 (Exam Campus grade bridge). Semuanya harus direview kompatibilitasnya dengan PR hardening Exam runtime sebelum penentuan staging candidate.

**Keputusan per 2026-10-23:** status akademik tetap **UAT PENDING / CROSS-TENANT STAGING PENDING**. Jangan ubah menjadi `PASS` tanpa bukti pengujian sesuai tabel.

## Eksekusi staging yang wajib (PR #118)

**Gate baru:** `.github/workflows/education-staging-uat.yml` dan `scripts/education/uat_staging.py`. Perintah CI PHPUnit/Pint di bagian atas hanya pemeriksaan regresi **di database test terisolasi**, **bukan** perintah menjalankan `migrate:fresh` atau PHPUnit pada database staging yang berisi data operasional.

### Persiapan lingkungan oleh operator berwenang

1. Pastikan staging FoundationOS yang **sesungguhnya** dan bukan URL produksi; dokumentasikan URL/identitas host hanya pada tiket terlindungi. Pastikan backup & rollback, sandbox mail/payment/Moodle, dan akun/objek uji sintetis Tenant A/B + Unit A1/A2 tersedia.
2. Deploy **kandidat integrasi yang telah direview** menggunakan prosedur DevOps. Di direktori staging yang terverifikasi, operator menjalankan **read-only** `git rev-parse HEAD`, kemudian mencatat full SHA dan tautan tiket bukti deployment. Tidak melakukan deployment dari pekerjaan UAT ini.
3. Siapkan GitHub Actions **environment bernama `education-staging`**, perlindungan reviewer, dan runner yang **betul-betul dapat mengakses staging** melalui HTTPS tervalidasi. Staging yang hanya bisa diakses lewat VPN dapat memakai pelaksana uji pada workstation/operator runner yang dipercaya; jangan membuka origin hanya demi GitHub Actions.
4. Konfigurasi variabel environment GitHub berikut setelah diverifikasi operator:
   - `EDUCATION_STAGING_URL` = **origin HTTPS** staging tanpa path/query
   - `EDUCATION_STAGING_DEPLOYED_SHA` = SHA penuh hasil pemeriksaan staging
   - `EDUCATION_STAGING_DEPLOYMENT_PROOF` = tautan/referensi bukti pada tiket terlindungi
   - `EDUCATION_STAGING_PROBES_JSON` = array JSON skenario GET/HEAD yang benar-benar tersedia dan cocok dengan tenant/role sintetis
5. Bila diperlukan, simpan authorization/cookie **akun uji staging** (bukan user produksi) sebagai secret `EDUCATION_UAT_AUTH_A` dan `EDUCATION_UAT_AUTH_B`. Nilai header harus lengkap (contoh `Bearer <token staging>`), jangan pernah menaruh token di URL, Git, artefak, atau komentar PR.
6. Operator akademik dan QA harus menjalankan **semua EDU-01…EDU-14 secara nyata** di UI/API/job/DB staging. Catat data sebelum/sesudah, isolasi lintas tenant, retry/replay, hasil, audit, timestamp dan bukti yang terlindungi. EDU-12/EducationQa dan EDU-13/Moodle **tetap BLOCKED** hingga persyaratan proses dan Moodle sandbox dipenuhi; tidak dapat ditandai PASS dari unit test.
7. Setelah **semua** benar-benar PASS, masukkan hasil UAT yang ditandatangani pemilik proses ke secret environment `EDUCATION_UAT_EVIDENCE_B64` sebagai JSON UTF-8 dalam base64. Jangan unggah bukti pribadi mentah ke Actions; isi JSON hanya rujukan tiket/tangkapan layar yang sudah disunting. GitHub workflow **tidak akan lulus** bila secret kosong atau ada EDU-xx tanpa bukti.
8. Dari Actions pilih `Education Staging UAT Evidence Gate` → **Run workflow** pada `main` dengan `candidate_sha` sama persis dengan SHA staging. **Jangan** checkout PR tidak direview di job yang memakai secrets. Catat URL run/artifak HTTP yang disunting dan approval owner. `PASS` workflow saja masih memerlukan pemeriksaan independen atas referensi bukti.

Contoh konfigurasi `EDUCATION_STAGING_PROBES_JSON` (ganti path sesuai hasil `route:list` kandidat staging, dan tambahkan pasangan positif/negatif nyata):

```json
[
  {
    "id": "EDU-04",
    "label": "school report own tenant",
    "kind": "positive",
    "method": "GET",
    "path": "/REPLACE_WITH_STAGING_SCHOOL_REPORT_ROUTE",
    "headers_from_env": {"Authorization": "EDUCATION_UAT_AUTH_A"},
    "expected_status": [200]
  },
  {
    "id": "EDU-04",
    "label": "school report cross-tenant denied",
    "kind": "negative",
    "method": "GET",
    "path": "/REPLACE_WITH_FOREIGN_TENANT_REPORT_ID",
    "headers_from_env": {"Authorization": "EDUCATION_UAT_AUTH_A"},
    "expected_status": [403, 404]
  }
]
```

Probe ini **read-only** dan hanya dapat membuktikan hasil HTTP pada endpoint yang dikonfigurasi. Tidak dapat menyimpulkan transaksi aman, penerimaan siswa hanya sekali, PDF sesuai periode, transaksi perpustakaan serentak, audit atau Moodle sinkron. Pengujian tersebut **wajib dieksekusi langsung oleh penguji** dalam sesi staging dan dibuktikan pada manifest.

Contoh bentuk bukti untuk **satu** kasus (ulangi EDU-01 s/d EDU-14 setelah pengujian nyata, bukan dengan menyalin contoh):

```json
{
  "environment": "education-staging",
  "deployed_sha": "40_CHARACTER_REAL_STAGING_COMMIT_SHA",
  "deployment_proof": "private-change-ticket-reference",
  "cases": [
    {
      "id": "EDU-01",
      "status": "PASS",
      "build_sha": "40_CHARACTER_REAL_STAGING_COMMIT_SHA",
      "positive": "private-positive-run-reference",
      "negative": "private-cross-tenant-rejection-reference",
      "database": "private-before-after-database-reference",
      "audit": "private-audit-trail-reference",
      "ticket": "private-evidence-ticket-reference",
      "tested_by": "assigned QA staff",
      "approved_by": "independent academic process owner",
      "executed_at": "2026-10-23T10:00:00+07:00"
    }
  ]
}
```

**Perintah CLI operator** (membaca bukti, tidak menulis DB):

```bash
python3 scripts/education/uat_staging.py probe \
  --spec /protected/education-staging-probe-spec.json \
  --output /protected/education-staging-http-results.json

python3 scripts/education/uat_staging.py verify \
  --evidence /protected/education-staging-evidence.json \
  --expected-sha "$(git rev-parse HEAD)"
```

**Perhatian:** Contoh JSON di atas **tidak** valid sebagai bukti kelulusan: hanya satu dari 14 kasus dan SHA placeholder. Status seluruh EDU-xx pada dokumen ini tidak boleh diedit menjadi PASS hanya karena gate otomatis tersedia.

### Hasil eksekusi hingga baseline dokumen

| Gerbang | Bukti yang ada | Status |
| --- | --- | --- |
| Pemeriksaan skrip/verifier pada PR #118 | CI/pull_request untuk kontrak kode, bukan staging | Verifikasi CI diperlukan |
| URL staging FoundationOS yang terverifikasi | Tidak tersedia di koneksi Education Engineer | BLOCKED |
| SHA kandidat terintegrasi yang telah terpasang | Belum ada bukti deployment staging | BLOCKED |
| Akses QA / akun sintetis Tenant A & B | Belum tersedia pada koneksi ini | BLOCKED |
| Eksekusi HTTP dan DB/queue/Livewire/Moodle staging | Belum dijalankan | NOT RUN |
| Bukti 14 skenario dan persetujuan owner | Belum ada | BLOCKED |
| Keputusan produksi | Tidak ada sign-off UAT | **NO-GO** |
