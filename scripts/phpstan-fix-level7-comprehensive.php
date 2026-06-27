#!/usr/bin/env php
<?php

$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';

$replacements = [
    $root.'/app/Console/Commands/SetupBudgetWorkflowCommand.php' => [
        "     * @return array<string, mixed>\n     */\n    protected function defaultApprovalActions(): array" => "     * @return list<array<string, list<string>|string>>\n     */\n    protected function defaultApprovalActions(): array",
    ],
    $root.'/app/Console/Commands/SetupProcurementWorkflowPilotCommand.php' => [
        "     * @return array<string, mixed>\n     */\n    protected function defaultApprovalActions(): array" => "     * @return list<array<string, list<string>|string>>\n     */\n    protected function defaultApprovalActions(): array",
    ],
    $root.'/app/Console/Commands/SetupThesisWorkflowCommand.php' => [
        "     * @return array<string, mixed>\n     */\n    protected function defaultApprovalActions(): array" => "     * @return list<array<string, list<string>|string>>\n     */\n    protected function defaultApprovalActions(): array",
    ],
    $root.'/app/Console/Commands/LibraryImportSlimsCommand.php' => [
        "     * @return array<string, mixed>\n     */\n    protected function parseAuthors" => "     * @return list<string>\n     */\n    protected function parseAuthors",
    ],
    $root.'/Modules/Workflow/app/Filament/Pages/WorkflowTaskHistoryPage.php' => [
        "            ->get()\n            ->all();" => "            ->get()\n            ->values()\n            ->all();",
    ],
    $root.'/Modules/Core/app/Support/UserPasswordPolicy.php' => [
        "        return array_values([\n            'required',\n            'string'," => "        /** @var list<\\Illuminate\\Contracts\\Validation\\ValidationRule|string> $rules */\n        $rules = [\n            'required',\n            'string',",
        "                ->symbols(),\n        ]);" => "                ->symbols(),\n        ];\n\n        return $rules;",
    ],
    $root.'/Modules/Core/app/Services/TenantAdminProvisioner.php' => [
        "        \$role = Role::firstOrCreate(\n            ['name' => \$superAdminRoleName, 'guard_name' => 'web'],\n            ['team_id' => (int) \$tenant->getKey()],\n        );" => "        \$role = Role::firstOrCreate([\n            'name' => \$superAdminRoleName,\n            'guard_name' => 'web',\n            'team_id' => (int) \$tenant->getKey(),\n        ]);",
    ],
    $root.'/Modules/Workflow/app/Services/DatabaseWorkflowEngine.php' => [
        "                \$evidenceQuery = \$instance->evidences();\n                \$uploaded = \$evidenceQuery\n                    ->where(\$evidenceQuery->qualifyColumn('workflow_step_id'), \$currentStep->getKey())\n                    ->count();" => "                \$uploaded = \$instance->evidences()\n                    ->where('workflow_step_id', \$currentStep->getKey())\n                    ->count();",
    ],
];

foreach ($replacements as $file => $pairs) {
    if (! is_file($file)) {
        continue;
    }
    $content = file_get_contents($file);
    $original = $content;
    foreach ($pairs as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo 'Updated: '.str_replace($root.'/', '', $file)."\n";
    }
}

// Fix FinancialReportService Collection shapes to include &\stdClass
$frs = $root.'/Modules/Finance/app/Services/FinancialReportService.php';
$content = file_get_contents($frs);
if ($content !== false) {
    $content = str_replace(
        'Collection<int, object{id: int|null, code: string, name: string, balance: float}>',
        'Collection<int, object{id: int|null, code: string, name: string, balance: float}&\stdClass>',
        $content,
    );
    $content = preg_replace(
        '/private function groupByAccount\(Collection \$lines, string \$type\): Collection\n    \{\n        \$creditNormal/',
        "private function groupByAccount(Collection \$lines, string \$type): Collection\n    {\n        \$creditNormal",
        $content,
    ) ?? $content;

    // Add explicit var before return in groupByAccount and groupByCashFlow
    $content = str_replace(
        "        return \$lines\n            ->filter(fn (\$l) => strtolower((string) \$l->chartOfAccount?->type) === \$type)",
        "        /** @var \\Illuminate\\Support\\Collection<int, object{id: int|null, code: string, name: string, balance: float}&\\stdClass> \$grouped */\n        \$grouped = \$lines\n            ->filter(fn (\$l) => strtolower((string) \$l->chartOfAccount?->type) === \$type)",
        $content,
    );
    $content = str_replace(
        "            ->values()\n            ->sortBy('code');\n    }\n\n    /**\n     * @param  Collection<int, JournalEntryLine>  \$lines\n     * @return \\Illuminate\\Support\\Collection<int, object{id: int|null, code: string, name: string, balance: float}&\\stdClass>\n     */\n    private function groupByCashFlow",
        "            ->values()\n            ->sortBy('code');\n\n        return \$grouped;\n    }\n\n    /**\n     * @param  Collection<int, JournalEntryLine>  \$lines\n     * @return \\Illuminate\\Support\\Collection<int, object{id: int|null, code: string, name: string, balance: float}&\\stdClass>\n     */\n    private function groupByCashFlow",
        $content,
    );
    $content = str_replace(
        "        return \$lines\n            ->filter(fn (\$l) => strtolower((string) \$l->chartOfAccount?->category) === \$category)",
        "        /** @var \\Illuminate\\Support\\Collection<int, object{id: int|null, code: string, name: string, balance: float}&\\stdClass> \$grouped */\n        \$grouped = \$lines\n            ->filter(fn (\$l) => strtolower((string) \$l->chartOfAccount?->category) === \$category)",
        $content,
    );
    $content = str_replace(
        "            ->values();\n    }\n}\n",
        "            ->values();\n\n        return \$grouped;\n    }\n}\n",
        $content,
    );
    file_put_contents($frs, $content);
    echo "Updated: FinancialReportService.php\n";
}

echo "Done.\n";
