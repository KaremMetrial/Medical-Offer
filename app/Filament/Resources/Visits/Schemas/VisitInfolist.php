<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VisitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('companion_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('provider_id')
                    ->numeric(),
                TextEntry::make('visit_date')
                    ->dateTime(),
                TextEntry::make('services')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('paid_amount')
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('comment')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
