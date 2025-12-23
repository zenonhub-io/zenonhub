<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\RelationManagers\FavoriteAccountsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\TokensRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\VerifiedAccountsRelationManager;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'username';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Tabs')
                    ->columnSpan(1)
                    ->tabs([
                        Tab::make('Details')
                            ->schema([
                                TextInput::make('username')->required(),
                                TextInput::make('email')->email()->required(),
                                Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => Str::headline($record->name))
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ]),
                        Tab::make('Security')
                            ->schema([
                                TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->required(fn (string $context): bool => $context === 'create'),
                                TextInput::make('passwordConfirmation')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->same('password')
                                    ->required(fn (string $context): bool => $context === 'create'),
                                DateTimePicker::make('two_factor_confirmed_at')
                                    ->hidden(fn (User $user): bool => $user->two_factor_confirmed_at === null)
                                    ->disabled(),
                            ]),
                        Tab::make('Notifications')
                            ->schema([
                                CheckboxList::make('notificationTypes')
                                    ->relationship(titleAttribute: 'name'),
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->columnSpan(1)
                    ->tabs([

                        Tab::make('Info')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('last_seen_at')
                                    ->label(__('Last seen'))
                                    ->content(fn (User $record): ?string => $record->last_seen_at?->diffForHumans()),
                                Placeholder::make('email_verified_at')
                                    ->label(__('Verified'))
                                    ->content(fn (User $record): ?string => $record->email_verified_at?->format('d/m/Y h:i A')),
                                Placeholder::make('created_at')
                                    ->label(__('Created'))
                                    ->content(fn (User $record): ?string => $record->created_at?->format('d/m/Y h:i A')),
                                Placeholder::make('updated_at')
                                    ->label(__('Updated'))
                                    ->content(fn (User $record): ?string => $record->updated_at?->format('d/m/Y h:i A')),
                                Placeholder::make('registration_ip')
                                    ->label(__('Registration IP'))
                                    ->content(fn (User $record): ?string => $record->registration_ip),
                                Placeholder::make('login_ip')
                                    ->label(__('Login IP'))
                                    ->content(fn (User $record): ?string => $record->login_ip),
                            ]),

                        Tab::make('Actions')
                            ->schema([

                                Actions::make([
                                    Action::make('resend_verification')
                                        ->label(__('Resend verification email'))
                                        ->color('info')
                                        ->action(fn (User $user) => static::doResendEmailVerification($user)),
                                ])
                                    ->hidden(fn (User $user): bool => $user->email_verified_at !== null)
                                    ->fullWidth(),

                                Actions::make([
                                    Action::make('send_password_reset')
                                        ->label(__('Send password reset email'))
                                        ->color('info')
                                        ->action(fn (User $user) => static::doSendPasswordReset($user)),
                                ])
                                    ->fullWidth(),

                                Actions::make([
                                    Action::make('force_logout')
                                        ->label(__('Force logout'))
                                        ->color('warning')
                                        ->action(fn (User $user) => static::doForceLogout($user)),
                                ])
                                    ->fullWidth(),

                                Actions::make([
                                    Action::make('disable_2fa')
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
                TextColumn::make('username')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['info'])
                    ->badge(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->sortable()
                    ->getStateUsing(fn (User $record): bool => $record->email_verified_at !== null)
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('last_seen_at')
                    ->label('Last seen')
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label('Email verification')
                    ->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Verified users')
                    ->falseLabel('Not verified users'),
                TernaryFilter::make('last_seen_at')
                    ->label('Active users')
                    ->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Active users')
                    ->falseLabel('Inactive user'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListUsers::route('/'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    //
    // Action Helpers

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
