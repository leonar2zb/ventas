<?php

namespace App\Filament\Resources\SaleOrderResource\RelationManagers;

use App\Enums\SaleOrderStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\SaleOrderDetail;
use App\Models\Product;
use App\Models\SaleOrder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class SaleOrderDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'saleOrderDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name', fn(Builder $query) => $query->where('stock', '>', 0)) // filter products with stock 
                    ->required()
                    ->label('Product')
                    ->searchable()
                    ->preload()
                    ->reactive(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->maxValue(fn($get) => Product::find($get('product_id'))?->stock ?? 0) // limit max quantity to available stock
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                Tables\Columns\TextColumn::make('product_id'),
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('product.price')
                    ->money('usd', true)
                    ->label('Unit Price')
                    ->formatStateUsing(fn($state) => number_format($state, 2)),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Subtotal')
                    ->state(function (SaleOrderDetail $record): float {
                        return $record->quantity * $record->product->price; // calculate subtotal based on quantity and product price
                    })->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(fn($livewire) => in_array($livewire->ownerRecord->status, [SaleOrderStatus::CONFIRMED, SaleOrderStatus::CANCELLED]))
            ])
            ->actions([
                /* Tables\Actions\ViewAction::make()
                    ->hidden(fn($record) => $record->status === SaleOrderStatus::PENDING), // Mostrar solo si la orden ya no es editable*/

                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => in_array($record->saleOrder->status, [SaleOrderStatus::CONFIRMED, SaleOrderStatus::CANCELLED])), // Ocultar en órdenes no editables

                Tables\Actions\DeleteAction::make()
                    ->hidden(fn($record) => in_array($record->saleOrder->status, [SaleOrderStatus::CONFIRMED, SaleOrderStatus::CANCELLED])), // Ocultar si la orden está confirmada o cancelada

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn($livewire) => in_array($livewire->ownerRecord->status, [SaleOrderStatus::CONFIRMED, SaleOrderStatus::CANCELLED])), // Accede al estado de SaleOrder correctamente
                ]),
            ])
        ;
    }

    public static function canCreateForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === SaleOrderStatus::PENDING;
    }

    public static function canViewAnyForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status !== SaleOrderStatus::PENDING;
    }

    public static function canDeleteAnyForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === SaleOrderStatus::PENDING;
    }

    public static function canDeleteForRecord(Model $ownerRecord, Model $record): bool
    {
        return $ownerRecord->status === SaleOrderStatus::PENDING;
    }

    public static function canEditAnyForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === SaleOrderStatus::PENDING;
    }

    public static function canEditForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === SaleOrderStatus::PENDING;
    }
}
