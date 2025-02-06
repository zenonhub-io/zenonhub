<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\FavoriteAccountsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\TokensRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\VerifiedAccountsRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'username';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

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
                                    ->hidden(fn (User $user): bool => $user->two_factor_confirmed_at === null)
                                    ->disabled(),
                            ]),
                        Tabs\Tab::make('Notifications')
                            ->schema([
                                Forms\Components\CheckboxList::make('notificationTypes')
                                    ->relationship(titleAttribute: 'name'),
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->columnSpan(1)
                    ->tabs([

                        Tabs\Tab::make('Info')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Placeholder::make('last_seen_at')
                                    ->label(__('Last seen'))
                                    ->content(fn (User $record): ?string => $record->last_seen_at?->diffForHumans()),
                                Forms\Components\Placeholder::make('email_verified_at')
                                    ->label(__('Verified'))
                                    ->content(fn (User $record): ?string => $record->email_verified_at?->format('d/m/Y h:i A')),
                                Forms\Components\Placeholder::make('created_at')
                                    ->label(__('Created'))
                                    ->content(fn (User $record): ?string => $record->created_at?->format('d/m/Y h:i A')),
                                Forms\Components\Placeholder::make('updated_at')
                                    ->label(__('Updated'))
                                    ->content(fn (User $record): ?string => $record->updated_at?->format('d/m/Y h:i A')),
                                Forms\Components\Placeholder::make('registration_ip')
                                    ->label(__('Registration IP'))
                                    ->content(fn (User $record): ?string => $record->registration_ip),
                                Forms\Components\Placeholder::make('login_ip')
                                    ->label(__('Login IP'))
                                    ->content(fn (User $record): ?string => $record->login_ip),
                            ]),

                        Tabs\Tab::make('Actions')
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

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('force_logout')
                                        ->label(__('Force logout'))
                                        ->color('warning')
                                        ->action(fn (User $user) => static::doForceLogout($user)),
                                ])
                                    ->fullWidth(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('disable_2fa')
                                        ->label(__('Disable 2FA'))
                                        ->color('warning')
                                        ->action(fn (User $user) => static::doDisable2Fa($user)),
                                ])
                                    ->hidden(fn (User $user): bool => $user->two_factor_confirmed_at === null)
                                    ->fullWidth(),
                            ]),
                    ])
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['info'])
                    ->badge(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->sortable()
                    ->getStateUsing(fn (User $record): bool => $record->email_verified_at !== null)
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('last_seen_at')
                    ->label('Last seen')
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
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email verification')
                    ->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Verified users')
                    ->falseLabel('Not verified users'),
                Tables\Filters\TernaryFilter::make('last_seen_at')
                    ->label('Active users')
                    ->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Active users')
                    ->falseLabel('Inactive user'),
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
            TokensRelationManager::class,
            FavoriteAccountsRelationManager::class,
            VerifiedAccountsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
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

    public static function doForceLogout(User $user): void
    {
        Auth::setUser($user);
        Auth::logout();
        Auth::forgetUser();

        Notification::make()
            ->title(__('User has been logged out'))
            ->success()
            ->send();
    }
}
