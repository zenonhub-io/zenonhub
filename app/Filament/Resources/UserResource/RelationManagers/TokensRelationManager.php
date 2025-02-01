<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TokensRelationManager extends RelationManager
{
    protected static string $relationship = 'tokens';

    protected static ?string $title = 'API Tokens';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('abilities')
                    ->label('Abilities')
                    ->colors(['info'])
                    ->badge(),
                Tables\Columns\TextColumn::make('last_used_at')->dateTime()->since()->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
