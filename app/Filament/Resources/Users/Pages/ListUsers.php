<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'user' => Tab::make(__('filament.options.role.user'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'user')),
            'provider' => Tab::make(__('filament.options.role.provider'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'provider')),
            'admin' => Tab::make(__('filament.options.role.admin'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('role', ['admin', 'super_admin'])),
        ];
    }
}
