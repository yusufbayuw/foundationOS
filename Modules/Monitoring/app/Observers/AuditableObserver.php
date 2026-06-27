<?php

namespace Modules\Monitoring\Observers;

use Illuminate\Database\Eloquent\Model;
use Modules\Monitoring\Services\AuditTrailRecorder;

class AuditableObserver
{
    public function created(Model $model): void
    {
        if (! $this->shouldAudit($model)) {
            return;
        }

        $action = $this->actionName($model, 'created');

        AuditTrailRecorder::record(
            $model,
            $action,
            null,
            $this->auditableAttributes($model),
        );
    }

    public function updated(Model $model): void
    {
        if (! $this->shouldAudit($model) || ! $model->wasChanged()) {
            return;
        }

        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $original = collect($model->getOriginal())
            ->only(array_keys($changes))
            ->all();

        AuditTrailRecorder::record(
            $model,
            $this->actionName($model, 'updated'),
            $original,
            $changes,
        );
    }

    public function deleted(Model $model): void
    {
        if (! $this->shouldAudit($model)) {
            return;
        }

        AuditTrailRecorder::record(
            $model,
            $this->actionName($model, 'deleted'),
            $this->auditableAttributes($model),
            null,
        );
    }

    protected function shouldAudit(Model $model): bool
    {
        if (! method_exists($model, 'shouldRecordAuditTrail')) {
            return true;
        }

        $shouldRecord = $model->shouldRecordAuditTrail();

        return is_bool($shouldRecord) ? $shouldRecord : true;
    }

    protected function actionName(Model $model, string $event): string
    {
        $basename = str(class_basename($model))->snake()->toString();

        return "{$basename}.{$event}";
    }

    /**
     * @return array<string, mixed>
     */
    protected function auditableAttributes(Model $model): array
    {
        return collect($model->getAttributes())
            ->except(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])
            ->all();
    }
}
