<?php

namespace App\Http\Livewire\Admin;

use App\Models\UserInformation;
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

class UnitOwnerList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return UserInformation::query()->where('resident_type', 'Unit Owner');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('user.name')->label('NAME')->searchable(),
            TextColumn::make('resident_type')->label('RESIDENT TYPE')->searchable(),
            TextColumn::make('unit_number')->label('UNIT NO.')->formatStateUsing(
                function ($record) {
                    return 'Room ' . ($record->unit_number ?? 'null');
                }
            )->searchable(),
            TextColumn::make('date_of_birth')->date()->label('BIRTHDATE')->searchable(),
            TextColumn::make('gender')->label('GENDER')->searchable(),
            TextColumn::make('civil_status')->label('CIVIL STATUS')->searchable(),
            TextColumn::make('phone_number')->label('PHONE NUMBER')->searchable(),
        ];

    }


    public function render()
    {
        return view('livewire.admin.unit-owner-list');
    }
}
