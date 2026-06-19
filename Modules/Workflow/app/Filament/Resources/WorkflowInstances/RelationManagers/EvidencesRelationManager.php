<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Support\FilamentUi;

class EvidencesRelationManager extends RelationManager
{
    protected static string $relationship = 'evidences';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('step.name')
                    ->label(FilamentUi::text('Step'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('label')
                    ->label(FilamentUi::field('label'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('original_filename')
                    ->label(FilamentUi::text('File'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label(FilamentUi::text('MIME Type'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('file_size')
                    ->label(FilamentUi::text('File Size'))
                    ->formatStateUsing(fn (?int $state): string => $state ? number_format($state / 1024, 1).' KB' : '-'),
                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label(FilamentUi::text('Uploaded By'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(FilamentUi::text('Uploaded At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(FilamentUi::text('Upload Evidence'))
                    ->schema($this->evidenceSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $instance = $this->getOwnerRecord();
                        $data['workflow_instance_id'] = $instance->getKey();
                        $data['uploaded_by'] = auth()->id();

                        // Resolve file metadata from the uploaded path
                        if (! empty($data['file_path'])) {
                            $path = $data['file_path'];
                            $data['original_filename'] = basename($path);
                            if (Storage::exists($path)) {
                                $data['file_size'] = Storage::size($path);
                                $data['mime_type'] = Storage::mimeType($path) ?: null;
                            }
                        }

                        return $data;
                    })
                    ->createAnother(false),
            ])
            ->recordActions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @return array<int, Component>
     */
    protected function evidenceSchema(): array
    {
        return [
            Select::make('workflow_step_id')
                ->label(FilamentUi::text('Step'))
                ->options(function (): array {
                    $instance = $this->getOwnerRecord();

                    return $instance->workflow?->steps()
                        ->orderBy('sort_order')
                        ->pluck('name', 'id')
                        ->toArray() ?? [];
                })
                ->required(),
            FileUpload::make('file_path')
                ->label(FilamentUi::text('File'))
                ->directory('workflow/evidence')
                ->maxSize(10240)
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                ->required()
                ->storeFileNamesIn('original_filename'),
            TextInput::make('label')
                ->label(FilamentUi::field('label'))
                ->maxLength(255),
            Textarea::make('notes')
                ->label(FilamentUi::field('notes'))
                ->rows(2),
        ];
    }
}
