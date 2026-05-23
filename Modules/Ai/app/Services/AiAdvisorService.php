<?php

namespace Modules\Ai\Services;

use Illuminate\Support\Facades\DB;

class AiAdvisorService
{
    /**
     * @param  array<string, mixed>  $input
     * @return array{ai_suggestion: bool, auto_executable: bool, provider: string, content: string}
     */
    public function advise(int $tenantId, string $feature, array $input): array
    {
        $output = [
            'ai_suggestion' => true,
            'auto_executable' => false,
            'provider' => 'fake',
            'content' => 'Draft recommendation generated for human review.',
        ];

        DB::table('ai_call_logs')->insert([
            'tenant_id' => $tenantId,
            'feature' => $feature,
            'provider' => 'fake',
            'input_payload' => json_encode($input, JSON_THROW_ON_ERROR),
            'output_payload' => json_encode($output, JSON_THROW_ON_ERROR),
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'cost_amount' => 0,
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $output;
    }
}
