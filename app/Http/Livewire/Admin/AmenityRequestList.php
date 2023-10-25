<?php

namespace App\Http\Livewire\Admin;

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

use Filament\Tables\Columns\ViewColumn;

class AmenityRequestList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    public $unitNumber;

    public $view_visitor = false;

    public function mount($unitNumber)
    {
        $this->unitNumber = $unitNumber;
    }

    protected function getTableQuery(): Builder
    {

        return AmenityRequest::query()->whereHas('user', function ($user) {
            $user->whereHas('user_information', function ($record) {
                $record->where('unit_number', $this->unitNumber);
            });
        });

    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('amenity.name')->label('TYPE')->searchable()->sortable(),
            TextColumn::make('user.user_information.unit_number')->label('UNIT')->searchable()->formatStateUsing(
                function ($record) {
                    return 'Room ' . ($record->user->user_information->unit_number);
                }
            )->sortable(),
            TextColumn::make('request_date')->label('REQUESTED DATE')->date()->searchable()->sortable(),
            TextColumn::make('preffered_time')->label('PREFFERED TIME')->formatStateUsing(
                function ($record) {
                    return \Carbon\Carbon::parse($record->preffered_time)->format('H:i A');
                }
            )->searchable()->sortable(),
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
            TextColumn::make('amount')->label('AMOUNT')->searchable()->formatStateUsing(
                function ($record) {
                    return '₱' . number_format($record->amount, 2);
                }
            )->sortable(),
            TextColumn::make('date_completed')->label('COMPLETED DATE')->date()->searchable()->sortable(),
            // TextColumn::make('visitor')->label('REGISTERED VISITOR')->formatStateUsing(
            //     function ($record) {
            //         return explode(',', $record->visitors);
            //     }
            // )->searchable()->sortable(),
            ViewColumn::make('visitor')->label('VISITORS')->view('admin.filament.visitors'),

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
        return view('livewire.admin.amenity-request-list');
    }
}
