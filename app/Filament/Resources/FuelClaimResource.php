<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelClaimResource\Pages;
use App\Filament\Resources\FuelClaimResource\RelationManagers;
use App\Models\FuelClaim;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use App\Models\RateDefinition;
use App\Models\policy;
use App\Models\ExpenseType;
use App\Models\VehicleNumber;
use App\Models\VehicleType;
use Illuminate\Support\Facades\Auth;
use Closure;

use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FuelClaimResource extends Resource
{
    protected static ?string $model = FuelClaim::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'Expense';

    protected static ?string $navigationLabel = 'Fuel';
    protected static ?string $pluralModelLabel = 'Fuel Application List';
    protected static ?string $modelLabel = 'Fuel Request';
    protected static ?int $navigationSort = 7;


    public static function form(Form $form): Form
    {
        $currentUserId = Auth::id();
        $expense = FUEL_ID;
         return $form
             ->schema([
                 Forms\Components\Hidden::make('expense_type_id')
                 ->default($expense)
                 ->disabled()
                 ->required()
                 ->afterStateHydrated(function ($state, Closure $set){
                     $policy = policy::where('expense_type_id', $state)->value('id');
                     $attachment_required = RateDefinition::where('policy_id', $policy)->value('attachment_required');
                     $set('attachment_required', $attachment_required);
                 }),
                 Forms\Components\Hidden::make('user_id')
                 ->default($currentUserId)
                 ->disabled()
                 ->required(),
                 Forms\Components\TextInput::make('application_date')
                 ->type('date')
                 ->default(now()->toDateString())  // Set default value to current date
                 ->disabled()  // Make the field disabled
                 ->required(),
                 Forms\Components\TextInput::make('location')
                 ->required(),
                 Forms\Components\Select::make('vehicle_type')
                 ->options(
                    VehicleType::all()->pluck('vehicle_type', 'id')->toArray()
                )
                ->label('Vechicle type')
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Closure $set) {
                //  // Use the relationship to get the related vehicle numbers
                //     $vehicleType = VehicleType::with('vehiclenumber')->find($state);

                //     // Get the related vehicle numbers and transform them into options
                //     $vehicleNumbers = $vehicleType->vehiclenumber->pluck('vehicle_number', 'id')->toArray();

                //     // Set the options for the second select box, using an empty array if the relationship is not loaded
                //     //dd($vehicleNumbers);
                //    // dd($set('vehicle_no', $vehicleType));
                    $rate = null;
                    $set('mileage', $rate);
                    $set('initial_km', $rate);
                    $set('final_km', $rate);
                    $set('quantity', $rate);
                    $set('rate', $rate);
                    $set('amount', $rate);
                    $set('vehicle_no', $vehicleNumbers ?? []);


                }),
                Forms\Components\Select::make('vehicle_no')
                ->relationship('vehicle', 'vehicle_number', fn (Builder $query, callable $get) => $query->whereRaw("vehicle_type = ?",[$get('vehicle_type')]))
                ->label('Vehicle number')
                ->required()
                ->reactive()
                 ->afterStateUpdated(function ($state, Closure $set){
                     $mileage = VehicleNumber::whereRaw("id =?", [$state])->value("vehicle_mileage");
                     // dd($amount);
                     $set('mileage', $mileage);
                     $rate = null;
                     $set('initial_km', $rate);
                     $set('final_km', $rate);
                     $set('quantity', $rate);
                     $set('rate', $rate);
                     $set('amount', $rate);
 
                 }),
                 Forms\Components\DatePicker::make('date')
                 ->required(),
                 Forms\Components\TextInput::make('mileage')
                 ->numeric()
                 ->disabled()
                 ->required()
                 ->reactive()
                 ->label("Mileage"),
                 Forms\Components\TextInput::make('initial_km')
                 ->numeric()
                 ->minValue(0)
                 ->required()
                 ->reactive()
                 ->afterStateUpdated(function ($state, Closure $set){
                     $rate = null;
                     $set('final_km', $rate);
                     $set('quantity', $rate);
                     $set('rate', $rate);
                     $set('amount', $rate);
 
                 }),
                 Forms\Components\TextInput::make('final_km')
                 ->numeric()
                 ->minValue(0)
                 ->required()
                 ->reactive()
                 ->afterStateUpdated(function ($state, Closure $set, $get){
                     $finalkm = $get('final_km');
                     $initialkm = $get('initial_km');
                     $mileage = $get('mileage');
                     // dd($amount);
                     $quantity = ($finalkm - $initialkm) / $mileage;
                     $quantity = round($quantity, 2);
                     $set('quantity',$quantity);
                     $rate = null;
                     $set('rate', $rate);
                     $set('amount', $rate);
 
                 }),
                 Forms\Components\TextInput::make('quantity')
                 ->minValue(0)
                 ->required()
                 ->reactive(),
                 Forms\Components\TextInput::make('rate')
                 ->numeric()
                 ->minValue(0)
                 ->required()
                 ->reactive()
                 ->afterStateUpdated(function ($state, Closure $set, $get){
                     $rate = $get('rate');
                     $quantity = $get('quantity');
                     // dd($amount);
                     $amount = $rate * $quantity;
                     $amount = round($amount,2);
                     $set('amount',$amount);
                 }),
                 Forms\Components\TextInput::make('amount')
                 ->numeric(10,2)
                 ->disabled()
                 ->required()
                 ->reactive(),
                 Forms\Components\FileUpload::make('attachment')
                 ->preserveFilenames()
                 ->required(function(callable $get){
                     if($get('attachment_required') == true){
                         return true;
                     }else{
                         return false;
                     }
                 })                    
             ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('application_date')
                ->dateTime(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('vehicletype.vehicle_type'),
                Tables\Columns\TextColumn::make('vehicle.vehicle_number'),
                Tables\Columns\TextColumn::make('mileage'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFuelClaims::route('/'),
            'create' => Pages\CreateFuelClaim::route('/create'),
            'edit' => Pages\EditFuelClaim::route('/{record}/edit'),
        ];
    }    
}
