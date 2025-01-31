<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('Tabs')
                    ->columnSpan(1)
                    ->tabs([
                        Tabs\Tab::make('Details')
                            ->schema([
                                Forms\Components\TextInput::make('username')->required(),
                                Forms\Components\TextInput::make('email')->email()->required(),
                                Forms\Components\Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => Str::headline($record->name))
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ]),
                        Tabs\Tab::make('Security')
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->required(fn (string $context): bool => $context === 'create'),
                                Forms\Components\TextInput::make('passwordConfirmation')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->same('password')
                                    ->required(fn (string $context): bool => $context === 'create'),

                                Forms\Components\DateTimePicker::make('two_factor_confirmed_at')
                                    ->hidden(fn (User $user): bool => $user->two_factor_confirmed_at !== null)
                                    ->disabled(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('disable_2fa')
                                        ->label(__('Disable 2FA'))
                                        ->color('warning')
                                        ->action(fn (User $user) => static::doDisable2Fa($user)),
                                ])
                                    ->hidden(fn (User $user): bool => $user->two_factor_confirmed_at === null)
                                    ->fullWidth(),
                            ]),
                        Tabs\Tab::make('Tab 3')
                            ->schema([

                            ]),
                    ]),

                Forms\Components\Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Actions')
                            ->schema([
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('resend_verification')
                                        ->label(__('Resend verification email'))
                                        ->color('info')
                                        ->action(fn (User $user) => static::doResendEmailVerification($user)),
                                ])
                                    ->hidden(fn (User $user): bool => $user->email_verified_at !== null)
                                    ->fullWidth(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('send_password_reset')
                                        ->label(__('Send password reset email'))
                                        ->color('info')
                                        ->action(fn (User $user) => static::doSendPasswordReset($user)),
                                ])
                                    ->fullWidth(),
                            ])
                            ->hidden(fn (string $operation): bool => $operation === 'create'),

                        Section::make('Info')
                            ->schema([
                                Forms\Components\DateTimePicker::make('last_login_at')->disabled(),
                                Forms\Components\DateTimePicker::make('last_seen_at')->disabled(),
                                Forms\Components\DateTimePicker::make('created_at')->disabled(),
                                Forms\Components\DateTimePicker::make('updated_at')->disabled(),
                            ])
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Role')
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['info'])
                    ->badge(),
                Tables\Columns\TextColumn::make('last_seen_at')->dateTime()->since()->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Filter::make('email_verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function doResendEmailVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();

        Notification::make()
            ->title(__('Verification email sent'))
            ->success()
            ->send();
    }

    public static function doSendPasswordReset(User $user): void
    {
        Password::sendResetLink($user->only('email'));

        Notification::make()
            ->title(__('Password reset email sent'))
            ->success()
            ->send();
    }

    public static function doDisable2Fa(User $user): void
    {
        app(DisableTwoFactorAuthentication::class)($user);

        Notification::make()
            ->title(__('2FA has been disabled'))
            ->success()
            ->send();
    }
}
