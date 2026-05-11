<?php

namespace App\Filament\Resources\LoincAnswers;

use App\Filament\Imports\LoincAnswerImporter;
use App\Filament\Resources\LoincAnswers\Pages\CreateLoincAnswer;
use App\Filament\Resources\LoincAnswers\Pages\EditLoincAnswer;
use App\Filament\Resources\LoincAnswers\Pages\ListLoincAnswers;
use App\Filament\Resources\LoincAnswers\Pages\ViewLoincAnswer;
use App\Models\LoincAnswer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LoincAnswerResource extends Resource
{
    protected static ?string $model = LoincAnswer::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'LOINC';

    protected static ?string $modelLabel = 'LOINC Answer';

    protected static ?string $pluralModelLabel = 'Loinc Answers';

    protected static ?int $navigationSort = 92;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('list_id')->searchable(),
                TextColumn::make('string_id')->searchable(),
                TextColumn::make('display_text')->searchable(),
                TextColumn::make('name')->searchable(),
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
            ->headerActions([
                ImportAction::make()
                    ->importer(LoincAnswerImporter::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextEntry::make('list_id')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Click on the loinc number for even more details')
                        ->label('List ID Detailed Information')
                        ->url(fn (LoincAnswer $record): string => url('https://loinc.org/'.$record->list_id))
                        ->openUrlInNewTab(),
                    TextEntry::make('string_id')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Click on the loinc number for even more details')
                        ->label('String ID Detailes Information')
                        ->url(fn (LoincAnswer $record): string => url('https://loinc.org/'.$record->string_id))
                        ->openUrlInNewTab(),
                    TextEntry::make('display_text')->label('Display Text'),
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('ext_code_display_name')->label('Ext Code Display Name'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoincAnswers::route('/'),
            'create' => CreateLoincAnswer::route('/create'),
            'edit' => EditLoincAnswer::route('/{record}/edit'),
            'view' => ViewLoincAnswer::route('/{record}'),
        ];
    }
}
