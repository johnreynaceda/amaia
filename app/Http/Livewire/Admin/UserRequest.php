<?php

namespace App\Http\Livewire\Admin;

use App\Models\Maintenance;
use App\Models\MaintenanceRequest;
use App\Models\UserInformation;
use Livewire\Component;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Tables;
// use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserRequest extends Component implements Tables\Contracts\HasTable
{

    use Tables\Concerns\InteractsWithTable;
    public $unit_number;
    public $request;
    public $requestor_data;


    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    Select::make('unit_number')->label('Requesting Unit')->searchable()->reactive()
                        ->options(
                            array_combine(
                                range(1, 200),
                                // Generate an array of numbers from 1 to 200
                                array_map(function ($number) {
                                    return 'Room ' . $number;
                                }, range(1, 200)) // Concatenate "room" to each number
                            )
                        ),
                ])

        ];
    }

    public function updatedUnitNumber()
    {
        $this->requestor_data = UserInformation::where('unit_number', '=', $this->unit_number)->first();
    }





    public function render()
    {
        return view('livewire.admin.user-request');
    }
}
