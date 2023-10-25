<?php

namespace App\Http\Livewire\Admin;

use App\Models\Message;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Livewire\WithFileUploads;

class MessageList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use WithFileUploads;
    public $compose = false;
    public $lot_number;
    public $details;
    public $attachment;

    protected function getTableQuery(): Builder
    {
        return Message::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label('RESIDENT NAME')->searchable(),
            TextColumn::make('unit')->label('COMPLAINEE UNIT')->searchable(),
            TextColumn::make('type')->label('NATURE OF COMPLAINT')->searchable(),
            TextColumn::make('created_at')->date()->label('DATE OF COMPLAINT')->searchable(),
            TextColumn::make('message')->label('MESSAGE')->searchable(),
            BadgeColumn::make('status')->label('STATUS')
                ->enum([
                    'active' => 'Active',
                    'completed' => 'Completed',
                ])->colors([
                        'success' => 'active',
                        'primary' => 'completed'
                    ])->sortable(),
            TextColumn::make('completed_date')->date()->label('DATE COMPLETED')->searchable(),

        ];

    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')->label('TYPE')
                ->options([
                    'complaint' => 'Complaint',
                    'messages' => 'Messages',
                ])
                ->attribute('nature_of_complaint')
        ];
    }

    public function render()
    {
        return view('livewire.admin.message-list');
    }

    public function updatedAttachment()
    {

    }

    public function updatedLotNumber()
    {
        $data = UserInformation::where('unit_number', $this->lot_number)->first();
        if ($data) {
            $this->details = $data;
        } else {
            sweetalert()->addError('No Tenant Found.');
            $this->reset('lot_number', 'details');
        }
    }
}
