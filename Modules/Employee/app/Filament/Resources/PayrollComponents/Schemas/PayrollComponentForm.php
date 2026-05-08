<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Employee\Enums\PayrollComponentCalculationType;
use Modules\Employee\Enums\PayrollComponentType;

class PayrollComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),

                Section::make('Identitas Komponen')
                    ->columns(2)
                    ->schema([
                        Select::make('organization_id')
                            ->label('Unit/Organisasi')
                            ->relationship('organization', 'name')
                            ->required(),

                        TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->placeholder('cth: BASIC_SAL'),

                        TextInput::make('name')
                            ->label('Nama Komponen')
                            ->required(),

                        TextInput::make('category')
                            ->label('Kategori')
                            ->placeholder('cth: allowance, insurance'),
                    ]),

                Section::make('Tipe & Perhitungan')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Tipe')
                            ->options(PayrollComponentType::class)
                            ->required(),

                        Select::make('calculation_type')
                            ->label('Metode Perhitungan')
                            ->options(PayrollComponentCalculationType::class)
                            ->required()
                            ->reactive(),

                        TextInput::make('amount')
                            ->label('Nominal Tetap')
                            ->numeric()
                            ->prefix('Rp')
                            ->visible(fn ($get) => $get('calculation_type') === PayrollComponentCalculationType::Fixed->value),

                        TextInput::make('percentage')
                            ->label('Persentase dari Gaji Pokok')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn ($get) => $get('calculation_type') === PayrollComponentCalculationType::Percentage->value),

                        Textarea::make('formula')
                            ->label('Formula')
                            ->hint('Gunakan {basic_salary} untuk merujuk gaji pokok. Contoh: {basic_salary} * 0.05')
                            ->columnSpanFull()
                            ->visible(fn ($get) => $get('calculation_type') === PayrollComponentCalculationType::Formula->value),
                    ]),

                Section::make('Pengaturan')
                    ->columns(4)
                    ->schema([
                        Toggle::make('is_taxable')
                            ->label('Kena Pajak')
                            ->default(true),

                        Toggle::make('is_mandatory')
                            ->label('Wajib')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        TextInput::make('display_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
