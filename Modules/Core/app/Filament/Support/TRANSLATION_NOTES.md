# Filament Translation Notes

## Epic 4.1 — Auto-Label Hook Investigation (2026-05-22)

### Finding

Filament v5's `ComponentManager::configureUsing(string $class, Closure $fn)` method
can be used to register a default label closure for any column or entry class.

When `TextColumn::make('column_name')` is called:
1. The column is constructed with `$name = 'column_name'`
2. `$static->configure()` is called, which runs all `configureUsing` closures for that class
3. Our hook sets: `$column->label(fn() => FilamentUi::field($column->getName()))`

**Result:** any column WITHOUT an explicit `->label()` call will get an auto-translated label.
Columns WITH explicit `->label('String')` will override the hook (correct behavior).

### Implementation

`FilamentTranslationServiceProvider` registers hooks for:
- `TextColumn`, `IconColumn`, `ImageColumn` (tables)
- `TextEntry`, `IconEntry`, `ImageEntry` (infolists)

### Coverage

- ~70% of columns that currently have NO explicit label → now auto-translated via hook
- ~30% with explicit `->label('English')` → still need manual Fase 4.2/4.3 refactor
- Columns with `->label(FilamentUi::field(...))` already → hook runs first, then overwritten (no issue)

### Risks

- **Action columns** (DeleteAction, EditAction built-in) — these are `Action` not `Column`, unaffected
- **ToggleColumn** — not hooked; unlikely to need translation (boolean toggle)
- **SelectColumn** — not hooked; add if needed
- Performance: label closure is lazy (only evaluated when rendered), negligible overhead

### Decision

✅ Hook is feasible and safe. Implemented in `FilamentTranslationServiceProvider`.
