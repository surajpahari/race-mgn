<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ParticipantImporter;
use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use App\Models\Race;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(15)
                    ->default(null),

                Forms\Components\DatePicker::make('dob')
                    ->required()
                    ->live(), // important for live updates

                Forms\Components\TextInput::make('gender'),

                Forms\Components\Select::make('race_id')
                    ->relationship(name: 'race', titleAttribute: 'distance')
                    ->searchable()
                    ->live()
                    ->required(),

                Forms\Components\Select::make('age_group_id')
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
                    })
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

    /* public static function form(Form $form): Form */
    /* { */
    /*    return $form */
    /*        ->schema([ */
    /*            Forms\Components\TextInput::make('fname') */
    /*                ->required() */
    /*                ->maxLength(255), */
    /*            Forms\Components\TextInput::make('lname') */
    /*                ->required() */
    /*                ->maxLength(255), */
    /*            Forms\Components\TextInput::make('email') */
    /*                ->email() */
    /*                ->required() */
    /*                ->maxLength(255), */
    /*            Forms\Components\TextInput::make('phone') */
    /*                ->tel() */
    /*                ->maxLength(15) */
    /*                ->default(null), */
    /*            Forms\Components\DatePicker::make('dob') */
    /*                ->required(), */
    /*            Forms\Components\TextInput::make('gender'), */
    /*            Forms\Components\Select::make('race_id') */
    /*                ->relationship(name: 'race', titleAttribute: 'distance') */
    /*                ->searchable() */
    /*                ->searchPrompt('Search race by distance') */
    /*                ->required() */
    /*                ->live(), // Add live() to update when value changes */
    /**/
    /*            Forms\Components\Select::make('age_group_id') */
    /*                ->label('Age Group') */
    /*                ->options(function (Get $get) { */
    /*                    $raceId = $get('race_id'); */
    /*                    if (! $raceId) { */
    /*                        return []; */
    /*                    } */
    /**/
    /*                    return AgeGroup::whereHas('races', function ($query) use ($raceId) { */
    /*                        $query->where('race_id', $raceId); */
    /*                    })->pluck('name', 'id'); */
    /*                }) */
    /*                ->searchable() */
    /*                ->required() */
    /*                ->hidden(fn (Get $get) => ! $get('race_id')), */
    /*        ]); */
    /* } */

    public static function table(Table $table): Table
    {
        return $table->headerActions([
            ImportAction::make()
                ->importer(ParticipantImporter::class)
                ->form([
                    /* Forms\Components\Select::make('race_id') */
                    /*    ->label('Race') */
                    /*    ->required(), */
                ]),
        ])->
            columns([
                Tables\Columns\TextColumn::make('fname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dob')->label('age')->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->dob)->age),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('race.distance')
                    ->label('Race Distance')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TagsColumn::make('ageGroup.name')
                    ->label('Age Group')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
