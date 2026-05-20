<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    // ── Left column (main content) ────────────────────────────
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Informasi Kategori')
                            ->description('Lengkapi data kategori yang akan ditampilkan di aplikasi.')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                // Buat form ini sesuai dengan field yang ada di tabel categories
                                // 1. form input name
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->helperText('Contoh: Rumah Kost, Apartemen, Villa')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama kategori')
                                    ->live(debounce: 1000) //fitur live debounce dan akan tampil pada form lainnya ketika user sudah selesai mengetik (1000)
                                    // ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->unique(
                                        table: \App\Models\Category::class,
                                        column: 'name',
                                        ignoreRecord: true  // ← saat edit, record sendiri dikecualikan
                                    )
                                    ->validationMessages([
                                        'unique' => 'Nama kategori :input sudah terdaftar!.',
                                    ])
                                    ->columnSpanFull(),

                                // 2. form input slug
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->helperText('Slug otomatis dibuat dari nama kategori.')
                                    ->placeholder('contoh: rumah-kost')
                                    ->unique(
                                        table: \App\Models\Category::class,
                                        column: 'slug',
                                        ignoreRecord: true  // ← saat edit, record sendiri dikecualikan
                                    )
                                    ->columnSpanFull(),
                            ]),

                    ])->columnSpan(['lg' => 2]),


                    // ── Right column (media & meta) 
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Foto Kategori')
                            ->description('Upload foto representatif kategori.')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                // 1. form upload image
                                Forms\Components\FileUpload::make('image')
                                    ->label('Gambar Kategori')
                                    ->helperText('Format: JPG, PNG, WEBP. Maks. 2 MB')
                                    ->image()
                                    ->directory('categories')
                                    ->required()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->columnSpanFull(),
                            ])

                    ])->columnSpan(['lg' => 1]),
                ]
            )->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan data category pada tabel
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->extraImgAttributes(['style' => 'border-radius: 8px']),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
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
            ->defaultSort('created_at', 'desc')
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
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('Belum Ada Kategori')
            ->emptyStateDescription('Mulai tambahkan kategori pertama untuk ditampilkan di aplikasi.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kategori')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
