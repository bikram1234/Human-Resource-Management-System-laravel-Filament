<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NodueResource\Pages;
use App\Filament\Resources\NodueResource\RelationManagers;
use App\Models\Nodue;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;


class NodueResource extends Resource
{
    protected static ?string $model = Nodue::class;

    protected static ?string $navigationIcon = 'heroicon-o-check';
    protected static ?string $navigationGroup = 'No Due';
    protected static ?string $navigationLabel = 'Apply';





    public static function form(Form $form): Form
    {
        $currentUserId = Auth::id();

        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                ->default($currentUserId)
                ->disabled()
                ->required(),
                Forms\Components\TextInput::make('date')
                    ->type('date')
                    ->default(now()->toDateString())  // Set default value to current date
                    ->disabled()  // Make the field disabled
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->rows(2)
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('remark')


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListNodues::route('/'),
            'create' => Pages\CreateNodue::route('/create'),
            'edit' => Pages\EditNodue::route('/{record}/edit'),
        ];
    }    
}
