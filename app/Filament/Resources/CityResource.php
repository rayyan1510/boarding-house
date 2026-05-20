<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Left column (main content) ────────────────────────────
                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Section::make('Informasi Kota')
                            ->description('Lengkapi data kota yang akan ditampilkan di aplikasi.')
                            ->icon('heroicon-o-building-office-2') //icon
                            ->schema([
                                // Buat form ini sesuai dengan field yang ada di tabel cities

                                // 1. form input name
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Kota')
                                    ->helperText('Contoh: Jakarta, Bandung, Surabaya')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama kota')
                                    ->live(debounce: 1000) //fitur live debounce dan akan tampil pada form lainnya ketika user sudah selesai mengetik (1000ms)
                                    // ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->unique(
                                        table: \App\Models\City::class,
                                        column: 'name',
                                        ignoreRecord: true  // ← saat edit, record sendiri dikecualikan
                                    )
                                    ->validationMessages([
                                        'unique' => 'Nama kota :input sudah terdaftar!.',
                                    ])
                                    ->columnSpanFull(),

                                // 2. form input slug
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->helperText('Slug otomatis dibuat dari nama kota.')
                                    ->placeholder('contoh: kota-jakarta')
                                    ->unique(
                                        table: \App\Models\City::class,
                                        column: 'slug',
                                        ignoreRecord: true
                                    )
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])->columnSpan(['lg' => 2]),

                // ── Right column (media & meta) ───────────────────────────
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->description('Upload foto representatif untuk kota ini.')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                // 3. form upload image
                                Forms\Components\FileUpload::make('image')
                                    ->label('Gambar Kota')
                                    ->helperText('Format: JPG, PNG, WEBP. Maks. 2 MB')
                                    ->image()
                                    ->directory('cities')
                                    ->required()
                                    // ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan data city pada tabel
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar Kota')
                    ->extraImgAttributes(['style' => 'border-radius: 8px']),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kota')
                    ->searchable()
                    ->weight('semibold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ->emptyStateIcon('heroicon-o-map-pin')
            ->emptyStateHeading('Belum Ada Kota')
            ->emptyStateDescription('Mulai tambahkan kota pertama untuk ditampilkan di aplikasi.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kota')
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
