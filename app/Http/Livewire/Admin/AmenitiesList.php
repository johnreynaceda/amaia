<?php

namespace App\Http\Livewire\Admin;

use App\Models\Amenity;
use Livewire\Component;
use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;

class AmenitiesList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Amenity::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label('NAME')->searchable(),
            TextColumn::make('amount')->label('AMOUNT')->searchable()->formatStateUsing(
                function ($record) {
                    return '₱' . number_format($record->amount, 2);
                }
            ),

        ];

    }

    protected function getTableHeaderActions()
    {
        return [
            Action::make('amenity')->label('New Amenity')->button()->icon('heroicon-o-plus')->size('sm')->color('gray')
                ->action(function ($record, $data): void {
                    Amenity::create([
                        'name' => $data['name'],
                        'amount' => $data['amount'],
                    ]);
                    sweetalert()->addSuccess('Amenity has been created');
                })
                ->form([

                    Grid::make(1)
                        ->schema([
                            TextInput::make('name')->label('Name')->required(),
                            TextInput::make('amount')->label('Amount')->required(),

                        ])
                ])->modalWidth('xl')
        ];

    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()->action(
                function ($record, $data) {
                    $record->update([
                        'name' => $data['name'],
                        'amount' => $data['amount'],
                    ]);
                    sweetalert()->addSuccess('Amenity has been updated');
                }
            )->form(
                    function ($record) {
                        return [
                            TextInput::make('name')->label('Name')->required(),
                            TextInput::make('amount')->label('Amount')->required(),
                        ];
                    }
                )->modalWidth('xl')->modalHeading('Edit Amenity'),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.amenities-list');
    }
}