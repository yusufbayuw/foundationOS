<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeeDocument;
use Tests\TestCase;

class EmployeeDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_document_tracks_private_file_metadata_for_core_document_types(): void
    {
        [$tenant, $employee] = $this->makeEmployee('employee-document');

        $document = EmployeeDocument::query()->create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'document_type' => 'ktp',
            'document_number' => '3201010101010001',
            'file_path' => 'employee-documents/ktp.pdf',
            'original_filename' => 'ktp.pdf',
            'verification_status' => 'unverified',
        ]);

        $this->assertSame($employee->id, $document->employee->id);
        $this->assertContains('ktp', array_keys(EmployeeDocument::documentTypeOptions()));
        $this->assertContains('npwp', array_keys(EmployeeDocument::documentTypeOptions()));
        $this->assertContains('ijazah', array_keys(EmployeeDocument::documentTypeOptions()));
        $this->assertContains('kontrak', array_keys(EmployeeDocument::documentTypeOptions()));
        $this->assertFalse($document->isExpired());
    }

    public function test_employee_document_expiry_date_is_valid_through_the_whole_date(): void
    {
        Carbon::setTestNow('2026-05-23 15:30:00');

        [, $employee] = $this->makeEmployee('employee-document-expiry');

        $document = EmployeeDocument::query()->create([
            'tenant_id' => $employee->tenant_id,
            'employee_id' => $employee->id,
            'document_type' => 'kontrak',
            'file_path' => 'employee-documents/contract.pdf',
            'expiry_date' => '2026-05-23',
            'verification_status' => 'verified',
        ]);

        $this->assertFalse($document->isExpired());

        Carbon::setTestNow('2026-05-24 00:00:00');

        $this->assertTrue($document->fresh()->isExpired());

        Carbon::setTestNow();
    }

    /**
     * @return array{0: Tenant, 1: Employee}
     */
    private function makeEmployee(string $code): array
    {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => Str::title(str_replace('-', ' ', $code)),
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => "{$code}-org",
            'name' => "{$code} Organization",
        ]);

        $user = User::factory()->create([
            'email' => "{$code}@example.test",
        ]);

        $employee = Employee::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'employee_number' => Str::upper($code),
            'full_name' => Str::title(str_replace('-', ' ', $code)),
            'email' => "{$code}@example.test",
            'employment_status' => 'active',
            'join_date' => '2026-01-01',
            'basic_salary' => 5_000_000,
        ]);

        return [$tenant, $employee];
    }
}
