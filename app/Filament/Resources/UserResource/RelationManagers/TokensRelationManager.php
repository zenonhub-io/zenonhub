<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class TokensRelationManager extends RelationManager
{
    protected static string $relationship = 'tokens';

    protected static ?string $title = 'API Tokens';

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->nullable(),
                Forms\Components\Select::make('abilities')
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
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('abilities')
                    ->label('Abilities')
                    ->colors(['info'])
                    ->badge(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Last used')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth(MaxWidth::Medium),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
