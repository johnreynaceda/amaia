<?php

namespace App\Http\Livewire\Admin;

use App\Models\Message;
use Livewire\Component;

use App\Models\Maintenance;
use App\Models\MaintenanceRequest;
use App\Models\UserInformation;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Tables;
// use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;

class MaintenanceRequestList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    public $unitNumber;

    public function mount($unitNumber)
    {
        $this->unitNumber = $unitNumber;
    }

    protected function getTableQuery(): Builder
    {

        return MaintenanceRequest::query()->whereHas('user', function ($user) {
            $user->whereHas('user_information', function ($record) {
                $record->where('unit_number', $this->unitNumber);
            });
        });

    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('maintenance.name')->label('TYPE')->searchable()->sortable(),
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

                        $message = Message::create([
                            'user_id' => auth()->user()->id,
                            'resident_name' => $record->user->name,
                            'receiver_id' => $record->user_id,
                            'complainee_unit' => $record->user->user_information->unit_number,
                            'label_type' => 'Maintenance',
                            // 'nature_of_complaint' => $this->complaint,
                            'subject' => 'null',
                            'message' => 'Your Maintenance (' . Maintenance::Where('id', $record->maintenance_id)->first()->name . ') request for UNIT ' . $record->user->user_information->unit_number . ' is approved by Admin. To proceed, please settle the amount on the lobby.',
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
                Action::make('put_amount')->label('Insert Amount')->icon('heroicon-o-cash')->color('success')->visible(
                    function ($record) {
                        return $record->status == 'approved';
                    }
                )->hidden(function ($record) {
                    return $record->amount != null;
                })->action(
                        function ($record, $data) {
                            $record->update([
                                'amount' => $data['request_amount'],
                            ]);
                            sweetalert()->addSuccess('Amount Added');
                        }
                    )->form([
                            TextInput::make('request_amount')->numeric()
                        ])->modalHeading('Insert Amount')->modalWidth('lg')
            ]),

        ];
    }
    public function render()
    {
        return view('livewire.admin.maintenance-request-list');
    }
}
