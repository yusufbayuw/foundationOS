<?php

namespace Tests\Feature;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Modules\School\Filament\Resources\Students\Schemas\StudentForm;
use Tests\TestCase;

class StudentFormSchemaTest extends TestCase
{
    public function test_controlled_fields_use_select_options(): void
    {
        $status = $this->field(StudentForm::enrollmentFields(), 'status');
        $entryType = $this->field(StudentForm::enrollmentFields(), 'entry_type');
        $track = $this->field(StudentForm::enrollmentFields(), 'track');
        $fatherEducation = $this->field(StudentForm::fatherInformationFields(), 'father_education');
        $motherEducation = $this->field(StudentForm::motherInformationFields(), 'mother_education');
        $residenceType = $this->field(StudentForm::residenceAndTransportFields(), 'residence_type');
        $transportType = $this->field(StudentForm::residenceAndTransportFields(), 'transport_type');

        $this->assertInstanceOf(Select::class, $status);
        $this->assertSame('Active', $status->getOptions()['active']);
        $this->assertTrue($status->isRequired());

        $this->assertInstanceOf(Select::class, $entryType);
        $this->assertArrayHasKey('new_student', $entryType->getOptions());

        $this->assertInstanceOf(Select::class, $track);
        $this->assertArrayHasKey('regular', $track->getOptions());

        $this->assertInstanceOf(Select::class, $fatherEducation);
        $this->assertSame($fatherEducation->getOptions(), $motherEducation->getOptions());

        $this->assertInstanceOf(Select::class, $residenceType);
        $this->assertArrayHasKey('parents', $residenceType->getOptions());

        $this->assertInstanceOf(Select::class, $transportType);
        $this->assertArrayHasKey('public_transport', $transportType->getOptions());
    }

    public function test_tenant_and_organization_fields_use_tenant_aware_components(): void
    {
        $tenantId = $this->field(StudentForm::basicInformationFields(), 'tenant_id');
        $organizationId = $this->field(StudentForm::basicInformationFields(), 'organization_id');

        $this->assertInstanceOf(Hidden::class, $tenantId);
        $this->assertInstanceOf(Select::class, $organizationId);
    }

    public function test_array_cast_fields_use_tags_inputs(): void
    {
        foreach (['extracurricular_activities', 'achievements', 'health_notes', 'special_needs'] as $fieldName) {
            $this->assertInstanceOf(
                TagsInput::class,
                $this->field(StudentForm::academicAndHealthFields(), $fieldName),
            );
        }
    }

    public function test_identifiers_phone_and_numeric_fields_have_validation_rules(): void
    {
        $nis = $this->field(StudentForm::basicInformationFields(), 'nis');
        $nisn = $this->field(StudentForm::basicInformationFields(), 'nisn');
        $fatherPhone = $this->field(StudentForm::fatherInformationFields(), 'father_phone');
        $travelTime = $this->field(StudentForm::residenceAndTransportFields(), 'travel_time_minutes');
        $distance = $this->field(StudentForm::residenceAndTransportFields(), 'distance_km');

        $this->assertInstanceOf(TextInput::class, $nis);
        $this->assertContains('max:50', $nis->getValidationRules());
        $this->assertContains('regex:/^[A-Za-z0-9.\/-]+$/', $nis->getValidationRules());

        $this->assertContains('max:20', $nisn->getValidationRules());
        $this->assertContains('regex:/^[0-9]+$/', $nisn->getValidationRules());

        $this->assertContains('max:30', $fatherPhone->getValidationRules());
        $this->assertContains('regex:/^[0-9+\-\s().]+$/', $fatherPhone->getValidationRules());

        $this->assertContains('integer', $travelTime->getValidationRules());
        $this->assertContains('min:0', $travelTime->getValidationRules());
        $this->assertContains('max:1440', $travelTime->getValidationRules());

        $this->assertContains('numeric', $distance->getValidationRules());
        $this->assertContains('min:0', $distance->getValidationRules());
    }

    /**
     * @param  Component[]  $fields
     */
    private function field(array $fields, string $name): Component
    {
        foreach ($fields as $field) {
            if (method_exists($field, 'getName') && $field->getName() === $name) {
                return $field;
            }
        }

        $this->fail("Field [{$name}] was not found.");
    }
}
