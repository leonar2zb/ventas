<?php

namespace App\Filament\Resources;

use App\Enums\ProductCategory;
use App\Enums\Unit;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\SelectFilter;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(999999.99)
                    ->default(0),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->maxLength(500),

                Forms\Components\FileUpload::make('image')
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->image()
                    ->maxSize(1024), // Límite de tamaño en KB (1MB)

                Forms\Components\Select::make('category')
                    ->options(
                        collect(ProductCategory::cases())
                            ->mapWithKeys(fn(ProductCategory $category) => [$category->value => $category->label()])
                    )
                    ->label('Category')
                    ->required(),

                Forms\Components\Select::make('unit')
                    ->options(collect(Unit::cases())->mapWithKeys(fn($case) => [$case->value => $case->value])->toArray()) // Usa los valores del enum
                    ->label('Unit')
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(999999.999),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                ImageColumn::make('image_url') // Aquí se usa el campo que guarda la URL
                    ->disk('public') // Asegura que la imagen se busque en el almacenamiento público
                    ->label('Picture')
                    ->height(50) // Opcional: ajusta el tamaño
                    ->width(50), // Opcional: ajusta el tamaño,                          

                TextColumn::make('category')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('unit')
                    ->label('Unit')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([

                SelectFilter::make('category')
                    ->multiple()
                    ->options(collect(ProductCategory::cases())
                        ->mapWithKeys(fn(ProductCategory $category) => [$category->value => $category->label()]))
                //
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
