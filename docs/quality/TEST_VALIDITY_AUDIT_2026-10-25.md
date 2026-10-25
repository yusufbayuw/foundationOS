# FoundationOS — Test Validity Audit (2026-10-25)

**Baseline:** `main` commit `5e4a2830aff0a83986d8f636530f31a72432d871`. Historical successfully reported PHPUnit run: **7,897 test cases / 24,176 assertions / 226 test files**. Recount on the eventual merge head from PHPUnit JUnit; the historical denominator is not an immutable current count.

## Progress — test cases strengthened / baseline total

**6 / 7,897** (code changes proposed in this branch; **not yet CI-verified**).

- Test cases rewritten or strengthened: **6**; deleted: **0**; newly added: **0**.
- Confirmed green on branch exact HEAD: **NOT TESTED** until GitHub Actions finishes.
- This is **not** a claim that the other 7,891 cases have been audited or are ineffective.

| Existing test case | Old gap | New independent oracle |
|---|---|---|
| `InventoryProcurementIntegrationTest::test_goods_receipt_creates_stock_in_moves_and_levels` | Count/quantity alone could pass for zero-value stock | PO unit price × received quantity = IDR 500,000; tenant identity and level |
| `InventoryProcurementIntegrationTest::test_stock_in_posts_balanced_journal_lines_when_coa_exists` | Equal debit/credit could pass with both equal to zero or wrong CoA | Exact IDR 200,000 debit inventory, credit GR/IR, tenant-bound ledger lines |
| `InventoryProcurementIntegrationTest::test_stock_out_reduces_on_hand` | Stock decrease without validating outbound movement value | IDR 40,000 outbound valuation, two units and tenant |
| `InventoryProcurementIntegrationTest::test_insufficient_stock_out_is_rejected` | Exception alone could hide partial writes | Error + unchanged on-hand + unchanged move count + no outbound movement |
| `ProcurementEndToEndPipelineTest::test_partial_receipt_marks_po_partial_then_full_receipt_marks_received` | Only checked PO status and cumulative qty; receipt could be economically wrong | Persisted receipt accepted quantity + totals IDR 10m and IDR 16m |
| `ProcurementEndToEndPipelineTest::test_three_way_mismatch_rejects_price_outside_tolerance` | Expected exception could hide a partially created vendor bill | Invalid 20%-higher unit price rejected + vendor bill count stays zero |

**Oracle selection:** numbers above are calculated from the test's own explicit input fixtures and expected business arithmetic, not copied from the implementation's return value.

## Additional material findings requiring separate business review

1. **False-positive draft invoice journal:** On baseline `main`, `VendorBillAutoCreationService::postDraftJournal()` creates a `JournalEntry` with `is_balanced=true` and matching totals, but does **not** create journal-entry debit/credit lines. `ProcurementEndToEndPipelineTest::test_full_pipeline_receipt_to_bill_posts_balanced_draft_journal` checks only the header. It can pass while the journal has no posted accounting lines. Resolve Finance/Procurement ledger contract before strengthening this case or changing posting behavior. **Status: defect suspected / not fixed by this PR.**
2. **Payroll test contract discrepancy on main:** `EmployeePayrollCalculationTest::test_paid_salary_slip_posts_balanced_payroll_journal` expects `is_posted=false` although its test name claims posting. Draft PR #96 independently expects `true` and adds audit and P&L assertions. **Do not duplicate PR #96.** Review as part of Finance PR sign-off.
3. **Potential concurrency/valuation issue:** `StockValuationService::consumeLayers()` modifies FIFO/LIFO layers before `StockMoveService::commit()` opens its database transaction. A failure in cost resolution may leave partially consumed layers. Do not assume fixed by ordinary on-hand tests; add explicit isolated real-database rollback test and a domain fix in a separate review.
4. **Parallel PR collision:** PR #103 changes both test files in this patch. QA/Operations must integrate or rebase carefully, compare assertions and preserve any new PR #103 scenarios.

## Definition of effective test

1. Independently traceable business, security, privacy, or platform contract.
2. Expected outputs derived from fixtures/specification, not copied from production method output.
3. Observes effect, persistence, and lack of forbidden side effects.
4. Fails against a relevant deliberate defect (mutation/negative fixture, prioritized for critical domains).
5. Adds distinct value relative to other tests and has known cost/flakiness.

Classify each tested case as VALID, WEAK, INCORRECT, DUPLICATE, OBSOLETE, or UNVERIFIED. A passing test is not automatically VALID.

## Execution and merge gates

- Execute the two impacted files first; then required SQL engines, security, supply-chain/static gates and full regression in the risk-appropriate pre-merge cadence.
- Update this numerator only after an actual test case is substantively strengthened; track **verified** count separately and NEVER count new assertions as additional repaired test cases.
- No change to business logic, schema, production environment, financial records, or tenant security.
- Do not merge to `main` until exact-HEAD CI is green and PR #103 collision is reconciled; branch protection/rulesets must be verified separately. Never make `main` the test environment.
- Do not lower the coverage threshold or weaken composer audit/secret scanning to make this branch green.
