<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
use Illuminate\Support\Facades\Validator;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\WorkflowStep;

class LaravelWorkflowFormSchemaValidator implements WorkflowFormSchemaValidator
{
    public function __construct(private readonly RuleEngine $ruleEngine) {}

    /**
     * @param  array<string, mixed>  $formData
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function validate(WorkflowStep $step, array $formData, array $context = []): array
    {
        $rules = [];
        /** @var list<array<string, mixed>> $schema */
        $schema = $this->ruleEngine->evaluateFieldState(
            array_values(is_array($step->form_schema ?? null) ? $step->form_schema : []),
            $context,
        );
        $filteredData = $formData;

        foreach ($schema as $field) {
            $name = TypedValue::string($field['name'] ?? null);

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

            /** @var list<mixed> $validationRules */
            $validationRules = is_array($field['validation'] ?? null) ? $field['validation'] : [];

            foreach ($validationRules as $rule) {
                if (is_string($rule)) {
                    $fieldRules[] = $rule;
                }
            }

            $type = TypedValue::string($field['type'] ?? null, 'text');

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
                /** @var list<string> $acceptedTypes */
                $acceptedTypes = array_values(array_filter($field['accepted_types'], 'is_string'));
                $fieldRules[] = 'mimes:'.implode(',', $acceptedTypes);
            }

            if ($type === 'file' && ! empty($field['max_size_kb'])) {
                $fieldRules[] = 'max:'.TypedValue::int($field['max_size_kb']);
            }

            $rules[$name] = array_values(array_unique($fieldRules, SORT_STRING));
        }

        /** @var array<string, mixed> $validated */
        $validated = Validator::make($filteredData, $rules)->validate();

        return $validated;
    }

    private function validateOptionsSourceShape(mixed $optionsSource, int|string $stepId): void
    {
        if (! is_array($optionsSource)) {
            throw new WorkflowConfigurationException("Step [{$stepId}]: options_source must be an array.");
        }

        $kind = $optionsSource['kind'] ?? null;
        $supportedKinds = ['eloquent', 'enum'];

        if (! is_string($kind) || ! in_array($kind, $supportedKinds, true)) {
            throw new WorkflowConfigurationException(
                "Step [{$stepId}]: options_source.kind must be one of: ".implode(', ', $supportedKinds).'.'
            );
        }

        if ($kind === 'eloquent') {
            $model = $optionsSource['model'] ?? null;

            if (! is_string($model) || $model === '') {
                throw new WorkflowConfigurationException("Step [{$stepId}]: options_source.model is required for kind=eloquent.");
            }

            /** @var array<string, class-string>|list<class-string> $allowed */
            $allowed = config('workflow-dynamic-sources.models', []);

            if (! array_key_exists($model, $allowed) && ! in_array($model, $allowed, true)) {
                throw new WorkflowConfigurationException(
                    "Step [{$stepId}]: model [{$model}] is not in the dynamic sources whitelist."
                );
            }
        }

        if ($kind === 'enum') {
            $class = $optionsSource['class'] ?? null;

            if (! is_string($class) || $class === '') {
                throw new WorkflowConfigurationException("Step [{$stepId}]: options_source.class is required for kind=enum.");
            }

            if (! enum_exists($class)) {
                throw new WorkflowConfigurationException(
                    "Step [{$stepId}]: options_source.class [{$class}] is not a valid enum."
                );
            }
        }
    }
}
