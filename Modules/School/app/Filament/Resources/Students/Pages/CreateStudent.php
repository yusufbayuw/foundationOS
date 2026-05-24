<?php

namespace Modules\School\Filament\Resources\Students\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Modules\School\Filament\Resources\Students\Schemas\StudentForm;
use Modules\School\Filament\Resources\Students\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Basic Information')
                    ->columns(2)
                    ->schema(StudentForm::basicInformationFields()),

                Step::make('Enrollment')
                    ->columns(2)
                    ->schema(StudentForm::enrollmentFields()),

                Step::make('Academic & Health')
                    ->columns(2)
                    ->schema(StudentForm::academicAndHealthFields()),

                Step::make('Father Information')
                    ->columns(2)
                    ->schema(StudentForm::fatherInformationFields()),

                Step::make('Mother Information')
                    ->columns(2)
                    ->schema(StudentForm::motherInformationFields()),

                Step::make('Guardian Information')
                    ->columns(2)
                    ->schema(StudentForm::guardianInformationFields()),

                Step::make('Residence & Transport')
                    ->columns(2)
                    ->schema(StudentForm::residenceAndTransportFields()),
            ])->columnSpanFull(),
        ]);
    }
}
