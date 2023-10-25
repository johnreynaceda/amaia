<?php

namespace App\Http\Livewire\Guest;

use App\Models\Amenity;
use App\Models\AmenityRequest;
use App\Models\Maintenance;
use App\Models\MaintenanceRequest;
use App\Models\Pass;
use App\Models\PassRequest;
use Livewire\Component;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;

class RequestTransaction extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $maintenance_id, $request_date, $request_time, $request, $amount = 0;

    public $amenity_type, $ocassion, $total_persons, $remark;

    public $visitor, $building, $unit, $particulars, $quantity, $purpose;

    public $relation, $contact, $reason;
    public $view_form = false;
    public $amenity_form = false;

    public $gate_form = false;
    public $visitor_form = false;
    public $visitor_clause = false;
    public function render()
    {
        return view('livewire.guest.request-transaction', [
            'maintenances' => Maintenance::get(),
            'amenities' => Amenity::get(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('request')->label('Request Type')->reactive()
                        ->options([
                            'Maintenance' => 'Maintenance',
                            'Amenities' => 'Amenities',
                            'Gate Pass' => 'Gate Pass',
                            'Visitor Pass' => 'Visitor Pass',
                            'Parcel Pass' => 'Parcel Pass',
                        ])
                ])

        ];
    }

    public function updatedAmenityType()
    {

        $data = Amenity::where('id', $this->amenity_type)->first();
        $this->amount = $data->amount;
    }

    public function submitMaintenance()
    {
        $this->validate([
            'maintenance_id' => 'required',
            'request_date' => 'required',
            'request_time' => 'required',
            'request' => 'required',
        ]);

        MaintenanceRequest::create([
            'user_id' => auth()->user()->id,
            'maintenance_id' => $this->maintenance_id,
            'request_date' => $this->request_date,
            'preffered_time' => $this->request_time
        ]);

        sweetalert()->addSuccess('Request Submitted');

        return redirect()->route('guest.requests');
    }

    public function submitAmenities()
    {
        $this->validate([
            'amenity_type' => 'required',
            'ocassion' => 'required',
            'remark' => 'required',
            'total_persons' => 'required',
            'request_date' => 'required',
            'request_time' => 'required',
            'request' => 'required',
            'visitor' => 'required',
        ]);
        AmenityRequest::create([
            'user_id' => auth()->user()->id,
            'amenity_id' => $this->amenity_type,
            'request_date' => $this->request_date,
            'preffered_time' => $this->request_time,
            'remark' => $this->remark,
            'no_of_person' => $this->total_persons,
            'amount' => $this->amount,
            'visitors' => $this->visitor,
        ]);

        sweetalert()->addSuccess('Request Submitted');

        return redirect()->route('guest.requests');
    }

    public function submitGatePass()
    {


        $this->validate([
            'visitor' => 'required',
            'building' => 'required',
            'unit' => 'required',
            'purpose' => 'required',
            'particulars' => 'required',
            'quantity' => 'required',
            'request_date' => 'required',
            'request_time' => 'required',
        ]);
        $data = Pass::where('name', 'like', '%' . 'Gate Pass' . '%')->first();

        PassRequest::create([
            'user_id' => auth()->user()->id,
            'pass_id' => $data->id,
            'visitor_name' => $this->visitor,
            'building' => $this->building,
            'unit' => $this->unit,
            'purpose' => $this->purpose,
            'particulars' => $this->particulars,
            'quantity' => $this->quantity,
            'request_date' => $this->request_date,
            'preffered_time' => $this->request_time
        ]);
        sleep(2);
        sweetalert()->addSuccess('Request Submitted');

        return redirect()->route('guest.requests');
    }

    public function submitVisitorPass()
    {
        $this->validate([
            'visitor' => 'required',
            'unit' => 'required',
            'relation' => 'required',
            'request_date' => 'required',
            'request_time' => 'required',
        ]);
        $data = Pass::where('name', 'like', '%' . 'Visitor Pass' . '%')->first();

        PassRequest::create([
            'user_id' => auth()->user()->id,
            'pass_id' => $data->id,
            'visitor_name' => $this->visitor,
            'unit' => $this->unit,
            'relation' => $this->relation,
            'request_date' => $this->request_date,
            'preffered_time' => $this->request_time

        ]);
        sleep(2);
        sweetalert()->addSuccess('Request Submitted');

        return redirect()->route('guest.requests');
    }

    public function submitParcelPass()
    {
        $this->validate([
            'contact' => 'required',
            'quantity' => 'required',
            'reason' => 'required',
            'request_date' => 'required',
        ]);
        $data = Pass::where('name', 'like', '%' . 'Parcel Pass' . '%')->first();

        PassRequest::create([
            'user_id' => auth()->user()->id,
            'pass_id' => $data->id,
            'contact_number' => $this->contact,
            'quantity' => $this->quantity,
            'purpose' => $this->reason,
            'request_date' => $this->request_date,
            'preffered_time' => now()

        ]);
        sleep(2);
        sweetalert()->addSuccess('Request Submitted');

        return redirect()->route('guest.requests');
    }
}
