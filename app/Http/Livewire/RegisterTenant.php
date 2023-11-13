<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\UserInformation;
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
use DB;

class RegisterTenant extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $view_term = false;

    public $email, $password, $confirm_password, $type, $unit_number, $lastname, $firstname, $middlename, $date_of_birth, $gender, $civil_status, $nationality, $phone_number;


    public $unitNumbers, $roomNames, $availableRoomNumber, $occupiedRoom = [];
    public function mount()
    {
        $this->occupiedRoom = UserInformation::whereHas('user', function ($record) {
            $record->where('is_accepted', 1);
        })->pluck('unit_number')->toArray();
        // dd($this->occupiedRoom);
        $this->availableRoomNumber = array_values(array_diff(range(1, 912), $this->occupiedRoom));

        $this->roomNames = array_map(function ($number) {
            return 'Room ' . $number;
        }, $this->availableRoomNumber);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(4)
                ->schema([
                    TextInput::make('email')->label('Email Address')->email(),
                    TextInput::make('password')->label('Password')->password(),
                    TextInput::make('confirm_password')->label('Confirm Password')->password()->same('password'),
                    Select::make('type')->label('Resident Type')
                        ->options([
                            'Unit Owner' => 'Unit Owner',
                            'Tenant' => 'Tenant',

                        ]),
                    Select::make('unit_number')->label('Unit Number')->searchable()
                        ->options(
                            array_combine($this->availableRoomNumber, $this->roomNames)
                        ),
                ]),
            Fieldset::make('GUEST INFORMATION')
                ->schema([
                    TextInput::make('lastname')->label('Lastname'),
                    TextInput::make('firstname')->label('Firtstname'),
                    TextInput::make('middlename')->label('Middlename'),
                    DatePicker::make('date_of_birth'),
                    Select::make('gender')->label('Gender')
                        ->options([
                            'Male' => 'Male',
                            'Female' => 'Female',
                        ]),
                    Select::make('civil_status')->label('Civil Status')
                        ->options([
                            'Single' => 'Single',
                            'Married' => 'Married',
                            'Widoweded' => 'Widoweded',
                            'Divorceced' => 'Divorced'
                        ]),

                    TextInput::make('nationality')->label('Nationality'),
                    TextInput::make('phone_number')->label('Phone Number'),

                ])
                ->columns(4)


        ];
    }

    public function registerUser()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'type' => 'required',
            'unit_number' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'middlename' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'civil_status' => 'required',
            'nationality' => 'required',
            'phone_number' => 'required',
        ]);

        DB::beginTransaction();
        $user = User::create([
            'name' => $this->firstname . ' ' . $this->lastname,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        UserInformation::create([
            'user_id' => $user->id,
            'resident_type' => $this->type,
            'unit_number' => $this->unit_number,
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'civil_status' => $this->civil_status,
            'nationality' => $this->nationality,
            'phone_number' => $this->phone_number
        ]);
        DB::commit();
        sleep(2);
        auth()->loginUsingId($user->id);
        sweetalert()->addSuccess('Registered Successfully');
        return redirect()->route('dashboard');






    }

    public function render()
    {
        return view('livewire.register-tenant');
    }
}
