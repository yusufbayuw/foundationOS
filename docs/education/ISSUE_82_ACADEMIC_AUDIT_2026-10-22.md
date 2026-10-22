# Academic domain (Issue #82) — preliminary code audit and pilot gate

**Date:** 2026-10-22  
**Baseline:** `main` as inspected on 2026-10-22  
**Owner:** Education Systems Engineering  
**Status:** Code inventory and targeted findings; **not** staging UAT or production sign-off.

## Scope and evidence

This review inspected the manifests, file trees, representative models, policies, business services, migrations and existing `tests/` for School, Campus, Enrollment, Exam, Library, EducationQa, Counseling and Clinic. The repository contains significant implementation beyond scaffolding. Module-local `tests/Feature` directories are mostly placeholders, but root-level `tests/Feature`, `tests/Tenant`, `tests/Queue` and `tests/Permission` provide targeted coverage. Presence of tests does not establish passing CI or functional acceptance.

| Module | Existing implementation evidence | Unproven / high-risk acceptance gate | Priority |
| --- | --- | --- | --- |
| Enrollment | Applicant / Registration / AdmissionPeriod, acceptance events, school promotion, exam schedules and result models, invoice integration | Duplicate/out-of-order acceptance queue deliveries; mode-sensitive School versus Campus conversion; manual reversal and paid invoice handling; user-facing registration UAT | P0 |
| School | Students, class membership, schedules, attendance, assessments, grades, report-card PDF services | Class membership matched to requested period; incomplete/ungraded marks must not be represented as zero; grade release and immutable history; parent access boundaries | P1 |
| Campus | Course/offering, StudyPlan / StudyResult, GPA calculator and transcript, Moodle-focused tests | Publish-only transcript/GPA, repeated-course rules, KRS capacity and prerequisites, advising/registration status, cross-tenant grade attribution | P1 |
| Exam | Definition/publishing, participant, attempt, grading, runtime/gradebook integration, result export | Concurrent attempts and callback replay, grading authorization, frozen published definition, exam runtime staging integration | P1 |
| Library | BookCopy, reservations, loans, fines, policies, public OPAC | Atomic copy checkout/return under concurrency, stock reconciliation, active-loan uniqueness, fee policy, tenant separation | P1 |
| EducationQa | Standards, accreditation cycles, surveys, improvement plans | Evidence provenance, approval ownership, locked audit history, measurable improvement closeout | P2 |
| Counseling | Confidential notes, cases, sessions, anonymous reports, policies | Confidential row-level visibility **including index, export and bulk actions**, counselor ownership, minors' privacy and retention | P0 |
| Clinic | HealthRecord, consent, allergies, visits, medication records and policies | Health-information least privilege, consent, field-level sensitive data access, tenancy and exports, retention/audit | P0 |

## Verified code-level findings

1. **Enrollment promotion**: Before this PR, `ApplicantPromotionService::promote()` checked `converted_to_student_id` before a row lock; a pair of simultaneously executing jobs could both create a `Student`. The same service could act on a stale accepted event after a decision was reverted, and returned a linked student without checking that student's tenant. This PR serializes the check/write around the applicant row, re-reads status from the database and resolves existing links within the tenant. This fixes the single-applicant race on DB engines with row locking, but does **not** prove concurrency on every production engine.
2. **School report card**: `ReportCardService` used the first class assignment regardless of academic period and converted a null score to zero. This may misrepresent a historical class/grade. Track a separate period-correctness PR.
3. **Campus**: `CampusGpaCalculator` currently includes any associated non-null `grade_point` while `TranscriptDocumentService` explicitly selects published results; audit whether a draft grade can affect cached GPA. Track separately from grading publication changes.
4. **Counseling / Clinic**: `CounselingNotePolicy` checks confidentiality for individual records, but offers `DeleteAny` and `ForceDeleteAny` based only on broad permissions. `HealthRecordPolicy` checks permission names but not per-record medical access; review resource queries, export paths, authorization and retention **before** pilot activation.
5. **Moodle**: Outbox retry, atomic claim, grade pulling, course sync and enrollment drift tests exist. Live Moodle configuration, callback idempotency, operational retries, reconciliation and tenant A/B testing remain external integration gates.

These are *scoped findings*, not a complete audit of every controller, integration or authorization surface.

## Admissions promotion PR acceptance criteria

- [x] Acceptance re-read occurs under a database row lock; repeat deliveries use the existing student link.
- [x] Reverted/non-accepted applications cannot create a student from delayed events.
- [x] Existing links cannot return a student belonging to another tenant.
- [x] No history rewrite, destructive migration, billing or Finance changes.
- [ ] Targeted automated tests confirmed passing in CI.
- [ ] MySQL/PostgreSQL parallel queue delivery test and transaction rollback.
- [ ] Two-tenant staging verification with accepted, reverted, duplicate and malformed linked records.
- [ ] Owner/approver signs off mode-specific Campus admissions conversion.
- [ ] Rollback strategy and database backup/restore validated.

## Golden paths and UAT acceptance targets

**K-12:** school-oriented admission → accepted → new school student → academic year/class → timetable → attendance/assessments → approved/report-card export → authorized parent communication.

**Higher education:** academic intake → admitted student → program and advisor → courses/offerings → KRS/registration → Moodle enrollment and grades → approved final results → KHS/transcript. Campus must not inherit School-specific conversion merely because it uses the same applicant model.

**Supporting operations:** physical-copy checkout/return/reservation/fine with concurrency; confidential counselor case/notes with negative actor tests; clinic visit/consent and medical records with least privilege; quality indicator/evidence/approval/closeout without silent historical changes.

For each path require: authorized actor + unauthorized actor + tenant A/B + duplicate submission + cancellation/reversal + queue retry + audit event + export/download + browser/Filament/mobile UX evidence, with owner and approver named in the UAT record.

## Cross-team coordination and remaining blockers

- **Finance (#81):** admissions fees, paid invoices, refunds and reversal rules; no unilateral changes.
- **Tenancy (#76):** row-level roles/membership, tenant switch, queued jobs, private files and exports.
- **Security (#75):** student/guardian identity, medical and counseling access, leaked fields, MFA.
- **DevOps / release (#72–#74):** staging topology, backups, migration rehearsal, deployment SHA and UAT signoff.
- **Moodle:** actual Moodle tenant/course configuration and sandbox credentials required for contract and reconciliation testing; do not mark synchronized from unit tests alone.

**Release rule:** Keep #82 open and modules in controlled-pilot candidate state until end-to-end staging evidence, independently reviewed permissions, relevant CI, approved business rules and rollback evidence are all documented. Do not merge this PR automatically.
