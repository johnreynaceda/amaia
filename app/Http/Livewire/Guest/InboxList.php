<?php

namespace App\Http\Livewire\Guest;

use App\Models\Message;
use App\Models\MessageAttachment;
use Livewire\Component;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\Action;

// use Illuminate\Contracts\View\View;

class InboxList extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $compose_modal = false;
    public $sort = "All";
    public $attachment, $label, $message, $subject, $complaint;
    public function render()
    {
        return view('livewire.guest.inbox-list');
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('label')->label('Label Type')
                ->options([
                    'Complaint' => 'Complaint',
                    'Concerns' => 'Concerns',
                    'Maintenance' => 'Maintenance',
                    'Amenity' => 'Amenity',
                    'Package' => 'Package',
                ]),
            Select::make('complaint')->label('Nature of Complaint')
                ->options([
                    'Noise' => 'Noise',
                    'Trash' => 'Trash',
                    'Security' => 'Security',
                    'Boundaries' => 'Boundaries',
                    'Rule Violation' => 'Rule Violation',
                ]),
            TextInput::make('subject'),
            Textarea::make('message')

        ];
    }

    protected function getTableQuery(): Builder
    {

        if ($this->sort == 'All') {
            return Message::query()->where('receiver_id', auth()->user()->id)->orderBy('created_at', 'desc');
        } else {
            return Message::query()->where('receiver_id', auth()->user()->id)->where('read_at', null)->orderBy('created_at', 'desc');
        }

    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view')->label('View Message')->icon('heroicon-o-eye')->color('warning')->action(
                function ($record) {
                    $record->update([
                        'read_at' => now(),
                    ]);


                }
            )
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Split::make([
                ViewColumn::make('status')->view('guest.filament.inbox'),
                ViewColumn::make('message')->view('guest.filament.message'),
                TextColumn::make('created_at')->date()->alignEnd(),
            ])
        ];
    }

    public function sendMessage()
    {
        $this->validate([
            'label' => 'required',
            'subject' => 'required',
            'complaint' => 'required',
            'message' => 'required',
        ]);

        if ($this->attachment != null) {
            $message = Message::create([
                'user_id' => auth()->user()->id,
                'resident_name' => auth()->user()->name,
                'receiver_id' => 1,
                'complainee_unit' => auth()->user()->user_information->unit_number,
                'label_type' => $this->label,
                'nature_of_complaint' => $this->complaint,
                'subject' => $this->subject,
                'message' => $this->message,
            ]);

            foreach ($this->attachment as $key => $value) {
                MessageAttachment::create([
                    'message_id' => $message->id,
                    'file_path' => $value->store('message_attachment', 'public'),
                ]);
            }

            $this->compose_modal = false;
            sweetalert()->addSuccess('Message Sent');



        } else {
            Message::create([
                'user_id' => auth()->user()->id,
                'receiver_id' => 1,
                'resident_name' => auth()->user()->name,
                'complainee_unit' => auth()->user()->user_information->unit_number,
                'label_type' => $this->label,
                'nature_of_complaint' => $this->complaint,
                'subject' => $this->subject,
                'message' => $this->message,
            ]);

            $this->compose_modal = false;
            sweetalert()->addSuccess('Message Sent');
        }
    }

    public function removeAttachment()
    {
        $this->reset('attachment');
    }
}
