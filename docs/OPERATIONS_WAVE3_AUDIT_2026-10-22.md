# FoundationOS Wave 3 — Operations code audit and pilot release gates

Date: 2026-10-22. Source: repository `main`, issue #83. Scope: Procurement, Inventory, Asset, Facility, Helpdesk, ItOps, Transport, EOffice, Dms, Sales, Cafeteria, Boarding, PhysicalSecurity.

**Classification:** code inspection, not deployment/UAT evidence. Presence of a model, Filament resource, policy, event or service does not establish a production-ready business process. No tenant A/B staging walkthrough, database restore drill, end-user sign-off or deployed SHA has been verified here.

## Evidence-based module matrix

| Module | Observed foundation | Required pilot proof / outstanding work | Priority |
| --- | --- | --- | --- |
| Procurement | PR, RFQ, PO, GR and vendor bill models/resources; auto-creation services; three-way-match validator; PDF services | Approval/award authorization, retry-safe GR→bill, duplicate and invalid invoice lines, legal partial billing, supplier discrepancy/reversal, accounting handoff | P0/P1 |
| Inventory | Stock levels, adjustments, moves, cost layers; GR listener, stock/valuation/journal services | Exactly-once receiving on event replay; concurrent stock out/adjustment; valuation reconciliation, reversals, GL sync | P0/P1 |
| Asset | Asset, movement, loan, maintenance, insurance and depreciation resources; depreciation service | Approve/return a loan; repair/maintenance and retirement authority, asset history and reconciliation against stock | P1 |
| Facility | Building/room, booking, rental, maintenance, utilities; booking conflict checker | Enforce overlap validation at write boundary, confirm/cancel booking and audit approval; rental invoicing with Finance owner | P1 |
| Helpdesk | Ticket/category/comments/attachments resources; assignment listener, observer, category SLA fields in model | Align schema/UI/assignment lifecycle; measure response and resolution SLA, escalation, notifications, queue replay, closure evidence and cross-tenant access | P0/P1 |
| ItOps | License, IP, account and backup resources; webhook receiver; NullMikroTikClient | Real device adapter and credential handling, webhook replay/security, backup/restore verification, operations handoff | P1 |
| Transport | Vehicle, driver, route/schedule/stops, maintenance, operating costs, shuttle/boarding logs resources | Driver and vehicle dispatch/loan workflow, collision detection, calendar, odometer and maintenance control; distinguish rentals | P1 |
| EOffice | Letter, template, disposition, attachment resources; numbering/document services | Draft→approve→sign→send→archive; immutable numbering, disposition controls, private document access and retention | P1 |
| Dms | Document/folder/version/access-log resources; folder registration service | Version creation/retrieval, access audit, permission-scoped download, retention, hold and secure deletion | P1 |
| Sales | Customers, sales orders/items, voucher claims, cooperative savings; document/journal services | Atomic order fulfillment and stock reservation, payment reconciliation, reversal, permission and tenant tests | P1 |
| Cafeteria | Tenant/menu/stock, subscriptions, wallets, transactions, ratings and inspections resources | Atomic wallet debit and stock decrement, refund/idempotency, meal claims, access and daily settlement | P0/P1 |
| Boarding | Dormitory/room assignment, leave, attendance, meal/laundry/inspection resources | Leave authorization, occupant/bed collision prevention, curfew, welfare/escalation and custody audit | P1 |
| PhysicalSecurity | Guard/patrol/visitor, incident, alert and checklist resources | Visitor in/out evidence, alert escalation/acknowledgment, incident confidentiality, shift roster and physical response UAT | P1 |

## Confirmed problems found in code

1. **P0 procurement billing:** `ThreeWayMatchValidator` checked quantity only as an upper bound, permitting empty/zero/negative lines and repeated PO item lines. Automated vendor billing had no replay guard before creating a new bill/journal.
2. **P0 receiving/inventory:** `GoodsReceiptAutoCreationService::receive()` read PO line remaining quantities without serializing operations; an empty receipt was possible after full receipt. `GoodsReceiptStockService` committed inbound moves again when the same confirmed-receipt event was delivered twice.
3. **P0 Helpdesk:** `Modules/Helpdesk/database/migrations/2026_05_24_000000_create_helpdesk_tables.php` omits columns used by `Ticket`, `TicketCategory` and `AssignTicketToAgent`: category, priority, assignee, escalation and closure, and response/resolution hours. `TicketForm` presents `active/inactive` while the assignment service counts `open/in_progress` tickets. `Ticket` both directly calls assignment on creation and emits a created event through `TicketObserver`; review duplicate handling.
4. **P1 Facility:** `RoomBookingConflictChecker` exists but `RoomBookingForm` does not itself show conflict checks or lifecycle enforcement; test at the write boundary and under concurrency.
5. **P1/P2 other modules:** many modules expose model/resource/policy sets, but a corresponding transactional orchestration or lifecycle service is not evident in their `app/Services` directories. This warrants directed code tests and actual UAT rather than assuming absence or maturity solely from file counts.

## Changes in operations integrity PR

- Serialize receipt processing against the PO; reject fully empty receipts.
- Serialize automatic vendor bills against a confirmed receipt; reuse a pre-existing active auto bill on replay and reject explicit rebilling/soft-deleted duplicates.
- Reject non-positive quantities, negative/non-finite prices, duplicate lines, zero items, unmatched tenants, and non-confirmed receipts during three-way validation.
- Collapse repeated GR lines for the same PO item into one default bill line.
- Serialize receipt-driven stock posting and skip inbound stock moves already committed for the same GR item, including soft-deleted move history.
- Add targeted regression cases for these invariants. **Test execution is still required by CI**; no test pass is asserted by this audit.

## Prioritized delivery backlog

### P0 — financial, stock and service continuity
- [x] Implement receipt, auto-bill and stock-replay guards on operations PR branch.
- [x] Add focused happy/failure regression tests (execution pending).
- [ ] Helpdesk additive schema alignment and unique ticket-created assignment path; verify on real migrated database.
- [ ] Atomic cafeteria wallet/stock purchase and duplicate transaction safeguards before enabling real money.
- [ ] Run tests and review two-tenant isolation, unauthorized actor, journal idempotency, rollback and retry behavior.

### P1 — full daily-use lifecycles
- [ ] Procurement approver matrix, supplier discrepancy/reversal and accounting reconciliation with ERP agent.
- [ ] Helpdesk ticket create→assign→SLA→escalate→resolve→close, with notifications and user feedback.
- [ ] Inventory count approvals, stock adjustment/transfer rollback, asset maintenance/loan/disposal and audit proof.
- [ ] Facility room-time collision enforcement; transport driver/car booking and dispatch; boarding approval and physical visitor/incident management.
- [ ] EOffice and DMS approval, access-controlled attachments, versioning and retention.
- [ ] ItOps backup restore tests and integration-specific health/status checks.
- [ ] Sales and cafeteria end-of-day settlement with Finance owner.

### P2 — workflow, observability and UX
- [ ] Mobile-first daily user tasks with a measured create/approve/close click budget; native Filament actions where appropriate.
- [ ] Operational dashboards for SLA, overdue items, stock discrepancies, preventative maintenance and renewal dates.
- [ ] Automated reminders, reconciliation reports, exception monitoring and tenant-scoped imports/exports.

## Non-negotiable release gates for issue #83

1. Feature owner, process owner and approver documented for every module released.
2. Migrations run safely against realistic data; backups, restore and rollback drill recorded.
3. All affected CI tests green, including duplicate submissions/events, unauthorized user, tenant A/B isolation, reversals and audit evidence.
4. Real staging walkthroughs of PR→RFQ→PO→GR→bill→GL; asset→maintenance→SLA ticket→completion; letter/document→approval→retention.
5. Notifications, queue retries, private attachments, migration/onboarding notes and Filament/mobile UX verified.
6. UAT signed by operational owners, approved release scope, deployed SHA and post-deploy smoke evidence recorded.

**Release decision on 2026-10-22: HOLD unrestricted production release.** Only independently validated modules may be piloted after satisfying gates. Changes to Finance core require coordination with the ERP agent; this operations PR does not modify Finance core.
