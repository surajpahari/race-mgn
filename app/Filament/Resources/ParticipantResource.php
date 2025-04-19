<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ParticipantImporter;
use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('fname')
                    ->required()
                    ->maxLength(255),
                TextInput::make('lname')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(15)
                    ->default(null),

                DatePicker::make('dob')
                    ->required()
                    ->live(),

                Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->native(false),
                Select::make('race_id')
                    ->relationship(name: 'race', titleAttribute: 'name')
                    ->searchable()
                    ->live()
                    ->required(),

                Select::make('age_group_id')
                    ->label('Age Group')
                    ->options(function (Get $get) {
                        $raceId = $get('race_id');
                        $dob = $get('dob');

                        if (! $raceId || ! $dob) {
                            return [];
                        }

                        $age = \Carbon\Carbon::parse($dob)->age;

                        return \App\Models\AgeGroup::whereHas('races', function ($query) use ($raceId) {
                            $query->where('race_id', $raceId);
                        })
                            ->where('from', '<=', $age)
                            ->where('to', '>=', $age)
                            ->pluck('name', 'id');
                    })->native()
                    ->hidden(fn (Get $get) => ! ($get('race_id') && $get('dob')))
                    ->helperText(function (Get $get) {
                        $raceId = $get('race_id');
                        $dob = $get('dob');

                        if (! $raceId || ! $dob) {
                            return null;
                        }

                        $age = \Carbon\Carbon::parse($dob)->age;

                        $hasGroup = \App\Models\AgeGroup::whereHas('races', function ($query) use ($raceId) {
                            $query->where('race_id', $raceId);
                        })
                            ->where('from', '<=', $age)
                            ->where('to', '>=', $age)
                            ->exists();

                        return $hasGroup ? null : '⚠️ No age group available for this participant.';
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->headerActions([
            ImportAction::make()
                ->importer(ParticipantImporter::class),
        ])->
            columns([
                TextColumn::make('id')->label('bib/chip_id'),
                TextColumn::make('fname')
                    ->searchable(),
                TextColumn::make('lname')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('dob')->label('age')->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->dob)->age),
                TextColumn::make('gender'),
                TextColumn::make('race.distance')
                    ->label('Race Distance')
                    ->sortable()
                    ->searchable(),
                TagsColumn::make('ageGroup.name')
                    ->label('Age Group')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}
