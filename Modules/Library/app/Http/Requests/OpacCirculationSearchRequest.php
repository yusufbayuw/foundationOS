<?php

namespace Modules\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Library\Data\OpacCirculationSearchFilter;
use Modules\Library\Support\QueryFlag;

class OpacCirculationSearchRequest extends FormRequest
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
            'member_q' => ['nullable', 'string', 'max:255'],
            'item_q' => ['nullable', 'string', 'max:255'],
            'scanner' => ['nullable', 'string'],
        ];
    }

    public function filter(): OpacCirculationSearchFilter
    {
        $validated = $this->validated();

        return new OpacCirculationSearchFilter(
            memberQuery: trim((string) ($validated['member_q'] ?? '')),
            itemQuery: trim((string) ($validated['item_q'] ?? '')),
            scannerMode: QueryFlag::isTruthy($validated['scanner'] ?? '1'),
        );
    }
}
