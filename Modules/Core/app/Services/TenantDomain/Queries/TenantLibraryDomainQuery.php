<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCategory;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\BookReservation;
use Modules\Library\Models\Fine;
use Modules\Library\Models\LibraryPolicy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;

final class TenantLibraryDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'bookCategories' => new TenantDomainRelationDefinition('bookCategories', BookCategory::class),
            'books' => new TenantDomainRelationDefinition('books', Book::class),
            'bookCopies' => new TenantDomainRelationDefinition('bookCopies', BookCopy::class),
            'members' => new TenantDomainRelationDefinition('members', Member::class),
            'bookReservations' => new TenantDomainRelationDefinition('bookReservations', BookReservation::class),
            'libraryPolicies' => new TenantDomainRelationDefinition('libraryPolicies', LibraryPolicy::class),
            'loans' => new TenantDomainRelationDefinition('loans', Loan::class),
            'fines' => new TenantDomainRelationDefinition('fines', Fine::class),
        ];
    }
}
