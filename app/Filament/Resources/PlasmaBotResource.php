<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Actions\PlasmaBot\Cancel;
use App\Filament\Resources\PlasmaBotResource\Pages;
use App\Models\PlasmaBotEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconSize;
use Filament\Tables;
use Filament\Tables\Table;
use Throwable;

class PlasmaBotResource extends Resource
{
    protected static ?string $model = PlasmaBotEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'NoM';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereConfirmed()->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.address')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('account.last_active_at')
                    ->label('Last active')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('amount')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('should_expire')->label('Expirable')->sortable()->boolean(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('should_expire')
                    ->label('Expirable')
                    ->nullable()
                    ->placeholder('All fuses')
                    ->trueLabel('Expirable fuses')
                    ->falseLabel('Permanent fuses'),
            ])
            ->actions([
                Tables\Actions\Action::make('End')
                    ->hiddenLabel()
                    ->hidden(fn (PlasmaBotEntry $entry) => $entry->created_at->lessThan(now()->subHours(10)))
                    ->icon('heroicon-o-x-circle')
                    ->iconSize(IconSize::Large)
                    ->color('danger')
                    ->tooltip(__('End fuse'))
                    ->action(function (PlasmaBotEntry $entry): void {
                        try {
                            Cancel::run($entry);
                        } catch (Throwable $th) {
                            Notification::make()
                                ->title(__('Error cancelling fuse'))
                                ->danger()
                                ->send();
                        }

                        Notification::make()
                            ->title(__('Fuse cancelled'))
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlasmaBots::route('/'),
        ];
    }
}
