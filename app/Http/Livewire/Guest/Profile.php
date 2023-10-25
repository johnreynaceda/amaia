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
    public function render()
    {
        return view('livewire.guest.profile');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    TextInput::make('email')->label('Email Address')->email()->disabled()->placeholder(auth()->user()->email),
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
                    TextInput::make('lastname')->label('Lastname')->disabled()->placeholder(auth()->user()->user_information->lastname),
                    TextInput::make('firstname')->label('Firtstname')->disabled()->placeholder(auth()->user()->user_information->firstname),
                    TextInput::make('middlename')->label('Middlename')->disabled()->placeholder(auth()->user()->user_information->middlename),
                    DatePicker::make('date_of_birth')->disabled()->placeholder(\Carbon\Carbon::parse(auth()->user()->user_information->date_of_birth)->format('F d, Y')),
                    Select::make('gender')->label('Gender')->disabled()->placeholder(auth()->user()->user_information->gender)
                        ->options([
                            'Male' => 'Male',
                            'Female' => 'Female',
                        ]),
                    Select::make('civil_status')->label('Civil Status')->disabled()->placeholder(auth()->user()->user_information->civil_status)
                        ->options([
                            'Single' => 'Single',
                            'Married' => 'Married',
                            'Widoweded' => 'Widoweded',
                            'Divorceced' => 'Divorced'
                        ]),

                    TextInput::make('nationality')->label('Nationality')->disabled()->placeholder(auth()->user()->user_information->nationality),
                    Textarea::make('preferred_address')->label('Preferred Mailing Address'),
                    TextInput::make('turn_over')->label('Turn Over Date'),
                    TextInput::make('phone_number')->label('Phone Number')->disabled()->placeholder(auth()->user()->user_information->phone_number),
                    Toggle::make('allow_notifications')->label('Allow Notifications')->inline(false)
                        ->onColor('primary')
                        ->offColor('danger')->onIcon('heroicon-s-bell')
                        ->offIcon('heroicon-s-ban')
                ])
                ->columns(4)


        ];
    }

}