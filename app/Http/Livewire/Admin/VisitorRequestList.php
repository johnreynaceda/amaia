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

class VisitorRequestList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    public $unitNumber;

    public function mount($unitNumber)
    {
        $this->unitNumber = $unitNumber;
    }

    protected function getTableQuery(): Builder
    {

        return PassRequest::query()->where('unit', $this->unitNumber)->whereHas('pass', function ($record) {
            $record->where('name', 'like', '%' . 'Visitor Pass' . '%');
        });

    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('pass.name')->label('TYPE')->searchable()->sortable(),
            TextColumn::make('unit')->label('UNIT')->searchable()->sortable(),
            TextColumn::make('visitor_name')->label('VISITOR NAME')->searchable()->sortable(),
            TextColumn::make('relation')->label('RELATION')->searchable()->sortable(),
            TextColumn::make('request_date')->label('REQUEST DATE')->date()->searchable()->sortable(),
            TextColumn::make('preffered_time')->label('TIME')->searchable()->sortable()->formatStateUsing(
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
        return view('livewire.admin.visitor-request-list');
    }

}