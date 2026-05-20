<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Boarding House Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Informasi Transaksi')
                    ->description('Data penyewa kost yang melakukan transaksi pemesanan kamar.')
                    ->icon('heroicon-o-user')

                    ->schema([

                        // Buat form ini sesuai dengan field yang ada di tabel Trasnsactions
                        // 1. form code transaction
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Transaksi')
                            ->helperText('Kode unik untuk setiap transaksi, contoh: TRX-20240001')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        // 2. form select boarding house
                        Forms\Components\Select::make('boarding_house_id')
                            ->label('Pilih Rumah Kost')
                            ->helperText('Pilih rumah kost yang dipesan')
                            ->relationship('boardingHouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('room_id', null)), //reset field room_id ketika boarding_house_id berubah

                        // 3. form select room
                        Forms\Components\Select::make('room_id')
                            ->label('Pilih Kamar')
                            ->helperText('Pilih kamar yang dipesan (*mohon tunggu sebentar setelah memilih rumah kost)')
                            ->relationship(
                                name: 'room',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query, Forms\Get $get) =>
                                $query->where('boarding_house_id', $get('boarding_house_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->preload()
                            ->required(),

                        // 4. form nama lengkap penyewa
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        // 5. form email penyewa
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('contoh@email.com')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        // 6. form nomor telepon penyewa
                        Forms\Components\TextInput::make('phone_number')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->placeholder('08xxxxxxxxxx')
                            ->required()
                            ->maxLength(20)
                            ->columnSpanFull(),

                        // 7. form metode pembayaran
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'down_payment' => 'Down Payment',
                                'full_payment' => 'Full Payment',
                            ])
                            ->placeholder('Pilih metode pembayaran')
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),

                        // 8. form status pembayaran
                        Forms\Components\Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending'   => 'Menunggu Pembayaran',
                                'paid'      => 'Sudah Dibayar',
                                'cancelled' => 'Dibatalkan',
                                'refunded'  => 'Dikembalikan',
                            ])
                            ->required()
                            ->native(false)
                            ->default('pending')
                            ->columnSpanFull(),

                        // 9. form tanggal mulai sewa
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai Sewa')
                            ->helperText('Pilih tanggal mulai sewa kost')
                            ->required()
                            // ->native(false)
                            ->displayFormat('d M Y')
                            // ->suffixIcon('heroicon-m-calendar')
                            ->minDate(now())
                            ->columnSpan(1),

                        // 10. form durasi sewa
                        Forms\Components\TextInput::make('duration')
                            ->label('Durasi Sewa')
                            ->helperText('Masukkan durasi sewa')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(24),

                        // 11. form total pembayaran
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Pembayaran')
                            ->required()
                            ->numeric()
                            ->prefix('IDR')
                            ->minValue(0),

                        // 12. form tanggal transaksi
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            // ->native(false)
                            ->displayFormat('d M Y')
                            ->default(now()),

                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan data transaksi pada tabel singkat
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Penyewa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No. Telepon')
                    ->searchable()
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('boardingHouse.name')
                    ->label('Nama Kos')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('room.number')
                    ->label('Kamar')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('payment_status'),
                Tables\Columns\TextColumn::make('total_amount')->prefix('IDR'),
                Tables\Columns\TextColumn::make('transaction_date')->dateTime('d M Y')->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), //fitur delete per data pada tabel
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-credit-card')
            ->emptyStateHeading('Belum Ada Transaksi')
            ->emptyStateDescription('Mulai tambahkan transaksi pertama untuk ditampilkan di aplikasi.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Transaksi')
                    ->icon('heroicon-o-plus'),
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
