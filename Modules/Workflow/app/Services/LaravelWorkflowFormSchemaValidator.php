<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Facades\Validator;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\WorkflowStep;

class LaravelWorkflowFormSchemaValidator implements WorkflowFormSchemaValidator
{
    public function __construct(private readonly RuleEngine $ruleEngine) {}

    public function validate(WorkflowStep $step, array $formData, array $context = []): array
    {
        $rules = [];
        $schema = $this->ruleEngine->evaluateFieldState($step->form_schema ?? [], $context);
        $filteredData = $formData;

        foreach ($schema as $field) {
            $name = (string) ($field['name'] ?? '');

            if ($name === '') {
                throw new WorkflowConfigurationException("Workflow step [{$step->id}] contains a field without a name.");
            }

            if (! ($field['_visible'] ?? true) || ($field['_disabled'] ?? false)) {
                unset($filteredData[$name]);

                continue;
            }

            $fieldRules = [];
            $fieldRules[] = ($field['_required'] ?? false) ? 'required' : 'nullable';

            foreach (($field['validation'] ?? []) as $rule) {
                $fieldRules[] = $rule;
            }

            $type = (string) ($field['type'] ?? 'text');

            $fieldRules = array_merge($fieldRules, match ($type) {
                'number' => ['numeric'],
                'date' => ['date'],
                'datetime' => ['date'],
                'checkbox' => ['boolean'],
                'file' => ['file'],
                default => ['string'],
            });

            if ($type === 'file' && ! empty($field['accepted_types']) && is_array($field['accepted_types'])) {
                $fieldRules[] = 'mimes:' . implode(',', $field['accepted_types']);
            }

            if ($type === 'file' && ! empty($field['max_size_kb'])) {
                $fieldRules[] = 'max:' . (int) $field['max_size_kb'];
            }

            $rules[$name] = array_values(array_unique($fieldRules));
        }

        return Validator::make($filteredData, $rules)->validate();
    }
}
