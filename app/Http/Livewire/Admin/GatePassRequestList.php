<?php

namespace App\Http\Livewire\Admin;

use App\Models\Message;
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
use Filament\Tables\Filters\SelectFilter;

class GatePassRequestList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    public $unitNumber;

    public function mount($unitNumber)
    {
        $this->unitNumber = $unitNumber;
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('pass.name')->label('TYPE')->sortable(),
            TextColumn::make('building')->label('BLDG')->sortable(),
            TextColumn::make('unit')->label('UNIT')->searchable()->sortable(),
            TextColumn::make('visitor_name')->label('VISITOR NAME')->sortable(),
            TextColumn::make('purpose')->label('PURPOSE')->sortable(),
            TextColumn::make('quantity')->label('QTY')->sortable(),
            TextColumn::make('request_date')->label('REQUEST DATE')->date()->sortable(),
            TextColumn::make('preffered_time')->label('TIME')->sortable()->formatStateUsing(
                function ($record) {
                    return \Carbon\Carbon::parse($record->preffered_time)->format('H:i A');
                }
            ),
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
                        if ($record->user_id != null) {
                            $message = Message::create([
                                'user_id' => auth()->check() ? auth()->user()->id : 'null',
                                'resident_name' => UserInformation::where('unit_number', $record->unit)->first()->user->name,
                                'receiver_id' => UserInformation::where('unit_number', $record->unit)->first()->user->id,
                                'complainee_unit' => $record->unit,
                                'label_type' => 'Pass',
                                // 'nature_of_complaint' => $this->complaint,
                                'subject' => 'null',
                                'message' => 'Your Gate Pass request for UNIT ' . UserInformation::where('unit_number', $record->unit)->first()->user->user_information->unit_number . ' is approved by Admin. To proceed, please settle the amount on the lobby.',
                            ]);
                        } else {

                        }
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


    protected function getTableQuery(): Builder
    {

        return PassRequest::query()->whereHas('pass', function ($record) {
            $record->where('name', 'like', 'Gate Pass');
        })->orderByDesc('status');

    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')->label('STATUS')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'completed' => 'Completed',
                ])
        ];
    }
    public function render()
    {
        return view('livewire.admin.gate-pass-request-list');
    }
}
