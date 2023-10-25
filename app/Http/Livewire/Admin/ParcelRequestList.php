<?php

namespace App\Http\Livewire\Admin;

use App\Models\PassRequest;
use Livewire\Component;
use App\Models\Maintenance;
use App\Models\AmenityRequest;
use App\Models\UserInformation;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;

class ParcelRequestList extends Component implements Tables\Contracts\HasTable
{

    use Tables\Concerns\InteractsWithTable;
    public $unitNumber;

    public function mount($unitNumber)
    {
        $this->unitNumber = $unitNumber;
    }


    protected function getTableQuery(): Builder
    {

        return PassRequest::query()->whereHas('pass', function ($record) {
            $record->where('name', 'like', '%' . 'Parcel Pass' . '%');
        })->whereHas('user', function ($record) {
            $record->whereHas('user_information', function ($record) {
                $record->where('unit_number', $this->unitNumber);
            });
        });

    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('pass.name')->label('TYPE')->searchable()->sortable(),
            TextColumn::make('contact_number')->label('CONTACT NUMBER')->searchable()->sortable(),
            TextColumn::make('quantity')->label('# OF EXPECTED PARCEL/S')->searchable()->sortable(),
            TextColumn::make('created_at')->label('DATE SUBMITTED')->date()->searchable()->sortable(),
            TextColumn::make('request_date')->label('EXPECTED DATE OF ARRIVAL')->date()->searchable()->sortable(),

            BadgeColumn::make('status')->label('STATUS')
                ->enum([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'completed' => 'Completed',
                ])->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'primary' => 'completed'
                    ])->sortable(),
            TextColumn::make('date_completed')->label('COMPLETED DATE')->date()->searchable()->sortable(),
        ];

    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Action::make('approve')->label('Approved')->icon('heroicon-o-thumb-up')->action(
                    function ($record) {
                        $record->update([
                            'status' => 'approved',
                        ]);
                        sweetalert()->addSuccess('Request approved');
                    }
                )->visible(
                        function ($record) {
                            return $record->status == 'pending';
                        }
                    ),
                Action::make('complete')->label('Completed')->icon('heroicon-o-check-circle')->visible(
                    function ($record) {
                        return $record->status == 'approved';
                    }

                )->action(
                        function ($record) {
                            $record->update([
                                'status' => 'completed',
                                'date_completed' => now(),
                            ]);
                            sweetalert()->addSuccess('Request completed');
                        }
                    ),
            ]),

        ];
    }

    public function render()
    {
        return view('livewire.admin.parcel-request-list');
    }
}