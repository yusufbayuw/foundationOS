# Exam Module — MVP Runbook

FoundationOS Exam module (`Modules/Exam`) provides bilingual Filament admin for question banks, exam definitions, participant tokens, Cloudflare runtime publish, result sync, manual essay grading, analytics, and control room access.

All **Exam domain primary keys are UUID strings** (see [UUID strategy](#uuid-strategy)).

---

## 1. Setup module

1. Ensure the Exam module is enabled (`php artisan module:list` — look for `Exam`).
2. Run migrations: `php artisan migrate`
3. Seed demo tenant + exam data: `php artisan db:seed` (runs `MvpDemoSeeder` → `ExamMvpDemoSeeder`)
4. Provision Shield roles/permissions per tenant:
   ```bash
   php artisan exam:provision-security
   php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
   php artisan shield:super-admin --user=1 --tenant=1 --panel=admin
   php artisan optimize:clear
   ```
5. Open Filament admin: `/admin` → tenant **Foundation Demo** → **Exam** navigation group.

---

## 2. UUID strategy

| Layer | Rule |
|--------|------|
| Exam tables | `$table->uuid('id')->primary()` — no `$table->id()` / `bigIncrements` |
| Models | Extend `ExamModel` → `HasExamUuid` (non-incrementing string keys) |
| Runtime sync | `participant_external_id` / `foundation_id` use FOS UUIDs, **not** participant tokens |
| Audit logs | `audit_logs.auditable_id` is UUID-capable (`nullableUuidMorphs`) |
| Permissions | Spatie/Shield names are strings; unrelated to row PK type |

Demo UUIDs (stable for docs/tests) are defined in `ExamMvpDemoSeeder` constants.

---

## 3. Environment — Cloudflare Runtime

Add to `.env`:

```env
EXAM_RUNTIME_BASE_URL=https://your-exam-runtime.example.com
EXAM_RUNTIME_API_KEY=your-api-key
EXAM_RUNTIME_HMAC_SECRET=optional-hmac-secret
EXAM_RUNTIME_WEBHOOK_SECRET=optional-webhook-secret
EXAM_RUNTIME_TIMEOUT=30

EXAM_CONTROL_ROOM_BASE_URL=https://your-control-room.example.com
```

Config file: `Modules/Exam/config/exam.php`.

---

## 4. Create a question bank

1. **Exam → Question banks → Create**
2. Set **Academic context**: School, Campus, or Standalone
3. For Standalone/OSN: fill **Standalone subject** and **Level**
4. Save (status **Active**)

---

## 5. Import questions

**CSV import** (Question bank → Import):

- Template: `Modules/Exam/resources/import/exam_question_import_demo.csv`
- Replace `__BANK_UUID__` with the bank’s UUID
- Required columns: `question_bank_uuid`, `academic_context_type`, `type`, `question_text`, `correct_answer`, `score`, etc.

**Manual:** Exam → Questions → Create, or attach from bank when building an exam.

Lint: `composer run lint:translations` after UI changes.

---

## 6. Create an exam

1. **Exam → Exam definitions → Create**
2. Choose context (School / Campus / Standalone) and link references (class, course, subject, etc.)
3. **Questions** tab: add questions from banks (builder)
4. **Mark as ready** when at least one question is attached

---

## 7. Generate participant tokens

1. Open exam → **Participants** tab
2. **Generate from class** (School) or **Generate from course class** (Campus), or **Add participant** / **Import CSV**
3. Per row: **Regenerate token** (requires `manage_exam_token`)
4. **Export tokens CSV** or **Print token cards** (PDF route, auth required)

CSV template: `Modules/Exam/resources/import/exam_participant_import_template.csv`

---

## 8. Publish to Cloudflare Runtime

1. Exam must be **Ready** with questions attached
2. **Publish to runtime** on the exam view page
3. On success, `runtime_exam_id` (UUID) is stored; status → **Published**
4. Optional: **Republish**, **Sync participants only**, **Sync admin access**

Permissions: `publish_exam`, Shield `Update:ExamDefinition`.

---

## 9. Gradebook export (School / Campus)

Exam results can feed academic gradebooks when the target module is available:

| Context | Target model | Requirement |
|---------|--------------|-------------|
| **School** | `StudentGrade` → `Assessment` | Exam context School, `school_assessment_id` set, participant linked via `school_student_reference` |
| **Campus** | `StudyResult` → `StudyPlanItem` | Exam context Campus, participant `context_reference_type` = `study_plan_item`, `grade_sync_target` component key (default `final`) |

**Manual push (Filament):** Exam view → **Push to School Gradebook** or **Push to Campus Gradebook** (permission `push_exam_gradebook`). Each row is logged in **Gradebook export logs** (UUID id, target module, integer `target_reference_id` + `target_reference_type` when School/Campus PKs are bigint).

**Automatic (optional):** Set `grade_sync_mode` to `push_to_student_grade` (School) or `push_to_study_result` (Campus) on the exam; runtime attempt ingest still uses `SchoolGradeBridgeService` / `CampusGradeBridgeService`.

**Deferred integration:** If tables are missing or links are incomplete, FOS dispatches domain events instead of forcing writes:

- `ExamResultPublished` — exam published to runtime (`exam_id` UUID)
- `ExamResultSynced` — after runtime result sync or skipped export (`exam_id`, `result_id`, `participant_id` UUIDs)
- `ExamResultGraded` — after manual essay grading updates a result

Subscribe from School/Campus modules when gradebook APIs stabilize.

---

## 10. Sync results

1. After runtime has attempts, click **Sync results** (or `php artisan exam:sync-results {examUuid}`)
2. Fills **Attempts**, **Answers**, **Results**, **Activity logs** tabs
3. Sync is **idempotent** (same runtime IDs upsert)

Permission: `sync_exam_result`.

---

## 11. Control Room

1. Assign proctors: exam `metadata_json.proctor_user_ids` = array of user IDs (demo seeder sets admin)
2. User needs role **proctor** (or exam_admin) + permission `open_control_room` + assignment
3. Exam view → **Open control room** (audited, opens `EXAM_CONTROL_ROOM_BASE_URL/exams/{examUuid}`)

---

## 12. CSV import formats

**Participants** (`exam_participant_import_template.csv`):

| Column | Description |
|--------|-------------|
| `student_name` | Display name |
| `student_identifier` | NIS / NIM / external id |
| `email` | Optional |

**Questions** (`exam_question_import_demo.csv`): see file header; must include `question_bank_uuid` and `academic_context_type`.

---

## 13. OSN Prep usage

1. Create **Standalone** question bank with subject/level (e.g. Physics, OSN Regional)
2. Import OSN-style items (see demo CSV)
3. Create exam: context **Standalone**, type **OSN prep**
4. Use analytics tab for **ranking**, **readiness**, **weak topics**, **tryout summary**
5. Permission `manage_osn_prep` for dedicated OSN coordinators (assigned via Shield role)

---

## 14. Troubleshooting

| Issue | Check |
|--------|--------|
| Exam list empty for teacher | Role + `school_teacher_reference` / scoped query |
| Publish fails | `EXAM_RUNTIME_*` env, exam Ready, ≥1 question, logs in `exam_runtime_sync_logs` |
| Sync 0 results | `runtime_exam_id` set; runtime API reachable; participant `id` matches `participant_external_id` |
| Token clash | Regenerate; uniqueness is per exam |
| 403 on PDF export | `export_exam_result` + `view` on exam |
| Control room denied | `proctor_user_ids` contains user id + `open_control_room` |
| Gradebook push skipped | Link `school_assessment_id` / study plan item; check **Gradebook export logs** |
| Tenant leakage | Wrong tenant selected in Filament; `TenantScope` + policies |

**Commands**

```bash
php artisan exam:sync-results
php artisan exam:provision-security
php artisan fos:workflow:health-check   # unrelated; exam has no debug routes in production
```

**Tests (MVP suite)**

```bash
php artisan test --compact tests/Feature/ExamMvpFoundationTest.php
php artisan test --compact tests/Feature/ExamGradebookExportTest.php
php artisan test --compact tests/Unit/ExamUuidMigrationAuditTest.php
```

---

## Security notes

- Web routes: `/admin/exams/{exam}/participant-tokens.pdf` and `results.pdf` — **auth middleware only** (no public debug routes).
- API: `POST /api/exam/runtime/attempts` — Sanctum + webhook validation.
- Assign roles: `exam_admin`, `teacher`, `lecturer`, `proctor`, `viewer`, `super_admin` (Shield, per tenant).

---

## Related docs

- `CLAUDE.md` — architecture, Shield, translations
- `MOODLE.md` — separate Moodle integration (not Exam runtime)
