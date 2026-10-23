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
