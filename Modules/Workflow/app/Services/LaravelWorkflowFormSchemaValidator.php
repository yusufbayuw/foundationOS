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

            if (isset($field['options_source'])) {
                $this->validateOptionsSourceShape($field['options_source'], $step->id);
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
                'select', 'multiselect' => [],
                default => ['string'],
            });

            if ($type === 'file' && ! empty($field['accepted_types']) && is_array($field['accepted_types'])) {
                $fieldRules[] = 'mimes:'.implode(',', $field['accepted_types']);
            }

            if ($type === 'file' && ! empty($field['max_size_kb'])) {
                $fieldRules[] = 'max:'.(int) $field['max_size_kb'];
            }

            $rules[$name] = array_values(array_unique($fieldRules));
        }

        return Validator::make($filteredData, $rules)->validate();
    }

    private function validateOptionsSourceShape(mixed $optionsSource, int|string $stepId): void
    {
        if (! is_array($optionsSource)) {
            throw new WorkflowConfigurationException("Step [{$stepId}]: options_source must be an array.");
        }

        $kind = $optionsSource['kind'] ?? null;
        $supportedKinds = config('workflow.allowed_options_sources', ['static', 'eloquent', 'enum']);

        if ($kind === 'static') {
            if (! isset($optionsSource['options']) || ! is_array($optionsSource['options'])) {
                throw new WorkflowConfigurationException("Step [{$stepId}]: options_source.options must be an array for kind=static.");
            }

            return;
        }

        if (! in_array($kind, $supportedKinds, true)) {
            throw new WorkflowConfigurationException(
                "Step [{$stepId}]: options_source.kind must be one of: ".implode(', ', $supportedKinds).'.'
            );
        }

        if ($kind === 'eloquent') {
            if (empty($optionsSource['model'])) {
                throw new WorkflowConfigurationException("Step [{$stepId}]: options_source.model is required for kind=eloquent.");
            }

            $allowed = config('workflow-dynamic-sources.models', []);

            if (! array_key_exists($optionsSource['model'], $allowed) && ! in_array($optionsSource['model'], $allowed, true)) {
                throw new WorkflowConfigurationException(
                    "Step [{$stepId}]: model [{$optionsSource['model']}] is not in the dynamic sources whitelist."
                );
            }
        }

        if ($kind === 'enum') {
            if (empty($optionsSource['class'])) {
                throw new WorkflowConfigurationException("Step [{$stepId}]: options_source.class is required for kind=enum.");
            }

            if (! enum_exists($optionsSource['class'])) {
                throw new WorkflowConfigurationException(
                    "Step [{$stepId}]: options_source.class [{$optionsSource['class']}] is not a valid enum."
                );
            }
        }
    }
}
