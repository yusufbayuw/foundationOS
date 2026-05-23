<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Finance\Models\JournalEntry;
use Modules\Workflow\Contracts\ProvidesWorkflowContext;
use Modules\Workflow\Contracts\StartsWorkflow;
use Modules\Workflow\Models\WorkflowInstance;

class SalarySlip extends Model implements ProvidesWorkflowContext, StartsWorkflow
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'period_month',
        'period_year',
        'period_label',
        'basic_salary',
        'earnings_details',
        'deductions_details',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'tax_details',
        'bpjs_details',
        'working_days',
        'working_hours',
        'overtime_hours',
        'leave_days',
        'absent_days',
        'status',
        'paid_at',
        'paid_via',
        'notes',
        'is_sent',
        'sent_at',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'earnings_details' => 'array',
            'deductions_details' => 'array',
            'total_earnings' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'tax_details' => 'array',
            'bpjs_details' => 'array',
            'working_days' => 'integer',
            'working_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'leave_days' => 'integer',
            'absent_days' => 'integer',
            'paid_at' => 'datetime',
            'is_sent' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(SalarySlipComponent::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function workflowInstances(): MorphMany
    {
        return $this->morphMany(WorkflowInstance::class, 'subject', 'subject_type', 'subject_id');
    }

    public function workflowCode(): string
    {
        return 'salary-slip-approval';
    }

    public function workflowContext(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'employee_id' => $this->employee_id,
            'period_label' => $this->period_label,
            'net_salary' => (float) $this->net_salary,
            'total_earnings' => (float) $this->total_earnings,
            'total_deductions' => (float) $this->total_deductions,
            'status' => $this->status,
        ];
    }

    public function workflowSubjectLabel(): string
    {
        return 'Slip Gaji #'.$this->getKey().' — '.($this->period_label ?? '');
    }

    public function workflowSubjectType(): string
    {
        return self::class;
    }
}
