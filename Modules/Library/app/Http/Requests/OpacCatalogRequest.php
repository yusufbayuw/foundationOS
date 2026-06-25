<?php

namespace Modules\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Library\Data\OpacCatalogFilter;
use Modules\Library\Support\QueryFlag;

class OpacCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'available_only' => ['nullable', 'string'],
        ];
    }

    public function filter(): OpacCatalogFilter
    {
        $validated = $this->validated();

        return new OpacCatalogFilter(
            search: trim((string) ($validated['q'] ?? '')),
            categoryId: is_numeric($validated['category'] ?? null) ? (int) $validated['category'] : null,
            publisher: trim((string) ($validated['publisher'] ?? '')),
            availableOnly: QueryFlag::isTruthy($validated['available_only'] ?? null),
        );
    }
}
