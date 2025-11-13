<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FavoriteAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'favoriteAccounts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('label')->required(),
                TextInput::make('address')->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')->searchable()->sortable(),
                TextColumn::make('address')->searchable()->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::Medium),
                DetachAction::make(),
            ]);
    }
}
