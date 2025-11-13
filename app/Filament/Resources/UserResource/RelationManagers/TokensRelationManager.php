<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TokensRelationManager extends RelationManager
{
    protected static string $relationship = 'tokens';

    protected static ?string $title = 'API Tokens';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('expires_at')
                    ->nullable(),
                Select::make('abilities')
                    ->multiple()
                    ->options([
                        'plasma-bot' => 'Plasma Bot',
                        'vip' => 'VIP',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('abilities')
                    ->label('Abilities')
                    ->colors(['info'])
                    ->badge(),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('last_used_at')
                    ->label('Last used')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::Medium),
                DeleteAction::make(),
            ]);
    }
}
