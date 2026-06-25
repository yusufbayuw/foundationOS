<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Library\Enums\MemberStatus;
use Modules\Library\Filament\Resources\BookCopies\BookCopyResource;
use Modules\Library\Filament\Resources\Books\BookResource;
use Modules\Library\Filament\Resources\Loans\LoanResource;
use Modules\Library\Filament\Resources\Members\MemberResource;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Tests\TestCase;

/**
 * Characterization tests for Library module Filament wiring and model defaults.
 */
class LibraryFilamentResourceCharacterizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_core_library_resources_map_to_expected_models(): void
    {
        $expected = [
            BookResource::class => Book::class,
            BookCopyResource::class => BookCopy::class,
            MemberResource::class => Member::class,
            LoanResource::class => Loan::class,
        ];

        foreach ($expected as $resourceClass => $modelClass) {
            $this->assertTrue(is_subclass_of($resourceClass, ModuleResource::class));
            $this->assertSame($modelClass, $resourceClass::getModel());
        }
    }

    public function test_all_library_filament_resources_extend_module_resource(): void
    {
        $resources = $this->libraryResourceClasses();

        $this->assertGreaterThanOrEqual(20, $resources->count());

        $resources->each(function (string $resourceClass): void {
            $this->assertTrue(
                is_subclass_of($resourceClass, ModuleResource::class),
                "{$resourceClass} should extend ".ModuleResource::class,
            );

            $this->assertTrue(class_exists($resourceClass::getModel()));
        });
    }

    public function test_book_persists_null_copy_counts_and_active_flag_by_default(): void
    {
        $tenant = $this->makeLibraryTenant();

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'title' => 'Characterization Title',
            'authors' => ['Author'],
        ]);

        $book->refresh();

        $this->assertSame(0, $book->total_copies);
        $this->assertSame(0, $book->available_copies);
        $this->assertTrue($book->is_active);
        $this->assertFalse($book->is_reference_only);
    }

    public function test_member_defaults_match_database_schema(): void
    {
        $tenant = $this->makeLibraryTenant();
        $user = User::factory()->create();

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'user_id' => $user->id,
            'member_number' => 'CHAR-001',
        ]);

        $member->refresh();

        $this->assertSame(MemberStatus::Active, $member->status);
        $this->assertSame(0.0, (float) $member->unpaid_fines);
        $this->assertSame(3, (int) $member->max_books);
    }

    /**
     * @return Collection<int, class-string>
     */
    private function libraryResourceClasses(): Collection
    {
        $base = base_path('Modules/Library/app/Filament/Resources');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));

        return collect($iterator)
            ->filter(fn (SplFileInfo $file): bool => $file->isFile() && str_ends_with($file->getFilename(), 'Resource.php'))
            ->map(fn (SplFileInfo $file): ?string => $this->classNameFromFile($file->getPathname()))
            ->filter(fn (?string $class): bool => is_string($class) && class_exists($class))
            ->filter(fn (string $class): bool => ! (new ReflectionClass($class))->isAbstract())
            ->values();
    }

    private function classNameFromFile(string $path): ?string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        preg_match('/^namespace\s+([^;]+);/m', $contents, $namespace);
        preg_match('/^(?:abstract\s+)?class\s+([A-Za-z0-9_]+)/m', $contents, $class);

        if (! isset($namespace[1], $class[1])) {
            return null;
        }

        return $namespace[1].'\\'.$class[1];
    }

    private function makeLibraryTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'library-char',
            'name' => 'Library Char Plan',
            'included_modules' => ['core', 'library'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'lib-char-tenant',
            'name' => 'Library Char Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
