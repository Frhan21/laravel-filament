<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationGroup = 'Transaksi dan Pembayaran';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = "Transaksi";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required(),

                Select::make('holiday_package_id')
                    ->label('Holiday Package')
                    ->relationship('holiday_package', 'name', function ($query) {
                        return $query->where('availability', '1');
                    })
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Ambil harga paket berdasarkan ID yang dipilih
                        $holidayPackage = \App\Models\HolidayPackage::find($state);
                        $totalAmount = $holidayPackage ? $holidayPackage->price : 0;
                        // Set nilai total_amount
                        $set('total_amount', $totalAmount);
                    }),

                DatePicker::make('transactionDate')
                    ->label('Transaction Date')
                    ->required(),

                Radio::make('status')
                    ->label('Status')
                    ->options([
                        '1' => 'Paid',
                        '0' => 'Unpaid'
                    ])
                    ->required(),
                Select::make('payment_method')
                    ->options([
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer Bank'
                    ]),
                // Menampilkan total_amount sebagai readonly
                TextInput::make('total_amount')
                    ->label('Total Amount')
                    ->readonly()
                    ->numeric()
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.'))
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->money('IDR')
                    ->label('Harga Paket')
                    ->getStateUsing(fn($record) => $record->holiday_package->price ?? 0),
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('holiday_package.name')->label('Holiday Package'),
                TextColumn::make('transactionDate')
                    ->label('Transaction Date')
                    ->datetime('l, d F Y'),
                BooleanColumn::make('status')
                    ->label('Status')
                    ->trueIcon('heroicon-o-currency-dollar')
                    ->falseIcon('heroicon-o-currency-dollar')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')->label('Created At')
                    ->dateTime('H:i')
                    ->formatStateUsing(fn($state) => $state->format('H:i') . " WIB")
                    ->timezone('Asia/Jakarta')
            ])
            ->filters([
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
