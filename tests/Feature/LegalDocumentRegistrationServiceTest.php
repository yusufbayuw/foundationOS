<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Legal\Exceptions\DuplicateLegalDocumentCodeException;
use Modules\Legal\Models\LegalDocument;
use Modules\Legal\Services\LegalDocumentRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class LegalDocumentRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_legal_document(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'legal']);

        $document = app(LegalDocumentRegistrationService::class)->register(
            $tenant->id,
            $organization->id,
            'pol-01',
            'Privacy Policy',
            documentType: 'policy',
        );

        $this->assertSame('POL-01', $document->code);
        $this->assertDatabaseHas(LegalDocument::class, ['id' => $document->id]);
    }

    public function test_register_rejects_duplicate_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'legal']);

        $service = app(LegalDocumentRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'DOC1', 'Document A');

        $this->expectException(DuplicateLegalDocumentCodeException::class);
        $service->register($tenant->id, $organization->id, 'doc1', 'Document B');
    }
}
