<?php

namespace Modules\Alumni\Filament\Resources\JobApplications\Pages;

use DomainException;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Modules\Alumni\Filament\Resources\JobApplications\JobApplicationResource;
use Modules\Alumni\Models\JobApplication;
use Modules\Core\Models\User;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof JobApplication) {
            return parent::handleRecordUpdate($record, $data);
        }

        $reviewer = auth()->user();

        if (! $reviewer instanceof User) {
            abort(403);
        }

        try {
            $record->transitionStatus((string) ($data['status'] ?? ''), $reviewer);
        } catch (DomainException $exception) {
            throw ValidationException::withMessages([
                'data.status' => $exception->getMessage(),
            ]);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
