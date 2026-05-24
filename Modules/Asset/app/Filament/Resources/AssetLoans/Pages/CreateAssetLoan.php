<?php

namespace Modules\Asset\Filament\Resources\AssetLoans\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Asset\Filament\Resources\AssetLoans\AssetLoanResource;

class CreateAssetLoan extends CreateRecord
{
    protected static string $resource = AssetLoanResource::class;
}
