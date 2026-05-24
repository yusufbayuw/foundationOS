<?php

namespace Modules\Cms\Filament\Resources\Testimonials;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use Modules\Cms\Filament\Resources\Testimonials\Pages\EditTestimonial;
use Modules\Cms\Filament\Resources\Testimonials\Pages\ListTestimonials;
use Modules\Cms\Filament\Resources\Testimonials\Pages\ViewTestimonial;
use Modules\Cms\Filament\Resources\Testimonials\Schemas\TestimonialForm;
use Modules\Cms\Filament\Resources\Testimonials\Schemas\TestimonialInfolist;
use Modules\Cms\Filament\Resources\Testimonials\Tables\TestimonialsTable;
use Modules\Cms\Models\Testimonial;
use Modules\Core\Filament\Support\ModuleResource;

class TestimonialResource extends ModuleResource
{
    protected static ?string $model = Testimonial::class;

    public static function form(Schema $schema): Schema
    {
        return TestimonialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TestimonialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestimonialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'view' => ViewTestimonial::route('/{record}'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
