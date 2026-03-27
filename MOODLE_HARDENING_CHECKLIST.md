# Moodle Hardening Checklist for FOS Multi-Tenancy

Checklist ini dipakai sebelum go-live dan saat audit berkala untuk integrasi FOS <-> Moodle pada mode shared Moodle instance.

## 1) Security Baseline

- [ ] HTTPS aktif end-to-end (`MOODLE_BASE_URL` pakai `https://`)
- [ ] Sertifikat valid, tidak bypass SSL (`MOODLE_VERIFY_SSL=true`)
- [ ] Cron Moodle aktif dan sehat
- [ ] Update keamanan Moodle terbaru sudah terpasang
- [ ] Plugin pihak ketiga direview dan minimum

## 2) Integration Service Account

- [ ] Gunakan akun integrasi khusus (bukan akun personal)
- [ ] Password akun integrasi kuat dan disimpan di secret manager
- [ ] Akun integrasi tidak dipakai login harian admin
- [ ] Token web service hanya untuk External Service FOS
- [ ] Jika memungkinkan, token di-rotate berkala (misal 90 hari)

## 3) External Service Scope (Least Privilege)

- [ ] Hanya function yang dibutuhkan FOS yang diizinkan
- [ ] Function destructive yang tidak dipakai tidak diberikan
- [ ] `restrictedusers=1` dan service user hanya akun integrasi
- [ ] `requiredcapability` service sesuai kebutuhan minimal
- [ ] Validasi daftar function pakai:
  - `php scripts/debug_moodle_token.php`
  - `php artisan fos:moodle:health-check`

## 4) Tenant Isolation (Logical)

- [ ] Setiap tenant FOS punya category Moodle `fos_tenant_{tenant_id}`
- [ ] Setiap tenant FOS punya cohort Moodle `fos_cohort_tenant_{tenant_id}`
- [ ] Course FOS selalu berada pada category tenant yang benar
- [ ] Enrollment menggunakan mapping `tenant_id + class_id` yang valid
- [ ] Tidak ada manual enroll lintas tenant tanpa approval

## 5) Naming & Identity Contract

- [ ] User Moodle hasil FOS selalu punya `idnumber = fos_user_{id}`
- [ ] Course Moodle hasil FOS selalu punya `idnumber = fos_course_{id}`
- [ ] Category tenant selalu `idnumber = fos_tenant_{id}`
- [ ] Cohort tenant selalu `idnumber = fos_cohort_tenant_{id}`
- [ ] Tim Moodle tidak mengubah `idnumber` dengan prefix `fos_`

## 6) Data Governance

- [ ] Master data user/course hanya diubah dari FOS
- [ ] Perubahan manual di Moodle dianggap drift, ditangani reconcile
- [ ] Soft delete policy dijaga (suspend/hide/unenroll, bukan hard delete)
- [ ] SOP incident jelas jika ada data `fos_` terhapus manual di Moodle

## 7) Queue & Reliability

- [ ] Queue worker aktif: `php artisan queue:work --queue=moodle-sync,default`
- [ ] Scheduler aktif: `php artisan schedule:work`
- [ ] Outbox backlog dipantau harian (`ops-report`)
- [ ] Retry gagal dijalankan (`retry-failed`) bila diperlukan
- [ ] Tidak ada outbox `failed` menumpuk > SLA internal

## 8) Monitoring & Alerting

- [ ] `php artisan fos:moodle:ops-report --json` diintegrasikan ke monitoring
- [ ] Alarm jika:
  - outbox pending > threshold
  - outbox failed > 0 selama X menit
  - health-check FAIL
  - reconcile drift meningkat tiba-tiba
- [ ] Log integration disimpan dan mudah ditelusuri

## 9) Reconcile & Drift Control

- [ ] Reconcile audit berjalan berkala (hourly/daily):
  - `php artisan fos:moodle:reconcile all --dry-run --limit=500`
- [ ] Prosedur fix drift disepakati:
  - `php artisan fos:moodle:reconcile all --fix --limit=500`
- [ ] Semua perubahan fix via outbox (audit trail tetap ada)

## 10) UAT Before Go-Live

- [ ] Create user di FOS -> muncul di Moodle
- [ ] Update user di FOS -> update di Moodle
- [ ] Soft delete user di FOS -> Moodle `suspended=1`
- [ ] Create course di FOS -> muncul di category tenant benar
- [ ] Soft delete course di FOS -> Moodle `visible=0`
- [ ] Enrollment active -> user enrolled ke course yang benar
- [ ] Unenroll path (exit/nonaktif/delete) -> enrollment terhapus di Moodle
- [ ] Reconcile mendeteksi dan memperbaiki drift uji coba

## 11) Multi-Tenant Risk Register (Shared Moodle)

- [ ] Risiko kebocoran lintas tenant didokumentasikan
- [ ] Control owner jelas (ops Moodle vs ops FOS)
- [ ] Approval matrix untuk perubahan category/cohort/enrollment manual
- [ ] Review akses admin Moodle berkala (least privilege)

## 12) Production Readiness Gate

Go-live hanya jika semua kondisi ini terpenuhi:

- [ ] `fos:moodle:health-check` = semua mandatory check `OK`
- [ ] Outbox `failed = 0`
- [ ] Reconcile drift kritis = 0
- [ ] UAT lifecycle utama lulus
- [ ] SOP incident + rollback disetujui tim

