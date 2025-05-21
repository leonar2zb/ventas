<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function canViewAny(): bool
    {
        // $role = Auth::user()?->role->name;
        return true; // any restriction will be based on policy
    }

    public static function canAccess(): bool
    {
        //$role = Auth::user()?->role->name;
        return true; // any restriction will be based on policy
    }

    public static function shouldRegisterNavigation(): bool
    {
        //$role = Auth::user()?->role->name;
        return true; // any restriction will be based on policy
    }


    public static function form(Form $form): Form
    {
        $role = auth()->user()->role->name;
        $userId = auth()->user()->id;
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()
                    ->disabled(
                        fn($get, $record) =>
                        $role === 'Seller' &&
                            $userId !== $record?->id
                    ),
                Forms\Components\TextInput::make('email')->email()->required(),
                // Password: solo visible al crear o si se edita uno mismo
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn($record) => $record === null) // solo requerido al crear
                    ->confirmed()
                    ->dehydrated(fn($state) => !blank($state)) // solo guarda si hay valor
                    ->visible(
                        fn($record) =>
                        $record === null || $userId === $record->id
                    ),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->dehydrated(false)
                    ->visible(
                        fn($record) =>
                        $record === null || $userId === $record->id
                    ),

                // Role: solo editable por Managers
                Forms\Components\Select::make('role_id')
                    ->relationship('role', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->reactive()
                    ->disabled($role !== 'Manager'), // bloqueado si no es Manager                
            ]);
    }

    public static function table(Table $table): Table
    {
        $role = auth()->user()->role->name;
        $userId = auth()->user()->id;
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('role.name')
                    ->label('Role')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn(User $record) =>
                        $role === 'Manager' ||
                            $userId === $record->id
                    ),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => $role === 'Manager'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->visible(fn() => $role === 'Manager'),
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
}
