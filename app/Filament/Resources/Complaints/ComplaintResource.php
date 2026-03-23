<?php

namespace App\Filament\Resources\Complaints;

use App\Filament\Resources\Complaints\Pages\CreateComplaint;
use App\Filament\Resources\Complaints\Pages\EditComplaint;
use App\Filament\Resources\Complaints\Pages\ListComplaints;
use App\Filament\Resources\Complaints\Pages\ViewComplaint;
use App\Filament\Resources\Complaints\Schemas\ComplaintForm;
use App\Filament\Resources\Complaints\Schemas\ComplaintInfolist;
use App\Filament\Resources\Complaints\Tables\ComplaintsTable;
use App\Models\Complaint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.support');
    }

    public static function getModelLabel(): string
    {
        return __('filament.complaint.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.complaint.plural_label');
    }


    public static function form(Schema $schema): Schema
    {
        return ComplaintForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ComplaintInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplaintsTable::configure($table);
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
            'index' => ListComplaints::route('/'),
            'create' => CreateComplaint::route('/create'),
            'view' => ViewComplaint::route('/{record}'),
            'edit' => EditComplaint::route('/{record}/edit'),
        ];
    }
}
