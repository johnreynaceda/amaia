<?php

namespace App\Http\Livewire\Guest;

use Livewire\Component;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class Profile extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $email, $type, $unit_number;
    public $lastname, $firstname, $middlename, $date_of_birth, $gender, $civil_status, $nationality, $preffered_address, $turn_over, $phone_number, $allow_notifications;

    public function mount()
    {
        $this->allow_notifications = auth()->user()->allow_notification;
    }
    public function render()
    {
        return view('livewire.guest.profile');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    TextInput::make('email')->label('Email Address')->email()->placeholder(auth()->user()->email),
                    Select::make('type')->label('Resident Type')->disabled()->placeholder(auth()->user()->user_information->resident_type)
                        ->options([
                            'Unit Owner' => 'Unit Owner',
                            'Tenant' => 'Tenant',

                        ]),
                    Select::make('unit_number')->label('Unit Number')->disabled()->placeholder('Room ' . auth()->user()->user_information->unit_number)
                        ->options([
                            'draft' => 'Draft',
                            'reviewing' => 'Reviewing',
                            'published' => 'Published',
                        ]),
                ]),
            Fieldset::make('GUEST INFORMATION')
                ->schema([
                    TextInput::make('lastname')->label('Lastname')->placeholder(auth()->user()->user_information->lastname),
                    TextInput::make('firstname')->label('Firtstname')->placeholder(auth()->user()->user_information->firstname),
                    TextInput::make('middlename')->label('Middlename')->placeholder(auth()->user()->user_information->middlename),
                    DatePicker::make('date_of_birth')->placeholder(\Carbon\Carbon::parse(auth()->user()->user_information->date_of_birth)->format('F d, Y')),
                    Select::make('gender')->label('Gender')->placeholder(auth()->user()->user_information->gender)
                        ->options([
                            'Male' => 'Male',
                            'Female' => 'Female',
                        ]),
                    Select::make('civil_status')->label('Civil Status')->placeholder(auth()->user()->user_information->civil_status)
                        ->options([
                            'Single' => 'Single',
                            'Married' => 'Married',
                            'Windowed' => 'Windowed',
                            'Divorceced' => 'Divorced'
                        ]),

                    TextInput::make('nationality')->label('Nationality')->placeholder(auth()->user()->user_information->nationality),
                    Textarea::make('preffered_address')->label('Preferred Mailing Address')->placeholder(auth()->user()->user_information->preferred_mailing_address),
                    DatePicker::make('turn_over')->placeholder(auth()->user()->user_information->turn_over_date == null ? '' : \Carbon\Carbon::parse(auth()->user()->user_information->turn_over_date)->format('F d, Y')),
                    TextInput::make('phone_number')->label('Phone Number')->placeholder(auth()->user()->user_information->phone_number)->mask(fn(TextInput\Mask $mask) => $mask->pattern('00000000000')),

                    // Toggle::make('allow_notifications')->label('Allow Notifications')->inline(false)
                    //     ->onColor('primary')
                    //     ->offColor('danger')->onIcon('heroicon-s-bell')
                    //     ->offIcon('heroicon-s-ban')
                ])
                ->columns(4)


        ];
    }

    public function submitProfile()
    {
        $data = auth()->user();
        $data->update([
            'email' => $this->email == null ? auth()->user()->email : $this->email,
            'allow_notification' => $this->allow_notifications != true ? false : true,
        ]);

        $data->user_information->update([
            'lastname' => $this->lastname == null ? auth()->user()->user_information->lastname : $this->lastname,
            'firstname' => $this->firstname == null ? auth()->user()->user_information->firstname : $this->firstname,
            'middlename' => $this->middlename == null ? auth()->user()->user_information->middlename : $this->middlename,
            'date_of_birth' => $this->date_of_birth == null ? auth()->user()->user_information->date_of_birth : $this->date_of_birth,
            'gender' => $this->gender == null ? auth()->user()->user_information->gender : $this->gender,
            'civil_status' => $this->civil_status == null ? auth()->user()->user_information->civil_status : $this->civil_status,
            'nationality' => $this->nationality == null ? auth()->user()->user_information->nationality : $this->nationality,
            'preferred_mailing_address' => $this->preffered_address == null ? auth()->user()->user_information->preferred_mailing_address : $this->preffered_address,
            'turn_over_date' => $this->turn_over == null ? auth()->user()->user_information->turn_over_date : $this->turn_over,
            'phone_number' => $this->phone_number == null ? auth()->user()->user_information->phone_number : $this->phone_number,
        ]);

        sweetalert()->addSuccess('Profile Updated');

    }

}

