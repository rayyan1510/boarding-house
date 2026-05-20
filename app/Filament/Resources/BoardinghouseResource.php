<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoardinghouseResource\Pages;
use App\Filament\Resources\BoardinghouseResource\RelationManagers;
use App\Models\Boardinghouse;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


class BoardinghouseResource extends Resource
{
    protected static ?string $model = Boardinghouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Boarding House Management';

    protected static ?string $navigationLabel = 'Boarding Houses';
    protected static ?string $pluralModelLabel = 'Boarding Houses';
    protected static ?string $modelLabel = 'Boarding Houses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Informasi Utama')
                            ->icon('heroicon-m-home')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                // Buat form ini sesuai dengan field yang ada di tabel boarding house
                                // 1. form upload image
                                FileUpload::make('thumbnail')
                                    ->label('Thumbnail Boarding House')
                                    ->helperText('Upload thumbnail boarding house. Format: JPG, PNG, WEBP.')
                                    ->image()
                                    ->directory('boarding-houses/thumbnails')
                                    ->required()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->columnSpanFull(),

                                // 2. form input name boarding house
                                TextInput::make('name')
                                    ->label('Nama Boarding House')
                                    ->helperText('Contoh: Rumah Kost, Apartemen, Villa')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama boarding house')
                                    ->debounce(1000) //fitur live debounce dan akan tampil pada form lainnya ketika user sudah selesai mengetik (1000)
                                    // ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    }),

                                // 3. form input slug
                                TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->helperText('Slug otomatis dibuat dari nama boarding house.')
                                    ->placeholder('contoh: rumah-kost'),

                                // 4. form select city
                                Select::make('city_id')
                                    ->label('Kota')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                // 5. form select category
                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                // 6. form text editor description
                                RichEditor::make('description')
                                    ->label('Deskripsi')
                                    ->helperText('Deskripsikan boarding house secara detail untuk memberikan informasi yang jelas kepada pengguna.')
                                    ->required()
                                    ->columnSpanFull(),

                                // 7. form text input harga
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->helperText('Harga per malam untuk boarding house ini.')
                                    ->placeholder('contoh: 100000')
                                    ->prefix('IDR')
                                    ->suffix(',-')
                                    ->minValue(0)
                                    ->columnSpanFull(),

                                // 8. form textarea address
                                Textarea::make('address')
                                    ->label('Alamat')
                                    ->helperText('Masukkan alamat lengkap boarding house, termasuk nama jalan, nomor, dan informasi tambahan lainnya.')
                                    ->required()
                                    ->columnSpanFull(),

                            ])->columns(2),

                        // ── Tab 2: Bonus ──────────────────────────────────
                        Tabs\Tab::make('Bonus')
                            ->icon('heroicon-m-gift')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                // Form ini akan menampilkan form untuk mengelola data bonus yang berelasi dengan boarding house. 
                                // Kita akan menggunakan repeater untuk menampilkan form inputan bonus secara dinamis sesuai dengan jumlah bonus yang ingin ditambahkan pada boarding house tersebut.
                                Repeater::make('bonuses')
                                    ->relationship('bonuses')
                                    ->schema([
                                        // Buat form ini sesuai dengan field yang ada di tabel bonus
                                        // 1. form upload image
                                        FileUpload::make('image')
                                            ->label('Bonus Image')
                                            ->helperText('Upload image untuk bonus boarding house. Format: JPG, PNG, WEBP.')
                                            ->image()
                                            ->directory('boarding-houses/bonuses')
                                            ->required()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->columnSpanFull(),

                                        // 2. form input name bonus
                                        TextInput::make('name')
                                            ->label('Nama Bonus')
                                            ->helperText('Contoh: Diskon 50%, Gratis Wi-Fi, Sarapan Gratis')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Masukkan nama bonus'),

                                        // 3. form textarea description bonus
                                        Textarea::make('description')
                                            ->label('Deskripsi Bonus')
                                            ->helperText('Contoh: Dapatkan diskon 50% untuk pemesanan pertama Anda, nikmati akses Wi-Fi gratis selama menginap, atau dapatkan sarapan gratis setiap pagi.')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Masukkan deskripsi bonus')
                                    ])
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Bonus Baru'),


                            ]),

                        // ── Tab 3: Kamar ──────────────────────────────────
                        Tabs\Tab::make('Kamar')
                            ->icon('heroicon-m-home-modern')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                // Foorm ini akan menampilkan form untuk mengelola data kamar yang berelasi dengan boarding house. Kita akan menggunakan repeater untuk menampilkan form inputan kamar secara dinamis sesuai dengan jumlah kamar yang ingin ditambahkan pada boarding house tersebut.
                                Repeater::make('rooms')
                                    ->relationship('rooms')
                                    ->schema([
                                        // Buat form ini sesuai dengan field yang ada di tabel kamar
                                        // 1. form input name kamar
                                        TextInput::make('name')
                                            ->label('Nama Kamar')
                                            ->helperText('Contoh: Kamar Standar, Kamar Superior, Kamar Deluxe')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Masukkan nama kamar'),

                                        // 2. form input tipe kamar
                                        TextInput::make('room_type')
                                            ->label('Tipe Kamar')
                                            ->helperText('Contoh: Single, Double, Suite')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Masukkan tipe kamar'),

                                        // 3. form input luas kamar
                                        TextInput::make('square_feet')
                                            ->label('Luas Kamar (ft²)')
                                            ->helperText('Contoh: 100, 150, 200')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('Masukkan luas kamar'),


                                        // 4. form input kapasistas kamar
                                        TextInput::make('capacity')
                                            ->label('Kapasitas Kamar')
                                            ->helperText('Contoh: 1, 2, 3')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('Masukkan kapasitas kamar'),

                                        // 5. form text input harga kamar per bulan
                                        TextInput::make('price_per_month')
                                            ->required()
                                            ->numeric()
                                            ->helperText('Harga per bulan untuk boarding house ini.')
                                            ->placeholder('contoh: 100000')
                                            ->minValue(0)
                                            ->prefix('IDR'),

                                        // 6. form switch untuk status ketersediaan kamar
                                        Forms\Components\Toggle::make('is_available')
                                            ->label('Ketersediaan Kamar')
                                            ->helperText('Aktifkan jika kamar tersedia untuk disewa, nonaktifkan jika kamar sedang tidak tersedia.')
                                            ->required(),

                                        // 7. form upload image kamar
                                        Repeater::make('images')
                                            ->relationship('images')
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label('Foto Kamar')
                                                    ->helperText('Upload image untuk kamar boarding house. Format: JPG, PNG, WEBP.')
                                                    ->image()
                                                    ->directory('boarding-houses/rooms')
                                                    ->required()
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                            ])->columnSpanFull(),

                                    ])
                                    ->collapsible()
                                    ->itemLabel(
                                        fn(array $state): ?string => ($state['name'] ?? 'Kamar Baru') .
                                            (isset($state['room_type']) ? ' — ' . $state['room_type'] : '')
                                    )
                                    ->columnSpanFull(),

                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan data boarding house pada tabel
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->size(56)
                    ->extraImgAttributes([
                        'style' => 'border-radius: 10px; object-fit: cover;',
                    ]),


                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga / Bulan')
                    ->money('IDR', true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Kota')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                // Tables\Columns\TextColumn::make('is_available'),

                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->address)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                // filter untuk mencari boarding house berdasarkan kota    
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Kota')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),

                // filter untuk mencari boarding house berdasarkan kategori
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

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
            ->emptyStateIcon('heroicon-o-home-modern')
            ->emptyStateHeading('Belum Ada Boarding House')
            ->emptyStateDescription('Mulai tambahkan boarding house pertama untuk ditampilkan di aplikasi.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Boarding House')
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
            'index' => Pages\ListBoardinghouses::route('/'),
            'create' => Pages\CreateBoardinghouse::route('/create'),
            'edit' => Pages\EditBoardinghouse::route('/{record}/edit'),
        ];
    }
}
