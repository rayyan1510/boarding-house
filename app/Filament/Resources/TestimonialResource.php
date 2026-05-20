<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Resources\TestimonialResource\RelationManagers;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Boarding House Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\Section::make('Informasi testimonial')
                    ->description('Data Testimonial yang melakukan transaksi pemesanan kamar.')
                    ->icon('heroicon-o-user')

                    ->schema([
                        // Buat form ini sesuai dengan field yang ada di tabel testimonials
                        // 1. form select photo testimonial
                        FileUpload::make('photo')
                            ->label('Sertakan Foto Testimonial')
                            ->helperText('Upload foto testimonial. Format: JPG, PNG, WEBP.')
                            ->image()
                            ->directory('testimonials/photos')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->columnSpanFull(),

                        // 2. form select boarding house
                        Forms\Components\Select::make('boarding_house_id')
                            ->label('Pilih Boarding House')
                            ->helperText('Pilih Boarding House yang dipesan')
                            ->relationship('boardingHouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        // 3. form nama lengkap penyewa
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap penyewa')
                            ->required()
                            ->maxLength(255),


                        // 4. from rating testimonial
                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->options([
                                1 => '⭐ 1 — Sangat Buruk',
                                2 => '⭐⭐ 2 — Buruk',
                                3 => '⭐⭐⭐ 3 — Cukup',
                                4 => '⭐⭐⭐⭐ 4 — Bagus',
                                5 => '⭐⭐⭐⭐⭐ 5 — Sangat Bagus',
                            ])
                            ->required()
                            ->native(false),

                        // 5. form textarea testimonial
                        Forms\Components\Textarea::make('content')
                            ->label('Isi Testimoni')
                            ->placeholder('Tulis ulasan kamu di sini...')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan data testimonial
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular(),
                // ->defaultImageUrl(asset('images/default-avatar.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('boardingHouse.name')
                    ->label('Boarding House')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Ulasan')
                    ->limit(60)
                    ->tooltip(fn(Testimonial $record) => $record->content),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn(int $state): string => str_repeat('⭐', $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //

                Tables\Filters\SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '⭐ 1',
                        2 => '⭐⭐ 2',
                        3 => '⭐⭐⭐ 3',
                        4 => '⭐⭐⭐⭐ 4',
                        5 => '⭐⭐⭐⭐⭐ 5',
                    ]),

                Tables\Filters\SelectFilter::make('boarding_house_id')
                    ->label('Rumah Kos')
                    ->relationship('boardingHouse', 'name'),

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
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text')
            ->emptyStateHeading('Belum Ada Testimoni')
            ->emptyStateDescription('Mulai tambahkan testimoni pertama untuk ditampilkan di aplikasi.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Testimoni')
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
