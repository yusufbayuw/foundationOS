# Fase 1.2 — Visual Workflow Designer (Full Scope)

Dokumen ini merencanakan implementasi **lengkap** Fase 1.2 dari [ROADMAP.md](ROADMAP.md): Visual Workflow Designer dengan drag-drop, versioning otomatis, dan import/export JSON. **Status: selesai 2026-06-03** — lihat `WORKFLOW.md` dan test `WorkflowDesigner*`.

---

## 1. Konteks & Tujuan

Saat ini definisi workflow hanya bisa dibuat lewat seeder/command (`SetupBudgetWorkflowCommand`, `SetupProcurementWorkflowPilotCommand`). Tenant admin non-teknis tidak punya cara mendesain workflow sendiri.

**Tujuan Fase 1.2.** Memberi tenant admin halaman visual untuk:
- Membuat & mengedit workflow (step + transition + gateway) lewat drag-drop canvas.
- Menyimpan sebagai draft tanpa mempengaruhi instance running pada versi aktif.
- Publish draft → versi aktif baru (memanfaatkan `WorkflowDefinitionLifecycleService` yang sudah ada).
- Export/import definisi sebagai JSON untuk template sharing antar tenant.

---

## 2. Hasil Survei Modul (Baseline)

### Yang sudah ada (tidak perlu dibuat ulang)

| Komponen | Status | Catatan |
|---|---|---|
| `Workflow` model + tabel `workflows` | ✅ | kolom `version`, `status` (draft/active/archived), `is_active`, `published_at` |
| `WorkflowStep` + tabel `workflow_steps` | ✅ | sudah termasuk `gateway_type`, `quorum_strategy`, `quorum_value` dari Fase 1.1, plus `form_schema`, `action_schema`, `sla_hours` |
| `WorkflowTransition` + tabel `workflow_transitions` | ✅ | sudah punya `condition_rules` (JSON Logic), `priority`, `is_default` |
| `WorkflowDefinitionLifecycleService` | ✅ | sudah punya `publish()`, `archive()`, `duplicateAsNewVersion()` |
| Snapshot mechanism (`DatabaseWorkflowInstanceStarter`) | ✅ | instance lama tetap punya `workflow_snapshot` — aman saat versi baru di-publish |
| `WorkflowFormSchemaValidator` | ✅ | bisa dipakai validate form_schema saat designer save |
| Filament `WorkflowResource` + relation managers | ✅ | tetap dipertahankan untuk power user — designer adalah _alternative UI_, bukan pengganti |
| Vite + Tailwind 4 | ✅ | siap untuk bundle canvas lib |

### Yang harus dibuat

- Halaman Filament baru (`WorkflowDesignerPage`).
- Livewire component (`WorkflowCanvas`) dengan integrasi JS canvas.
- Kolom `canvas_position` (json) di `workflow_steps` — `{x: number, y: number}` per node.
- Service export/import (`WorkflowDefinitionPorter` atau setara).
- Endpoint Livewire untuk operasi CRUD step/transition dari canvas.

---

## 3. Keputusan Arsitektur

### 3.1 Canvas library

**Pilihan: [Cytoscape.js](https://js.cytoscape.org/)** (~400KB minified+gzipped).

Alasan:
- Pure JS — kompatibel dengan Filament+Livewire tanpa butuh Inertia/React island.
- Built-in drag, zoom, pan, layout algorithms (dagre via `cytoscape-dagre`).
- Bisa di-init dari Alpine.js dalam Livewire blade.
- Lebih ringan dari react-flow (~500KB+) dan tidak butuh React runtime.
- Aktif dimaintain, lisensi MIT.

**Alternatif yang ditolak:**
- `react-flow` — butuh React runtime, menambah ~150KB + kompleksitas mounting di Livewire.
- `Mermaid.js` — read-only, tidak interaktif untuk drag-drop.
- `JointJS` — komersial untuk fitur lengkap.

**Lazy-loading:** import Cytoscape hanya di entry-point `resources/js/workflow-designer.js`, di-load via Vite `@vite('resources/js/workflow-designer.js')` di blade halaman designer saja. Tidak masuk bundle utama Filament.

### 3.2 Tidak bikin `WorkflowVersionSnapshotService` baru

Roadmap menyebut "service baru: `WorkflowVersionSnapshotService` — auto-create draft version saat designer disimpan". Tapi `WorkflowDefinitionLifecycleService::duplicateAsNewVersion()` sudah melakukan ini persis. **Reuse, jangan duplikasi.** Designer cukup memanggil `duplicateAsNewVersion()` saat pertama kali edit workflow yang sudah `active`.

### 3.3 Tidak ganti relation manager existing

`WorkflowResource` dengan `StepsRelationManager` & `TransitionsRelationManager` tetap dipertahankan sebagai _form-based fallback_ untuk:
- Power user yang lebih nyaman edit struktur via form.
- Edge case yang sulit di canvas (mis. `action_schema` JSON yang kompleks).
- Akses dari mobile/screen reader (canvas SVG kurang aksesibel).

Designer adalah **opt-in alternative**, bukan pengganti.

### 3.4 Position persistence

Tambah kolom `canvas_position` (json nullable) di `workflow_steps`. Default null → designer auto-layout dengan dagre saat first render. Setelah user drag node, simpan `{x, y}` per step.

Tidak perlu kolom posisi untuk transition — Cytoscape auto-route edge antara source & target node.

### 3.5 Workflow JSON shape (export/import)

Format JSON canonical untuk export:

```json
{
  "schema_version": "1.0",
  "exported_at": "2026-05-22T10:00:00Z",
  "workflow": {
    "code": "procurement_pilot",
    "name": "Procurement Approval (Pilot)",
    "description": "...",
    "config": { ... }
  },
  "steps": [
    {
      "uuid": "01HXXXX...",
      "code": "manager_review",
      "name": "Manager Review",
      "step_type": "approval",
      "gateway_type": "none",
      "quorum_strategy": null,
      "quorum_value": null,
      "assignee_type": "user",
      "assignee_value": "{{manager_user_id}}",
      "assignee_config": null,
      "form_schema": [...],
      "action_schema": null,
      "sla_hours": 48,
      "is_initial": true,
      "is_terminal": false,
      "sort_order": 1,
      "canvas_position": {"x": 100, "y": 200}
    }
  ],
  "transitions": [
    {
      "from_uuid": "01HXXXX...",
      "to_uuid": "01HYYYY...",
      "action_name": "approve",
      "rule_type": "json_logic",
      "condition_rules": [...],
      "priority": 1,
      "is_default": false
    }
  ],
  "automated_actions": [...]
}
```

**Keputusan keamanan:**
- **Whitelist field** — IDs database (`id`, `workflow_id`, `from_step_id`, `to_step_id`) **diganti dengan UUID step** saat export. Tidak ada user ID, tenant ID, atau organization ID di JSON.
- **Placeholder `{{...}}`** untuk `assignee_value` yang merujuk user/role. Saat import, user harus map placeholder ke entity tenant tujuan.
- **UUID step** dipertahankan saat export agar transition bisa direlink saat import. Saat import ke tenant baru, generate UUID baru tapi pertahankan mapping `old_uuid → new_uuid`.

### 3.6 Versioning saat designer save

Behavior:

| State workflow saat dibuka di designer | Action | Hasil |
|---|---|---|
| Belum ada (new) | Save → buat `Workflow` baru status `draft` | Tidak ada efek ke produksi |
| Status `draft` | Save → update in-place | Tidak ada efek ke produksi (draft belum dipakai) |
| Status `active` | Save → otomatis `duplicateAsNewVersion()` → buat draft v(n+1), edit draft | **Versi aktif tidak tersentuh.** Instance running tetap pakai snapshot. |
| Status `archived` | Read-only — tombol "Duplicate as new draft" baru bisa edit |

Tombol "Publish" di designer memanggil `WorkflowDefinitionLifecycleService::publish()` → versi draft jadi `active`, versi sebelumnya jadi `archived`.

---

## 4. Deliverable Breakdown

### 4.1 Migration

**File:** `Modules/Workflow/database/migrations/2026_05_2X_XXXXXX_add_canvas_position_to_workflow_steps_table.php`

```php
Schema::table('workflow_steps', function (Blueprint $table) {
    $table->json('canvas_position')->nullable()->after('sort_order');
});
```

Reversible (`down()` drop column).

### 4.2 Backend services

**`Modules\Workflow\Services\WorkflowDefinitionPorter`**
- `export(Workflow $workflow): array` — return canonical JSON shape (lihat 3.5).
- `import(array $payload, int $tenantId, ?int $organizationId = null): Workflow` — buat workflow baru status `draft` di tenant tujuan, regenerate UUID, relink transitions.
- `validatePayload(array $payload): array` — return list error string (empty kalau valid). Cek schema_version, required fields, UUID consistency antara steps & transitions.

**`Modules\Workflow\Services\WorkflowCanvasLayoutService`** (opsional, ringan)
- `autoLayout(Workflow $workflow): array` — return `{stepUuid => {x, y}}` pakai algoritma dagre-like sederhana di PHP (atau biarkan frontend yang handle via `cytoscape-dagre`).
- Default: kalau ada step tanpa `canvas_position`, frontend yang auto-layout. Service ini optional — bisa di-skip di MVP.

**`Modules\Workflow\Http\Requests\WorkflowDesignerSaveRequest`**
- Validate payload `{steps: [...], transitions: [...]}` dari Livewire.
- Pakai `WorkflowFormSchemaValidator` untuk validate per-step `form_schema`.

### 4.3 Filament page

**`Modules\Workflow\Filament\Pages\WorkflowDesignerPage`**

- Route: `/admin/workflow-designer/{workflow?}` (parameter optional — kalau kosong = new workflow).
- Navigation: di group "Workflow", icon `Heroicon::Squares2x2`.
- Authorization: pakai policy `Workflow.update` + super admin / role custom.
- Blade view: `resources/views/filament/pages/workflow-designer.blade.php` yang mount Livewire `WorkflowCanvas`.

**Layout halaman:**
- Topbar: nama workflow, version badge, status badge, tombol [Save Draft] [Publish] [Export JSON] [Import JSON] [Auto-layout].
- Main: canvas Cytoscape (full-width, height: calc(100vh - 200px)).
- Sidepanel (right, collapsible, ~400px): form properties step/transition yang sedang dipilih.
- Sidebar (left, ~200px): palette step type & gateway type — drag ke canvas untuk create.

### 4.4 Livewire component

**`Modules\Workflow\Livewire\WorkflowCanvas`**

Properties:
- `public ?int $workflowId`
- `public array $steps = []` — in-memory representation, sync ke JS via Alpine
- `public array $transitions = []`
- `public ?string $selectedStepUuid = null`
- `public ?int $selectedTransitionIndex = null`
- `public array $dirtyFlags = []`

Methods:
- `mount(?int $workflowId)` — load workflow dari DB, populate `steps` & `transitions`.
- `addStep(array $payload)` — append ke `$steps`, generate UUID.
- `updateStep(string $uuid, array $payload)`.
- `deleteStep(string $uuid)` — juga hapus transitions yang refer.
- `addTransition(array $payload)`.
- `updateTransition(int $index, array $payload)`.
- `deleteTransition(int $index)`.
- `updateStepPosition(string $uuid, float $x, float $y)`.
- `saveDraft()` — persist ke DB. Kalau workflow status `active`, panggil `duplicateAsNewVersion()` dulu, lalu apply changes ke draft baru.
- `publish()` — panggil `WorkflowDefinitionLifecycleService::publish()`.
- `exportJson()` — return download response via `WorkflowDefinitionPorter::export()`.
- `importJson(string $json)` — parse + validate + replace state in-memory (user masih harus save).

**Komunikasi Livewire ↔ JS:**
- Livewire dispatch event `workflow-canvas:state-updated` → Alpine listener re-render Cytoscape graph.
- Cytoscape callback (drag, click) → call Livewire method via `$wire.updateStepPosition(...)`.
- Debounce position updates ~500ms untuk hindari spam server.

### 4.5 Frontend assets

**File baru:**
- `resources/js/workflow-designer.js` — entry point, init Cytoscape, register Alpine component.
- `resources/css/workflow-designer.css` — styling node, edge, sidepanel (kalau tidak cukup pakai Tailwind utility).

**Update:**
- `vite.config.js` — tambah `resources/js/workflow-designer.js` ke input.
- `package.json` — tambah dependency:
  - `cytoscape: ^3.30`
  - `cytoscape-dagre: ^2.5`
  - `dagre: ^0.8.5` (peer dep)

Estimasi penambahan bundle: ~450KB minified+gzipped (lazy-loaded, tidak masuk Filament chunk utama).

### 4.6 JSON import/export UI

- **Export**: tombol "Export JSON" → download `workflow_{code}_v{version}_{date}.json`.
- **Import**: tombol "Import JSON" → modal Filament dengan file upload + textarea preview + tombol "Validate" → "Import as Draft". Hasil import = new workflow status `draft`, user lanjut edit di designer.

---

## 5. Acceptance Criteria (Wajib)

Diambil dari ROADMAP.md Fase 1.2 + clarification:

- [ ] **AC1** — Tenant admin bisa membuat workflow dengan **minimal 5 step + 2 paralel gateway** lewat designer tanpa menyentuh kode PHP/seeder.
- [ ] **AC2** — Versi `draft` yang disimpan **tidak mempengaruhi** workflow instance yang sudah running di versi `active` (verify via integration test).
- [ ] **AC3** — Tombol "Publish" mengubah draft → active, dan versi sebelumnya → archived. Instance baru pakai definisi baru; instance lama tetap selesai pakai snapshot.
- [ ] **AC4** — Export workflow → JSON file yang sesuai schema (lihat 3.5). Import JSON di tenant lain menghasilkan workflow `draft` dengan struktur identik (kecuali ID + assignee placeholder).
- [ ] **AC5** — Test `WorkflowDesignerSavesValidDefinitionTest` — Livewire test yang fill form 3 step + 2 transition, call `saveDraft()`, assert DB state.
- [ ] **AC6** — Test `WorkflowDesignerVersioningTest` — buka workflow `active`, save → assert versi baru dibuat dengan status `draft`, versi lama tetap `active`.
- [ ] **AC7** — Test `WorkflowDefinitionPorterTest` — export workflow, import balik, assert struktur identik (modulo ID & UUID).
- [ ] **AC8** — Test `WorkflowDefinitionPorterValidatesPayloadTest` — payload invalid (missing UUID, transition refer non-existent step) → error jelas.
- [ ] **AC9** — `npm run build` sukses, bundle utama Filament **tidak bertambah** > 10KB (verify pakai bundle analyzer atau manual size diff).
- [ ] **AC10** — Dokumentasi visual designer di `WORKFLOW.md` (section baru "Visual Designer" dengan screenshot + usage walkthrough).

---

## 6. Dependensi

- **Fase 1.1 ✅ selesai** — designer butuh UI untuk gateway type (parallel_split, parallel_join, inclusive, exclusive) + quorum strategy. Schema sudah siap.
- Tidak ada dependensi eksternal lain.

---

## 7. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Bundle JS canvas berat menghambat load Filament | Lazy-load: entry point terpisah, hanya di-load di halaman designer (lihat 3.1). |
| Race condition saat 2 admin edit workflow yang sama bersamaan | Tambah kolom `editing_lock_user_id` + `editing_lock_at` di `workflows`. Pertama-buka-pertama-edit, lock auto-expire setelah 15 menit idle. **Decision:** opsional di Phase 1.2 MVP — bisa ditunda ke follow-up kalau jadi masalah praktis. |
| User accidentally publish broken workflow | Sebelum `publish()`, jalankan validator: (a) tepat 1 step `is_initial=true`, (b) minimal 1 step `is_terminal=true`, (c) tidak ada step orphan (unreachable dari initial), (d) tidak ada cycle tanpa exit. Tampilkan error sebelum publish. |
| Import JSON dari sumber tidak dipercaya berisi payload jahat | Whitelist field saat import — drop key yang tidak dikenal. Validate `assignee_type` enum. JSON Logic di `condition_rules` di-pass ke `JsonLogicRuleEngine` yang sudah sandbox (tidak eval PHP arbitrary). |
| Drag-drop UX di mobile/tablet | Designer **desktop-only** untuk MVP. Tampilkan warning kalau viewport < 1024px → arahkan pakai relation manager existing. |
| Cytoscape SVG tidak accessible (screen reader) | Sediakan link "Edit via form" di sidepanel → fallback ke `WorkflowResource` relation manager. |
| Filament permission untuk halaman designer | Tambah custom permission `workflow.designer.access` via Shield generate. Default assigned ke `Workflow.update`. |

---

## 8. Breakdown PR (Saran)

Untuk minimize blast radius, pecah jadi 3 PR:

### PR 1: Backend foundation (no UI changes)
- Migration `canvas_position`.
- `WorkflowDefinitionPorter` service + tests (`WorkflowDefinitionPorterTest`, `WorkflowDefinitionPorterValidatesPayloadTest`).
- Pre-publish validator (initial/terminal/orphan/cycle checks) — tambah ke `WorkflowDefinitionLifecycleService::publish()` dengan opt-in flag.
- **Acceptance:** AC7, AC8 hijau. Tidak ada perubahan UI.

### PR 2: Filament page + Livewire skeleton (no canvas JS)
- `WorkflowDesignerPage` (page route + nav entry).
- `WorkflowCanvas` Livewire component dengan **table view** dulu (list step + transition, edit via form). Tanpa Cytoscape.
- Save draft + publish button.
- Test `WorkflowDesignerSavesValidDefinitionTest`, `WorkflowDesignerVersioningTest`.
- **Acceptance:** AC2, AC3, AC5, AC6 hijau. Tenant sudah bisa pakai designer (UX masih basic).

### PR 3: Canvas JS + export/import UI
- Tambah Cytoscape + dagre ke package.json.
- `resources/js/workflow-designer.js` + Alpine binding.
- Drag-drop, click-to-select, draw transition.
- Export/import JSON modal.
- Dokumentasi `WORKFLOW.md`.
- **Acceptance:** AC1, AC4, AC9, AC10 hijau.

Setelah PR 3 merged → Fase 1.2 status `[x]`.

---

## 9. Estimasi Effort

| PR | Estimasi |
|---|---|
| PR 1 | 1 sesi (~4–6 jam koding + test) |
| PR 2 | 1–1.5 sesi |
| PR 3 | 2 sesi (canvas JS + UX polish + dokumentasi) |
| **Total** | **~4–5 sesi** |

---

## 10. Definition of Done

Selain acceptance criteria di section 5, juga harus:

- [ ] `vendor/bin/pint --dirty --format agent` clean.
- [ ] `php artisan test --compact --filter=WorkflowDesigner` hijau.
- [ ] `php artisan test --compact --filter=WorkflowDefinitionPorter` hijau.
- [ ] `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction` dijalankan.
- [ ] `npm run build` sukses.
- [ ] Migration reversible — tested via `migrate:rollback` di dev.
- [ ] `WORKFLOW.md` updated.
- [ ] Update status `[ ]` → `[x]` di [ROADMAP.md](ROADMAP.md) section Fase 1.2, tambah tanggal selesai + commit SHA.
