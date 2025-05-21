<?php

namespace App\Filament\Resources;

use App\Enums\SaleOrderStatus;
use App\Filament\Resources\SaleOrderResource\Pages;
use App\Filament\Resources\SaleOrderResource\RelationManagers;
use App\Filament\Resources\SaleOrderResource\RelationManagers\SaleOrderDetailsRelationManager;
use App\Models\SaleOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleOrderResource extends Resource
{
    protected static ?string $model = SaleOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255)
                            ->readOnly(fn($record) => in_array($record?->status, [SaleOrderStatus::CONFIRMED, SaleOrderStatus::CANCELLED])),
                    ]),
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $role = auth()->user()->role->name;
        $userId = auth()->user()->id;
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->sortable()
                    ->searchable()
                    ->money('usd', true)
                    ->formatStateUsing(fn($state) => number_format($state, 2)),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Seller')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn($record) => (($userId === $record->user_id && $record->status === SaleOrderStatus::PENDING))), // Mostrar solo si la orden no es editable(otro user o ya confir/cancelada)

                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => ($record->status !== SaleOrderStatus::PENDING || ($record->user_id !== $userId))), // Solo aparece si el estado es PENDING y del usuario logueado,
                Tables\Actions\Action::make('Confirm')
                    ->requiresConfirmation()
                    ->action(fn(SaleOrder $saleOrder) => $saleOrder->confirm())
                    ->hidden(fn($record) => ($record->status !== SaleOrderStatus::PENDING || ($record->user_id !== $userId))), // Solo aparece si el estado es PENDING y del usuario logueado,
                Tables\Actions\Action::make('Cancel')
                    ->requiresConfirmation()
                    ->action(fn(SaleOrder $saleOrder) => $saleOrder->cancel())
                    ->hidden(fn($record) => ($record->status !== SaleOrderStatus::PENDING || ($record->user_id !== $userId))), // Solo aparece si el estado es PENDING y del usuario logueado,
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $role = auth()->user()->role->name;
        $userId = auth()->user()->id;
        return (in_array($ownerRecord->status, [SaleOrderStatus::CONFIRMED, SaleOrderStatus::CANCELLED]) || $userId !== $ownerRecord->user_id);
    }

    public static function canView(Model $record): bool
    {
        $role = auth()->user()->role->name;
        $userId = auth()->user()->id;
        return (in_array($record->status, [SaleOrderStatus::CONFIRMED, SaleOrderStatus::CANCELLED]) || $userId !== $record->user_id);
    }

    // Only editable if the status is PENDING and the user is the one who created it
    public static function canEdit(Model $record): bool
    {
        return ($record->status === SaleOrderStatus::PENDING && $record->user_id === auth()->user()->id);
    }


    public static function getRelations(): array
    {
        return [
            SaleOrderDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSaleOrders::route('/'),
            'create' => Pages\CreateSaleOrder::route('/create'),
            'edit' => Pages\EditSaleOrder::route('/{record}/edit'),
            'view' => Pages\ViewSaleOrder::route('/{record}'),
        ];
    }
}
