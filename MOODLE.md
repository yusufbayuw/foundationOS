# Moodle Setup Guide for FoundationOS (FOS)

Panduan ini fokus pada setup Moodle baru agar integrasi dengan FOS langsung siap dipakai, terutama untuk skenario **multi-tenant**.

## 1) Arsitektur Integrasi

Model integrasi yang dipakai:

- `FOS -> Moodle` untuk master data (`User`, `Course`, `Enrollment`)
- Outbox + Queue untuk reliabilitas (retry, idempotent)
- `Moodle -> FOS` untuk sinyal/pull data learning (grade/progress/attendance) dan reconcile drift

Source of truth tetap di FOS.

## 2) Model Multi-Tenant di Moodle (Yang Dipakai FOS)

FOS mengimplementasikan multi-tenancy Moodle secara **logical partitioning** dalam 1 instance Moodle:

- Tenant FOS -> Moodle Course Category  
  `idnumber = fos_tenant_{tenant_id}`
- Tenant FOS -> Moodle Cohort  
  `idnumber = fos_cohort_tenant_{tenant_id}`
- User FOS -> Moodle User (global)  
  `idnumber = fos_user_{user_id}`
- Course FOS -> Moodle Course  
  `idnumber = fos_course_{course_id}`
- Enrollment ditentukan oleh mapping kelas FOS ke course Moodle pada tabel `moodle_class_course_mappings`

Catatan penting:

- User di Moodle bersifat global (bukan per-tenant table fisik), tetapi partition membership dilakukan via cohort + category + enrollment mapping.

## 3) Prasyarat Moodle

Lakukan di Moodle baru:

1. Aktifkan Web Services.
2. Aktifkan REST protocol.
3. Buat service account integrasi (disarankan akun khusus, bukan akun personal).
4. Buat External Service untuk FOS.
5. Generate token untuk service account tersebut.

## 4) Fungsi Web Service yang Wajib

Minimal function:

- `core_webservice_get_site_info`
- `core_user_create_users`
- `core_user_update_users`
- `core_user_get_users_by_field`
- `core_course_create_categories`
- `core_course_update_categories`
- `core_course_get_categories`
- `core_course_create_courses`
- `core_course_update_courses`
- `core_course_get_courses_by_field`
- `enrol_manual_enrol_users`
- `enrol_manual_unenrol_users`
- `core_cohort_create_cohorts`
- `core_cohort_add_cohort_members`
- `core_cohort_get_cohorts`
- `core_cohort_search_cohorts`
- `core_calendar_create_calendar_events`
- `gradereport_overview_get_course_grades`
- `core_completion_get_course_completion_status`
- `core_completion_get_activities_completion_status`

Opsional (jika plugin attendance terpasang):

- `mod_attendance_get_courses_with_today_sessions`
- `mod_attendance_get_user_absences`

## 5) Bootstrap Otomatis Service Moodle

Di server FOS, jalankan:

```bash
php scripts/setup_moodle_foundation_service.php
```

Script akan:

- menemukan token dari `.env`
- menemukan external service token tersebut
- menambahkan function yang diperlukan ke external service
- menghubungkan user ke service jika `restrictedusers=1`

Jika function attendance tidak ada di Moodle, script akan skip otomatis (tidak gagal total).

## 6) Konfigurasi FOS (.env)

Pastikan konfigurasi berikut:

```bash
MOODLE_SYNC_ENABLED=true
MOODLE_SYNC_READONLY=false
MOODLE_BASE_URL=https://your-moodle-host
MOODLE_WS_TOKEN=your_token
MOODLE_WS_FORMAT=json
MOODLE_TIMEOUT=15
MOODLE_VERIFY_SSL=true
MOODLE_CATEGORY_PARENT_ID=
MOODLE_ENROL_ROLE_ID=5
MOODLE_COHORT_SYNC_ENABLED=true
MOODLE_ROLE_STUDENT=5
MOODLE_ROLE_TEACHER=3
MOODLE_ROLE_ASSISTANT_TEACHER=4
MOODLE_ROLE_MANAGER=1
MOODLE_CALENDAR_SYNC_ENABLED=true
MOODLE_LEARNING_PULL_ENABLED=true
MOODLE_ATTENDANCE_PULL_ENABLED=false
MOODLE_SYNC_QUEUE=moodle-sync
MOODLE_SYNC_MAX_ATTEMPTS=7
MOODLE_SYNC_BATCH_LIMIT=100
```

## 7) Checklist Go-Live di Moodle Baru

Urutan rekomendasi:

1. `php artisan config:clear`
2. `php artisan fos:moodle:health-check`
3. `php artisan fos:moodle:backfill all --tenant=<id>`
4. `php artisan fos:moodle:drain-outbox --limit=200`
5. Jalankan worker:  
   `php artisan queue:work --queue=moodle-sync,default`
6. Validasi cohort:  
   `php artisan fos:moodle:sync-cohorts --tenant=<id>`
7. Validasi drift:  
   `php artisan fos:moodle:reconcile all --dry-run`
8. Pantau status:  
   `php artisan fos:moodle:ops-report`

## 8) Operasional Harian

Command utama:

- `php artisan fos:moodle:health-check`
- `php artisan fos:moodle:drain-outbox --limit=100`
- `php artisan fos:moodle:retry-failed --limit=100`
- `php artisan fos:moodle:reconcile all --dry-run --limit=500`
- `php artisan fos:moodle:ops-report --json`

Scheduler yang sudah aktif:

- `drain-outbox` tiap menit
- `reconcile --dry-run` tiap jam

## 9) Kebijakan Inbound (Moodle -> FOS)

Kebijakan default: **strict source-of-truth**.

- Jika data master di Moodle berubah manual (contoh user terhapus), FOS tidak ikut menghapus.
- Drift dideteksi lewat `reconcile`.
- Dengan `--fix`, FOS akan enqueue remediasi untuk memulihkan state di Moodle.

## 10) Review Integrasi Existing: Sudah Multi-Tenant atau Belum?

Status saat ini: **sudah multi-tenant secara logical di Moodle**, dengan catatan:

Yang sudah benar:

- Tenant dipisahkan lewat category (`fos_tenant_{id}`).
- Membership tenant dipisahkan lewat cohort (`fos_cohort_tenant_{id}`).
- Course sinkron ke category tenant yang sesuai.
- Enrollment memakai mapping `tenant_id + class_id` -> course Moodle.
- Outbox/mapping tabel di FOS sudah tenant-aware.

Batasan yang perlu dipahami:

- Ini bukan isolasi fisik per-tenant Moodle database/site terpisah.
- User Moodle tetap global; isolasi tenant bergantung pada category/cohort/enrollment governance.
- Reconcile inbound saat ini fokus user/course (belum enrollment-level reconciliation dua arah penuh).
- Attendance pull tergantung plugin Moodle attendance.

## 11) Course Offering Sync (Per-Semester Moodle Course)

Sejak Roadmap Epic 6 Fase 6.1, FOS membuat **satu Moodle course per `CourseOffering`** (mata kuliah ditawarkan per semester), bukan per `Course` master. Konvensi idnumber:

| FOS entity | Moodle entity | idnumber |
|---|---|---|
| `Tenant` | Category root tenant | `fos_tenant_{id}` |
| `Tenant` (template root, opsional) | Sub-category templat | `fos_template_{tenant_id}` |
| `Tenant + AcademicPeriod` | Sub-category semester | `fos_semester_t{tenant}_p{period}` |
| `Course` (master, legacy) | Course | `fos_course_{id}` |
| `CourseOffering` | Course per-semester | `fos_offering_{id}` |

Alur:

1. Tenant menambahkan/mengubah `CourseOffering` → `CourseOfferingObserver` enqueue outbox `course_offering` action `upsert`.
2. Worker mengeksekusi `MoodleSyncService::syncCourseOfferingOutbox`:
   - Pastikan kategori tenant ada (`fos_tenant_{id}`).
   - Pastikan kategori semester ada (`fos_semester_t{tenant}_p{period}`).
   - Buat/Update Moodle course dengan idnumber `fos_offering_{id}`, set `startdate`/`enddate` dari `AcademicPeriod`.
   - Tulis ke `MoodleEntityMapping` entity_type `course_offering`.
3. `resolveMoodleCourseIdForOffering` (dipakai oleh enrollment & lecturer assignment) prefer mapping `course_offering`; fallback ke mapping `course` master untuk data lama yang belum dimigrasi.
4. Status `cancelled`/`archived`/`closed` → outbox action `deactivate` → Moodle course di-set `visible=0`.

Prerequisite (POC): `course_prerequisites` di FOS disisipkan ke `summary` Moodle course offering sebagai daftar teks "Prerequisites (FOS-managed):". Restrict access otomatis tidak diimplementasi karena keterbatasan Moodle web service — dipertimbangkan via plugin terpisah jika diperlukan.

Trade-off: jumlah course Moodle bertambah linier dengan semester × offering. Untuk universitas dengan 1000+ offering/semester, pastikan Moodle DB dan kapasitas storage memadai.

## 12) Lecturer Assignment (Multi-Teacher per Course Offering)

Sejak Roadmap Epic 6 Fase 6.3, FOS mendukung penugasan **banyak dosen** ke satu `CourseOffering` (mata kuliah ditawarkan per semester) dengan dua peran:

| FOS role (`course_offering_lecturers.role`) | Moodle role shortname | Default role id | Hak akses |
|---|---|---|---|
| `primary` | `editingteacher` | `MOODLE_ROLE_TEACHER=3` | Dosen pengampu utama, bisa edit konten kursus. |
| `assistant` | `teacher` (non-editing) | `MOODLE_ROLE_ASSISTANT_TEACHER=4` | Asisten/co-teacher, akses melihat & menilai tanpa edit struktur. |

Alur sinkronisasi:

1. Admin/Kaprodi menambahkan dosen ke `CourseOffering` via Filament panel (relation manager "Lecturers" pada halaman CourseOffering).
2. `CourseOfferingLecturerObserver` enqueue entri `moodle_sync_outbox` dengan `entity_type=lecturer_assignment`, action `assign`/`unassign`.
3. Worker `queue:work --queue=moodle-sync` memproses outbox → `MoodleSyncService::syncLecturerAssignmentOutbox()`:
   - Upsert user Moodle untuk dosen (idnumber `fos_user_{id}`).
   - Resolve Moodle course id via mapping `course` (fallback: `core_course_get_courses_by_field` dengan idnumber `fos_course_{course_id}`). Saat Fase 6.1 diaktifkan, akan ditambah resolusi via `moodle_offering_id`.
   - Pastikan keanggotaan cohort tenant.
   - `enrol_manual_enrol_users` dengan `roleid` sesuai (3 atau 4).
4. Penghapusan assignment menghasilkan `enrol_manual_unenrol_users` lewat outbox action `unassign`.

Idempotency dijaga via unique index `(course_offering_id, lecturer_id)` di pivot, dan dedupe-key outbox `lecturer_assignment:{id}:{action}:{updated_at}`.

## 13) Guardrail untuk Tim Operasional Moodle

Agar tidak terjadi kebocoran antar-tenant:

1. Jangan edit/hapus entity ber-`idnumber` prefix `fos_` secara manual.
2. Jangan pindahkan course FOS keluar category tenant.
3. Hindari manual enroll lintas tenant di Moodle.
4. Terapkan SOP: perubahan master dilakukan di FOS, bukan Moodle.
5. Pantau rutin `ops-report` + `reconcile --dry-run`.

