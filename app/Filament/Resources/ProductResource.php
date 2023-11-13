<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use App\Models\Category;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TextInput::make('id')->numeric(),
                TextInput::make('name'),
                SelectTree::make('category_id')
                    ->relationship('category', 'name', 'category_id', function ($query) {
                        return $query;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->numeric(),
                TextColumn::make('name'),
                TextColumn::make('category_id')->numeric(),
            ])
            ->filters([
                Filter::make('tree')
                    ->form([
                        SelectTree::make('category')
                            ->relationship('category', 'name', 'category_id')
                            ->independent(false)
                            ->enableBranchNode()
                            // ->alwaysOpen()
                            // ->expandSelected(false) // only works if field is dependent
                            // ->parentNullValue(-1) // default = null
                            // ->defaultOpenLevel(2)
                            // ->direction('top')
                            // ->grouped(false)
                            // ->clearable(false)
                            // ->searchable()
                            ,
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['category'], function ($query, $categories) {
                            return $query->whereHas('category', fn ($query) =>
                                // $query->dd()
                                //"select * from `categories` where `products`.`category_id` = `categories`.`id`"
                                $query->whereIn('id', [ $categories ])
                            );
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['category']) {
                            return null;
                        }
                        return implode(', ', Category::whereIn('id', [ $data['category'] ])->get()->pluck('name')->toArray());
                    })
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
